<div class="container mt-4">
    <h2>Dashboard</h2>
    <hr>

    @php
        // Filter Role Akses
        $allowedRoles = ['super_admin', 'hr_manager', 'hr_generalist'];
        $userRole = Auth::user()->role; 
    @endphp

    @if(in_array($userRole, $allowedRoles))
        <div class="card mb-4 shadow-sm border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-success font-weight-bold">
                            <i class="fas fa-user-shield"></i> Admin Panel
                        </h5>
                        <p class="card-text text-muted" data-id="Unduh laporan lengkap data karyawan dalam format Excel." data-en="Download full employee data report in Excel format.">
                            Unduh laporan lengkap data karyawan dalam format Excel.
                        </p>
                    </div>
                    <a href="{{ route('employee.export') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-file-excel"></i> 
                        <span data-id="Export ke Excel" data-en="Export to Excel">Export ke Excel</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    </div>