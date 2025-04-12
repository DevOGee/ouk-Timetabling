@extends('layouts.app')

@section('title', 'Curriculum Setup')

@section('content')
    <div class="container">
        <h2 class="mb-4">Curriculum Setup</h2>

        @if (session('success_report') || session('error_report'))
            <div class="mt-4">
                @if (session('success_report'))
                    <div class="alert alert-success">
                        <strong>Successful Mappings:</strong>
                        <ul>
                            @foreach (session('success_report') as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error_report'))
                    <div class="alert alert-warning">
                        <strong>Failed Mappings:</strong>
                        <ul>
                            @foreach (session('error_report') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif


        <div class="mb-4">
            <form action="{{ route('curriculum.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="csv_file" class="form-label">Bulk Upload (CSV):</label>
                    <input type="file" class="form-control" name="csv_file" id="csv_file" required>
                </div>
                <button type="submit" class="btn btn-success">Upload Mappings</button>
                <a href="{{ route('curriculum.download-sample') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download"></i> Download Sample CSV
                </a>

            </form>
        </div>

        {{-- <h4>Programmes</h4> --}}
        @foreach ($schools as $school)
            <div class="mb-4">
                <h5 class="mb-3">{{ $school->name }}</h5>

                @if ($school->programmes->isEmpty())
                    <p class="text-muted">No programmes available in this school.</p>
                @else
                    <ul class="list-group">
                        @foreach ($school->programmes->sortBy('programme_code') as $programme)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $programme->programme_code }} - {{ $programme->name }}
                                <a href="{{ route('curriculum.show', $programme->id) }}" class="btn btn-sm btn-primary">
                                    Setup Curriculum
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
@endsection
