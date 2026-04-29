@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')

@php
    $house = $user['house'] ?? null;
    $houseConfig = [
        'Gryffindor' => ['img'=>'gryffindor.png', 'desc'=>'Keberanian, Keteguhan, Kehormatan'],
        'Ravenclaw'  => ['img'=>'ravenclaw.png',  'desc'=>'Kecerdasan, Kreativitas, Kebijaksanaan'],
        'Hufflepuff' => ['img'=>'Hufflepuff.png', 'desc'=>'Kesetiaan, Kesabaran, Kerja Keras'],
        'Slytherin'  => ['img'=>'Slytherin.png',  'desc'=>'Ambisi, Kecerdikan, Kepemimpinan'],
    ];
    $hc = $houseConfig[$house] ?? null;
@endphp

<div class="page-title">👤 PROFIL SAYA</div>
<div class="page-sub">Informasi akun, house, dan tongkat sihir</div>

<div style="display:grid; grid-template-columns:300px 1fr; gap:20px; max-width:900px;">

    {{-- LEFT --}}
    <div>
        {{-- Avatar Card --}}
        <div class="card" style="padding:24px; text-align:center; margin-bottom:16px;">
            <div style="margin-bottom:16px;">
                @if(!empty($user['photo']))
                    <img src="{{ asset($user['photo']) }}" alt="Avatar"
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid var(--copper); display:block; margin:0 auto;">
                @else
                    <img src="{{ asset('images/dummy profile.jpg') }}" alt="Avatar"
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid var(--copper); display:block; margin:0 auto;">
                @endif
            </div>

            <div style="color:var(--candle); font-size:16px; font-weight:bold;">{{ $user['name'] }}</div>
            <div style="color:var(--parchment-dim); font-size:12px; margin-top:4px;">Level {{ $user['level'] }} Student</div>

            @if($hc)
                <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(200,169,110,0.08); border:1px solid rgba(200,169,110,0.25); padding:4px 12px; font-size:11px; margin-top:8px; color:var(--copper); letter-spacing:1px;">
                    <img src="{{ asset('images/'.$hc['img']) }}" style="width:16px; height:16px; object-fit:contain;">
                    {{ $house }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.profile.photo') }}" enctype="multipart/form-data" style="margin-top:16px;">
                @csrf
                <label for="photo" style="display:block; background:rgba(0,0,0,0.3); border:1px dashed rgba(200,169,110,0.3); padding:10px; cursor:pointer; font-size:11px; color:var(--parchment-dim); transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='var(--copper)'" onmouseout="this.style.borderColor='rgba(200,169,110,0.3)'">
                    📷 Klik untuk ganti foto
                    <input type="file" id="photo" name="photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                </label>
            </form>
        </div>

        {{-- Info Card --}}
        <div class="card" style="padding:20px;">
            <div style="margin-bottom:12px;">
                <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">EMAIL</div>
                <div style="color:var(--copper); font-size:12px; margin-top:2px; word-break:break-all;">{{ $user['email'] }}</div>
            </div>
            <div style="margin-bottom:12px;">
                <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">BERGABUNG</div>
                <div style="color:var(--copper); font-size:12px; margin-top:2px;">{{ date('d M Y', strtotime($user['created_at'])) }}</div>
            </div>
            <div>
                <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">XP — LEVEL {{ $user['level'] }}</div>
                <div class="xp-bar">
                    <div class="xp-fill" style="width:{{ $user['max_xp'] > 0 ? round(($user['xp'] / $user['max_xp']) * 100) : 0 }}%"></div>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:4px; font-size:11px; color:var(--parchment-dim);">
                    <span>{{ $user['xp'] }} XP</span>
                    <span>{{ $user['max_xp'] }} XP</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div>
        {{-- House --}}
        @if($hc)
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <div style="color:var(--parchment-dim); font-size:10px; font-weight:bold; letter-spacing:2px; margin-bottom:14px;">🏰 HOUSE</div>
            <div style="display:flex; align-items:center; gap:20px;">
                <img src="{{ asset('images/'.$hc['img']) }}" alt="{{ $house }}"
                     style="width:72px; height:72px; object-fit:contain; filter:drop-shadow(0 0 10px rgba(200,169,110,0.3));">
                <div>
                    <div style="color:var(--candle); font-size:20px; font-weight:bold; letter-spacing:3px;">{{ strtoupper($house) }}</div>
                    <div style="color:var(--parchment-dim); font-size:12px; margin-top:6px;">{{ $hc['desc'] }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Wand --}}
        @if($wand)
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <div style="color:var(--parchment-dim); font-size:10px; font-weight:bold; letter-spacing:2px; margin-bottom:14px;">🪄 TONGKAT SIHIR — OLLIVANDERS</div>
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:14px;">
                <img src="{{ asset('images/'.$wand['gambar']) }}" alt="{{ $wand['nama'] }}"
                     style="width:60px; height:80px; object-fit:contain; filter:drop-shadow(0 0 8px rgba(200,169,110,0.4));">
                <div>
                    <div style="color:var(--candle); font-size:14px; font-weight:bold;">{{ $wand['nama'] }}</div>
                    <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px; font-style:italic; line-height:1.5;">{{ $wand['deskripsi'] }}</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                @foreach([['BAHAN KAYU',$wand['bahan_kayu']],['BAHAN INTI',$wand['bahan_inti']],['PANJANG',$wand['panjang']],['FLEKSIBILITAS',$wand['fleksibilitas']]] as [$label,$val])
                <div style="background:rgba(0,0,0,0.3); padding:10px; border:1px solid var(--stone-border);">
                    <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">{{ $label }}</div>
                    <div style="color:var(--parchment); font-size:12px; margin-top:2px;">{{ $val }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Stats --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:12px;">
            <div class="card" style="padding:14px; text-align:center;">
                <div style="font-size:20px; margin-bottom:4px;">🧪</div>
                <div style="color:var(--candle); font-size:22px; font-weight:bold;">{{ $stats['total'] }}</div>
                <div style="color:var(--parchment-dim); font-size:10px;">TOTAL</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
                <div style="font-size:20px; margin-bottom:4px;">✅</div>
                <div style="color:#7cfc00; font-size:22px; font-weight:bold;">{{ $stats['approved'] }}</div>
                <div style="color:var(--parchment-dim); font-size:10px;">APPROVED</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
                <div style="font-size:20px; margin-bottom:4px;">⏳</div>
                <div style="color:var(--copper); font-size:22px; font-weight:bold;">{{ $stats['pending'] }}</div>
                <div style="color:var(--parchment-dim); font-size:10px;">PENDING</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
                <div style="font-size:20px; margin-bottom:4px;">❌</div>
                <div style="color:#e07060; font-size:22px; font-weight:bold;">{{ $stats['rejected'] }}</div>
                <div style="color:var(--parchment-dim); font-size:10px;">REJECTED</div>
            </div>
        </div>

        @if($stats['total'] > 0)
        <div class="card" style="padding:14px; text-align:center;">
            <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:4px;">SUCCESS RATE</div>
            <div style="color:var(--candle); font-size:28px; font-weight:bold;">{{ round(($stats['approved'] / $stats['total']) * 100) }}%</div>
        </div>
        @endif
    </div>
</div>
@endsection
