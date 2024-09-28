@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Recycle Bin</h2>

    <form action="{{ route('projects.restoreMultiple') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Deleted Projects</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">Select</th>
                            <th scope="col">Project Name</th>
                            <th scope="col">Deleted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deletedProjects as $project)
                            <tr>
                                <td>
                                    <input type="checkbox" name="project_ids[]" value="{{ $project->id }}">
                                </td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->deleted_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No projects in the recycle bin</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($deletedProjects->count() > 0)
                    <button type="submit" class="btn btn-success mt-3">Restore Selected Projects</button>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection