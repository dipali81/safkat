<!DOCTYPE html>
<html>
<head>
    <title>Project Status Changed</title>
    <style>
        /* Add custom styles for status colors */
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        .status-hold {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Project Status Updated</h1>

    @php
        $status = '';
        $statusClass = '';

        if ($project->status == 0) {
            $status = 'In Active';
            $statusClass = 'status-inactive';
        } elseif ($project->status == 1) {
            $status = 'Active';
            $statusClass = 'status-active';
        } else {
            $status = 'On Hold';
            $statusClass = 'status-hold';
        }
    @endphp

    <p>The status of the project "{{ $project->name }}" has been changed to 
        <span class="{{ $statusClass }}">{{ $status }}</span>.
    </p>
</body>
</html>
