@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-history mr-2"></i>System Logs</h4>
                        <div class="d-flex gap-2">
                            <select id="actionFilter" class="form-control form-control-sm" style="width: 120px;">
                                <option value="">All Actions</option>
                                <option value="Created">Created</option>
                                <option value="Updated">Updated</option>
                                <option value="Deleted">Deleted</option>
                                <option value="Accessed">Accessed</option>
                                <option value="Login">Login</option>
                            </select>
                            <select id="monthFilter" class="form-control form-control-sm" style="width: 120px;">
                                <option value="">All Months</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <input type="date" id="dateFilter" class="form-control form-control-sm" style="width: 140px;">
                            <button onclick="printLogs()" class="btn btn-light btn-sm">
                                <i class="fas fa-print mr-1" style="color: black;"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="border-0"><i class="fas fa-user mr-1"></i>User</th>
                                    <th class="border-0"><i class="fas fa-bolt mr-1"></i>Action</th>
                                    <th class="border-0"><i class="fas fa-clock mr-1"></i>Date/Time</th>
                                    <th class="border-0"><i class="fas fa-globe mr-1"></i>IP Address</th>
                                    <th class="border-0"><i class="fas fa-info-circle mr-1"></i>Details</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr class="log-row">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <span class="font-weight-bold">{{ $log->user->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @php
                                                $badgeClass = 'badge-primary';
                                                $icon = 'fas fa-cog';
                                                if(str_contains($log->action, 'Created')) { $badgeClass = 'badge-success'; $icon = 'fas fa-plus'; }
                                                elseif(str_contains($log->action, 'Updated')) { $badgeClass = 'badge-warning text-dark'; $icon = 'fas fa-edit'; }
                                                elseif(str_contains($log->action, 'Deleted')) { $badgeClass = 'badge-danger'; $icon = 'fas fa-trash'; }
                                                elseif(str_contains($log->action, 'Accessed')) { $badgeClass = 'badge-info'; $icon = 'fas fa-eye'; }
                                                elseif(str_contains($log->action, 'Login') || str_contains($log->action, 'Logged')) { $badgeClass = 'badge-dark'; $icon = 'fas fa-sign-in-alt'; }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                                <i class="{{ $icon }} mr-1"></i>{{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt mr-1"></i>{{ $log->created_at->format('M d, Y') }}<br>
                                                <i class="fas fa-clock mr-1"></i>{{ $log->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light">{{ $log->ip_address ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-outline-info btn-sm" onclick="showDetails({{ $log->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <div id="details-{{ $log->id }}" class="details-box mt-2" style="display:none;">
                                                <div class="alert alert-info mb-0">
                                                    <small>{{ $log->details }}</small>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>



<!-- Scripts -->
<script>
    function showDetails(logId) {
        const details = document.getElementById('details-' + logId);
        if (details.style.display === 'none') {
            details.style.display = 'block';
        } else {
            details.style.display = 'none';
        }
    }

    // Filter functionality
    function applyFilters() {
        const actionFilter = document.getElementById('actionFilter').value.toLowerCase();
        const monthFilter = document.getElementById('monthFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        const rows = document.querySelectorAll('.log-row');
        
        rows.forEach(row => {
            const actionCell = row.querySelector('td:nth-child(2)');
            const dateCell = row.querySelector('td:nth-child(3)');
            const actionText = actionCell.textContent.toLowerCase();
            const dateText = dateCell.textContent;
            
            let showRow = true;
            
            // Action filter
            if (actionFilter && !actionText.includes(actionFilter)) {
                showRow = false;
            }
            
            // Month filter
            if (monthFilter && showRow) {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const selectedMonth = monthNames[parseInt(monthFilter) - 1];
                if (!dateText.includes(selectedMonth)) {
                    showRow = false;
                }
            }
            
            // Date filter
            if (dateFilter && showRow) {
                const filterDate = new Date(dateFilter);
                const logDateMatch = dateText.match(/(\w{3}) (\d{1,2}), (\d{4})/);
                if (logDateMatch) {
                    const logDate = new Date(logDateMatch[3], 
                        ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].indexOf(logDateMatch[1]), 
                        parseInt(logDateMatch[2]));
                    if (logDate.toDateString() !== filterDate.toDateString()) {
                        showRow = false;
                    }
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    document.getElementById('actionFilter').addEventListener('change', applyFilters);
    document.getElementById('monthFilter').addEventListener('change', applyFilters);
    document.getElementById('dateFilter').addEventListener('change', applyFilters);

    // Print functionality
    function printLogs() {
        const printWindow = window.open('', '_blank');
        const visibleRows = Array.from(document.querySelectorAll('.log-row')).filter(row => row.style.display !== 'none');
        
        let printContent = `
            <html>
            <head>
                <title>System Logs Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .header { text-align: center; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Admin/Pastor Activity Logs Report</h2>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Date/Time</th>
                            <th>IP Address</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        visibleRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const user = cells[0].querySelector('span').textContent;
            const action = cells[1].querySelector('.badge').textContent.trim();
            const datetime = cells[2].textContent.trim();
            const ip = cells[3].textContent.trim();
            const details = cells[4].querySelector('.alert') ? cells[4].querySelector('.alert').textContent.trim() : 'Click to view';
            
            printContent += `
                <tr>
                    <td>${user}</td>
                    <td>${action}</td>
                    <td>${datetime}</td>
                    <td>${ip}</td>
                    <td>${details}</td>
                </tr>`;
        });
        
        printContent += `
                    </tbody>
                </table>
            </body>
            </html>`;
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.log-row').forEach(row => {
            row.classList.add('fade-in');
        });
    });
</script>

<!-- Enhanced CSS -->
<style>
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.8s ease-in forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white !important;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .table tbody tr {
        border-bottom: 1px solid #b1b1b1;
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: #424242;
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .avatar-sm {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .badge {
        font-size: 18px;
        font-weight: 500;
        border-radius: 10px;
        color: rgb(27, 27, 27) !important;
    }

    .badge.text-dark {
        color: #0c0c0c !important;
    }

    .btn {
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .d-flex.gap-2 > * {
        margin-right: 8px;
    }
    
    .d-flex.gap-2 > *:last-child {
        margin-right: 0;
    }

    .details-box {
        max-width: 300px;
        word-wrap: break-word;
    }

    .card-header h4 {
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        color: white !important;
    }

    .card-header {
        color: white !important;
    }

    .card-header i {
        color: white !important;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    
    .btn .fas.fa-print {
        color: #000000 !important;
    }
</style>
@endsection