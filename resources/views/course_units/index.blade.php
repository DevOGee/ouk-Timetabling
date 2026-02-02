@extends('layouts.app')

@section('title', 'Course Units')

@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Course Units</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- <div class="mb-3 d-flex justify-content-between">
            <a href="{{ route('course_units.create') }}" class="btn btn-primary">Add Course Unit</a>
            <a href="{{ route('course_units.upload') }}" class="btn btn-secondary">Bulk Upload</a>
        </div> --}}

        <div class="mb-3 d-flex justify-content-between">
            <div>
                <a href="{{ route('course_units.create') }}" class="btn btn-primary">Add Course Unit</a>
                <a href="{{ route('course_units.upload') }}" class="btn btn-secondary">Bulk Upload</a>
            </div>
            <form method="GET" action="{{ route('course_units.index') }}" class="d-flex align-items-center">
                <select name="department_id" class="form-select me-2" style="max-width: 200px;" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Code or Name"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th>Course Code</th>
                    <th>Course Unit Name</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courseUnits as $index => $courseUnit)
                    <tr>
                        <td>{{ ($courseUnits->currentPage() - 1) * $courseUnits->perPage() + $index + 1 }}</td>
                        <td
                            style="background-color: {{ $courseUnit->color ?? '#000000' }}; color: white; text-align: center;">
                            <!-- Color square -->
                            <span
                                style="display: inline-block; width: 20px; height: 20px; background-color: {{ $courseUnit->color ?? '#000000' }};"></span>
                        </td>
                        <td>{{ $courseUnit->code }}</td>
                        <td>{{ $courseUnit->name }}</td>
                        <td>{{ $courseUnit->department->name ?? '-' }}</td>
                        <td>
                            @can('view', $courseUnit)
                                <a href="{{ route('course_units.show', $courseUnit) }}" class="btn btn-warning btn-sm">View</a>
                            @endcan
                            <a href="{{ route('course_units.edit', $courseUnit) }}" class="btn btn-warning btn-sm">Edit</a>
                            @can('delete', $courseUnit)
                                <form action="{{ route('course_units.destroy', $courseUnit) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $courseUnits->links() }}
        </div>
    </div>
@endsection
