@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if($programme)
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.programmes.index') }}">Programmes</a></li>
                        <li class="breadcrumb-item active">{{ $programme->programme_code }}</li>
                    </ol>
                </nav>
                <h4>Specialisations for {{ $programme->name }}</h4>
            @else
                <h4>Programme Specialisations</h4>
            @endif
        </div>
        <div>
            <a href="{{ $programme ? route('admin.programmes.index') : route('admin.programmes.index') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Programmes
            </a>
            <a href="{{ route('admin.specialisations.create', ['programme_id' => $programme->id ?? null]) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Specialisation
            </a>
        </div>
    </div>

    @if($specialisations->isEmpty())
        <div class="alert alert-info shadow-sm">
            @if($programme)
                No specialisations found for this programme yet.
            @else
                No specialisations found. You can add them to programmes that have the "Has Specialisations" toggle enabled.
            @endif
        </div>
    @else
        @foreach($specialisations as $programmeId => $programmeSpecialisations)
            @php $programme = $programmeSpecialisations->first()->programme; @endphp
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        {{ $programme->name }} ({{ $programme->programme_code }})
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            {{ $programme->department->school->name }} - {{ $programme->department->name }}
                        </small>
                    </h5>
                    <a href="{{ route('admin.specialisations.create', ['programme_id' => $programme->id]) }}" class="btn btn-sm btn-outline-primary">
                        Add More
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>Code</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programmeSpecialisations as $specialisation)
                                    <tr>
                                        <td class="ps-3">{{ $specialisation->name }}</td>
                                        <td>{{ $specialisation->code ?? 'N/A' }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.specialisations.edit', $specialisation) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.specialisations.destroy', $specialisation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this specialisation?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
