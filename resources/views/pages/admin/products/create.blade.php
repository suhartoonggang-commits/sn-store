@extends('layouts.admin')

@section('title', 'Tambah Produk — Admin SN Store')
@section('page_name', 'Produk')
@section('description', 'Tambahkan produk baru ke katalog SN Store.')

@section('content')
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Tambah produk</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Isi informasi produk dan unggah gambar untuk katalog.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-slate-600 transition hover:text-rose-700">Kembali ke produk</a>
    </section>

    <section class="mt-7 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.02] sm:p-6">
        @include('pages.admin.products._form', ['product' => null])
    </section>
@endsection
