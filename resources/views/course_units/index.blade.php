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

        <div class="mb-3">
            <a href="{{ route('course_units.create') }}" class="btn btn-primary">Add Course Unit</a>
            <a href="{{ route('course_units.upload') }}" class="btn btn-secondary">Bulk Upload</a>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form action="{{ route('course_units.bulk_action') }}" method="POST" id="bulk-action-form" class="d-flex align-items-center gap-2">
                @csrf
                <select name="action" class="form-select w-auto" id="bulk-action-select" required>
                    <option value="">Bulk Actions</option>
                    <option value="delete">Delete</option>
                    <option value="move">Move to Department</option>
                </select>
                <select name="target_department_id" class="form-select w-auto d-none" id="target-department-select">
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">Apply</button>
            </form>

            <form method="GET" action="{{ route('course_units.index') }}" class="d-flex align-items-center">
                <select name="per_page" class="form-select me-2" style="max-width: 100px;" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
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
                    <th style="width: 40px;"><input type="checkbox" id="select-all" class="form-check-input"></th>
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
                        <td>
                            <input type="checkbox" name="selected_ids[]" value="{{ $courseUnit->id }}" form="bulk-action-form" class="form-check-input course-checkbox">
                        </td>
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
                            <form action="{{ route('course_units.destroy', $courseUnit) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $courseUnits->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    courseCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Bulk Action Dropdown functionality
            const bulkActionSelect = document.getElementById('bulk-action-select');
            const targetDepartmentSelect = document.getElementById('target-department-select');

            if (bulkActionSelect && targetDepartmentSelect) {
                bulkActionSelect.addEventListener('change', function() {
                    if (this.value === 'move') {
                        targetDepartmentSelect.classList.remove('d-none');
                        targetDepartmentSelect.required = true;
                    } else {
                        targetDepartmentSelect.classList.add('d-none');
                        targetDepartmentSelect.required = false;
                        targetDepartmentSelect.value = '';
                    }
                });
            }
        });
    </script>
@endsection
