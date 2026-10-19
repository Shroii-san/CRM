@extends('layouts.main')

@section('title', 'HRD')

@section('content')
    <div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium">Data belum dapat disimpan:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- HRD Card -->
        <div x-data="hrdTable"
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-2">

            <!-- Card Header -->
            <div class="px-6 py-2 border-b border-gray-200">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 m-0">HRD Management</h3>
                        <p class="text-sm text-gray-500 mt-1 mb-0">Kelola data calon karyawan dan proses rekrutmen</p>
                    </div>

                    <div class="flex gap-2.5 items-center flex-wrap">
                        <button type="button" @click="$dispatch('open-modal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white border-0 rounded-lg font-semibold text-sm cursor-pointer shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg shrink-0">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Calon Karyawan</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <x-hrd.attribut.filtersearch :divisions="$divisions ?? []" />

            <!-- Table -->
            <x-hrd.table.table />

            <!-- Pagination -->
            <x-hrd.table.table-footer />
        </div>
    </div>

    @include('components.hrd.action.action')
@endsection

@push('scripts')
    @vite('resources/js/pages/hrd.js')
@endpush