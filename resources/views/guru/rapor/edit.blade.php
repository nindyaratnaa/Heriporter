@extends('layouts.app')
@section('title', 'Raport - {{ $student["name"] }}')
@section('content')

{{-- Success Popup --}}
@if(session('success'))
<div id="successPopup" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:999; display:flex; align-items:center; justify-content:center;">
    <div style="background:#1a1410; border:1px solid rgba(200,169,110,0.4); padding:32px 40px; text-align:center; max-width:400px; width:90%;">
        <div style="font-size:36px; margin-bottom:12px;">✅</div>
        <div style="color:var(--candle); font-size:15px; font-weight:bold; letter-spacing:1px; margin-bottom:8px;">BERHASIL DIPERBARUI</div>
        <div style="color:var(--parchment-dim); font-size:13px; line-height:1.6; margin-bottom:24px;">{{ session('success') }}</div>
        <button onclick="document.getElementById('successPopup').style.display='none'"
            style="background:linear-gradient(135deg,var(--copper),#8b6914); color:#0d0d0f; font-weight:bold; padding:10px 32px; border:none; cursor:pointer; font-family:'Courier New',monospace; letter-spacing:1px;">
            OK
        </button>
    </div>
</div>
@endif

<div style="margin-bottom:16px;">
    <a href="{{ route('guru.rapor') }}" style="color:#888; font-size:13px; text-decoration:none;">← Kembali ke Raport</a>
</div>

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
    <div>
        <div class="page-title">📜 RAPORT MURID</div>
        <div class="page-sub">{{ $student['name'] }} — {{ $rapor['semester'] }}</div>
    </div>
    {{-- Semester switcher --}}
    <div style="display:flex; flex-wrap:wrap; gap:5px; max-width:400px;">
        @foreach($semesters as $sem)
            @php $semNum = filter_var($sem, FILTER_SANITIZE_NUMBER_INT); @endphp
            <a href="{{ route('guru.rapor.edit', ['student_name' => strtolower(str_replace(' ', '-', $student['name'])), 'semester' => $semNum]) }}"
               style="padding:5px 10px; font-size:11px; font-family:'Courier New',monospace; text-decoration:none; border:2px solid {{ $sem === $rapor['semester'] ? 'var(--gold)' : 'rgba(212,175,55,0.3)' }}; background:{{ $sem === $rapor['semester'] ? 'var(--gold)' : 'transparent' }}; color:{{ $sem === $rapor['semester'] ? '#000' : 'var(--gold)' }}; font-weight:{{ $sem === $rapor['semester'] ? 'bold' : 'normal' }};">
                {{ str_replace('Semester ', 'S', $sem) }}
            </a>
        @endforeach
    </div>
</div>

