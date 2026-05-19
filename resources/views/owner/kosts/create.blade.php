@extends('layouts.app')

@section('title', 'Tambah Kost - KostFinder')

@section('content')
    <section class="form-card mx-auto max-w-5xl">
        <p class="eyebrow">Kelola Kost</p>
        <h1 class="font-display text-4xl font-semibold">Tambah Data Kost</h1>
        <p class="mt-3 text-base leading-8 text-[var(--muted)]">Isi informasi lengkap kost agar calon penyewa bisa melihat detail yang jelas sejak awal.</p>

        <form action="{{ route('owner.kosts.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
            @include('owner.kosts._form')
        </form>
    </section>
@endsection
