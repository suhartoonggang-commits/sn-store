<?php

use App\Contracts\PaymentGateway;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\MidtransPaymentGateway;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravolt\Indonesia\Facade as Indonesia;

use function Pest\Laravel\mock;

function createIndonesianRegionHierarchy(): array
{
    $provinceId = DB::table('indonesia_provinces')->insertGetId([
        'code' => '11',
        'name' => 'Aceh',
    ]);
    $cityId = DB::table('indonesia_cities')->insertGetId([
        'code' => '1101',
        'province_code' => '11',
        'name' => 'Kabupaten Aceh Selatan',
    ]);
    $districtId = DB::table('indonesia_districts')->insertGetId([
        'code' => '1101010',
        'city_code' => '1101',
        'name' => 'Bakongan',
    ]);
    $villageId = DB::table('indonesia_villages')->insertGetId([
        'code' => '1101010001',
        'district_code' => '1101010',
        'name' => 'Keude Bakongan',
    ]);

    Indonesia::clearCache();

    return [
        'provinceId' => (string) $provinceId,
        'cityId' => (string) $cityId,
        'districtId' => (string) $districtId,
        'villageId' => (string) $villageId,
    ];
}

function fakeRajaOngkirShippingApi(int $shippingCost = 25000, bool $connectionFailure = false): void
{
    config([
        'services.rajaongkir.api_key' => 'test-rajaongkir-api-key',
        'services.rajaongkir.base_url' => 'https://rajaongkir.komerce.id/api/v1',
        'services.rajaongkir.origin_province' => 'Sulawesi Selatan',
        'services.rajaongkir.origin_city' => 'Makassar',
        'services.rajaongkir.origin_district' => 'Makassar',
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'rajaongkir.komerce.id/api/v1/destination/province' => Http::response(['data' => [
            ['id' => 1, 'name' => 'Sulawesi Selatan'],
            ['id' => 2, 'name' => 'Aceh'],
        ]]),
        'rajaongkir.komerce.id/api/v1/destination/city/1' => Http::response(['data' => [
            ['id' => 10, 'name' => 'Makassar'],
        ]]),
        'rajaongkir.komerce.id/api/v1/destination/district/10' => Http::response(['data' => [
            ['id' => 100, 'name' => 'Makassar'],
        ]]),
        'rajaongkir.komerce.id/api/v1/destination/city/2' => Http::response(['data' => [
            ['id' => 20, 'name' => 'Aceh Selatan'],
        ]]),
        'rajaongkir.komerce.id/api/v1/destination/district/20' => Http::response(['data' => [
            ['id' => 200, 'name' => 'Bakongan'],
        ]]),
        'rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost' => $connectionFailure
            ? Http::failedConnection()
            : Http::response(['data' => [[
                'code' => 'jne',
                'name' => 'JNE',
                'costs' => [[
                    'service' => 'REG',
                    'description' => 'Layanan Reguler',
                    'cost' => $shippingCost,
                    'etd' => '2-3',
                ]],
            ]]]),
    ]);
}

it('redirects guests to login before they can use the cart', function () {
    $product = Product::factory()->create(['stock' => 3]);

    $this->get(route('cart.index'))->assertRedirect(route('login'));
    $this->post(route('cart.items.store', $product))->assertRedirect(route('login'));

    $this->assertDatabaseCount('cart_items', 0);
});

it('rejects adding products without stock and quantities above available stock', function () {
    $user = User::factory()->create();
    $emptyProduct = Product::factory()->create(['stock' => 0]);
    $limitedProduct = Product::factory()->create(['stock' => 2]);

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $emptyProduct))
        ->assertSessionHasErrors('product');

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $limitedProduct), ['quantity' => 3])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

it('adds items and prevents cart quantities from exceeding stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);

    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $cartItem = CartItem::query()->firstOrFail();
    expect($cartItem->quantity)->toBe(2);

    $this->actingAs($user)
        ->from(route('cart.index'))
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 4])
        ->assertSessionHasErrors('quantity');

    expect($cartItem->refresh()->quantity)->toBe(2);

    $this->actingAs($user)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 3])
        ->assertRedirect(route('cart.index'));

    expect($cartItem->refresh()->quantity)->toBe(3);
});

