@extends('layouts.app')
@section('title', 'Inventori Ramuan')
@section('content')

<div class="page-title">📦 INVENTORI RAMUAN</div>
<div class="page-sub">Koleksi ramuan yang telah disetujui guru</div>

@if(count($inventory) > 0)
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:16px;">
        @foreach($inventory as $p)
            <div class="card" style="padding:0; overflow:hidden; border-color:rgba(39,174,96,0.4);">
                <div style="padding:16px; border-bottom:1px solid rgba(39,174,96,0.2);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <span style="font-size:28px;">🧪</span>
                        <span class="badge-approved">APPROVED</span>
                    </div>
                    <div style="font-weight:bold; font-size:14px; margin-bottom:4px;">{{ $p['name'] }}</div>
                    <div style="color:#888; font-size:12px;">{{ Str::limit($p['description'], 70) }}</div>
                    @if($p['rating'])
                        <div style="margin-top:8px; color:var(--gold); font-size:12px;">⭐ Rating: {{ $p['rating'] }}/10</div>
                    @endif
                </div>
                <div style="padding:10px 16px; background:rgba(0,0,0,0.2); display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#888; font-size:11px;">{{ date('d M Y', strtotime($p['created_at'])) }}</span>
                    <form method="POST" action="{{ route('student.inventory.destroy', $p['id']) }}" onsubmit="return confirm('Hapus dari inventori?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger" style="padding:4px 10px; font-size:11px;">🗑 Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card" style="padding:48px; text-align:center;">
        <div style="font-size:48px; margin-bottom:12px;">📦</div>
        <div class="gold" style="font-size:16px; font-weight:bold; margin-bottom:8px;">Inventori Kosong</div>
        <div style="color:#888; font-size:13px; margin-bottom:20px;">Belum ada ramuan yang disetujui guru.</div>
        <a href="{{ route('student.potions.create') }}" class="btn-gold">+ BUAT RAMUAN</a>
    </div>
@endif
@endsection
