@extends('layouts.app')
@section('title', 'Raport Murid')
@section('content')

<div class="page-title">📜 RAPORT MURID</div>
<div class="page-sub">Pilih murid dan semester untuk mengisi raport</div>

{{-- Selector Form --}}
<div class="card-gold" style="padding:24px; margin-bottom:24px; max-width:500px;">
    <div class="gold" style="font-size:13px; font-weight:bold; letter-spacing:1px; margin-bottom:16px;">🔍 PILIH RAPORT</div>
    <form method="GET" action="{{ route('guru.rapor.edit') }}">
        <div style="margin-bottom:14px;">
            <label for="student_id" class="form-label">MURID</label>
            <select name="student_id" id="student_id" class="form-input" required autocomplete="off">
                <option value="">-- Pilih Murid --</option>
                @foreach($students as $s)
                    <option value="{{ $s['id'] }}">{{ $s['name'] }} ({{ $s['email'] }})</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:20px;">
            <label for="semester" class="form-label">SEMESTER</label>
            <select name="semester" id="semester" class="form-input" required autocomplete="off">
                <option value="">-- Pilih Semester --</option>
                <option value="Semester 1">Semester 1</option>
                <option value="Semester 2">Semester 2</option>
                <option value="Semester 3">Semester 3</option>
                <option value="Semester 4">Semester 4</option>
                <option value="Semester 5">Semester 5</option>
                <option value="Semester 6">Semester 6</option>
                <option value="Semester 7">Semester 7</option>
                <option value="Semester 8">Semester 8</option>
            </select>
        </div>
        <button type="submit" class="btn-gold" style="width:100%;">
            ✏️ BUKA RAPORT
        </button>
    </form>
</div>

{{-- Quick overview table --}}
<div class="card" style="overflow:hidden; padding:0;">
    <div style="padding:14px 20px; border-bottom:2px solid rgba(212,175,55,0.3);">
        <span class="gold" style="font-weight:bold; font-size:13px; letter-spacing:1px;">📊 RINGKASAN SEMUA MURID</span>
    </div>
    <table class="pixel-table">
        <thead>
            <tr>
                <th>Murid</th>
                <th style="text-align:center;">S1</th>
                <th style="text-align:center;">S2</th>
                <th style="text-align:center;">S3</th>
                <th style="text-align:center;">S4</th>
                <th style="text-align:center;">S5</th>
                <th style="text-align:center;">S6</th>
                <th style="text-align:center;">S7</th>
                <th style="text-align:center;">S8</th>
                <th style="text-align:center;">Rata-rata</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $s)
                @php
                    $rapors = collect(app(\App\Services\JsonService::class)->read('rapor'))
                        ->where('student_id', $s['id']);
                    $avg = fn($r) => $r ? round(collect($r['mata_pelajaran'])->pluck('nilai')->filter(fn($n) => $n > 0)->avg() ?? 0) : 0;
                    $sems = [];
                    for ($i = 1; $i <= 8; $i++) {
                        $r = $rapors->firstWhere('semester', 'Semester ' . $i);
                        $sems[$i] = $avg($r);
                    }
                    $overall = collect($sems)->filter(fn($v) => $v > 0)->avg() ?? 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:bold;">{{ $s['name'] }}</div>
                        <div style="color:#888; font-size:11px;">{{ $s['email'] }}</div>
                    </td>
                    @for ($i = 1; $i <= 8; $i++)
                        @php $a = $sems[$i]; @endphp
                        <td style="text-align:center; color:{{ $a >= 85 ? '#7cfc00' : ($a >= 70 ? 'var(--gold)' : ($a > 0 ? '#ff6b6b' : '#555')) }}; font-weight:bold; font-size:12px;">
                            {{ $a > 0 ? $a : '—' }}
                        </td>
                    @endfor
                    <td style="text-align:center; color:var(--gold); font-weight:bold; font-size:16px;">
                        {{ $overall > 0 ? round($overall) : '—' }}
                    </td>
                    <td style="display:flex; gap:4px; flex-wrap:wrap;">
                        @for ($i = 1; $i <= 8; $i++)
                            <a href="{{ route('guru.rapor.edit', ['student_id' => $s['id'], 'semester' => 'Semester ' . $i]) }}" class="btn-outline" style="padding:3px 8px; font-size:10px;">S{{ $i }}</a>
                        @endfor
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
