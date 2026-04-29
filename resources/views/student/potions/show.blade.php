@extends('layouts.app')
@section('title', $potion['name'])
@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('student.potions.index') }}\" style="color:#888; font-size:13px; text-decoration:none;">
        ← Kembali ke Daftar Ramuan
    </a>
</div>

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
    <div>
        <div class="page-title">🧪 {{ $potion['name'] }}</div>
        <div style="margin-top:8px;">
            <span class="badge-{{ $potion['status'] }}">{{ strtoupper($potion['status']) }}</span>
            <span style="color:#888; font-size:11px; margin-left:10px;">📅 {{ date('d M Y', strtotime($potion['created_at'])) }}</span>
        </div>
    </div>
    <div style="text-align:right;">
        @php
            $diff = $potion['tingkat_kesulitan'] ?? 'Medium';
            $diffColor = $diff === 'Hard' ? '#ff6b6b' : ($diff === 'Medium' ? 'var(--gold)' : '#7cfc00');
        @endphp
        <div style="color:#888; font-size:10px; letter-spacing:1px;">KESULITAN</div>
        <div style="color:{{ $diffColor }}; font-size:14px; font-weight:bold;">{{ $diff }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1.2fr 1fr; gap:20px;">
    {{-- Left: Details --}}
    <div>
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <div class="gold" style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:8px;">📝 DESKRIPSI</div>
            <p style="color:#ccc; font-size:13px; line-height:1.6;">{{ $potion['description'] }}</p>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <div class="gold" style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:10px;">🌿 BAHAN-BAHAN ({{ count($potion['ingredients']) }})</div>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @foreach($potion['ingredients'] as $ing)
                    <span style="background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); color:var(--gold); padding:4px 10px; font-size:11px;">{{ $ing }}</span>
                @endforeach
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <div class="gold" style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:8px;">📋 CARA PEMBUATAN</div>
            <p style="color:#ccc; font-size:12px; line-height:1.7; white-space:pre-line;">{{ $potion['cara_pembuatan'] ?? '-' }}</p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
            <div class="card" style="padding:16px;">
                <div style="color:#888; font-size:10px; letter-spacing:1px; margin-bottom:4px;">⏱ DURASI EFEK</div>
                <div class="gold" style="font-size:13px; font-weight:bold;">{{ $potion['durasi_efek'] ?? '-' }}</div>
            </div>
            <div class="card" style="padding:16px;">
                <div style="color:#888; font-size:10px; letter-spacing:1px; margin-bottom:4px;">🎨 WARNA</div>
                <div class="gold" style="font-size:13px; font-weight:bold;">{{ $potion['warna_ramuan'] ?? '-' }}</div>
            </div>
        </div>

        <div class="card" style="padding:16px; margin-bottom:16px; border-left:3px solid #ff6b6b;">
            <div style="color:#ff6b6b; font-size:10px; font-weight:bold; letter-spacing:1px; margin-bottom:6px;">⚠️ EFEK SAMPING</div>
            <p style="color:#ccc; font-size:12px; line-height:1.5;">{{ $potion['efek_samping'] ?? '-' }}</p>
        </div>

        <div class="card" style="padding:16px; border-left:3px solid #888;">
            <div style="color:#888; font-size:10px; font-weight:bold; letter-spacing:1px; margin-bottom:6px;">🛡 KELEMAHAN</div>
            <p style="color:#ccc; font-size:12px; line-height:1.5;">{{ $potion['kelemahan'] ?? '-' }}</p>
        </div>

        @if($potion['image'])
            <div class="card" style="padding:12px; margin-top:16px;">
                <img src="{{ asset($potion['image']) }}" alt="Potion" style="width:100%; border:2px solid var(--gold);">
            </div>
        @endif
    </div>

    {{-- Right: Status & Actions --}}
    <div>
        @if($potion['status'] !== 'pending')
            <div class="card" style="padding:24px; margin-bottom:16px; border-color:{{ $potion['status'] === 'approved' ? '#7cfc00' : '#ff6b6b' }};">
                <div style="font-size:12px; font-weight:bold; letter-spacing:1px; margin-bottom:12px; color:{{ $potion['status'] === 'approved' ? '#7cfc00' : '#ff6b6b' }};">
                    {{ $potion['status'] === 'approved' ? '✅ DISETUJUI' : '❌ DITOLAK' }}
                </div>
                @if($potion['rating'])
                    <div style="margin-bottom:10px;">
                        <span style="color:#888; font-size:11px;">Rating:</span>
                        <span class="gold" style="font-size:20px; font-weight:bold; margin-left:8px;">{{ $potion['rating'] }}/10</span>
                        <div style="font-size:14px; margin-top:4px;">
                            @for($i=1; $i<=10; $i++)
                                {{ $i <= $potion['rating'] ? '⭐' : '☆' }}
                            @endfor
                        </div>
                    </div>
                @endif
                @if($potion['guru_comment'])
                    <div style="background:rgba(0,0,0,0.3); padding:12px; font-size:12px; color:#ccc; line-height:1.5; border-left:3px solid var(--gold); margin-top:12px;">
                        "{{ $potion['guru_comment'] }}"
                    </div>
                @endif
                @if($guru)
                    <div style="color:#888; font-size:11px; margin-top:10px;">
                        — {{ $guru['name'] }}, {{ date('d M Y', strtotime($potion['validated_at'])) }}
                    </div>
                @endif
            </div>
        @else
            <div class="card-gold" style="padding:24px; text-align:center;">
                <div style="font-size:40px; margin-bottom:10px;">⏳</div>
                <div class="gold" style="font-weight:bold;">Menunggu Validasi</div>
                <div style="color:#888; font-size:12px; margin-top:6px;">Guru akan segera mereview ramuanmu</div>
            </div>
        @endif

        @if($potion['status'] === 'pending')
            <form method="POST" action="{{ route('student.potions.destroy', $potion['id']) }}" onsubmit="return confirm('Hapus ramuan ini?')" style="margin-top:12px;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger" style="width:100%; padding:10px;">🗑 Hapus Ramuan</button>
            </form>
        @endif
    </div>
</div>
@endsection
