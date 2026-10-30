@php($isEditing = $product !== null)

<form
    method="POST"
    action="{{ $isEditing ? route('admin.products.update', $product) : route('admin.products.store') }}"
    enctype="multipart/form-data"
    class="space-y-5"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama produk</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $product?->name) }}"
            required
            maxlength="255"
            autofocus
            class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200"
            placeholder="Contoh: Headphone Wireless Pro"
        >
        @error('name')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category_id" class="mb-2 block text-sm font-bold text-slate-700">Kategori</label>
        <select
            id="category_id"
            name="category_id"
            required
            class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-400 focus:ring-rose-200"
        >
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>
                    {{ $category->name }}{{ $category->is_active ? '' : ' (Nonaktif)' }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
        @if ($categories->isEmpty())
            <p class="mt-2 text-xs font-semibold text-amber-700">Buat kategori terlebih dahulu sebelum menambahkan produk.</p>
        @endif
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-bold text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            maxlength="5000"
            class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200"
            placeholder="Jelaskan produk ini..."
        >{{ old('description', $product?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="mb-2 block text-sm font-bold text-slate-700">Harga (Rp)</label>
            <input
                id="price"
                name="price"
                type="number"
                value="{{ old('price', $product?->price) }}"
                min="0"
                max="9999999999.99"
                step="0.01"
                required
                class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-400 focus:ring-rose-200"
                placeholder="850000"
            >
            @error('price')
                <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="mb-2 block text-sm font-bold text-slate-700">Stok</label>
            <input
                id="stock"
                name="stock"
                type="number"
                value="{{ old('stock', $product?->stock ?? 0) }}"
                min="0"
                step="1"
                required
                class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-400 focus:ring-rose-200"
                placeholder="10"
            >
            @error('stock')
                <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="image" class="mb-2 block text-sm font-bold text-slate-700">Gambar produk</label>
        @if ($product?->image_path)
            <div class="mb-3 flex items-center gap-3">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}" class="size-16 rounded-xl border border-slate-100 object-cover">
                <span class="text-xs text-slate-500">Gambar saat ini</span>
            </div>
        @endif
        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            @required(! $isEditing)
            class="w-full rounded-xl border border-slate-200 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-rose-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-rose-700"
        >
        <p class="mt-2 text-xs text-slate-500">Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB{{ $isEditing ? '; kosongkan jika tidak ingin mengganti gambar.' : '.' }}</p>
        @error('image')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3.5">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked((bool) old('is_active', $product?->is_active ?? true))
                class="rounded border-slate-300 text-rose-700 focus:ring-rose-300"
            >
            <span>
                <span class="block text-sm font-bold text-slate-700">Produk aktif</span>
                <span class="mt-0.5 block text-xs text-slate-500">Tandai produk ini aktif di katalog.</span>
            </span>
        </label>
        @error('is_active')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" @disabled($categories->isEmpty()) class="inline-flex items-center justify-center rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-800 disabled:cursor-not-allowed disabled:opacity-50">
            {{ $isEditing ? 'Simpan perubahan' : 'Simpan produk' }}
        </button>
    </div>
</form>