it('does not allow users to change another users cart item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $cartItem = $owner->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($otherUser)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertNotFound();

    expect($cartItem->refresh()->quantity)->toBe(1);
});

it('prefills checkout fields from the authenticated account', function () {
    createIndonesianRegionHierarchy();

    $user = User::factory()->create([
        'name' => 'Nadia Putri',
        'email' => 'nadia@example.test',
        'phone' => '08123456789',
        'address' => 'Jalan Merdeka Nomor 10',
    ]);
    $product = Product::factory()->create(['stock' => 2]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)
        ->get(route('checkout.create'))
        ->assertOk()
        ->assertViewIs('pages.public.checkout')
        ->assertSee('value="Nadia Putri"', false)
        ->assertSee('value="nadia@example.test"', false)
        ->assertSee('value="08123456789"', false)
        ->assertSee('Jalan Merdeka Nomor 10')
        ->assertSee('name="province_id"', false)
        ->assertSee('name="postal_code"', false)
        ->assertSee(':disabled="!provinceId', false);
});

it('returns RajaOngkir shipping rates from Makassar for the selected destination', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi();

    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($user)
        ->postJson(route('shipping.quotes'), [
            'province_id' => $regions['provinceId'],
            'city_id' => $regions['cityId'],
            'district_id' => $regions['districtId'],
            'courier' => 'jne',
        ])
        ->assertOk()
        ->assertJsonPath('rates.0.courier_code', 'jne')
        ->assertJsonPath('rates.0.service', 'REG')
        ->assertJsonPath('rates.0.cost', 25000);

    Http::assertSent(fn (ClientRequest $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost'
        && $request->hasHeader('key', 'test-rajaongkir-api-key'));
    Http::assertSent(fn (ClientRequest $request): bool => $request->data() == [
        'origin' => '100',
        'destination' => '200',
        'weight' => '2000',
        'courier' => 'jne',
        'price' => 'lowest',
    ]);
});

it('returns a controlled error when RajaOngkir is unreachable while checking rates', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi(connectionFailure: true);

    $this->actingAs(User::factory()->create())
        ->postJson(route('shipping.quotes'), [
            'province_id' => $regions['provinceId'],
            'city_id' => $regions['cityId'],
            'district_id' => $regions['districtId'],
            'courier' => 'jne',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Ongkos kirim belum dapat dihitung. Silakan coba kembali.');
});

it('creates a pending order and deducts stock when checkout succeeds', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi();

    mock(PaymentGateway::class)
        ->shouldReceive('createSnapPayment')
        ->once()
        ->andReturn(['token' => 'snap-test-token']);

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Headphone Wireless',
        'price' => 850000,
        'stock' => 5,
    ]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $response = $this->actingAs($user)->post(route('checkout.store'), [
        'name' => 'Nadia Putri',
        'phone' => '08123456789',
        'email' => 'nadia@example.test',
        'address' => 'Jalan Merdeka Nomor 10, Jakarta',
        'province_id' => $regions['provinceId'],
        'city_id' => $regions['cityId'],
        'district_id' => $regions['districtId'],
        'village_id' => $regions['villageId'],
        'postal_code' => '23773',
        'shipping_courier' => 'jne',
        'shipping_service' => 'REG',
        'shipping_cost' => 1,
    ]);

    $order = Order::query()->firstOrFail();
    $response->assertRedirect(route('orders.show', $order));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'customer_name' => 'Nadia Putri',
        'customer_phone' => '08123456789',
        'customer_email' => 'nadia@example.test',
        'province_code' => '11',
        'province_name' => 'Aceh',
        'city_code' => '1101',
        'city_name' => 'Kabupaten Aceh Selatan',
        'district_code' => '1101010',
        'district_name' => 'Bakongan',
        'village_code' => '1101010001',
        'village_name' => 'Keude Bakongan',
        'postal_code' => '23773',
        'shipping_cost' => 25000,
        'shipping_courier' => 'jne',
        'shipping_service' => 'REG',
        'payment_status' => Order::PAYMENT_PENDING,
        'snap_token' => 'snap-test-token',
        'total' => '1725000.00',
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Headphone Wireless',
        'quantity' => 2,
        'line_total' => '1700000.00',
    ]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseCount('cart_items', 0);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'phone' => '08123456789',
        'address' => 'Jalan Merdeka Nomor 10, Jakarta',
    ]);

    $this->actingAs($user)
        ->get(route('orders.show', $order))
        ->assertOk()
        ->assertViewIs('pages.public.order')
        ->assertSeeText($order->order_number)
        ->assertSeeText('Menunggu pembayaran')
        ->assertSeeText('Headphone Wireless')
        ->assertSeeText('Kabupaten Aceh Selatan')
        ->assertSeeText('23773');
});

