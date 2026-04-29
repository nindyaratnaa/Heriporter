@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('content')

<div class="page-title">👨🏫 DASHBOARD GURU</div>
<div class="page-sub">Selamat datang, {{ session('user_name') }}!</div>

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:20px; text-align:center;">
        <div style="font-size:28px; margin-bottom:6px;">👥</div>
        <div class="gold" style="font-size:28px; font-weight:bold;">{{ $stats['total_students'] }}</div>
        <div style="color:#888; font-size:11px; margin-top:4px;">TOTAL MURID</div>
    </div>
    <div class="card-gold" style="padding:20px; text-align:center;">
        <div style="font-size:28px; margin-bottom:6px;">⏳</div>
        <div class="gold" style="font-size:28px; font-weight:bold;">{{ $stats['pending'] }}</div>
        <div style="color:#888; font-size:11px; margin-top:4px;">PERLU VALIDASI</div>
    </div>
    <div class="card" style="padding:20px; text-align:center;">
        <div style="font-size:28px; margin-bottom:6px;">✅</div>
        <div style="color:#7cfc00; font-size:28px; font-weight:bold;">{{ $stats['approved'] }}</div>
        <div style="color:#888; font-size:11px; margin-top:4px;">DISETUJUI</div>
    </div>
    <div class="card" style="padding:20px; text-align:center;">
        <div style="font-size:28px; margin-bottom:6px;">❌</div>
        <div style="color:#ff6b6b; font-size:28px; font-weight:bold;">{{ $stats['rejected'] }}</div>
        <div style="color:#888; font-size:11px; margin-top:4px;">DITOLAK</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
    <a href="{{ route('guru.potions') }}" class="card" style="padding:24px; text-align:center; text-decoration:none; display:block;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
        <div style="font-size:36px; margin-bottom:10px;">⚗️</div>
        <div class="gold" style="font-weight:bold; font-size:14px; letter-spacing:1px;">VALIDASI RAMUAN</div>
        <div style="color:#888; font-size:12px; margin-top:6px;">Review & approve/reject</div>
        @if($stats['pending'] > 0)
            <div style="margin-top:10px; background:rgba(212,175,55,0.15); border:1px solid var(--gold); padding:4px 12px; font-size:12px; color:var(--gold); display:inline-block;">
                {{ $stats['pending'] }} Pending
            </div>
        @endif
    </a>
    <a href="{{ route('guru.rapor') }}" class="card" style="padding:24px; text-align:center; text-decoration:none; display:block;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
        <div style="font-size:36px; margin-bottom:10px;">📜</div>
        <div class="gold" style="font-weight:bold; font-size:14px; letter-spacing:1px;">RAPORT MURID</div>
        <div style="color:#888; font-size:12px; margin-top:6px;">Buat & edit raport</div>
    </a>
    <a href="{{ route('guru.users') }}" class="card" style="padding:24px; text-align:center; text-decoration:none; display:block;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
        <div style="font-size:36px; margin-bottom:10px;">👥</div>
        <div class="gold" style="font-weight:bold; font-size:14px; letter-spacing:1px;">DATA MURID</div>
        <div style="color:#888; font-size:12px; margin-top:6px;">Lihat semua murid</div>
    </a>
</div>
@endsection