{{-- READ MODE --}}
<div id="readMode">
    <div class="card" style="overflow:hidden; padding:0; margin-bottom:16px;">
        <table class="pixel-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengampu</th>
                    <th style="text-align:center;">Nilai</th>
                    <th style="text-align:center;">Huruf</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapor['mata_pelajaran'] as $i => $mp)
                    <tr>
                        <td style="color:#555; font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-weight:bold;">{{ $mp['nama'] }}</td>
                        <td style="color:#aaa; font-size:13px;">{{ $mp['guru_pengampu'] }}</td>
                        <td style="text-align:center; font-weight:bold; color:var(--candle);">{{ $mp['nilai'] > 0 ? $mp['nilai'] : '—' }}</td>
                        <td style="text-align:center; font-weight:bold; color:var(--gold);">{{ $mp['nilai_huruf'] ?? '-' }}</td>
                        <td style="color:var(--parchment-dim); font-size:12px;">{{ $mp['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:14px 20px; background:#11161B; border-top:2px solid rgba(212,175,55,0.3); display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#806D36; font-size:14px;">Rata-rata Nilai</span>
            @php
                $vals = collect($rapor['mata_pelajaran'])->pluck('nilai')->filter(fn($n) => $n > 0);
                $avg  = $vals->count() ? round($vals->avg()) : 0;
            @endphp
            <span style="font-size:24px; font-weight:bold; color:#B28A38;">{{ $avg > 0 ? $avg : '—' }}</span>
        </div>
    </div>

    <div class="card" style="padding:20px; margin-bottom:16px;">
        <div style="color:var(--parchment-dim); font-size:11px; letter-spacing:1px; margin-bottom:6px;">💬 CATATAN</div>
        <div style="color:var(--parchment); font-size:13px; line-height:1.6;">{{ $rapor['catatan'] ?: '—' }}</div>
    </div>

    <button onclick="enterEditMode()" class="btn-gold" style="padding:12px 32px;">
        ✏️ EDIT RAPORT
    </button>
</div>

{{-- EDIT MODE --}}
<div id="editMode" style="display:none;">
    <form method="POST" action="{{ route('guru.rapor.update', ['student_name' => strtolower(str_replace(' ', '-', $student['name'])), 'semester' => filter_var($rapor['semester'], FILTER_SANITIZE_NUMBER_INT)]) }}">
        @csrf @method('PUT')

        <div class="card" style="overflow:hidden; padding:0; margin-bottom:16px;">
            <table class="pixel-table">
                <thead>
                    <tr>
                        <th style="width:30px;">#</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th style="width:110px; text-align:center;">Nilai (0-100)</th>
                        <th style="width:60px; text-align:center;">Huruf</th>
                        <th style="width:140px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapor['mata_pelajaran'] as $i => $mp)
                        <tr>
                            <td style="color:#555; font-size:12px;">{{ $i + 1 }}</td>
                            <td style="font-weight:bold;">{{ $mp['nama'] }}</td>
                            <td style="color:#aaa; font-size:13px;">{{ $mp['guru_pengampu'] }}</td>
                            <td style="text-align:center;">
                                <input type="number"
                                       name="mata_pelajaran[{{ $i }}][nilai]"
                                       value="{{ $mp['nilai'] }}"
                                       min="0" max="100"
                                       class="form-input nilai-input"
                                       style="text-align:center; padding:6px; width:80px;"
                                       autocomplete="off"
                                       data-idx="{{ $i }}"
                                       required>
                            </td>
                            <td style="text-align:center;">
                                <span id="huruf_{{ $i }}" style="font-size:16px; font-weight:bold; color:var(--gold);">{{ $mp['nilai_huruf'] ?? '-' }}</span>
                            </td>
                            <td>
                                <span id="ket_{{ $i }}" style="font-size:12px; color:var(--parchment-dim);">{{ $mp['keterangan'] }}</span>
                                <input type="hidden" name="mata_pelajaran[{{ $i }}][keterangan]" id="ket_input_{{ $i }}" value="{{ $mp['keterangan'] }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:14px 20px; background:#11161B; border-top:2px solid rgba(212,175,55,0.3); display:flex; justify-content:space-between; align-items:center;">
                <span style="color:#806D36; font-size:14px;">Rata-rata Nilai</span>
                <span id="avgDisplay" style="font-size:24px; font-weight:bold; color:#B28A38;">—</span>
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <label for="catatan" class="form-label">💬 CATATAN UNTUK MURID</label>
            <textarea id="catatan" name="catatan" class="form-input" rows="3" placeholder="Catatan perkembangan murid...">{{ $rapor['catatan'] }}</textarea>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn-gold" style="flex:1; padding:12px;">💾 SIMPAN PERUBAHAN</button>
            <button type="button" onclick="exitEditMode()" class="btn-outline" style="padding:12px 24px;">Batal</button>
        </div>
    </form>
</div>

<script>
    function enterEditMode() {
        document.getElementById('readMode').style.display = 'none';
        document.getElementById('editMode').style.display = 'block';
        updateAvg();
    }
    function exitEditMode() {
        document.getElementById('editMode').style.display = 'none';
        document.getElementById('readMode').style.display = 'block';
    }
    function toHuruf(n) {
        if (n >= 90) return 'A';
        if (n >= 85) return 'A-';
        if (n >= 80) return 'B+';
        if (n >= 75) return 'B';
        if (n >= 70) return 'B-';
        if (n >= 65) return 'C+';
        if (n >= 60) return 'C';
        if (n >= 55) return 'C-';
        if (n >= 50) return 'D';
        return n > 0 ? 'E' : '-';
    }
    function toKet(n) {
        if (n >= 85) return 'Sangat Baik';
        if (n >= 70) return 'Baik';
        if (n >= 55) return 'Cukup';
        return n > 0 ? 'Kurang' : '-';
    }
    function updateAvg() {
        const inputs = document.querySelectorAll('.nilai-input');
        inputs.forEach(inp => {
            const n   = parseInt(inp.value) || 0;
            const idx = inp.dataset.idx;
            const hurufEl  = document.getElementById('huruf_' + idx);
            const ketEl    = document.getElementById('ket_' + idx);
            const ketInput = document.getElementById('ket_input_' + idx);
            if (hurufEl)  hurufEl.textContent  = toHuruf(n);
            if (ketEl)    ketEl.textContent    = toKet(n);
            if (ketInput) ketInput.value       = toKet(n);
        });
        const vals = Array.from(inputs).map(i => parseInt(i.value) || 0).filter(v => v > 0);
        const avg  = vals.length ? Math.round(vals.reduce((a,b) => a+b, 0) / vals.length) : 0;
        document.getElementById('avgDisplay').textContent = avg > 0 ? avg + ' (' + toHuruf(avg) + ')' : '—';
    }
    document.querySelectorAll('.nilai-input').forEach(i => i.addEventListener('input', updateAvg));
</script>
@endsection
