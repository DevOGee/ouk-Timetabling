@extends('layouts.app')

@section('title', 'Course Unit Details')

@section('content')
    <div class="container mt-5">
        <!-- Fancy Card for Course Unit Details -->
        <div class="border-0 shadow-lg card rounded-3">
            <div class="text-white card-header bg-primary">
                <h3 class="mb-0">Course Unit Details</h3>
            </div>
            <div class="card-body">
                <!-- Course Unit Details Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="table-primary">
                            <th scope="col">Attribute</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Course Unit Name</strong></td>
                            <td>{{ $courseUnit->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Code</strong></td>
                            <td>{{ $courseUnit->code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Color</strong></td>
                            <td>
                                <span
                                    style="background-color: {{ $courseUnit->color }}; padding: 8px 12px; color: white; border-radius: 5px;">
                                    {{ $courseUnit->color }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- List of Programmes Taking This Course -->
                <h5 class="mt-4">Programmes Taking This Course</h5>

                @if ($courseUnit->programmes->count() > 0)
                    <!-- Programmes Table -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Programme Name</th>
                                <th scope="col">Instructors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseUnit->programmes as $programme)
                                <tr>
                                    <td>{{ $programme->name }}</td>
                                    <td>
                                        <!-- Debugging: Check if lecturers are returned -->
                                        @php
                                            $instructors = $programme->lecturersForCourseUnit($courseUnit->id);
                                        @endphp
                                        @if ($instructors->count() > 0)
                                            <ul class="list-unstyled">
                                                @foreach ($instructors as $instructor)
                                                    <li>{{ $instructor->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted">No instructors assigned yet.</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No programmes are currently taking this course.</p>
                @endif

                <!-- Edit Button & Back Button -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('course_units.edit', $courseUnit->id) }}" class="px-4 py-2 btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('course_units.index') }}" class="px-4 py-2 btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
