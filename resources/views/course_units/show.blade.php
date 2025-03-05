@extends('layouts.app')

@section('title', 'Course Unit Details')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Course Unit: {{ $courseUnit->name }}</h2>

        <div class="mb-3">
            <strong>Code:</strong> {{ $courseUnit->code }}
        </div>

        <a href="{{ route('course_units.index') }}" class="btn btn-secondary">Back</a>
    </div>
@endsection
