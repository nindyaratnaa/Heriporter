@extends('layouts.app')
@section('title', 'Review Ramuan')
@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('guru.potions') }}" style="color:#888; font-size:13px; text-decoration:none;">← Kembali ke Daftar</a>
</div>

<div class="page-title">🔍 REVIEW RAMUAN</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    {{-- Potion Info --}}
    <div class="card" style="padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <div>
                <div class="gold" style="font-size:18px; font-weight:bold;">{{ $potion['name'] }}</div>
                <div style="color:#888; font-size:12px; margin-top:2px;">
                    oleh {{ $student['name'] ?? '-' }} &nbsp;|&nbsp; {{ date('d M Y', strtotime($potion['created_at'])) }}
                </div>
            </div>
            <span class="badge-{{ $potion['status'] }}">{{ strtoupper($potion['status']) }}</span>
        </div>

        <div style="margin-bottom:16px;">
            <div class="gold" style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:6px;">DESKRIPSI</div>
            <p style="color:#ccc; font-size:13px; line-height:1.6; background:rgba(0,0,0,0.3); padding:12px;">{{ $potion['description'] }}</p>
        </div>

        <div style="margin-bottom:16px;">
            <div class="gold" style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:8px;">BAHAN-BAHAN ({{ count($potion['ingredients']) }})</div>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @foreach($potion['ingredients'] as $ing)
                    <span style="background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); color:var(--gold); padding:4px 10px; font-size:11px;">{{ $ing }}</span>
                @endforeach
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
            <div style="background:rgba(0,0,0,0.3); padding:10px;">
                <div style="color:#888; font-size:10px; letter-spacing:1px;">KESULITAN</div>
                @php $d=$potion['tingkat_kesulitan']??'-'; @endphp
                <div style="color:{{ $d==='Hard'?'#ff6b6b':($d==='Medium'?'var(--gold)':'#7cfc00') }}; font-weight:bold; font-size:13px;">{{ $d }}</div>
            </div>
            <div style="background:rgba(0,0,0,0.3); padding:10px;">
                <div style="color:#888; font-size:10px; letter-spacing:1px;">DURASI EFEK</div>
                <div class="gold" style="font-weight:bold; font-size:13px;">{{ $potion['durasi_efek']??'-' }}</div>
            </div>
            <div style="background:rgba(0,0,0,0.3); padding:10px;">
                <div style="color:#888; font-size:10px; letter-spacing:1px;">WARNA</div>
                <div class="gold" style="font-size:13px;">{{ $potion['warna_ramuan']??'-' }}</div>
            </div>
        </div>

        <div style="margin-bottom:12px; border-left:3px solid #ff6b6b; padding-left:10px;">
            <div style="color:#ff6b6b; font-size:10px; font-weight:bold; letter-spacing:1px; margin-bottom:4px;">⚠️ EFEK SAMPING</div>
            <p style="color:#ccc; font-size:12px;">{{ $potion['efek_samping']??'-' }}</p>
        </div>

        <div style="margin-bottom:12px; border-left:3px solid #888; padding-left:10px;">
            <div style="color:#888; font-size:10px; font-weight:bold; letter-spacing:1px; margin-bottom:4px;">🛡 KELEMAHAN</div>
            <p style="color:#ccc; font-size:12px;">{{ $potion['kelemahan']??'-' }}</p>
        </div>

        <div style="margin-bottom:16px; border-left:3px solid var(--gold); padding-left:10px;">
            <div class="gold" style="font-size:10px; font-weight:bold; letter-spacing:1px; margin-bottom:4px;">📋 CARA PEMBUATAN</div>
            <p style="color:#ccc; font-size:12px; line-height:1.6; white-space:pre-line;">{{ $potion['cara_pembuatan']??'-' }}</p>
        </div>

        @if($potion['image'])
            <div style="margin-top:16px;">
                <img src="{{ asset($potion['image']) }}" alt="Potion" style="max-width:100%; border:2px solid var(--gold);">
            </div>
        @endif
    </div>

    {{-- Validation Form --}}
    <div>
        @if($potion['status'] === 'pending')
            <div class="card-gold" style="padding:24px;">
                <div class="gold" style="font-size:14px; font-weight:bold; letter-spacing:1px; margin-bottom:20px;">⚖️ FORM VALIDASI</div>

                <form method="POST" action="{{ route('guru.potions.validate', $potion['id']) }}">
                    @csrf

                    <div style="margin-bottom:16px;">
                        <label class="form-label">STATUS VALIDASI</label>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <label style="cursor:pointer;">
                                <input type="radio" name="status" value="approved" required style="display:none;" id="r_approved" onchange="toggleRating(true)">
                                <div id="lbl_approved" style="border:2px solid rgba(39,174,96,0.4); padding:12px; text-align:center; font-size:13px; color:#7cfc00; transition:all 0.2s;" onclick="document.getElementById('r_approved').checked=true; toggleRating(true); highlightStatus('approved')">
                                    ✅ APPROVE
                                </div>
                            </label>
                            <label style="cursor:pointer;">
                                <input type="radio" name="status" value="rejected" style="display:none;" id="r_rejected" onchange="toggleRating(false)">
                                <div id="lbl_rejected" style="border:2px solid rgba(192,57,43,0.4); padding:12px; text-align:center; font-size:13px; color:#ff6b6b; transition:all 0.2s;" onclick="document.getElementById('r_rejected').checked=true; toggleRating(false); highlightStatus('rejected')">
                                    ❌ REJECT
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="ratingSection" style="margin-bottom:16px; display:none;">
                        <label class="form-label">RATING (1-10)</label>
                        <input type="number" name="rating" class="form-input" min="1" max="10" placeholder="Masukkan nilai 1-10">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">KOMENTAR / FEEDBACK</label>
                        <textarea name="guru_comment" class="form-input" rows="4" placeholder="Berikan feedback untuk murid..."></textarea>
                    </div>

                    <button type="submit" class="btn-gold" style="width:100%;">
                        <i class="fas fa-check"></i> SUBMIT VALIDASI
                    </button>
                </form>
            </div>
        @else
            <div class="card" style="padding:24px; border-color:{{ $potion['status'] === 'approved' ? '#7cfc00' : '#ff6b6b' }};">
                <div style="font-size:14px; font-weight:bold; letter-spacing:1px; margin-bottom:16px; color:{{ $potion['status'] === 'approved' ? '#7cfc00' : '#ff6b6b' }};">
                    {{ $potion['status'] === 'approved' ? '✅ SUDAH DISETUJUI' : '❌ SUDAH DITOLAK' }}
                </div>
                @if($potion['rating'])
                    <div style="margin-bottom:12px;">
                        <span style="color:#888; font-size:12px;">Rating:</span>
                        <span class="gold" style="font-size:24px; font-weight:bold; margin-left:8px;">{{ $potion['rating'] }}/10</span>
                    </div>
                @endif
                @if($potion['guru_comment'])
                    <div style="background:rgba(0,0,0,0.3); padding:12px; font-size:13px; color:#ccc; border-left:3px solid var(--gold);">
                        "{{ $potion['guru_comment'] }}"
                    </div>
                @endif
                <div style="color:#888; font-size:11px; margin-top:12px;">
                    Divalidasi: {{ date('d M Y H:i', strtotime($potion['validated_at'])) }}
                </div>
            </div>
        @endif

        {{-- Student Info --}}
        <div class="card" style="padding:20px; margin-top:16px;">
            <div class="gold" style="font-size:12px; font-weight:bold; letter-spacing:1px; margin-bottom:12px;">👤 INFO MURID</div>
            <div style="font-size:14px; font-weight:bold;">{{ $student['name'] ?? '-' }}</div>
            <div style="color:#888; font-size:12px; margin-top:2px;">{{ $student['email'] ?? '' }}</div>
            @if(isset($student['level']))
                <div style="color:var(--gold); font-size:12px; margin-top:4px;">Level {{ $student['level'] }}</div>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleRating(show) {
        document.getElementById('ratingSection').style.display = show ? 'block' : 'none';
    }
    function highlightStatus(status) {
        document.getElementById('lbl_approved').style.background = status === 'approved' ? 'rgba(39,174,96,0.2)' : 'transparent';
        document.getElementById('lbl_rejected').style.background = status === 'rejected' ? 'rgba(192,57,43,0.2)' : 'transparent';
    }
</script>
@endsection