it('keeps the cart and stock when checkout finds insufficient stock', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi();

    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Alamat lengkap nomor 10',
            'province_id' => $regions['provinceId'],
            'city_id' => $regions['cityId'],
            'district_id' => $regions['districtId'],
            'village_id' => $regions['villageId'],
            'postal_code' => '23773',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('keeps the cart when RajaOngkir is unreachable during checkout', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi(connectionFailure: true);

    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Alamat lengkap nomor 10',
            'province_id' => $regions['provinceId'],
            'city_id' => $regions['cityId'],
            'district_id' => $regions['districtId'],
            'village_id' => $regions['villageId'],
            'postal_code' => '23773',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ])
        ->assertRedirect(route('checkout.create'))
        ->assertSessionHasErrors('shipping');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
});

it('restores stock and cart when Midtrans cannot create a Snap payment', function () {
    $regions = createIndonesianRegionHierarchy();
    fakeRajaOngkirShippingApi();

    mock(PaymentGateway::class)
        ->shouldReceive('createSnapPayment')
        ->once()
        ->andThrow(new RuntimeException('Midtrans unavailable'));

    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Alamat lengkap nomor 10',
            'province_id' => $regions['provinceId'],
            'city_id' => $regions['cityId'],
            'district_id' => $regions['districtId'],
            'village_id' => $regions['villageId'],
            'postal_code' => '23773',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ])
        ->assertRedirect(route('cart.index'))
        ->assertSessionHasErrors('payment');

    $order = Order::query()->firstOrFail();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_status' => Order::PAYMENT_FAILED,
    ]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('does not allow users to view another users order', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $owner->id,
        'order_number' => 'SN-20261106-ABC12345',
        'customer_name' => $owner->name,
        'customer_phone' => '08123456789',
        'customer_email' => $owner->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 10000,
        'total' => 10000,
    ]);

    $this->actingAs($otherUser)
        ->get(route('orders.show', $order))
        ->assertNotFound();
});

it('marks pending orders as paid idempotently for a future gateway callback', function () {
    $user = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'SN-20261106-PAY12345',
        'customer_name' => $user->name,
        'customer_phone' => '08123456789',
        'customer_email' => $user->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 10000,
        'total' => 10000,
    ]);

    $checkoutService = app(CheckoutService::class);
    $checkoutService->markPaid($order, 'gateway-test', 'PAYMENT-123');
    $checkoutService->markPaid($order->refresh(), 'gateway-test', 'PAYMENT-456');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_status' => Order::PAYMENT_PAID,
        'payment_gateway' => 'gateway-test',
        'payment_reference' => 'PAYMENT-123',
    ]);
});

it('verifies Midtrans notification signatures without making network requests', function () {
    config(['services.midtrans.server_key' => 'test-server-key']);
    $notification = [
        'order_id' => 'SN-20261106-SIGN1234',
        'status_code' => '200',
        'gross_amount' => '10000.00',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].'test-server-key'
    );

    expect(app(MidtransPaymentGateway::class)->verifyNotification($notification))->toBeTrue();
    expect(app(MidtransPaymentGateway::class)->verifyNotification([
        ...$notification,
        'signature_key' => str_repeat('0', 128),
    ]))->toBeFalse();
});

