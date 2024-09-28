const dropArea = document.querySelector(".drag-area");
const dropArea2 = document.querySelector(".area2");
const button = document.getElementById('browse');
const input = document.getElementById('doc');
const fileFormateError = document.getElementById('file_validation');
let file;
let isUploading = false; // Flag to track if a file is being uploaded

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop area when item is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

// Unhighlight on leave/drop
['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropArea.addEventListener('drop', handleDrop, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropArea.classList.add('active');
}

function unhighlight(e) {
    dropArea.classList.remove('active');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

dropArea.onclick = () => {
    if (!isUploading) {
        input.value = ""; // Reset input value to ensure the change event triggers
        input.click();
        fileFormateError.innerHTML = "";
    }
};

input.addEventListener("change", function(e) {
    const newFile = e.target.files[0]; // Get the newly selected file
    if (newFile && newFile !== file) { // Only handle if it's a new file
        file = newFile;
        handleFiles([file]);
    }
});

function handleFiles(files) {
    if (files.length > 0) {
        file = files[0];
        uploadFile(file);
    }
}

function uploadFile(file) {
    isUploading = true; // Set flag to true when uploading
    fileFormateError.innerHTML = "";
    let validExtensions = [
        "application/pdf",
        "image/jpeg",  // JPEG images
        "image/png",   // PNG images
        "image/gif",   // GIF images
        "image/webp"   // WebP images
    ];

    if (validExtensions.includes(file.type)) {
        if (file.size <= 4000000) { // 4MB limit
            let reader = new FileReader();
            reader.onload = function(e) {
                let fileIcon = getFileIcon(file.type);
                let filePreview = `
                    <div class="file-preview">
                        ${fileIcon}
                        <span>${file.name}</span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="cancelUpload()">Cancel</button>
                    </div>
                `;
                dropArea.style.display = "none";
                dropArea2.innerHTML = filePreview;
                isUploading = false; // Reset flag after upload is complete
            }
            reader.readAsDataURL(file);
        } else {
            fileFormateError.innerHTML = "<p style='color:red'>File size exceeds 4MB limit.</p>";
            resetUpload();
        }
    } else {
        fileFormateError.innerHTML = "<p style='color:red'>Invalid file format.</p>";
        resetUpload();
    }
}

function getFileIcon(fileType) {
    // You can expand this function to return different icons based on file type
    return '<i class="fas fa-file" style="font-size: 2em; margin-right: 10px;"></i>';
}

window.cancelUpload = function() {
    resetUpload();
}

function resetUpload() {
    input.value = null;
    dropArea.classList.remove("active");
    dropArea2.innerHTML = '';
    dropArea.style.display = "block";
    isUploading = false; // Reset the flag when upload is canceled
    file = null; // Clear the file reference
}
