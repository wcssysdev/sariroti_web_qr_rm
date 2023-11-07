@extends('layouts.app')

@section('page_title', 'Home Page')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/home/dashboard/datatable.js') }}"></script>
@endpush

@section('content')
@endsection
