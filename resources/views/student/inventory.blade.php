@extends('layouts.app')
@section('title', 'Inventori Ramuan')
@section('content')

<div class="page-title">📦 INVENTORI RAMUAN</div>
<div class="page-sub">Koleksi ramuan yang telah disetujui guru</div>

<div id="inventory-app"></div>

@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush