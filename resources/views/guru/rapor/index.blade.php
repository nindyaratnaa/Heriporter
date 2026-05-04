@extends('layouts.app')
@section('title', 'Raport Murid')
@section('content')

<div class="page-title">📜 RAPORT MURID</div>
<div class="page-sub">Pilih murid dan semester untuk mengisi raport</div>

{{-- Selector Form --}}
<div class="card-gold" style="padding:24px; margin-bottom:24px; max-width:500px;">
    <div class="gold" style="font-size:13px; font-weight:bold; letter-spacing:1px; margin-bottom:16px;">🔍 PILIH RAPORT</div>
    <form id="raporForm">
        <div style="margin-bottom:14px;">
            <label for="student_select" class="form-label">MURID</label>
            <select id="student_select" class="form-input" required autocomplete="off">
                <option value="">-- Pilih Murid --</option>
                @foreach($students as $s)
                    <option value="{{ strtolower(str_replace(' ', '-', $s['name'])) }}">{{ $s['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:20px;">
            <label for="semester_select" class="form-label">SEMESTER</label>
            <select id="semester_select" class="form-input" required autocomplete="off">
                <option value="">-- Pilih Semester --</option>
                @for($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}">Semester {{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="button" onclick="goToRapor()" class="btn-gold" style="width:100%;">
            📖 BUKA RAPORT
        </button>
    </form>
</div>

<script>
function goToRapor() {
    const name = document.getElementById('student_select').value;
    const sem  = document.getElementById('semester_select').value;
    if (!name || !sem) return alert('Pilih murid dan semester terlebih dahulu.');
    window.location.href = '/guru/rapor/' + name + '/' + sem;
}
</script>

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
                            <a href="{{ route('guru.rapor.edit', ['student_name' => strtolower(str_replace(' ', '-', $s['name'])), 'semester' => $i]) }}" class="btn-outline" style="padding:3px 8px; font-size:10px;">S{{ $i }}</a>
                        @endfor
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
