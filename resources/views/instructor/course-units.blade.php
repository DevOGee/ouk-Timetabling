@extends('layouts.app')

@section('title', 'My Course Units')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-journal-text me-2"></i>My Course Units
        </h1>
    </div>

    @if($courseUnits->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> You don't have any assigned course units yet.
        </div>
    @else
        @foreach($courseUnits as $academicSessionId => $mappings)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ $mappings->first()->academicSession->name ?? 'Academic Session' }}
                        <span class="badge bg-primary ms-2">
                            {{ $mappings->count() }} course unit{{ $mappings->count() > 1 ? 's' : '' }}
                        </span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Unit</th>
                                    <th>Programme</th>
                                    <th>Year/Sem</th>
                                    <th>Day</th>
                                    <th>Schedule</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mappings as $mapping)
                                    <tr>
                                        <td>{{ $mapping->courseUnit->code ?? 'N/A' }}</td>
                                        <td>{{ $mapping->courseUnit->name ?? 'N/A' }}</td>
                                        <td>{{ $mapping->programme->name ?? 'N/A' }}</td>
                                        <td>
                                            Y{{ $mapping->yearOfStudy->name ?? 'N/A' }}/
                                            S{{ $mapping->semester->name ?? 'N/A' }}
                                        </td>
                                        <td>{{ $mapping->day->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($mapping->morning_start_time)
                                                <div>Morning: {{ date('H:i', strtotime($mapping->morning_start_time)) }}
                                                    ({{ $mapping->morning_duration }} hrs)</div>
                                            @endif
                                            @if($mapping->evening_start_time)
                                                <div>Evening: {{ date('H:i', strtotime($mapping->evening_start_time)) }}
                                                    ({{ $mapping->evening_duration }} hrs)</div>
                                            @endif
                                            @if(!$mapping->morning_start_time && !$mapping->evening_start_time)
                                                Not scheduled
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@push('styles')
<style>
    .table th {
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush
@endsection
