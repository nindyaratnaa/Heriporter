@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')

<div id="profile-app"></div>

@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush