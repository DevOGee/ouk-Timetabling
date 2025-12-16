@extends('layouts.app')

@section('title', 'Instructors')

@section('content')
    <style>        
        table {
            width: 100%;
            border-spacing: 0;
            border-top: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr {
            border: none;
        }

        /* Alternating row colors */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: white;
        }

        /* Remove vertical borders */
        td,
        th {
            border-left: none;
            border-right: none;
        }
    </style>
    <div class="container mt-5">
        <h2 class="mb-4">Instructors</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3 d-flex justify-content-between">
            @can('create', App\Models\User::class)
            <div>
                <a href="{{ route('instructors.create') }}" class="btn btn-primary">Add Lecturer</a>
                <a href="{{ route('instructors.upload') }}" class="btn btn-secondary">Bulk Upload</a>
            </div>
            @else
            <div></div> <!-- Empty div to maintain flex layout -->
            @endcan
            <form method="GET" action="{{ route('instructors.index') }}" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or Email"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>


        <table class="table" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($instructors as $instructor)
                    <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : 'white' }};">
                        <td style="">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; margin-right: 10px; background-color: #f0f0f0;">
                                    @if ($instructor->image_path)
                                        <img src="{{ asset('storage/' . $instructor->image_path) }}" 
                                            alt="{{ $instructor->name }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <img src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png"
                                            alt="Default Image"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @endif
                                </div>
                                @if($instructor->title)
                                    <span class="text-muted me-1">{{ $instructor->title->abbreviation ?? $instructor->title->name }}</span>
                                @endif
                                {{ $instructor->name }}
                            </div>
                        </td>
                        <td style="">{{ $instructor->email }}</td>
                        <td style="">
                            @can('view', $instructor)
                                <a href="{{ route('instructors.show', $instructor) }}" class="btn btn-info btn-sm">View</a>
                            @endcan
                            <a href="{{ route('instructors.edit', $instructor) }}" class="btn btn-warning btn-sm">Edit</a>
                            @can('delete', $instructor)
                                <form action="{{ route('instructors.destroy', $instructor) }}" method="POST" class="d-inline" id="delete-form-{{ $instructor->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $instructor->id }})">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>





        <div class="mt-3 d-flex justify-content-center">
            {{ $instructors->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(instructorId) {
            if (confirm('Are you sure you want to delete this instructor? This action cannot be undone.')) {
                event.preventDefault();
                document.getElementById('delete-form-' + instructorId).submit();
            }
        }
    </script>
    @endpush
@endsection
