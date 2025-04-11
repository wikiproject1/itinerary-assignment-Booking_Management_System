<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

define('BASEPATH', true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guides - Travel Management System</title>
    
    <?php include 'includes/site_icons.php'; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php include 'includes/styles.php'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Guides Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">GUIDES MANAGEMENT</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuideModal">
                        <i class="bi bi-plus-lg"></i> Add New Guide
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="guidesTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Car Plate Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Guide data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guide Assignments Section -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">GUIDE ASSIGNMENTS</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                        <i class="bi bi-plus-lg"></i> Add New Assignment
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="assignmentsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Guide Name</th>
                                    <th>Itinerary Group</th>
                                    <th>Assignment Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Assignment data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Guide Modal -->
    <div class="modal fade" id="addGuideModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addGuideForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Car Plate Number</label>
                                <input type="text" class="form-control" name="car_plate_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveGuide()">Save Guide</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Assignment Modal -->
    <div class="modal fade" id="addAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addAssignmentForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guide</label>
                                <select class="form-select" name="guide_id" required>
                                    <!-- Guide options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Itinerary</label>
                                <select class="form-select" name="itinerary_id" required>
                                    <!-- Itinerary options will be loaded here -->
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assignment Date</label>
                                <input type="date" class="form-control" name="assignment_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="assigned">Assigned</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAssignment()">Save Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#guidesTable').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });

            $('#assignmentsTable').DataTable({
                responsive: true,
                order: [[2, 'desc']]
            });

            // Load initial data
            loadGuides();
            loadAssignments();
            loadItineraries();

            // Reset forms when modals are closed
            $('#addGuideModal').on('hidden.bs.modal', function() {
                const form = document.getElementById('addGuideForm');
                form.reset();
                $(form).data('edit-id', '');
                $(this).find('.modal-title').text('Add New Guide');
            });

            $('#addAssignmentModal').on('hidden.bs.modal', function() {
                const form = document.getElementById('addAssignmentForm');
                form.reset();
                $(form).data('edit-id', '');
                $(this).find('.modal-title').text('Add New Assignment');
            });
        });

        function loadGuides() {
            $.ajax({
                url: 'api/guides.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const table = $('#guidesTable').DataTable();
                    table.clear();
                    
                    data.forEach(guide => {
                        table.row.add([
                            guide.first_name,
                            guide.last_name,
                            guide.phone_number || '-',
                            guide.email || '-',
                            guide.car_plate_number || '-',
                            formatGuideStatus(guide.status),
                            `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" onclick="editGuide(${guide.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteGuide(${guide.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `
                        ]);
                    });
                    
                    table.draw();
                    updateGuideDropdowns(data);
                },
                error: function() {
                    showAlert('danger', 'Failed to load guides');
                }
            });
        }

        function loadAssignments() {
            $.ajax({
                url: 'api/guide_assignments.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const table = $('#assignmentsTable').DataTable();
                    table.clear();
                    
                    data.forEach(assignment => {
                        table.row.add([
                            `${assignment.guide_first_name} ${assignment.guide_last_name}`,
                            assignment.itinerary_group_name,
                            formatDate(assignment.assignment_date),
                            formatAssignmentStatus(assignment.status),
                            assignment.notes || '-',
                            `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" onclick="editAssignment(${assignment.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteAssignment(${assignment.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `
                        ]);
                    });
                    
                    table.draw();
                },
                error: function() {
                    showAlert('danger', 'Failed to load assignments');
                }
            });
        }

        function formatGuideStatus(status) {
            const statusClass = status === 'active' ? 'success' : 'secondary';
            return `<span class="badge bg-${statusClass}">${status}</span>`;
        }

        function formatAssignmentStatus(status) {
            const statusClasses = {
                'assigned': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return `<span class="badge bg-${statusClasses[status]}">${status}</span>`;
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString();
        }

        function updateGuideDropdowns(guides) {
            const guideSelects = $('select[name="guide_id"]');
            guideSelects.empty();
            guideSelects.append('<option value="">Select Guide</option>');
            
            guides.forEach(guide => {
                if (guide.status === 'active') {
                    guideSelects.append(`<option value="${guide.id}">${guide.first_name} ${guide.last_name}</option>`);
                }
            });
        }

        function loadItineraries() {
            $.ajax({
                url: 'api/itineraries.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const itinerarySelects = $('select[name="itinerary_id"]');
                    itinerarySelects.empty();
                    itinerarySelects.append('<option value="">Select Itinerary</option>');
                    
                    data.forEach(itinerary => {
                        itinerarySelects.append(`<option value="${itinerary.id}">${itinerary.group_name} - ${itinerary.month}</option>`);
                    });
                }
            });
        }

        function saveGuide() {
            const form = document.getElementById('addGuideForm');
            const editId = $(form).data('edit-id');
            const formData = new FormData(form);
            const data = {};
            
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            if (editId) {
                data.id = editId;
            }
            
            // Show loading state
            const saveButton = $('#addGuideModal .btn-primary');
            const originalText = saveButton.html();
            saveButton.prop('disabled', true);
            saveButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            $.ajax({
                url: 'api/guides.php',
                type: editId ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.success) {
                        $('#addGuideModal').modal('hide');
                        showAlert('success', response.message);
                        loadGuides();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to save guide';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {}
                    showAlert('error', message);
                },
                complete: function() {
                    saveButton.prop('disabled', false);
                    saveButton.html(originalText);
                }
            });
        }

        function saveAssignment() {
            const form = document.getElementById('addAssignmentForm');
            const editId = $(form).data('edit-id');
            const formData = new FormData(form);
            const data = {};
            
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            if (editId) {
                data.id = editId;
            }
            
            // Show loading state
            const saveButton = $('#addAssignmentModal .btn-primary');
            const originalText = saveButton.html();
            saveButton.prop('disabled', true);
            saveButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            $.ajax({
                url: 'api/guide_assignments.php',
                type: editId ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.success) {
                        $('#addAssignmentModal').modal('hide');
                        showAlert('success', response.message);
                        loadAssignments();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to save assignment';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {}
                    showAlert('error', message);
                },
                complete: function() {
                    saveButton.prop('disabled', false);
                    saveButton.html(originalText);
                }
            });
        }

        function editGuide(id) {
            // Show loading state
            const modal = $('#addGuideModal');
            const form = document.getElementById('addGuideForm');
            const saveButton = modal.find('.btn-primary');
            saveButton.prop('disabled', true);
            saveButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Fetch guide data
            $.ajax({
                url: 'api/guides.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(guide) {
                    // Reset form and set edit mode
                    form.reset();
                    $(form).data('edit-id', id);
                    
                    // Set form values
                    form.elements.first_name.value = guide.first_name;
                    form.elements.last_name.value = guide.last_name;
                    form.elements.phone_number.value = guide.phone_number || '';
                    form.elements.email.value = guide.email || '';
                    form.elements.car_plate_number.value = guide.car_plate_number || '';
                    form.elements.status.value = guide.status;
                    
                    // Update modal title
                    modal.find('.modal-title').text('Edit Guide');
                    
                    // Show modal
                    modal.modal('show');
                    
                    // Reset save button
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');
                },
                error: function() {
                    showAlert('error', 'Failed to load guide data');
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');
                }
            });
        }

        function editAssignment(id) {
            // Show loading state
            const modal = $('#addAssignmentModal');
            const form = document.getElementById('addAssignmentForm');
            const saveButton = modal.find('.btn-primary');
            saveButton.prop('disabled', true);
            saveButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Fetch assignment data
            $.ajax({
                url: 'api/guide_assignments.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(assignment) {
                    // Reset form and set edit mode
                    form.reset();
                    $(form).data('edit-id', id);
                    
                    // Set form values
                    form.elements.guide_id.value = assignment.guide_id;
                    form.elements.itinerary_id.value = assignment.itinerary_id;
                    form.elements.assignment_date.value = assignment.assignment_date;
                    form.elements.status.value = assignment.status;
                    form.elements.notes.value = assignment.notes || '';
                    
                    // Update modal title
                    modal.find('.modal-title').text('Edit Assignment');
                    
                    // Show modal
                    modal.modal('show');
                    
                    // Reset save button
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');
                },
                error: function() {
                    showAlert('error', 'Failed to load assignment data');
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');
                }
            });
        }

        function deleteGuide(id) {
            Swal.fire({
                title: 'Delete Guide?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/guides.php?id=' + id,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                loadGuides();
                            } else {
                                showAlert('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to delete guide';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                message = response.message || message;
                            } catch (e) {}
                            showAlert('error', message);
                        }
                    });
                }
            });
        }

        function deleteAssignment(id) {
            Swal.fire({
                title: 'Delete Assignment?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/guide_assignments.php?id=' + id,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                loadAssignments();
                            } else {
                                showAlert('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to delete assignment';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                message = response.message || message;
                            } catch (e) {}
                            showAlert('error', message);
                        }
                    });
                }
            });
        }

        // Show success/error alerts
        function showAlert(type, message) {
            Swal.fire({
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                icon: type,
                timer: 2000,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html> 