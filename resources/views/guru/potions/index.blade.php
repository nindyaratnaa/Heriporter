@extends('layouts.app')
@section('title', 'Validasi Ramuan')
@section('content')

<div class="page-title">⚗️ VALIDASI RAMUAN</div>
<div class="page-sub">Review dan validasi ramuan dari murid</div>

<div style="display:flex; gap:12px; margin-bottom:20px;">
    <span style="background:rgba(212,175,55,0.15); border:1px solid var(--gold); color:var(--gold); padding:4px 14px; font-size:12px;">⏳ Pending: {{ $pending }}</span>
    <span style="background:rgba(39,174,96,0.15); border:1px solid #7cfc00; color:#7cfc00; padding:4px 14px; font-size:12px;">✅ Approved: {{ $approved }}</span>
    <span style="background:rgba(192,57,43,0.15); border:1px solid #ff6b6b; color:#ff6b6b; padding:4px 14px; font-size:12px;">❌ Rejected: {{ $rejected }}</span>
</div>

<div class="card" style="overflow:hidden; padding:0;">
    @if(count($queue) > 0)
        <table class="pixel-table">
            <thead>
                <tr>
                    <th>RAMUAN</th>
                    <th>MURID</th>
                    <th>TANGGAL</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($queue as $p)
                    <tr>
                        <td>
                            <div style="font-weight:bold;">{{ $p['name'] }}</div>
                            <div style="color:#888; font-size:11px; margin-top:2px;">{{ count($p['ingredients']) }} bahan</div>
                        </td>
                        <td>
                            <div style="font-size:13px;">{{ $p['student']['name'] ?? '-' }}</div>
                            <div style="color:#888; font-size:11px;">{{ $p['student']['email'] ?? '' }}</div>
                        </td>
                        <td style="color:#888; font-size:12px;">{{ date('d M Y', strtotime($p['created_at'])) }}</td>
                        <td><span class="badge-{{ $p['status'] }}">{{ strtoupper($p['status']) }}</span></td>
                        <td>
                            <a href="{{ route('guru.potions.show', $p['id']) }}" class="btn-outline" style="padding:5px 12px; font-size:11px;">
                                {{ $p['status'] === 'pending' ? '🔍 Review' : '👁 Detail' }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding:48px; text-align:center;">
            <div style="font-size:48px; margin-bottom:12px;">✅</div>
            <div class="gold" style="font-size:16px; font-weight:bold;">Semua sudah divalidasi!</div>
        </div>
    @endif
</div>
@endsection
