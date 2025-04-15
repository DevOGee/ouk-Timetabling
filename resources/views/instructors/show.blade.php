@extends('layouts.app')

@section('title', 'Lecturer Profile')

@section('content')
    <style>
        /* Styling for the profile section */
        .profile-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0;
        }

        .profile-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .lecturer-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .profile-right {
            flex: 2;
            margin-left: 30px;
        }

        .btn i {
            margin-right: 5px;
        }

        .table-responsive {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(3, 123, 144, 0.1);
        }

        /* Styling for the top banner */
        .header-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 5px solid #037b90;
        }

        /* Left-align the Programme column */
        .programme-column {
            text-align: left;
        }

        /* Custom Button Styles */
        .btn-custom-edit {
            background-color: #037b90;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-custom-edit:hover {
            background-color: #026f7f;
        }

        .btn-custom-edit i {
            margin-right: 8px;
        }

        .btn-custom-customize {
            background-color: transparent;
            color: #037b90;
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid #037b90;
            border-radius: 25px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-custom-customize:hover {
            background-color: #037b90;
            color: white;
        }

        .btn-custom-customize i {
            margin-right: 8px;
        }
    </style>

    <img src="https://ouk.ac.ke/sites/default/files/slides/ouk_home3.jpg" class="header-image" alt="Top Header Image">

    <div class="container mt-5">
        <!-- Top Header Image -->

        <!-- Profile Section -->
        <div class="profile-container">
            <!-- Left Side: Profile Info -->
            <div class="profile-left">
                <img src="{{ !empty($lecturer->image_path) ? asset('storage/' . $lecturer->image_path) : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}"
                    class="lecturer-image" alt="{{ $lecturer->name }}">

                <h5 class="mt-3">{{ optional($lecturer->title)->name ?? 'N/A' }} {{ $lecturer->name }}</h5>
                <p class="mb-1">{{ $lecturer->email }}</p>

                <!-- Edit & Back Buttons -->
                <div class="mt-4">
                    <a href="{{ route('instructors.edit', ['instructor' => $lecturer->id]) }}"
                        class="btn btn-custom-edit me-2">
                        <i class="fas fa-edit"></i> Edit Profile Info
                    </a>
                    <a href="{{ route('instructors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <!-- Delete Form -->
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

            <!-- Right Side: Courses Being Taught -->
            <div class="profile-right">
                <h3 class="text-primary">Courses Being Taught</h3>

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
                                    <th class="programme-column">Programme</th>
                                    <th>Year & Semester</th>
                                    <th>Day(s) Scheduled</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lecturer->courseUnitProgrammeMappings as $mapping)
                                    <tr>
                                        <td class="fw-bold">{{ $mapping->courseUnit?->code ?? '-' }}</td>
                                        <td>{{ $mapping->courseUnit?->name ?? '-' }}</td>
                                        <td class="programme-column">
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
    </div>

    {{-- FontAwesome for Icons --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
