@php($isEditing = $category !== null)

<form
    method="POST"
    action="{{ $isEditing ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="space-y-5"
    x-data="{
        categoryName: @js(old('name', $category?->name ?? '')),
        slugPreview(name) {
            return name
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '') || 'kategori';
        }
    }"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama kategori</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $category?->name) }}"
            x-model="categoryName"
            required
            maxlength="255"
            autofocus
            class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200"
            placeholder="Contoh: Perangkat rumah"
        >
        @error('name')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-xs text-slate-500">
            Slug dibuat otomatis:
            <code x-text="slugPreview(categoryName)" class="rounded bg-slate-100 px-1.5 py-0.5 font-semibold text-slate-700"></code>
            <span class="block pt-1">Slug akhir disimpan otomatis saat formulir dikirim.</span>
        </p>
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-bold text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            maxlength="5000"
            class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 focus:border-rose-400 focus:ring-rose-200"
            placeholder="Jelaskan kategori produk ini..."
        >{{ old('description', $category?->description) }}</textarea>
        @error('description')
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
                @checked((bool) old('is_active', $category?->is_active ?? true))
                class="rounded border-slate-300 text-rose-700 focus:ring-rose-300"
            >
            <span>
                <span class="block text-sm font-bold text-slate-700">Kategori aktif</span>
                <span class="mt-0.5 block text-xs text-slate-500">Tampilkan kategori ini di toko.</span>
            </span>
        </label>
        @error('is_active')
            <p class="mt-2 text-xs font-semibold text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-800">
            {{ $isEditing ? 'Simpan perubahan' : 'Simpan kategori' }}
        </button>
    </div>
</form>
