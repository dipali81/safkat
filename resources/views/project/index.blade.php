@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Projects</h1>
    
    {{-- Display Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Display Error Message --}}
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">Add Project</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Staff Name</th>
                    <th>File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->getStaff->name }}</td>
                    <td>
                        @if ($project->original_file_name && File::exists(public_path('projectUploads/' . $project->file_name)))
                            <a href="{{ asset('projectUploads/' . $project->file_name) }}" download>
                                {{ $project->original_file_name }}
                            </a>
                        @else
                            {{ $project->original_file_name }} (File not available)
                        @endif
                    </td>
                    
                    <td>
                        <a href="{{ route('projects.create', ['id'=> $project->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-project" data-id="{{ $project->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $projects->links() }}
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this project?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        var deleteForm = $('#deleteForm');

        $('.delete-project').on('click', function() {
            var projectId = $(this).data('id');
            deleteForm.attr('action', '/projects/delete/' + projectId);
        });
    });
</script>
@endsection
