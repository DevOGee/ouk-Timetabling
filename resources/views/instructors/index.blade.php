@extends('layouts.app')

@section('title', 'Lecturers')

@section('content')
    <style>
        .pagination {
            display: flex;
            padding: 0;
            list-style: none;
        }

        .pagination .page-item {
            margin: 0 5px;
        }

        .pagination .page-item .page-link {
            color: #037b90;
            /* Your primary color */
            border-radius: 5px;
            border: 1px solid #037b90;
            padding: 8px 12px;
            transition: all 0.3s;
        }

        .pagination .page-item.active .page-link {
            background-color: #037b90;
            color: white;
            border: 1px solid #037b90;
        }

        .pagination .page-item.disabled .page-link {
            color: #aaa;
            cursor: not-allowed;
        }
    </style>
    <div class="container mt-5">
        <h2 class="mb-4">Lecturers</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3 d-flex justify-content-between">
            <div>
                <a href="{{ route('instructors.create') }}" class="btn btn-primary">Add Lecturer</a>
                <a href="{{ route('instructors.upload') }}" class="btn btn-secondary">Bulk Upload</a>
            </div>
            <form method="GET" action="{{ route('instructors.index') }}" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or Email"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lecturers as $lecturer)
                    <tr>
                        <td>{{ ($lecturers->currentPage() - 1) * $lecturers->perPage() + $loop->iteration }}</td>
                        <td>{{ $lecturer->title->name }} {{ $lecturer->name }}</td>
                        <td>{{ $lecturer->email }}</td>
                        <td>
                            @if ($lecturer->image_path)
                                <img src="{{ asset('storage/' . $lecturer->image_path) }}" alt="{{ $lecturer->name }}"
                                    width="50">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('instructors.show', $lecturer) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('instructors.edit', $lecturer) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('instructors.destroy', $lecturer) }}" method="POST" class="d-inline">
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
            <nav>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($lecturers->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">« Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $lecturers->previousPageUrl() }}&search={{ request('search') }}"
                                rel="prev">« Prev</a>
                        </li>
                    @endif

                    {{-- Page Number Links --}}
                    @for ($page = 1; $page <= $lecturers->lastPage(); $page++)
                        <li class="page-item {{ $page == $lecturers->currentPage() ? 'active' : '' }}">
                            <a class="page-link"
                                href="{{ $lecturers->url($page) }}&search={{ request('search') }}">{{ $page }}</a>
                        </li>
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($lecturers->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $lecturers->nextPageUrl() }}&search={{ request('search') }}"
                                rel="next">Next »</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next »</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
@endsection
