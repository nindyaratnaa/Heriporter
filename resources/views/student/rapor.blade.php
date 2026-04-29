@extends('layouts.app')
@section('title', 'Raport Saya')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <div class="page-title">📜 RAPORT SAYA</div>
        <div class="page-sub">Hasil evaluasi akademik per semester</div>
    </div>

    {{-- Semester Tabs --}}
    <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-end;">
        <span style="color:var(--parchment-dim); font-size:10px; letter-spacing:2px;">SEMESTER:</span>
        <div style="display:flex; flex-wrap:wrap; gap:4px; justify-content:flex-end;">
            @foreach($allRapor as $r)
                @php $isActive = $r['semester'] === $selectedSemester; $hasFilled = !is_null($r['updated_at']); @endphp
                <a href="{{ route('student.rapor', ['semester' => $r['semester']]) }}"
                   style="padding:5px 11px; font-size:11px; font-family:'Courier New',monospace; text-decoration:none;
                          border:1px solid {{ $isActive ? 'var(--copper)' : ($hasFilled ? 'rgba(200,169,110,0.3)' : 'var(--stone-border)') }};
                          background:{{ $isActive ? 'var(--copper)' : 'transparent' }};
                          color:{{ $isActive ? '#0d0d0f' : ($hasFilled ? 'var(--copper)' : 'var(--parchment-dim)') }};
                          font-weight:{{ $isActive ? 'bold' : 'normal' }};
                          transition:all 0.15s;">
                    {{ str_replace('Semester ', 'S', $r['semester']) }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px;">
    <div class="card" style="padding:16px; text-align:center;">
        <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">RATA-RATA KESELURUHAN</div>
        @php $overall = round($rataKeseluruhan); @endphp
        <div style="font-size:32px; font-weight:bold; color:{{ $overall >= 85 ? '#7cfc00' : ($overall >= 70 ? 'var(--candle)' : ($overall > 0 ? '#e07060' : 'var(--parchment-dim)')) }};">
            {{ $overall > 0 ? $overall : '—' }}
        </div>
        <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Semua Semester</div>
    </div>

    @php
        $nilaiAktif = collect($rapor['mata_pelajaran'] ?? [])->pluck('nilai')->filter(fn($n) => $n > 0);
        $rataAktif  = $nilaiAktif->count() ? round($nilaiAktif->avg()) : 0;
    @endphp
    <div class="card-gold" style="padding:16px; text-align:center;">
        <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">RATA-RATA {{ strtoupper($selectedSemester) }}</div>
        <div style="font-size:32px; font-weight:bold; color:{{ $rataAktif >= 85 ? '#7cfc00' : ($rataAktif >= 70 ? 'var(--candle)' : ($rataAktif > 0 ? '#e07060' : 'var(--parchment-dim)')) }};">
            {{ $rataAktif > 0 ? $rataAktif : '—' }}
        </div>
        <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Semester Ini</div>
    </div>

    <div class="card" style="padding:16px; text-align:center;">
        <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">MATA PELAJARAN</div>
        <div style="color:var(--copper); font-size:32px; font-weight:bold;">
            {{ count($rapor['mata_pelajaran'] ?? []) }}
        </div>
        <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Total Mapel</div>
    </div>
</div>

{{-- Raport Table --}}
@if($rapor)
    <div class="card" style="overflow:hidden; padding:0; margin-bottom:16px;">
        <div style="padding:14px 20px; border-bottom:1px solid var(--stone-border); display:flex; justify-content:space-between; align-items:center; background:rgba(200,169,110,0.04);">
            <span style="color:var(--copper); font-weight:bold; font-size:13px; letter-spacing:1px;">📋 {{ $rapor['semester'] }}</span>
            @if($rapor['updated_at'])
                <span style="color:var(--parchment-dim); font-size:11px;">Diperbarui: {{ date('d M Y', strtotime($rapor['updated_at'])) }}</span>
            @else
                <span style="color:var(--stone-border); font-size:11px; font-style:italic;">Belum diisi guru</span>
            @endif
        </div>

        <table class="pixel-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Mata Pelajaran</th>
                    <th style="text-align:center; width:70px;">Nilai</th>
                    <th style="text-align:center; width:60px;">Huruf</th>
                    <th>Guru Pengampu</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapor['mata_pelajaran'] as $i => $mp)
                    @php
                        $n     = $mp['nilai'];
                        $warna = $n >= 85 ? '#7cfc00' : ($n >= 70 ? 'var(--candle)' : ($n > 0 ? '#e07060' : 'var(--stone-border)'));
                        $huruf = $mp['nilai_huruf'] ?? ($n > 0 ? \App\Http\Controllers\RaporController::nilaiHuruf($n) : '—');
                    @endphp
                    <tr>
                        <td style="color:var(--parchment-dim); font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-weight:bold; color:var(--parchment);">{{ $mp['nama'] }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:{{ $n > 0 ? '18px' : '14px' }}; font-weight:bold; color:{{ $warna }};">
                                {{ $n > 0 ? $n : '—' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:15px; font-weight:bold; color:{{ $warna }};">
                                {{ $huruf !== '-' ? $huruf : '—' }}
                            </span>
                        </td>
                        <td style="color:var(--parchment-dim); font-size:12px;">{{ $mp['guru_pengampu'] }}</td>
                        <td>
                            @if($mp['keterangan'] && $mp['keterangan'] !== '-')
                                <span style="background:rgba(200,169,110,0.08); border:1px solid rgba(200,169,110,0.2); color:var(--copper); padding:2px 10px; font-size:11px;">
                                    {{ $mp['keterangan'] }}
                                </span>
                            @else
                                <span style="color:var(--stone-border); font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding:14px 20px; background:rgba(0,0,0,0.3); border-top:1px solid var(--stone-border); display:flex; justify-content:space-between; align-items:center;">
            <span style="color:var(--copper); font-size:13px; font-weight:500;">Rata-rata {{ $rapor['semester'] }}</span>
            <span style="font-size:26px; font-weight:bold; color:var(--candle); font-family:'Times New Roman', serif;">
                {{ $rataAktif > 0 ? $rataAktif : '—' }}
            </span>
        </div>
    </div>

    @if($rapor['catatan'])
        <div class="card" style="padding:16px; border-left:3px solid var(--copper);">
            <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">💬 CATATAN GURU</div>
            <div style="color:var(--parchment); font-size:13px; line-height:1.6;">{{ $rapor['catatan'] }}</div>
        </div>
    @endif
@else
    <div class="card" style="padding:48px; text-align:center;">
        <div style="font-size:48px; margin-bottom:12px;">📜</div>
        <div style="color:var(--copper); font-size:16px; font-weight:bold;">Raport belum tersedia</div>
        <div style="color:var(--parchment-dim); font-size:13px; margin-top:6px;">Guru belum mengisi raport untuk semester ini.</div>
    </div>
@endif
@endsection
