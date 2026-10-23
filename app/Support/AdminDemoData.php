<?php

namespace App\Support;

use Illuminate\Support\Collection;

class AdminDemoData
{
    /**
     * @return Collection<int, array{name: string, slug: string, description: string, product_count: int, is_active: bool, created_at: string}>
     */
    public static function categories(): Collection
    {
        return collect([
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphone, earbud, dan speaker untuk setiap momen.',
                'product_count' => 24,
                'is_active' => true,
                'created_at' => '14 Okt 2026',
            ],
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Ponsel pintar untuk komunikasi dan produktivitas.',
                'product_count' => 18,
                'is_active' => true,
                'created_at' => '12 Okt 2026',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop untuk bekerja, belajar, dan berkarya.',
                'product_count' => 12,
                'is_active' => true,
                'created_at' => '10 Okt 2026',
            ],
            [
                'name' => 'Wearable',
                'slug' => 'wearable',
                'description' => 'Perangkat wearable untuk aktivitas sehari-hari.',
                'product_count' => 9,
                'is_active' => true,
                'created_at' => '8 Okt 2026',
            ],
            [
                'name' => 'Kamera',
                'slug' => 'kamera',
                'description' => 'Kamera dan perlengkapan untuk mengabadikan momen.',
                'product_count' => 7,
                'is_active' => true,
                'created_at' => '6 Okt 2026',
            ],
            [
                'name' => 'Aksesori',
                'slug' => 'aksesori',
                'description' => 'Aksesori pelengkap perangkat elektronik.',
                'product_count' => 31,
                'is_active' => true,
                'created_at' => '4 Okt 2026',
            ],
            [
                'name' => 'Tablet',
                'slug' => 'tablet',
                'description' => 'Tablet untuk hiburan dan produktivitas.',
                'product_count' => 8,
                'is_active' => true,
                'created_at' => '2 Okt 2026',
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Konsol dan perlengkapan gaming.',
                'product_count' => 11,
                'is_active' => false,
                'created_at' => '1 Okt 2026',
            ],
        ]);
    }

    /**
     * @return Collection<int, array{
     *     code: string,
     *     date: string,
     *     buyer_id: int,
     *     buyer: string,
     *     email: string,
     *     product: string,
     *     category: string,
     *     price: int,
     *     quantity: int,
     *     payment_method: string,
     *     payment_gateway: string,
     *     gateway_reference: string,
     *     payment_status: string,
     *     order_status: string
     * }>
     */
    public static function transactions(): Collection
    {
        return collect([
            [
                'code' => 'SN-2026-1023-005',
                'date' => '23 Okt 2026, 11.35',
                'buyer_id' => 2,
                'buyer' => 'Bunga Lestari',
                'email' => 'bunga.lestari@example.test',
                'product' => 'Smartphone Nova X',
                'category' => 'Smartphone',
                'price' => 4899000,
                'quantity' => 1,
                'payment_method' => 'QRIS',
                'payment_gateway' => 'Midtrans (demo)',
                'gateway_reference' => 'MID-DEMO-84022',
                'payment_status' => 'Menunggu pembayaran',
                'order_status' => 'Diproses',
            ],
            [
                'code' => 'SN-2026-1023-004',
                'date' => '23 Okt 2026, 11.02',
                'buyer_id' => 1,
                'buyer' => 'Andi Pratama',
                'email' => 'andi.pratama@example.test',
                'product' => 'Headphone Wireless Pro',
                'category' => 'Audio',
                'price' => 850000,
                'quantity' => 1,
                'payment_method' => 'QRIS',
                'payment_gateway' => 'Midtrans (demo)',
                'gateway_reference' => 'MID-DEMO-84021',
                'payment_status' => 'Lunas',
                'order_status' => 'Selesai',
            ],
            [
                'code' => 'SN-2026-1023-003',
                'date' => '23 Okt 2026, 10.18',
                'buyer_id' => 2,
                'buyer' => 'Bunga Lestari',
                'email' => 'bunga.lestari@example.test',
                'product' => 'Smartphone Nova X',
                'category' => 'Smartphone',
                'price' => 4899000,
                'quantity' => 1,
                'payment_method' => 'Virtual Account BCA',
                'payment_gateway' => 'Midtrans (demo)',
                'gateway_reference' => 'MID-DEMO-84020',
                'payment_status' => 'Lunas',
                'order_status' => 'Selesai',
            ],
            [
                'code' => 'SN-2026-1023-002',
                'date' => '23 Okt 2026, 09.25',
                'buyer_id' => 1,
                'buyer' => 'Andi Pratama',
                'email' => 'andi.pratama@example.test',
                'product' => 'Headphone Wireless Pro',
                'category' => 'Audio',
                'price' => 850000,
                'quantity' => 2,
                'payment_method' => 'Transfer Bank BCA',
                'payment_gateway' => 'Midtrans (demo)',
                'gateway_reference' => 'MID-DEMO-84019',
                'payment_status' => 'Lunas',
                'order_status' => 'Selesai',
            ],
            [
                'code' => 'SN-2026-1023-001',
                'date' => '23 Okt 2026, 08.10',
                'buyer_id' => 3,
                'buyer' => 'Citra Maharani',
                'email' => 'citra.maharani@example.test',
                'product' => 'Laptop Studio 14',
                'category' => 'Laptop',
                'price' => 12499000,
                'quantity' => 1,
                'payment_method' => 'GoPay',
                'payment_gateway' => 'Midtrans (demo)',
                'gateway_reference' => 'MID-DEMO-84018',
                'payment_status' => 'Dibatalkan',
                'order_status' => 'Dibatalkan',
            ],
        ]);
    }

    /**
     * @return Collection<int, array{name: string, category: string, price: int, sold_quantity: int, revenue: int, buyers: list<string>}>
     */
    public static function products(): Collection
    {
        return self::transactions()
            ->where('payment_status', 'Lunas')
            ->groupBy('product')
            ->map(function (Collection $productTransactions): array {
                $product = $productTransactions->first();

                return [
                    'name' => $product['product'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'sold_quantity' => $productTransactions->sum('quantity'),
                    'revenue' => $productTransactions->sum(fn (array $transaction): int => $transaction['price'] * $transaction['quantity']),
                    'buyers' => $productTransactions->pluck('buyer')->unique()->values()->all(),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, array{name: string, email: string, transaction_count: int, paid_total: int, last_transaction: string}>
     */
    public static function users(): Collection
    {
        return self::transactions()
            ->groupBy('buyer_id')
            ->map(function (Collection $userTransactions): array {
                $user = $userTransactions->first();

                return [
                    'name' => $user['buyer'],
                    'email' => $user['email'],
                    'transaction_count' => $userTransactions->count(),
                    'paid_total' => $userTransactions
                        ->where('payment_status', 'Lunas')
                        ->sum(fn (array $transaction): int => $transaction['price'] * $transaction['quantity']),
                    'last_transaction' => $user['date'],
                ];
            })
            ->values();
    }
}
