<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Mail\StatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectCreated; 

class ProjectController extends Controller {
    public function index() {
        try {
            $projects = Project::with( 'getStaff' )->paginate( 10 );
            return view( 'project.index', compact( 'projects' ) );
        } catch ( Exception $e ) {
            Log::error( 'Error fetching projects: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while fetching projects: ' . $e->getMessage() );
        }
    }

    public function create( $id = null ) {
        try {
            $staff = User::where( 'role', 'staff' )->get();
            $projectData = $id ? Project::find( $id ) : null;

            return view( 'project.create', compact( 'staff', 'projectData' ) );
        } catch ( Exception $e ) {
            Log::error( 'Error fetching project creation data: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while fetching projects: ' . $e->getMessage() );
        }
    }

    public function store( Request $request ) {
        try {
            $request->validate( [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'staff_id' => 'required',
                'doc' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'status' => 'required',
            ] );

            if ( $request->doc ) {
                $currentTime = strtotime( now() );
                $fileDocument = $request->doc;
                $originalFileName = $fileDocument->getClientOriginalName();
                $fileExtension = $fileDocument->extension();
                $fileName = 'project_' . $currentTime . '.' . $fileExtension;
                $directoryPath = public_path( 'projectUploads' );

                if ( !File::isDirectory( $directoryPath ) ) {
                    File::makeDirectory( $directoryPath, 0777, true, true );
                }

                $filePath = $directoryPath . '/' . $fileName;
                $fileDocument->move( $directoryPath, $fileName );
            }

            $project = Project::create( [
                'name' => $request->name,
                'description' => $request->description,
                'staff_id' => $request->staff_id,
                'original_file_name' => $originalFileName,
                'file_name' => $fileName,
                'status' => $request->status,
            ] );

            // Send email to all admin users
            $admins = User::where( 'role', 'admin' )->get();
            foreach ( $admins as $admin ) {
                Mail::to( $admin->email )->send( new ProjectCreated( $project ) );
            }

            return redirect()->route( 'projects.index' )->with( 'success', 'Project created successfully' );
        } catch ( Exception $e ) {
            Log::error( 'Error storing project: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while storing projects: ' . $e->getMessage() );
        }
    }

    public function update( Request $request, $id ) {
        try {
            // Validate incoming request
            $request->validate( [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'staff_id' => 'required',
                'doc' => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'status' => 'required',
            ] );

            // Find the existing project
            $project = Project::findOrFail( $id );
            $originalStatus = $project->status;

            // Initialize variables for new file details
            $originalFileName = $project->original_file_name;
            $fileName = $project->file_name;
            $directoryPath = public_path( 'projectUploads' );

            // Check if a new file is uploaded
            if ( $request->doc ) {
                // Generate new file name
                $currentTime = strtotime( now() );
                $fileDocument = $request->doc;
                $originalFileName = $fileDocument->getClientOriginalName();
                $fileExtension = $fileDocument->extension();
                $fileName = 'project_' . $currentTime . '.' . $fileExtension;

                // Ensure the upload directory exists
                if ( !File::isDirectory( $directoryPath ) ) {
                    File::makeDirectory( $directoryPath, 0777, true, true );
                }

                // Move the uploaded file
                $fileDocument->move( $directoryPath, $fileName );
            }

            // Update the project
            $project->update( [
                'name' => $request->name,
                'description' => $request->description,
                'staff_id' => $request->staff_id,
                'original_file_name' => $originalFileName,
                'file_name' => $fileName,
                'status' => $request->status,
            ] );

            // Check if status has changed
            if ( $originalStatus !== $request->status ) {
                // Get all admin users
                $admins = User::where( 'role', 'admin' )->get();

                // Send email to each admin
                foreach ( $admins as $admin ) {
                    Mail::to( $admin->email )->send( new StatusChanged( $project ) );
                }
            }

            return redirect()->route( 'projects.index' )->with( 'success', 'Project updated successfully' );
        } catch ( Exception $e ) {
            Log::error( 'Error updating project: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while updating the project: ' . $e->getMessage() );
        }
    }

    public function recycleBin() {
        try {
            $deletedProjects = Project::onlyTrashed()->get();
            return view( 'project.recycle_bin', compact( 'deletedProjects' ) );
        } catch ( Exception $e ) {
            Log::error( 'Error fetching recycle bin data: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while fetching recycle bin data: ' . $e->getMessage() );
        }
    }

    public function restoreMultiple( Request $request ) {
        try {
            $request->validate( [
                'project_ids' => 'required|array',
                'project_ids.*' => 'exists:projects,id',
            ] );

            Project::onlyTrashed()->whereIn( 'id', $request->project_ids )->restore();
            return redirect()->route( 'projects.index' )->with( 'success', 'Selected projects have been restored!' );
        } catch ( Exception $e ) {
            Log::error( 'Error restoring projects: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while restoring projects: ' . $e->getMessage() );
        }
    }

    public function destroy( $id ) {
        try {
            $project = Project::findOrFail( $id );
            $project->delete();
            return redirect()->route( 'projects.index' )->with( 'success', 'Project deleted successfully.' );
        } catch ( Exception $e ) {
            Log::error( 'Error deleting project: ' . $e->getMessage(), [ 'line' => $e->getLine(), 'file' => $e->getFile() ] );
            return back()->with( 'error', 'An error occurred while deleting the project: ' . $e->getMessage() );
        }
    }
}
