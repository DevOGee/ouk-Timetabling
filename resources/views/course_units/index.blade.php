@extends('layouts.app')

@section('title', 'Course Units')

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
            <form method="GET" action="{{ route('course_units.index') }}" class="d-flex">
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
                        <td>
                            <a href="{{ route('course_units.show', $courseUnit) }}" class="btn btn-warning btn-sm">View</a>
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
            <nav>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($courseUnits->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">« Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $courseUnits->previousPageUrl() }}" rel="prev">« Prev</a>
                        </li>
                    @endif

                    {{-- Page Number Links --}}
                    @foreach ($courseUnits->getUrlRange(1, $courseUnits->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $courseUnits->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($courseUnits->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $courseUnits->nextPageUrl() }}" rel="next">Next »</a>
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
