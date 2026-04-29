@extends('layouts.app')
@section('title', 'Buat Ramuan')
@section('content')

<div class="page-title">⚗️ BUAT RAMUAN BARU</div>
<div class="page-sub">Racik ramuan dan submit untuk divalidasi guru</div>

<div class="card" style="padding:32px; max-width:700px;">
    <form method="POST" action="{{ route('student.potions.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label for="name" class="form-label">🧪 NAMA RAMUAN</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Contoh: Polyjuice Potion" value="{{ old('name') }}" autocomplete="off" required>
            </div>
            <div>
                <label for="tingkat_kesulitan" class="form-label">⚡ TINGKAT KESULITAN</label>
                <select id="tingkat_kesulitan" name="tingkat_kesulitan" class="form-input" autocomplete="off" required>
                    <option value="">-- Pilih --</option>
                    <option value="Easy"   {{ old('tingkat_kesulitan') === 'Easy'   ? 'selected' : '' }}>🟢 Easy</option>
                    <option value="Medium" {{ old('tingkat_kesulitan') === 'Medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="Hard"   {{ old('tingkat_kesulitan') === 'Hard'   ? 'selected' : '' }}>🔴 Hard</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label for="description" class="form-label">📝 DESKRIPSI & EFEK RAMUAN</label>
            <textarea id="description" name="description" class="form-input" rows="3" placeholder="Jelaskan efek dan kegunaan ramuan..." required style="resize:vertical;">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom:16px;">
            <label for="ingredients" class="form-label">🌿 BAHAN-BAHAN</label>
            <input type="text" id="ingredients" name="ingredients" class="form-input" placeholder="Lacewing flies, Leeches, Knotgrass" value="{{ old('ingredients') }}" autocomplete="off" required>
            <div style="color:#555; font-size:11px; margin-top:3px;">Pisahkan dengan koma (,)</div>
        </div>

        <div style="margin-bottom:16px;">
            <label for="cara_pembuatan" class="form-label">📋 CARA PEMBUATAN</label>
            <textarea id="cara_pembuatan" name="cara_pembuatan" class="form-input" rows="4" placeholder="1. Langkah pertama...&#10;2. Langkah kedua...&#10;3. dst." required style="resize:vertical;">{{ old('cara_pembuatan') }}</textarea>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label for="durasi_efek" class="form-label">⏱ DURASI EFEK</label>
                <input type="text" id="durasi_efek" name="durasi_efek" class="form-input" placeholder="Contoh: 2 jam, 30 menit" value="{{ old('durasi_efek') }}" autocomplete="off" required>
            </div>
            <div>
                <label for="warna_ramuan" class="form-label">🎨 WARNA RAMUAN</label>
                <input type="text" id="warna_ramuan" name="warna_ramuan" class="form-input" placeholder="Contoh: Hijau tua, Biru pucat" value="{{ old('warna_ramuan') }}" autocomplete="off" required>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label for="efek_samping" class="form-label">⚠️ EFEK SAMPING</label>
            <textarea id="efek_samping" name="efek_samping" class="form-input" rows="2" placeholder="Reaksi fisik yang mungkin terjadi setelah mengonsumsi ramuan..." required style="resize:vertical;">{{ old('efek_samping') }}</textarea>
        </div>

        <div style="margin-bottom:16px;">
            <label for="kelemahan" class="form-label">🛡 KELEMAHAN RAMUAN</label>
            <textarea id="kelemahan" name="kelemahan" class="form-input" rows="2" placeholder="Batasan atau kondisi di mana ramuan tidak bekerja..." required style="resize:vertical;">{{ old('kelemahan') }}</textarea>
        </div>

        <div style="margin-bottom:24px;">
            <label for="image" class="form-label">🖼 GAMBAR RAMUAN (Opsional)</label>
            <input type="file" id="image" name="image" class="form-input" accept="image/*" style="padding:8px;">
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn-gold" style="flex:1;">
                <i class="fas fa-paper-plane"></i> SUBMIT RAMUAN
            </button>
            <a href="{{ route('student.potions.index') }}" class="btn-outline" style="padding:10px 20px;">Batal</a>
        </div>
    </form>
</div>
@endsection
