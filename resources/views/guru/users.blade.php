@extends('layouts.app')
@section('title', 'Data Murid')
@section('content')

<div class="page-title">👥 DATA MURID</div>
<div class="page-sub">Daftar semua murid terdaftar</div>

<div class="card" style="overflow:hidden; padding:0;">
    @if(count($users) > 0)
        <table class="pixel-table">
            <thead>
                <tr>
                    <th>MURID</th>
                    <th>EMAIL</th>
                    <th style="text-align:center;">LEVEL</th>
                    <th style="text-align:center;">XP</th>
                    <th>BERGABUNG</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                @if(!empty($u['photo']))
                                    <img src="{{ asset($u['photo']) }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:1px solid var(--copper-dim);">
                                @else
                                    <img src="{{ asset('images/dummy profile.jpg') }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:1px solid var(--copper-dim);">
                                @endif
                                <div>
                                    <div style="font-weight:bold;">{{ $u['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#888; font-size:12px;">{{ $u['email'] }}</td>
                        <td style="text-align:center;">
                            <span class="gold" style="font-weight:bold;">Lv.{{ $u['level'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <div style="font-size:12px; color:#888;">{{ $u['xp'] }}/{{ $u['max_xp'] }}</div>
                            <div style="background:rgba(0,0,0,0.4); border:1px solid rgba(212,175,55,0.3); height:6px; overflow:hidden; margin-top:4px; width:80px;">
                                <div style="background:var(--gold); height:100%; width:{{ $u['max_xp'] > 0 ? round(($u['xp'] / $u['max_xp']) * 100) : 0 }}%;"></div>
                            </div>
                        </td>
                        <td style="color:#888; font-size:12px;">{{ date('d M Y', strtotime($u['created_at'])) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding:48px; text-align:center; color:#888;">
            <div style="font-size:48px; margin-bottom:12px;">👥</div>
            Belum ada murid terdaftar.
        </div>
    @endif
</div>
@endsection
