@extends('layouts.peneliti')
@section('title', $fitur . ' — Segera Hadir')

@section('content')

<div class="page-header">
    <h1>{{ $fitur }}</h1>
    <p>Fitur ini sedang dalam pengembangan</p>
</div>

<div class="kep-card" style="text-align:center;padding:4rem 2rem;">

    {{-- Animated icon --}}
    <div style="
        width: 96px;
        height: 96px;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--blue-pale) 0%, #dbedf7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.75rem;
        font-size: 2.5rem;
        color: var(--blue-accent);
        box-shadow: 0 4px 20px rgba(74,127,167,.18);
        animation: pulseIcon 2.5s ease-in-out infinite;
    ">
        <i class="bi {{ $icon }}"></i>
    </div>

    {{-- Title --}}
    <h2 style="
        font-size: 1.45rem;
        font-weight: 600;
        color: var(--navy-deep);
        margin-bottom: .6rem;
        letter-spacing: -.02em;
    ">Segera Hadir</h2>

    {{-- Badge --}}
    <span style="
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #fef3c7;
        color: #92400e;
        font-size: .75rem;
        font-weight: 600;
        padding: .3rem .85rem;
        border-radius: 20px;
        margin-bottom: 1.5rem;
        border: 1px solid #fcd34d;
    ">
        <i class="bi bi-tools" style="font-size:.7rem;"></i>
        Dalam Pengembangan
    </span>

    {{-- Description --}}
    <p style="
        font-size: .92rem;
        color: var(--text-muted);
        max-width: 480px;
        margin: 0 auto 2rem;
        line-height: 1.65;
    ">{{ $deskripsi }}</p>

    {{-- Divider --}}
    <div style="
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--blue-accent), var(--blue-light));
        border-radius: 4px;
        margin: 0 auto 2rem;
    "></div>

    {{-- Back to dashboard --}}
    <a href="{{ route('peneliti.dashboard') }}" class="btn-kep btn-primary" style="display:inline-flex;">
        <i class="bi bi-grid-1x2"></i> Kembali ke Dashboard
    </a>

</div>

<style>
@keyframes pulseIcon {
    0%, 100% { transform: scale(1); box-shadow: 0 4px 20px rgba(74,127,167,.18); }
    50%       { transform: scale(1.06); box-shadow: 0 8px 32px rgba(74,127,167,.30); }
}
</style>

@endsection