<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple Image Upload & CRUD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <!-- Toastify.js CDN for styling and functionality -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- SweetAlert2 CDN for the delete confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Custom Styles */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .delete-image {
            background-color: red;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .update-image {
            background-color: #f0ad4e;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }



        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            transition: background-color 0.3s ease;
        }

        .dropzone:hover {
            background-color: #f1f1f1;
        }

        .card-body {
            padding: 10px;
        }

        .toastify {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Multiple Image Upload & CRUD</h2>

        <!-- Image Upload Form -->
        <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="dropzone"
            id="imageUpload">
            @csrf
        </form>

        <!-- Uploaded Images -->
        <h3 class="text-center mt-5">Uploaded Images</h3>
        <div class="mt-3">
            <div class="row d-flex flex-wrap justify-content-start" id="imageGallery">
                @foreach ($images as $image)
                    <div class="col-md-3 col-sm-4 col-6 mb-3 d-flex justify-content-center">
                        <div class="card" id="imageCard_{{ $image->id }}">
                            <img src="{{ asset('uploads/' . $image->image) }}" class="card-img-top img-fluid"
                                alt="Image" style="width: 100%; height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <button class="btn btn-warning btn-sm update-image" data-id="{{ $image->id }}"
                                    data-image="{{ asset('uploads/' . $image->image) }}" data-toggle="modal"
                                    data-target="#updateImageModal">Update</button>
                                <button class="btn btn-danger btn-sm delete-image"
                                    data-id="{{ $image->id }}">Delete</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Update Image Modal -->
    <div class="modal fade" id="updateImageModal" tabindex="-1" role="dialog" aria-labelledby="updateImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateImageModalLabel">Update Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateImageForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="image">Choose New Image</label>
                            <input type="file" class="form-control-file" name="image" id="newImage" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dropzone Options for Image Upload
        Dropzone.options.imageUpload = {
            paramName: "images",
            acceptedFiles: "image/*",
            maxFilesize: 2, // MB
            uploadMultiple: true,
            parallelUploads: 5,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                // Show success toast when image is uploaded
                Toastify({
                    text: "Image uploaded successfully!",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4caf50",
                    stopOnFocus: true
                }).showToast();

                location.reload();
            }
        };

        // Delete Image with SweetAlert Confirmation
        $(document).on("click", ".delete-image", function() {
            let imageId = $(this).data("id");
            let element = $(this).closest('.card');

            // Show SweetAlert Confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '/images/' + imageId,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {

                            $('#imageCard_' + imageId).remove();

                            // Show success toast after deletion
                            Toastify({
                                text: "Image deleted successfully!",
                                duration: 3000,
                                close: true,
                                gravity: "top", // top or bottom
                                position: "right", // left or right
                                backgroundColor: "#4caf50", // success color
                                stopOnFocus: true
                            }).showToast();
                        },
                        error: function() {

                            Toastify({
                                text: "Error deleting image.",
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#f44336",
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                }
            });
        });

        // Set the image data in the modal when clicking the update button
        $(document).on('click', '.update-image', function() {
            let imageId = $(this).data('id');
            let currentImage = $(this).data('image');
            $('#updateImageForm').attr('action', '/images/' + imageId); // Set the form action
        });

        // Update Image via AJAX
        $('#updateImageForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success toast after image update
                    Toastify({
                        text: "Image updated successfully!",
                        duration: 3000,
                        close: true,
                        gravity: "top", // top or bottom
                        position: "right", // left or right
                        backgroundColor: "#4caf50", // success color
                        stopOnFocus: true
                    }).showToast();
                    // Close the modal and reload page after successful update
                    $('#updateImageModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    // Show error toast if update fails
                    Toastify({
                        text: "Error updating image.",
                        duration: 3000,
                        close: true,
                        gravity: "top", // top or bottom
                        position: "right", // left or right
                        backgroundColor: "#f44336", // error color
                        stopOnFocus: true
                    }).showToast();
                }
            });
        });
    </script>
</body>

</html>
