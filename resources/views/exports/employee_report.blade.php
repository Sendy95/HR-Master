@extends('layouts.app') @section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    th { cursor: pointer; position: relative; }
    th::after { content: ' \2195'; font-size: 0.6rem; color: #ccc; }

    @media (min-width: 1200px) {
        .modal-xl {
            --bs-modal-width: 2000px;
            max-width: 95%;
        }
    }
    .container { max-width: 100%;}
    .dashboard-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin: 20px;}
    .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
    .table-wrapper { overflow-x: auto; max-height: 650px; border: 1px solid #dee2e6; border-radius: 8px; }
    table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.78rem; }
    th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px 8px; position: sticky; top: 0; z-index: 10; text-align: center; white-space: nowrap; }
    td { border: 1px solid #dee2e6; padding: 8px; white-space: nowrap; vertical-align: middle; }
    tr:hover { background-color: #f1f8f4; }
    .btn-export-excel { background-color: #1f7244; color: white !important; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; display: flex; align-items: center; border: none; transition: 0.3s; }
    .btn-export-excel:hover { background-color: #165331; }
    .view-selector { padding: 8px 12px; border-radius: 6px; border: 1px solid #dee2e6; background: #f8f9fa; font-weight: 500; outline: none; }
    .btn-view { background-color: #0d6efd; color: white !important; border: none; padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; }

    /* Warna Kelompok Kolom (Sesuai Query SQL) */
    .col-edu { background-color: #cfe2f3 !important; }
    .col-bank { background-color: #fff2cc !important; }
    .col-tax { background-color: #f4cccc !important; }
    .col-id { background-color: #ead1dc !important; }
    .col-family { background-color: #d9d2e9 !important; }

    .section-title { background: #343a40; padding: 6px 15px; font-weight: bold; border-radius: 4px; margin: 15px 0 10px 0; color: #fff; font-size: 0.85rem; }
    .detail-label { font-weight: bold; color: #555; font-size: 0.75rem; background: #f8f9fa; padding: 3px 8px; border-radius: 4px; display: block; margin-top: 5px; }
    .detail-value { font-weight: 600; padding: 5px 8px; display: block; border-bottom: 1px solid #eee; margin-bottom: 8px; color: #111; }
</style>

<div class="dashboard-container">
    <div class="header-flex">
        <div class="d-flex align-items-center gap-3">
            <h4 class="mb-0">Data Master Karyawan</h4>
            <select id="viewSelector" class="view-selector" onchange="switchView()">
                <option value="action">Action View (Ringkas)</option>
                <option value="full">Full Data View (Semua Kolom)</option>
            </select>
        </div>
        
        @if(in_array(Auth::user()->role, ['super_admin', 'hr_manager', 'hr_generalist']))
            <a href="{{ route('admin.export.excel') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Export Excel (Full Data)
            </a>
        @endif
    </div>

    <div id="actionView" class="table-wrapper">
        <table id="tableAction" class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">No Karyawan</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Perusahaan</th>
                    <th class="text-center">Pendidikan Terakhir</th>
                    <th class="text-center">No Rekening Mandiri</th>
                    <th class="text-center">No Pajak</th>
                    <th class="text-center">No KTP</th>
                    <th class="text-center">No KK</th>
                    <th class="text-center">Aksi</th>
                    <th class="text-center">Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $row)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ $row->employee_no }}</td>
                    <td class="text-center">{{ $row->employee_name }}</td>
                    <td class="text-center">{{ $row->company_name }}</td>
                    <td class="text-center">{{ $row->education_level }}</td>
                    <td class="text-center">{{ $row->bank_account_number }}</td>
                    <td class="text-center">{{ $row->npwp_number }}</td>
                    <td class="text-center">{{ $row->identity_number }}</td>
                    <td class="text-center">{{ $row->family_card_number }}</td>
                    <td class="text-center">
                        <button type="button" class="btn-view" data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->employee_no }}">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </button>
                    </td>
                    <td class="text-center">{{ $row->last_update_time ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


<div id="fullDataView" class="table-wrapper" style="display: none;"> 
        <table id="tableFull">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">No Karyawan</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Perusahaan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Gender</th>
                    <th class="text-center">POB</th>
                    <th class="text-center">DOB</th>
                    <th class="text-center">Blood</th>
                    <th class="text-center">Religion</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Tribe</th>
                    <th class="text-center">Phone 1</th>
                    <th class="text-center">P1 Status</th>
                    <th class="text-center">Phone 2</th>
                    <th class="text-center">P2 Status</th>
                    <th class="text-center col-edu">Pendidikan Terakhir</th>
                    <th class="text-center col-edu">Ijazah File</th>
                    <th class="text-center col-bank">No Rekening Mandiri</th>
                    <th class="text-center col-bank">Bank File</th>
                    <th class="text-center col-tax">NPWP No</th>
                    <th class="text-center col-tax">NPWP File</th>
                    <th class="text-center col-id">Identity No</th>
                    <th class="text-center col-id">Expiry</th>
                    <th class="text-center col-id">KTP File</th>
                    <th class="text-center col-family">Family Card No</th>
                    <th class="text-center col-family">KK File</th>
                    <th class="text-center">Marital</th>
                    <th class="text-center">Family Status</th>
                    <th class="text-center">Spouse Relation</th>
                    <th class="text-center">PTKP</th>
                    <th class="text-center">Spouse Name</th>
                    <th class="text-center">Spouse DOB</th>
                    <th class="text-center">Child</th>
                    <th class="text-center">C1 Name</th>
                    <th class="text-center">C1 Relation</th>                    
                    <th class="text-center">C1 DOB</th>
                    <th class="text-center">C2 Name</th>
                    <th class="text-center">C2 Relation</th>  
                    <th class="text-center">C2 DOB</th>
                    <th class="text-center">C3 Name</th>
                    <th class="text-center">C3 Relation</th>  
                    <th class="text-center">C3 DOB</th>
                    <th class="text-center">Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $row)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ $row->employee_no }}</td>
                    <td class="text-center">{{ $row->employee_name }}</td>
                    <td class="text-center">{{ $row->company_name }}</td>
                    <td class="text-center">{{ $row->status }}</td>
                    <td class="text-center">{{ $row->gender }}</td>
                    <td class="text-center">{{ $row->pob }}</td>
                    <td class="text-center">{{ $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d-M-y') : '-' }}</td>
                    <td class="text-center">{{ $row->blood_type }}</td>
                    <td class="text-center">{{ $row->religion }}</td>
                    <td class="text-center">{{ $row->personal_email }}</td>
                    <td class="text-center">{{ $row->tribe }}</td>
                    <td class="text-center">{{ $row->phone_1 }}</td>
                    <td class="text-center">{{ $row->phone_1_status }}</td>
                    <td class="text-center">{{ $row->phone_2 }}</td>
                    <td class="text-center">{{ $row->phone_2_status }}</td>
                    
                    <td class="text-center col-edu">{{ $row->education_level }}</td>
                    <td class="text-center col-edu small text-muted">{{ $row->ijazah_file }}</td>
                    
                    <td class="text-center col-bank">{{ $row->bank_account_number }}</td>
                    <td class="text-center col-bank small text-muted">{{ $row->bank_book_file }}</td>
                    
                    <td class="text-center col-tax">{{ $row->npwp_number }}</td>
                    <td class="text-center col-tax small text-muted">{{ $row->npwp_file }}</td>
                    
                    <td class="text-center col-id">{{ $row->identity_number }}</td>
                    <td class="text-center col-id">{{ $row->identity_expiry }}</td>
                    <td class="text-center col-id small text-muted">{{ $row->ktp_file }}</td>
                    
                    <td class="text-center col-family">{{ $row->family_card_number }}</td>
                    <td class="text-center col-family small text-muted">{{ $row->family_card_file }}</td>
                    
                    <td class="text-center">{{ $row->marital_status }}</td>
                    <td class="text-center">{{ $row->family_status }}</td>
                    <td class="text-center">{{ $row->spouse_relation }}</td>
                    <td class="text-center">{{ $row->ptkp_status }}</td>
                    <td class="text-center">{{ $row->spouse_name }}</td>
                    <td class="text-center">{{ $row->spouse_dob }}</td>
                    <td class="text-center">{{ $row->child_count }}</td>
                    <td class="text-center">{{ $row->child_1_name }}</td>
                    <td class="text-center">{{ $row->child_2_relation }}</td>
                    <td class="text-center">{{ $row->child_1_dob }}</td>
                    <td class="text-center">{{ $row->child_2_name }}</td>
                    <td class="text-center">{{ $row->child_2_relation }}</td>
                    <td class="text-center">{{ $row->child_2_dob }}</td>
                    <td class="text-center">{{ $row->child_3_name }}</td>
                    <td class="text-center">{{ $row->child_3_relation }}</td>
                    <td class="text-center">{{ $row->child_3_dob }}</td>
                    <td class="text-center">{{ $row->last_update_time }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@foreach($data as $row)
<div class="modal fade" id="detailModal{{ $row->employee_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i> {{ $row->employee_name }} ({{ $row->employee_no }})</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12"><div class="section-title">1. IDENTITAS & DATA UMUM</div></div>
                    <div class="col-md-3"><small class="detail-label">Jenis Kelamin</small><div class="detail-value">{{ $row->gender }}</div></div>
                    <div class="col-md-3"><small class="detail-label">Tempat, Tanggal Lahir</small><div class="detail-value">{{ $row->pob }}, {{ \Carbon\Carbon::parse($row->dob)->format('d-M-y') }}</div></div>
                    <div class="col-md-3"><small class="detail-label">Agama</small><div class="detail-value">{{ $row->religion }}</div></div>
                    <div class="col-md-3"><small class="detail-label">Golongan Darah | Suku</small><div class="detail-value">{{ $row->blood_type }} | {{ $row->tribe }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Alamat Email Pribadi</small><div class="detail-value">{{ $row->personal_email }}</div></div>
                    <div class="col-md-4"><small class="detail-label">No Telepon 1 | Status</small><div class="detail-value">{{ $row->phone_1 }} | {{ $row->phone_1_status }}</div></div>
                    <div class="col-md-4"><small class="detail-label">No Telepon 2 | Status</small><div class="detail-value">{{ $row->phone_2 }} | {{ $row->phone_2_status }}</div></div>

                    <div class="col-md-6">
                        <div class="section-title">2. PENDIDIKAN</div>
                        <small class="detail-label">Pendidikan Terakhir</small>
                        <div class="detail-value">{{ $row->education_level }}</div>
                        <small class="detail-label">Ijazah File</small>
                        <div class="detail-value small text-muted">{{ $row->ijazah_file ?? 'No file' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">3. PERBANKAN</div>
                        <small class="detail-label">Nomor Rekening Mandiri</small>
                        <div class="detail-value">{{ $row->bank_account_number }}</div>
                        <small class="detail-label">Bank File Path</small>
                        <div class="detail-value small text-muted">{{ $row->bank_book_file ?? 'No file' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">4. PERPAJAKAN (NPWP)</div>
                        <small class="detail-label">Nomor NPWP</small>
                        <div class="detail-value">{{ $row->npwp_number }}</div>
                        <small class="detail-label">NPWP File Path</small>
                        <div class="detail-value small text-muted">{{ $row->npwp_file ?? 'No file' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">5. IDENTITAS (KTP)</div>
                        <small class="detail-label">Nomor KTP | Masa Berlaku</small>
                        <div class="detail-value">{{ $row->identity_number }} | {{ $row->identity_expiry }}</div>
                        <small class="detail-label">KTP File Path</small>
                        <div class="detail-value small text-muted">{{ $row->ktp_file ?? 'No file' }}</div>
                    </div>

                    <div class="col-12"><div class="section-title">6. KELUARGA & PTKP</div></div>
                    <div class="col-md-4"><small class="detail-label">No. Kartu Keluarga (KK)</small><div class="detail-value">{{ $row->family_card_number }}</div></div>
                    <div class="col-md-4"><small class="detail-label">KK File Path</small><div class="detail-value small text-muted">{{ $row->family_card_file ?? 'No file' }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Marital | PTKP Status</small><div class="detail-value">{{ $row->marital_status }} | {{ $row->ptkp_status }}</div></div>
                    
                    <div class="col-md-4"><small class="detail-label">Nama Pasangan | Hubungan</small><div class="detail-value">{{ $row->spouse_name }} | {{ $row->spouse_relation }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Tanggal Lahir Pasangan</small><div class="detail-value">{{ $row->spouse_dob }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Jumlah Anak</small><div class="detail-value">{{ $row->child_count }}</div></div>

                    <div class="col-md-4"><small class="detail-label">Anak 1 (Nama|Hubugan|Tanggal Lahir)</small><div class="detail-value small">{{ $row->child_1_name }} | {{ $row->child_1_relation }} | {{ $row->child_1_dob }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Anak 2 (Nama|Hubugan|Tanggal Lahir)</small><div class="detail-value small">{{ $row->child_2_name }} | {{ $row->child_2_relation }} | {{ $row->child_2_dob }}</div></div>
                    <div class="col-md-4"><small class="detail-label">Anak 3 (Nama|Hubugan|Tanggal Lahir)</small><div class="detail-value small">{{ $row->child_3_name }} | {{ $row->child_3_relation }} | {{ $row->child_3_dob }}</div></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="me-auto small text-muted italic">Updated Terakhir: {{ $row->last_update_time }}</div>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    function switchView() {
        const val = document.getElementById('viewSelector').value;
        const action = document.getElementById('actionView');
        const full = document.getElementById('fullDataView');

        if (val === 'full') {
            action.style.display = 'none';
            full.style.display = 'block';
        } else {
            action.style.display = 'block';
            full.style.display = 'none';
        }
    }

    $(document).ready(function() {
        // Inisialisasi DataTable Action
        const tableAction = $('#tableAction').DataTable({
            "pageLength": 10,
            "responsive": true
        });

        // Inisialisasi DataTable Full (Hidden initially)
        const tableFull = $('#tableFull').DataTable({
            "scrollX": true,
            "pageLength": 10,
            "autoWidth": false
        });

        // Handler Switch View
        $('#viewSelector').on('change', function() {
            const val = $(this).val();
            if (val === 'full') {
                $('#actionView').hide();
                $('#fullDataView').show();
                // RE-ADJUST tabel karena tadinya hidden
                tableFull.columns.adjust().draw();
            } else {
                $('#fullDataView').hide();
                $('#actionView').show();
                tableAction.columns.adjust().draw();
            }
        });
    });
</script>
@endsection