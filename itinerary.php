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
    <title>Itineraries - Travel Management System</title>
    
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
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php include 'includes/styles.php'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Filters Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <select class="form-select" id="monthFilter">
                                <option value="">All Months</option>
                                <option value="JANUARY">JANUARY</option>
                                <option value="FEBRUARY">FEBRUARY</option>
                                <option value="MARCH">MARCH</option>
                                <option value="APRIL">APRIL</option>
                                <option value="MAY">MAY</option>
                                <option value="JUNE">JUNE</option>
                                <option value="JULY">JULY</option>
                                <option value="AUGUST">AUGUST</option>
                                <option value="SEPTEMBER">SEPTEMBER</option>
                                <option value="OCTOBER">OCTOBER</option>
                                <option value="NOVEMBER">NOVEMBER</option>
                                <option value="DECEMBER">DECEMBER</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="Safari">Safari</option>
                                <option value="Kilimanjaro Climbing">Kilimanjaro Climbing</option>
                                <option value="Day Trip">Day Trip</option>
                                <option value="Zanzibar">Zanzibar</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="groupNameFilter" placeholder="Search by group name">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary me-2" onclick="applyFilters()">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itinerary Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">ITINERARY TABLE</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItineraryModal">
                        <i class="bi bi-plus-lg"></i> Add New Itinerary
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="itineraryTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Group Name</th>
                                    <th>Starting Location</th>
                                    <th>Final Destination</th>
                                    <th>Arrival Time</th>
                                    <th>Departure Time</th>
                                    <th>Trip Types</th>
                                    <th>Completion Status</th>
                                    <th>Total Amount</th>
                                    <th>Deposit Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Notes</th>
                                    <th>Safari Days</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Itinerary data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Itinerary Modal -->
    <div class="modal fade" id="addItineraryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Itinerary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItineraryForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Month</label>
                                <select class="form-select" name="month" required>
                                    <option value="">Select Month</option>
                                    <option value="JANUARY">JANUARY</option>
                                    <option value="FEBRUARY">FEBRUARY</option>
                                    <option value="MARCH">MARCH</option>
                                    <option value="APRIL">APRIL</option>
                                    <option value="MAY">MAY</option>
                                    <option value="JUNE">JUNE</option>
                                    <option value="JULY">JULY</option>
                                    <option value="AUGUST">AUGUST</option>
                                    <option value="SEPTEMBER">SEPTEMBER</option>
                                    <option value="OCTOBER">OCTOBER</option>
                                    <option value="NOVEMBER">NOVEMBER</option>
                                    <option value="DECEMBER">DECEMBER</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Group Name</label>
                                <input type="text" class="form-control" name="group_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Starting Location</label>
                                <input type="text" class="form-control" name="starting_location" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Final Destination</label>
                                <input type="text" class="form-control" name="final_destination" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Arrival Time</label>
                                <input type="datetime-local" class="form-control" name="arrival_time" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departure Time</label>
                                <input type="datetime-local" class="form-control" name="departure_time" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Safari">
                                    <label class="form-check-label">Safari</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Kilimanjaro Climbing">
                                    <label class="form-check-label">Kilimanjaro Climbing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Day Trip">
                                    <label class="form-check-label">Day Trip</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Zanzibar">
                                    <label class="form-check-label">Zanzibar</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="total_amount" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Deposit Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" placeholder="Put 0 If Not Deposit" name="deposit_amount" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Remaining Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="remaining_amount" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Safari Days</label>
                                <input type="text" class="form-control" name="safari_days" placeholder="e.g., 5,6,7,8 Safari & 8 Drop OFF">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Itinerary Status</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="itinerary_status" value="Pending" checked>
                                        <label class="form-check-label d-flex align-items-center gap-2">
                                            <i class="bi bi-clock-fill text-warning"></i>
                                            <span class="badge bg-warning">Pending</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="itinerary_status" value="Completed">
                                        <label class="form-check-label d-flex align-items-center gap-2">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span class="badge bg-success">Completed</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveItinerary()">Save Itinerary</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#itineraryTable').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                scrollX: true,
                destroy: true,
                columnDefs: [
                    {
                        targets: 0,
                        type: 'month',
                        render: function(data, type, row) {
                            if (type === 'sort') {
                                const months = {
                                    'JANUARY': '01', 'FEBRUARY': '02', 'MARCH': '03',
                                    'APRIL': '04', 'MAY': '05', 'JUNE': '06',
                                    'JULY': '07', 'AUGUST': '08', 'SEPTEMBER': '09',
                                    'OCTOBER': '10', 'NOVEMBER': '11', 'DECEMBER': '12'
                                };
                                return months[data] || '00';
                            }
                            return data;
                        }
                    }
                ]
            });

            // Load itineraries
            loadItineraries();

            // Calculate remaining amount automatically
            $('input[name="total_amount"], input[name="deposit_amount"]').on('input', function() {
                const total = parseFloat($('input[name="total_amount"]').val()) || 0;
                const deposit = parseFloat($('input[name="deposit_amount"]').val()) || 0;
                $('input[name="remaining_amount"]').val((total - deposit).toFixed(2));
            });
        });

        function loadItineraries() {
            $.ajax({
                url: 'api/itineraries.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const table = $('#itineraryTable').DataTable();
                    table.clear();
                    
                    data.forEach(itinerary => {
                        table.row.add([
                            itinerary.month,
                            itinerary.group_name,
                            itinerary.starting_location,
                            itinerary.final_destination,
                            formatDateTime(itinerary.arrival_time),
                            formatDateTime(itinerary.departure_time),
                            formatStatus(itinerary.status),
                            formatItineraryStatus(itinerary.completion_status || 'Pending'),
                            `$${parseFloat(itinerary.total_amount).toFixed(2)}`,
                            `$${parseFloat(itinerary.deposit_amount).toFixed(2)}`,
                            `$${parseFloat(itinerary.remaining_amount).toFixed(2)}`,
                            itinerary.notes || '-',
                            itinerary.safari_days || '-',
                            `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" onclick="editItinerary(${itinerary.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItinerary(${itinerary.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `
                        ]);
                    });
                    
                    table.draw();
                },
                error: function(xhr) {
                    showAlert('danger', 'Failed to load itineraries');
                }
            });
        }

        function formatDateTime(dateTime) {
            if (!dateTime) return '-';
            const dt = new Date(dateTime);
            return dt.toLocaleString();
        }

        function formatStatus(status) {
            if (!status) return '-';
            const statusArray = status.split(',');
            return statusArray.map(s => `<span class="badge bg-primary">${s.trim()}</span>`).join(' ');
        }

        function formatItineraryStatus(status) {
            if (!status) return '-';
            const statusClass = status === 'Completed' ? 'completed' : 'pending';
            const icon = status === 'Completed' ? 'bi-check-circle-fill' : 'bi-clock-fill';
            return `
                <div class="status-badge ${statusClass}">
                    <i class="bi ${icon}"></i>
                    <span>${status}</span>
                </div>
            `;
        }

        function saveItinerary() {
            const form = document.getElementById('addItineraryForm');
            const editId = $(form).data('edit-id');
            
            // Get all form elements
            const formElements = form.elements;
            const data = {};
            
            // Loop through all form elements
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                
                // Skip buttons and file inputs
                if (element.type === 'button' || element.type === 'submit' || element.type === 'file') {
                    continue;
                }
                
                // Handle checkboxes for trip types
                if (element.type === 'checkbox') {
                    if (element.checked) {
                        if (!data.status) {
                            data.status = [];
                        }
                        data.status.push(element.value);
                    }
                }
                // Handle radio buttons for completion status
                else if (element.type === 'radio' && element.checked) {
                    if (element.name === 'itinerary_status') {
                        data.completion_status = element.value;
                    }
                }
                // Handle datetime inputs
                else if (element.type === 'datetime-local') {
                    data[element.name] = element.value.replace('T', ' ');
                }
                // Handle other inputs
                else if (element.name && element.name !== 'itinerary_status') {
                    data[element.name] = element.value;
                }
            }
            
            // Add edit ID if in edit mode
            if (editId) {
                data.id = editId;
            }
            
            // Validate required fields
            const requiredFields = ['month', 'group_name', 'starting_location', 'final_destination', 
                                  'arrival_time', 'departure_time', 'total_amount'];
            for (const field of requiredFields) {
                if (!data[field]) {
                    showAlert('danger', `Please fill in the ${field.replace('_', ' ')} field`);
                    return;
                }
            }
            
            // Validate status
            if (!data.status || data.status.length === 0) {
                showAlert('danger', 'Please select at least one trip type');
                return;
            }
            
            // Convert status array to comma-separated string
            data.status = data.status.join(', ');
            
            // Calculate remaining amount
            const totalAmount = parseFloat(data.total_amount) || 0;
            const depositAmount = parseFloat(data.deposit_amount) || 0;
            data.remaining_amount = (totalAmount - depositAmount).toFixed(2);
            
            // If no deposit amount, set it to 0
            if (!data.deposit_amount) {
                data.deposit_amount = '0';
            }

            // Debug: Log the data being sent
            console.log('Sending data:', data);
            
            // Show loading state
            const saveButton = document.querySelector('#addItineraryModal .btn-primary');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            saveButton.disabled = true;
            
            // Send data to API
            $.ajax({
                url: 'api/itineraries.php',
                type: editId ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    console.log('API Response:', response);
                    if (response.success) {
                        $('#addItineraryModal').modal('hide');
                        form.reset();
                        $(form).data('edit-id', '');
                        loadItineraries();
                        showAlert('success', editId ? 'Itinerary updated successfully!' : 'Itinerary saved successfully!');
                    } else {
                        showAlert('danger', response.message || 'Error saving itinerary');
                    }
                },
                error: function(xhr) {
                    console.error('API Error:', xhr.responseText);
                    let errorMessage = 'Error saving itinerary. Please try again.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || response.error || errorMessage;
                        } catch (e) {
                            errorMessage = xhr.responseText;
                        }
                    }
                    
                    showAlert('danger', errorMessage);
                },
                complete: function() {
                    // Reset button state
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                }
            });
        }

        function applyFilters() {
            const month = $('#monthFilter').val();
            const status = $('#statusFilter').val();
            const groupName = $('#groupNameFilter').val();

            const table = $('#itineraryTable').DataTable();
            
            // Clear existing filters
            table.search('').columns().search('').draw();

            // Apply new filters
            if (month) table.column(0).search(month);
            if (status) table.column(6).search(status);
            if (groupName) table.column(1).search(groupName);

            table.draw();
        }

        function resetFilters() {
            $('#monthFilter').val('');
            $('#statusFilter').val('');
            $('#groupNameFilter').val('');
            
            const table = $('#itineraryTable').DataTable();
            table.search('').columns().search('').draw();
        }

        function editItinerary(id) {
            // Show loading state
            const modal = $('#addItineraryModal');
            const form = document.getElementById('addItineraryForm');
            const saveButton = modal.find('.btn-primary');
            saveButton.prop('disabled', true);
            saveButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Fetch itinerary data
            $.ajax({
                url: 'api/itineraries.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(itinerary) {
                    // Reset form and set edit mode
                    form.reset();
                    $(form).data('edit-id', id);
                    
                    // Format datetime strings for input fields
                    const arrivalTime = itinerary.arrival_time.replace(' ', 'T');
                    const departureTime = itinerary.departure_time.replace(' ', 'T');
                    
                    // Set form values
                    form.elements.month.value = itinerary.month;
                    form.elements.group_name.value = itinerary.group_name;
                    form.elements.starting_location.value = itinerary.starting_location;
                    form.elements.final_destination.value = itinerary.final_destination;
                    form.elements.arrival_time.value = arrivalTime;
                    form.elements.departure_time.value = departureTime;
                    form.elements.total_amount.value = itinerary.total_amount;
                    form.elements.deposit_amount.value = itinerary.deposit_amount;
                    form.elements.remaining_amount.value = itinerary.remaining_amount;
                    form.elements.notes.value = itinerary.notes || '';
                    form.elements.safari_days.value = itinerary.safari_days || '';
                    
                    // Set status checkboxes
                    const statusArray = itinerary.status.split(',').map(s => s.trim());
                    const statusCheckboxes = form.querySelectorAll('input[name="status[]"]');
                    statusCheckboxes.forEach(checkbox => {
                        checkbox.checked = statusArray.includes(checkbox.value);
                    });
                    
                    // Set completion status radio buttons
                    const currentStatus = itinerary.completion_status || 'Pending';
                    const statusRadios = form.querySelectorAll('input[name="itinerary_status"]');
                    statusRadios.forEach(radio => {
                        if (radio.value === currentStatus) {
                            radio.checked = true;
                        }
                    });
                    
                    // Update modal title
                    modal.find('.modal-title').text('Edit Itinerary');
                    
                    // Show modal
                    modal.modal('show');
                    
                    // Reset save button
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');

                    // Debug log
                    console.log('Loaded itinerary data:', itinerary);
                },
                error: function(xhr) {
                    showAlert('danger', 'Failed to load itinerary data');
                    console.error('Error loading itinerary:', xhr.responseText);
                    
                    // Reset save button
                    saveButton.prop('disabled', false);
                    saveButton.html('Save Changes');
                }
            });
        }

        // Add event listener for modal close to reset form
        $('#addItineraryModal').on('hidden.bs.modal', function () {
            const form = document.getElementById('addItineraryForm');
            form.reset();
            $(form).data('edit-id', '');
            $(this).find('.modal-title').text('Add New Itinerary');
        });

        function deleteItinerary(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                customClass: {
                    popup: 'animated bounceIn',
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `api/itineraries.php?id=${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The itinerary has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'animated bounceIn'
                                }
                            });
                            loadItineraries();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to delete itinerary. Please try again.',
                                icon: 'error',
                                customClass: {
                                    popup: 'animated bounceIn'
                                }
                            });
                        }
                    });
                }
            });
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Remove any existing alerts
            $('.alert').remove();
            
            // Add new alert
            $('.container-fluid').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    </script>

    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .status-badge.completed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-badge i {
            font-size: 1.1rem;
        }
        
        .status-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</body>
</html> 