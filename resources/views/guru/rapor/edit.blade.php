@extends('layouts.app')
@section('title', 'Edit Raport')
@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('guru.rapor') }}" style="color:#888; font-size:13px; text-decoration:none;">← Kembali ke Raport</a>
</div>

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
    <div>
        <div class="page-title">✏️ EDIT RAPORT</div>
        <div class="page-sub">{{ $student['name'] }} — {{ $rapor['semester'] }}</div>
    </div>
    {{-- Semester switcher --}}
    <div style="display:flex; flex-wrap:wrap; gap:5px; max-width:400px;">
        @foreach($semesters as $sem)
            <a href="{{ route('guru.rapor.edit', ['student_id' => $student['id'], 'semester' => $sem]) }}"
               style="padding:5px 10px; font-size:11px; font-family:'Courier New',monospace; text-decoration:none; border:2px solid {{ $sem === $rapor['semester'] ? 'var(--gold)' : 'rgba(212,175,55,0.3)' }}; background:{{ $sem === $rapor['semester'] ? 'var(--gold)' : 'transparent' }}; color:{{ $sem === $rapor['semester'] ? '#000' : 'var(--gold)' }}; font-weight:{{ $sem === $rapor['semester'] ? 'bold' : 'normal' }};">
                {{ str_replace('Semester ', 'S', $sem) }}
            </a>
        @endforeach
    </div>
</div>

<form method="POST" action="{{ route('guru.rapor.update', $rapor['id']) }}">
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
                    <th style="width:160px;">Keterangan</th>
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
                            <span id="huruf_{{ $i }}" style="font-size:16px; font-weight:bold; color:var(--gold);">{{ $mp['nilai_huruf'] ?? ($mp['nilai'] > 0 ? \App\Http\Controllers\RaporController::nilaiHuruf($mp['nilai']) : '-') }}</span>
                        </td>
                        <td>
                            <select name="mata_pelajaran[{{ $i }}][keterangan]" class="form-input" style="padding:6px;" autocomplete="off">
                                @foreach(['-', 'Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $ket)
                                    <option value="{{ $ket }}" {{ $mp['keterangan'] === $ket ? 'selected' : '' }}>{{ $ket }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Footer rata-rata preview --}}
        <div style="padding:14px 20px; background:#11161B; border-top:2px solid rgba(212,175,55,0.3); display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#806D36; font-size:14px; font-weight:500;">Rata-rata Nilai</span>
            <span id="avgDisplay" style="font-size:24px; font-weight:bold; color:#B28A38; font-family:'Times New Roman', serif;">—</span>
        </div>
    </div>

    <div class="card" style="padding:20px; margin-bottom:16px;">
        <label for="catatan" class="form-label">💬 CATATAN UNTUK MURID</label>
        <textarea id="catatan" name="catatan" class="form-input" rows="3" placeholder="Catatan perkembangan murid...">{{ $rapor['catatan'] }}</textarea>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn-gold" style="flex:1; padding:12px;">💾 SIMPAN RAPORT</button>
        <a href="{{ route('guru.rapor') }}" class="btn-outline" style="padding:12px 24px;">Batal</a>
    </div>
</form>

<script>
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
    function updateAvg() {
        const inputs = document.querySelectorAll('.nilai-input');
        inputs.forEach(inp => {
            const n = parseInt(inp.value) || 0;
            const idx = inp.dataset.idx;
            const el = document.getElementById('huruf_' + idx);
            if (el) el.textContent = toHuruf(n);
        });
        const vals = Array.from(inputs).map(i => parseInt(i.value) || 0).filter(v => v > 0);
        const avg = vals.length ? Math.round(vals.reduce((a,b) => a+b, 0) / vals.length) : 0;
        document.getElementById('avgDisplay').textContent = avg > 0 ? avg + ' (' + toHuruf(avg) + ')' : '—';
    }
    document.querySelectorAll('.nilai-input').forEach(i => i.addEventListener('input', updateAvg));
    updateAvg();
</script>
@endsection
