<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval PDM - HR Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .table-container { overflow-x: auto; }
        .diff-changed { color: #dc3545; font-weight: bold; background-color: #fff5f5; }
        .modal-xl { max-width: 90%; }
        .btn-action { min-width: 100px; }
    </style>
</head>
<body>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="fw-bold text-primary mb-0">
                        <i class="fa-solid fa-user-check me-2"></i> Persetujuan Perubahan Data Mandiri (PDM)
                    </h4>
                    <span class="badge bg-primary px-3 py-2 fs-6">{{ count($approvals) }} Permintaan Pending</span>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-container">
                    <table id="pdmTable" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Karyawan</th>
                                <th>Tgl Pengajuan</th>
                                <th>Info Singkat</th>
                                <th>Dokumen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvals as $row)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $row->employee_name }}</span><br>
                                    <small class="text-muted">{{ $row->employee_no }}</small>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }} WIB</small>
                                </td>
                                <td>
                                    <small>
                                        <strong>Marital:</strong> {{ $row->marital_status }}<br>
                                        <strong>Anak:</strong> {{ $row->child_count }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if($row->identity_url)
                                            <a href="{{ asset('storage/' . $row->identity_url) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="KTP"><i class="fa-solid fa-id-card"></i></a>
                                        @endif
                                        @if($row->family_card_url)
                                            <a href="{{ asset('storage/' . $row->family_card_url) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="KK"><i class="fa-solid fa-users"></i></a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-info btn-sm text-white btn-action" data-bs-toggle="modal" data-bs-target="#modalReview{{ $row->id }}">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i> Review
                                        </button>

                                        <form action="{{ route('admin.pdm.action', $row->id) }}" method="POST" onsubmit="return confirm('Langsung setujui tanpa review?')">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm btn-action">
                                                <i class="fa-solid fa-check me-1"></i> Setuju
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalReview{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title">Review Perubahan Data: {{ $row->employee_name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th width="30%">Kategori</th>
                                                        <th width="35%">Data Saat Ini</th>
                                                        <th width="35%">Data Pengajuan Baru</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Status Pernikahan</td>
                                                        <td>{{ $row->old_marital }}</td>
                                                        <td class="{{ $row->old_marital != $row->marital_status ? 'diff-changed' : '' }}">
                                                            {{ $row->marital_status }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Status Pajak (PTKP)</td>
                                                        <td>{{ $row->old_tax }}</td>
                                                        <td class="{{ $row->old_tax != $row->ptkp_status ? 'diff-changed' : '' }}">
                                                            {{ $row->ptkp_status }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Email Personal</td>
                                                        <td>-</td> <td class="text-primary">{{ $row->personal_email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>No. Telepon</td>
                                                        <td>-</td>
                                                        <td class="text-primary">{{ $row->phone_1 }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <div class="p-3 bg-light">
                                                <h6 class="fw-bold"><i class="fa-solid fa-paperclip me-2"></i>Lampiran Dokumen:</h6>
                                                <div class="row text-center mt-2">
                                                    <div class="col-4">
                                                        <p class="small mb-1">KTP</p>
                                                        <a href="{{ asset('storage/' . $row->identity_url) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $row->identity_url) }}" class="img-thumbnail" style="height: 100px; object-fit: cover" onerror="this.src='https://placehold.co/100x100?text=No+File'">
                                                        </a>
                                                    </div>
                                                    <div class="col-4">
                                                        <p class="small mb-1">KK</p>
                                                        <a href="{{ asset('storage/' . $row->family_card_url) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $row->family_card_url) }}" class="img-thumbnail" style="height: 100px; object-fit: cover" onerror="this.src='https://placehold.co/100x100?text=No+File'">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <form action="{{ route('admin.pdm.action', $row->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-outline-danger">Tolak Pengajuan</button>
                                            </form>
                                            <form action="{{ route('admin.pdm.action', $row->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-success px-4">Setujui & Perbarui Data</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#pdmTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" },
            "pageLength": 10,
            "order": [[1, "desc"]] 
        });
    });
</script>

</body>
</html>