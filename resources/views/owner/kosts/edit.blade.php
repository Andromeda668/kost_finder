@extends('layouts.app')

@section('title', 'Edit Kost - KostFinder')

@section('content')
    <section class="form-card mx-auto max-w-5xl">
        <p class="eyebrow">Dashboard Owner</p>
        <h1 class="font-display text-4xl font-semibold">Edit Data Kost</h1>
        <p class="mt-3 text-base leading-8 text-[var(--muted)]">Perbarui informasi kost agar calon penyewa selalu melihat data terbaru.</p>

        @if ($kost->display_image_url)
            <img src="{{ $kost->display_image_url }}" alt="{{ $kost->nama_kost }}" class="mt-8 h-72 w-full rounded-[30px] object-cover">
        @endif

        <form action="{{ route('owner.kosts.update', $kost) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
            @method('PUT')
            @include('owner.kosts._form')
        </form>
    </section>
@endsection
