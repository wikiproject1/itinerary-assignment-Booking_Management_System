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
    <title>Reports - Travel Management System</title>
    
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
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-body {
                padding: 0 !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Reports Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">REPORTS</h5>
                    <div class="d-flex gap-2 no-print">
                        <button type="button" class="btn btn-primary" onclick="generateReport()">
                            <i class="bi bi-file-earmark-text"></i> Generate Report
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportToXML()">
                            <i class="bi bi-file-earmark-code"></i> Export XML
                        </button>
                        <button type="button" class="btn btn-info" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Filters -->
                    <div class="row mb-4 no-print">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="itineraries">Itineraries Report</option>
                                <option value="guides">Guides Report</option>
                                <option value="assignments">Assignments Report</option>
                                <option value="financial">Financial Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 custom-date-range" style="display: none;">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-3 custom-date-range" style="display: none;">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>

                    <!-- Report Content -->
                    <div id="reportContent">
                        <div class="print-only text-center mb-4">
                            <h2>Travel Management System</h2>
                            <h4 id="reportTitle">Report</h4>
                            <p id="reportDateRange">Date Range: </p>
                        </div>
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <!-- Table headers will be loaded dynamically -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Table data will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with empty data
            const reportTable = $('#reportTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                order: [[0, 'asc']]
            });

            // Handle date range selection
            $('#dateRange').change(function() {
                if ($(this).val() === 'custom') {
                    $('.custom-date-range').show();
                } else {
                    $('.custom-date-range').hide();
                }
            });

            // Generate initial report
            generateReport();
        });

        function generateReport() {
            const reportType = $('#reportType').val();
            const dateRange = $('#dateRange').val();
            let startDate = '';
            let endDate = '';

            if (dateRange === 'custom') {
                startDate = $('#startDate').val();
                endDate = $('#endDate').val();
            }

            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we generate your report',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'api/reports.php',
                type: 'GET',
                data: {
                    type: reportType,
                    date_range: dateRange,
                    start_date: startDate,
                    end_date: endDate
                },
                dataType: 'json',
                success: function(data) {
                    Swal.close();
                    
                    if (!data.success) {
                        showAlert('error', data.message || 'Failed to generate report');
                        return;
                    }

                    // Update report title and date range
                    $('#reportTitle').text(data.title);
                    $('#reportDateRange').text('Date Range: ' + data.date_range);

                    // Destroy existing DataTable
                    const table = $('#reportTable').DataTable();
                    table.destroy();

                    // Reinitialize DataTable with new data
                    $('#reportTable').DataTable({
                        data: data.data,
                        columns: data.columns,
                        responsive: true,
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        pageLength: 25,
                        order: [[0, 'asc']]
                    });
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    showAlert('error', 'Failed to generate report: ' + error);
                }
            });
        }

        function exportToXML() {
            const reportType = $('#reportType').val();
            const dateRange = $('#dateRange').val();
            let startDate = '';
            let endDate = '';

            if (dateRange === 'custom') {
                startDate = $('#startDate').val();
                endDate = $('#endDate').val();
            }

            // Show loading state
            Swal.fire({
                title: 'Exporting...',
                text: 'Please wait while we prepare your XML file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create a form and submit it to download the XML file
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'api/export_xml.php';
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.value = reportType;
            form.appendChild(typeInput);

            const rangeInput = document.createElement('input');
            rangeInput.type = 'hidden';
            rangeInput.name = 'date_range';
            rangeInput.value = dateRange;
            form.appendChild(rangeInput);

            if (startDate) {
                const startInput = document.createElement('input');
                startInput.type = 'hidden';
                startInput.name = 'start_date';
                startInput.value = startDate;
                form.appendChild(startInput);
            }

            if (endDate) {
                const endInput = document.createElement('input');
                endInput.type = 'hidden';
                endInput.name = 'end_date';
                endInput.value = endDate;
                form.appendChild(endInput);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Close loading state after a short delay
            setTimeout(() => {
                Swal.close();
            }, 1000);
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