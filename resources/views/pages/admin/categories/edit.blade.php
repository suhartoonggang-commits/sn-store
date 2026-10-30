@extends('layouts.admin')

@section('title', 'Ubah Kategori — Admin SN Store')
@section('page_name', 'Kategori')
@section('description', 'Ubah informasi kategori produk.')

@section('content')
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Ubah kategori</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Perbarui informasi kategori {{ $category->name }}.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm font-bold text-slate-600 transition hover:text-rose-700">Kembali ke kategori</a>
    </section>

    <section class="mt-7 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02] sm:p-6">
        @include('pages.admin.categories._form', ['category' => $category])
    </section>
@endsection
