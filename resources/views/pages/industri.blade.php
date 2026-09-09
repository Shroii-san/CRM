@extends('layout.main')
@section('title','industri')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8">
        
        <x-industri.action.action />
        

        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="p-6">
                
               

                <!-- Customer Table -->
                <div class="bg-white rounded-xl shadow-sm border">
                    <x-industri.table.table />
                    <x-industri.action.edit />
                    
                @push('scripts')

                <script src="{{ asset('js/search.js') }}"></script>
                @endpush
@endsection