it('rejects invalid Midtrans notifications and accepts provider-confirmed settlement', function () {
    $invalidGateway = mock(PaymentGateway::class);
    $invalidGateway->shouldReceive('verifyNotification')->once()->andReturn(false);

    $notification = [
        'order_id' => 'SN-20261106-WEBH1234',
        'status_code' => '200',
        'gross_amount' => '10000.00',
        'signature_key' => str_repeat('0', 128),
        'transaction_id' => 'transaction-123',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
    ];

    $this->postJson(route('midtrans.notifications'), $notification)->assertForbidden();

    $user = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'SN-20261106-WEBH1234',
        'customer_name' => $user->name,
        'customer_phone' => '08123456789',
        'customer_email' => $user->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 10000,
        'total' => 10000,
    ]);

    $validGateway = mock(PaymentGateway::class);
    $validGateway->shouldReceive('verifyNotification')->once()->andReturn(true);
    $validGateway->shouldReceive('transactionStatus')->once()->with($order->order_number)->andReturn((object) [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '10000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'transaction-123',
    ]);

    $this->postJson(route('midtrans.notifications'), $notification)
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_status' => Order::PAYMENT_PAID,
        'payment_gateway' => 'midtrans',
        'payment_reference' => 'transaction-123',
    ]);
});

it('restores stock and cart when Midtrans confirms an expired payment', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'SN-20261106-EXP12345',
        'customer_name' => $user->name,
        'customer_phone' => '08123456789',
        'customer_email' => $user->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 20000,
        'total' => 20000,
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'unit_price' => 10000,
        'quantity' => 2,
        'line_total' => 20000,
    ]);
    $product->decrement('stock', 2);

    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('verifyNotification')->once()->andReturn(true);
    $gateway->shouldReceive('transactionStatus')->once()->andReturn((object) [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '20000.00',
        'transaction_status' => 'expire',
        'transaction_id' => 'transaction-expired',
    ]);

    $this->postJson(route('midtrans.notifications'), [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '20000.00',
        'signature_key' => str_repeat('a', 128),
        'transaction_id' => 'transaction-expired',
        'transaction_status' => 'expire',
    ])->assertNoContent();

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => Order::PAYMENT_FAILED]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('returns a json validation response for malformed Midtrans notifications', function () {
    $this->postJson(route('midtrans.notifications'), [])->assertUnprocessable();
});

it('returns only children belonging to the selected region', function () {
    $regions = createIndonesianRegionHierarchy();

    $this->getJson(route('regions.provinces'))
        ->assertOk()
        ->assertJsonFragment(['id' => $regions['provinceId'], 'name' => 'Aceh']);
    $this->getJson(route('regions.cities', $regions['provinceId']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $regions['cityId'], 'name' => 'Kabupaten Aceh Selatan']);
    $this->getJson(route('regions.districts', $regions['cityId']))
        ->assertOk()
        ->assertJsonFragment(['id' => $regions['districtId'], 'name' => 'Bakongan']);
    $this->getJson(route('regions.villages', $regions['districtId']))
        ->assertOk()
        ->assertJsonFragment(['id' => $regions['villageId'], 'name' => 'Keude Bakongan']);
});

it('rejects checkout when selected city does not belong to selected province', function () {
    $regions = createIndonesianRegionHierarchy();
    DB::table('indonesia_provinces')->insert([
        'code' => '12',
        'name' => 'Sumatera Utara',
    ]);
    $otherCityId = DB::table('indonesia_cities')->insertGetId([
        'code' => '1201',
        'province_code' => '12',
        'name' => 'Kabupaten Nias',
    ]);
    Indonesia::clearCache();
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Alamat lengkap nomor 10',
            'province_id' => $regions['provinceId'],
            'city_id' => $otherCityId,
            'district_id' => $regions['districtId'],
            'village_id' => $regions['villageId'],
            'postal_code' => '23773',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ])
        ->assertSessionHasErrors('city_id');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
});
