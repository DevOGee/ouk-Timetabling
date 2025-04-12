@extends('layouts.app')

@section('title', 'Lecturer Details')

@section('content')
    <style>
        .lecturer-card {
            max-width: 400px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
        }

        .lecturer-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .table-responsive {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(3, 123, 144, 0.1);
        }

        .btn i {
            margin-right: 5px;
        }
    </style>
    <div class="container mt-5">
        <h2 class="mb-4 text-center text-primary">Lecturer Profile</h2>

        <div class="shadow-lg card lecturer-card">
            {{-- Display Lecturer's Image --}}
            <div class="text-center text-white card-header bg-dark">
                <img src="{{ !empty($lecturer->image_path) ? asset('storage/' . $lecturer->image_path) : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}"
                    class="mx-auto lecturer-image d-block" alt="{{ $lecturer->name }}">
                <h5 class="mt-3">{{ optional($lecturer->title)->name ?? 'N/A' }} {{ $lecturer->name }}</h5>
                <p class="mb-1">{{ $lecturer->email }}</p>
            </div>

            <div class="text-center card-body">
                {{-- Edit & Back Buttons --}}
                <a href="{{ route('instructors.edit', ['instructor' => $lecturer->id]) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('instructors.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                {{-- Delete Form --}}
                <form action="{{ route('instructors.destroy', ['instructor' => $lecturer->id]) }}" method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger ms-2"
                        onclick="return confirm('Are you sure you want to delete this lecturer?')">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Courses Assigned to Lecturer --}}
        <div class="mt-5">
            <h3 class="text-primary">Assigned Courses</h3>

            @if ($lecturer->courseUnitProgrammeMappings->isEmpty())
                <div class="text-center alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No courses assigned.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table text-center table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Programme</th>
                                <th>Year & Semester</th>
                                <th>Day(s) Scheduled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lecturer->courseUnitProgrammeMappings as $mapping)
                                <tr>
                                    <td class="fw-bold">{{ $mapping->courseUnit?->code ?? '-' }}</td>
                                    <td>{{ $mapping->courseUnit?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $mapping->programme?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $mapping->yearOfStudy?->name ?? '-' }} -
                                            {{ $mapping->semester?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $mapping->day?->name ?? 'Not Assigned' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- FontAwesome for Icons --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
