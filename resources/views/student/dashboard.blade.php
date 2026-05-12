@extends('layouts.app')
@section('title', 'Dashboard - Student')
@section('content')

<div id="dashboard-app"></div>

@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush