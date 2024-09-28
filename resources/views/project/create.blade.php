@extends('layouts.app')

@section('content')
    <style>
        /* Remove bullet points */
ul.parsley-errors-list {
    list-style-type: none;  /* Removes bullet points */
    padding-left: 0;
}

/* Set error message color to red */
ul.parsley-errors-list li {
    color: red;  /* Changes error text color to red */
    font-weight: bold;  /* Optional: makes the text bold */
}

/* Optional: Add margin or padding to adjust spacing */
ul.parsley-errors-list li {
    margin: 0.25rem 0;  /* Adds some spacing between error messages */
}

        #drop-area {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #drop-area:hover {
            background-color: #f0f8ff;
        }

        .file-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }

        .close-icon {
            cursor: pointer;
            color: red;
        }

        .drag-area,
        .doc-drag-area {
            /* border: 2px dashed #fff; */
            /* height: 120px; */
            /* border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column; */
            padding: 2rem;
            cursor: pointer;

        }

        .drag-area.active,
        .doc-drag-area.active {
            border: 2px solid #fff;
        }

        .drag-area .icon,
        .doc-drag-area .icon {
            font-size: inherit;
            /* font-size: 100px; */
            color: rgb(36, 125, 241);
        }

        .drag-area header {
            font-size: 30px;
            font-weight: 700;
            color: #007abc;
        }

        .drag-area span,
        .doc-drag-area span {
            font-size: 16px;
            font-weight: 700;
            color: #007abc;
            margin: 10px 0 15px 0;
            font-family: "Noto Sans", sans-serif;

        }

        .drag-area button,
        .doc-drag-area button {
            padding: 10px 25px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            outline: none;
            background: #fff;
            color: #007abc;
            border-radius: 5px;
            cursor: pointer;
        }

        .drag-area img,
        .doc-drag-area img {
            /* height: 100%;
      width: 100%; */
            object-fit: cover;
            border-radius: 5px;
        }


        /* custom */

        .upload-document--custom .upload-document__item,
        .upload-document--custom {
            padding: 0;
        }

        .upload-document--custom .upload-document__inner {
            max-width: 100%;
            display: block;
        }

        .area2 {
            /* padding: 2rem; */
        }

        .area2 img,
        .doc-area2 img {
            max-width: 60px;
            margin-right: 1rem;
            padding: 2rem 0;
        }

        .upload-document {
  border: 1px dashed #707070;
  padding: 2rem;
  margin-bottom: 1.5rem;
}
.upload-document__inner {
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-justify-content: space-around;
  -ms-flex-pack: distribute;
  justify-content: space-around;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -moz-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  text-align: center;
  max-width: 500px;
  margin: 0 auto;
}
.upload-document__item {
  padding: 0 1rem;
}
.upload-document__item p:last-child {
  margin-bottom: 0;
}
.upload-document__item p a {
  font-weight: 700;
}
.upload-document__item p a span {
  display: block;
  margin-top: 10px;
}
.upload-document--tooltip {
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -moz-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  margin-bottom: 1.5rem;
}
.upload-document--tooltip .upload-document {
  margin-bottom: 0;
}
.upload-document--tooltip__left {
  -webkit-box-flex: 1;
  -webkit-flex: 1 0 0%;
  -moz-box-flex: 1;
  -ms-flex: 1 0 0%;
  flex: 1 0 0%;
}
.upload-document--tooltip__right {
  -webkit-box-flex: 0;
  -webkit-flex: 0 0 auto;
  -moz-box-flex: 0;
  -ms-flex: 0 0 auto;
  flex: 0 0 auto;
  width: auto;
}
   
    </style>
    <div class="container py-5">
        <div class="row justify-content-center">
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
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ $projectData ? 'Edit Project' : 'Create New Project' }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="projectForm"
                            action="{{ $projectData ? route('projects.update', $projectData->id) : route('projects.store') }}"
                            method="POST" enctype="multipart/form-data" data-parsley-validate>
                            @csrf
                            @if ($projectData)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Project Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $projectData->name ?? '') }}" required
                                    data-parsley-required-message="Project name is required.">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required
                                    data-parsley-required-message="Project description is required.">{{ old('description', $projectData->description ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Assign Project to Staff</label>
                                <select class="form-select" id="staff_id" name="staff_id" required
                                    data-parsley-required-message="Staff member is required.">
                                   <option value="" >Select Staff</option>
                                    @foreach ($staff as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('staff_id', $projectData->staff_id ?? '') == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="upload-document upload-document--custom" id="file-upload">
                                <div class="upload-document__inner">
                                    <div class="upload-document__item">
                                        <div class="drag-area">
                                            <div class="icon"><i class="fas fa-cloud-upload-alt">
                                                    <img src="{{ asset('/images/download.png') }}" style="height: 50px"
                                                        alt="Browse File">
                                                </i></div>
                                            <p>
                                                <a>
                                                    <span style="cursor: pointer;" id="browse">Drag and Drop or Upload File</span>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="area2"></div>
                                        <input type="file" id="doc" name="doc" {{ $projectData ? '' : 'required'}}  hidden
                                            data-parsley-required-message="File is required."
                                            accept="image/*,application/pdf,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/zip,.rar,.eml,.msg">
                                        <input type="hidden" id="filelogo" value="">
                                    </div>
                                </div>
                            </div>
                            <span id="file_validation"></span>

                            <div class="mb-3">
                                <label for="status" class="form-label">Project Status</label>
                                <select class="form-select" id="status" name="status" required
                                    data-parsley-required-message="Project status is required.">
                                    <option value="0" {{ old('status', $projectData->status ?? '') == 0 ? 'selected' : '' }}
                                        class="text-danger">In Active</option>
                                    <option value="1" {{ old('status', $projectData->status ?? '') == 1 ? 'selected' : '' }}
                                        class="text-success">Active</option>
                                    <option value="2" {{ old('status', $projectData->status ?? '') == 2 ? 'selected' : '' }}
                                        class="text-warning">Hold</option>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit"
                                    class="btn btn-primary">{{ $projectData ? 'Update Project' : 'Create Project' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://parsleyjs.org/dist/parsley.min.js"></script> <!-- Parsley.js CDN -->
<script src="{{ asset('assets/js/dragdrop.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize Parsley validation
        $('#projectForm').parsley();
    
        // Combine click event for both #browse and .drag-area
        $('#browse, .drag-area').on('click', function(e) {
            e.stopPropagation();  // Prevent event bubbling
            $('#doc').trigger('click');  // Trigger file input click
        });
    
        // Handle file input change (file selected via browse)
        $('#doc').on('change', function() {
            handleFileUpload(this.files);
        });
    
      
    
        $('.drag-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('active');
            
            // Get dropped files
            var files = e.originalEvent.dataTransfer.files;
            handleFileUpload(files);
        });
    
        // Handle form submission
        $('#projectForm').on('submit', function(e) {
            e.preventDefault();
            if ($(this).parsley().isValid()) {
                this.submit(); // Submit the form normally
            }
        });
    });
    
    // Function to handle file upload and validation
    function handleFileUpload(files) {
        // Update the hidden file input with the dropped files
        $('#doc')[0].files = files;
    
        // Optionally display the selected file name or thumbnail
        if (files.length > 0) {
            let fileName = files[0].name;
            $('.area2').html(`<span>${fileName}</span>`); // Adjust this line to show the file name only
        }
    
        // Trigger Parsley validation on the file input
        $('#doc').parsley().validate();
    }
    </script>
    
@endsection