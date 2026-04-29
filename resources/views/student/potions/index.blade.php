@extends('layouts.app')
@section('title', 'Racik Ramuan')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
    <div>
        <div class="page-title">⚗️ RACIK RAMUAN</div>
        <div class="page-sub">Semua ramuan yang kamu buat</div>
    </div>
    <a href="{{ route('student.potions.create') }}" class="btn-gold">
        <i class="fas fa-plus"></i> BUAT RAMUAN
    </a>
</div>

@if(count($potions) > 0)
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:16px;">
        @foreach($potions as $p)
            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding:16px; border-bottom:1px solid rgba(212,175,55,0.2);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div style="font-size:24px; margin-bottom:6px;">🧪</div>
                        <span class="badge-{{ $p['status'] }}">{{ strtoupper($p['status']) }}</span>
                    </div>
                    <div style="font-weight:bold; font-size:14px; margin-bottom:4px;">{{ $p['name'] }}</div>
                    <div style="color:#888; font-size:12px; line-height:1.4;">{{ Str::limit($p['description'], 80) }}</div>
                </div>
                <div style="padding:12px 16px; background:rgba(0,0,0,0.2);">
                    <div style="color:#888; font-size:11px; margin-bottom:10px;">
                        📅 {{ date('d M Y', strtotime($p['created_at'])) }}
                        @if($p['rating']) &nbsp;⭐ {{ $p['rating'] }}/10 @endif
                    </div>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('student.potions.show', $p['id']) }}" class="btn-outline" style="flex:1; text-align:center; padding:6px;">Detail</a>
                        @if($p['status'] === 'pending')
                            <form method="POST" action="{{ route('student.potions.destroy', $p['id']) }}" onsubmit="return confirm('Hapus ramuan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding:6px 12px;">🗑</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card" style="padding:48px; text-align:center;">
        <div style="font-size:48px; margin-bottom:12px;">🧪</div>
        <div class="gold" style="font-size:16px; font-weight:bold; margin-bottom:8px;">Belum ada ramuan</div>
        <div style="color:#888; font-size:13px; margin-bottom:20px;">Mulai racik ramuan pertamamu!</div>
        <a href="{{ route('student.potions.create') }}" class="btn-gold">+ BUAT RAMUAN</a>
    </div>
@endif
@endsection
