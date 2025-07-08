@extends('layouts.app')

@section('title', 'Instructors')

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
                                @if ($instructor->image_path)
                                    <img src="{{ asset('storage/' . $instructor->image_path) }}" alt="{{ $instructor->name }}"
                                        style="border-radius: 50%; width: 50px; height: 50px; margin-right: 10px;">
                                @else
                                    <img src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png"
                                        alt="Default Image"
                                        style="border-radius: 50%; width: 50px; height: 50px; margin-right: 10px;">
                                @endif
                                {{ $instructor->title->name }} {{ $instructor->name }}
                            </div>
                        </td>
                        <td style="">{{ $instructor->email }}</td>
                        <td style="">
                            <a href="{{ route('instructors.show', $instructor) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('instructors.edit', $instructor) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('instructors.destroy', $instructor) }}" method="POST" class="d-inline">
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
                    @if ($instructors->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">« Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $instructors->previousPageUrl() }}&search={{ request('search') }}"
                                rel="prev">« Prev</a>
                        </li>
                    @endif

                    {{-- Page Number Links --}}
                    @for ($page = 1; $page <= $instructors->lastPage(); $page++)
                        <li class="page-item {{ $page == $instructors->currentPage() ? 'active' : '' }}">
                            <a class="page-link"
                                href="{{ $instructors->url($page) }}&search={{ request('search') }}">{{ $page }}</a>
                        </li>
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($instructors->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $instructors->nextPageUrl() }}&search={{ request('search') }}"
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
