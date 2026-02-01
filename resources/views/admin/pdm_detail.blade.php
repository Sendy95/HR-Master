@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header & Global Actions --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ url('/admin/pdm-approval') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>

        <div class="dropdown">
            <button class="btn btn-sm btn-dark dropdown-toggle px-3 shadow-sm" type="button" id="bulkActionDrop" data-bs-toggle="dropdown">
                <i class="fas fa-tasks me-1"></i> Tindakan Masal
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item text-success fw-bold" href="javascript:void(0)" onclick="bulkAction('approve')"><i class="fas fa-check-circle me-2"></i>Setuju Semua</a></li>
                <li><a class="dropdown-item text-danger fw-bold" href="javascript:void(0)" onclick="bulkAction('reject')"><i class="fas fa-times-circle me-2"></i>Tolak Semua</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-muted" href="javascript:void(0)" onclick="bulkAction('clear')"><i class="fas fa-undo me-2"></i>Bersihkan Pilihan</a></li>
            </ul>
        </div>
    </div>

    <form id="pdmForm">
        @csrf
        <input type="hidden" name="id" value="{{ $update->id }}">
        
        {{-- BAGIAN 1: INFORMASI DATA TEKS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-primary">Detail Perubahan Data: <span class="text-dark">{{ $master->employee_name }}</span></h5>
                <small class="text-muted small">Silakan verifikasi setiap perubahan field di bawah ini.</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th style="width: 20%;">Item Informasi</th>
                                <th style="width: 25%;">Data Karyawan (Lama)</th>
                                <th style="width: 25%;">Data Karyawan (Lama)</th>
                                <th class="text-center" style="width: 250px;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $fields = [
                                    // Profil Utama
                                    'company_name' => 'Perusahaan', 'status' => 'Status Karyawan', 'gender' => 'Jenis Kelamin',
                                    'pob' => 'Tempat Lahir', 'dob' => 'Tanggal Lahir', 'blood_type' => 'Gol. Darah',
                                    'religion' => 'Agama', 'tribe' => 'Suku', 'nationality' => 'Kewarganegaraan',
                                    'personal_email' => 'Email Pribadi', 'phone_1' => 'No. Telepon 1', 'phone_1_status' => 'Status No. Telepon 1', 'phone_2' => 'No. Telepon 2', 'phone_2_status' => 'Status No. Telepon 2',
                                    
                                    // Administrasi & Keluarga
                                    'education_level' => 'Pendidikan', 'bank_account_number' => 'No. Rekening',
                                    'identity_number' => 'No. KTP', 'family_card_number' => 'No. KK',
                                    'npwp_number' => 'No. NPWP', 'marital_status' => 'Status Nikah',
                                    'family_status' => 'Status Keluarga', 'child_count' => 'Jumlah Anak',
                                    
                                    // Detail Keluarga
                                    'spouse_name' => 'Nama Pasangan', 'spouse_dob' => 'Tgl Lahir Pasangan',
                                    'child_1_name' => 'Nama Anak 1', 'child_1_dob' => 'Tgl Lahir Anak 1',
                                    'child_2_name' => 'Nama Anak 2', 'child_2_dob' => 'Tgl Lahir Anak 2',
                                    'child_3_name' => 'Nama Anak 3', 'child_3_dob' => 'Tgl Lahir Anak 3',
                                    
                                    // BPJS & Expat
                                    'bpjs_ketenagakerjaan' => 'BPJS TK', 'bpjs_kesehatan' => 'BPJS Kesehatan',
                                    'kitas_number' => 'No. KITAS', 'imta_number' => 'No. IMTA'
                                ];
                                $no = 1;
                                $foundAnyChange = false;
                            @endphp

                            @foreach($fields as $key => $label)
                                @php
                                    $valOld = $master->$key ?? '';
                                    $valNew = $update->$key ?? '';
                                @endphp
                                @if($valOld != $valNew && !empty($valNew))
                                    @php $foundAnyChange = true; @endphp
                                    <tr>
                                        <td class="text-center text-muted small">{{ $no++ }}</td>
                                        <td class="text-dark small">{{ $label }}</td>
                                        <td><span class="text-muted small">{{ $valOld ?: '(Kosong)' }}</span></td>
                                        <td><span class="text-primary">{{ $valNew }}</span></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm border rounded-pill p-1 bg-white shadow-sm" role="group">
                                                <input type="radio" class="btn-check" name="actions[{{ $key }}]" id="app_{{ $key }}" value="approve" autocomplete="off">
                                                <label class="btn btn-outline-success border-0 rounded-pill px-3" for="app_{{ $key }}">Setuju</label>

                                                <input type="radio" class="btn-check" name="actions[{{ $key }}]" id="rej_{{ $key }}" value="reject" autocomplete="off">
                                                <label class="btn btn-outline-danger border-0 rounded-pill px-3" for="rej_{{ $key }}">Tolak</label>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if(!$foundAnyChange)
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small italic">Tidak ada perubahan data teks terdeteksi.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: VERIFIKASI DOKUMEN PENDUKUNG --}}
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-primary">Verifikasi Dokumen Pendukung</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th style="width: 20%;">Jenis Dokumen</th>
                                <th style="width: 25%;">File (Lama)</th>
                                <th style="width: 25%;">File (Baru)</th>
                                <th class="text-center" style="width: 250px;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $docs = [
                                    'identity_url' => 'KTP', 'family_card_url' => 'KK', 
                                    'npwp_url' => 'NPWP', 'bank_book_url' => 'Buku Tabungan',
                                    'education_certificate_url' => 'Ijazah'
                                ];
                                $noDoc = 1;
                                $foundDoc = false;
                            @endphp
                            @foreach($docs as $f => $l)
                                @if($update->$f)
                                    @php $foundDoc = true; @endphp
                                    <tr>
                                        <td class="text-center text-muted small">{{ $noDoc++ }}</td>
                                        <td class="fw-bold text-dark small">{{ $l }}</td>
                                        <td>
                                            @if(!empty($master->$f))
                                                <a href="{{ asset('storage/'.$master->$f) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-1 px-2">
                                                    <i class="fas fa-external-link-alt me-1"></i> Lihat File Lama
                                                </a>
                                            @else
                                                <span class="text-muted small italic">(Kosong)</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ asset('storage/'.$update->$f) }}" target="_blank" class="btn btn-sm btn-link text-primary p-0 fw-bold">
                                                <i class="fas fa-file-pdf me-1"></i> Buka File
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm border rounded-pill p-1 bg-white shadow-sm" role="group">
                                                <input type="radio" class="btn-check" name="actions[{{ $f }}]" id="app_{{ $f }}" value="approve" autocomplete="off">
                                                <label class="btn btn-outline-success border-0 rounded-pill px-3" for="app_{{ $f }}">Setuju</label>

                                                <input type="radio" class="btn-check" name="actions[{{ $f }}]" id="rej_{{ $f }}" value="reject" autocomplete="off">
                                                <label class="btn btn-outline-danger border-0 rounded-pill px-3" for="rej_{{ $f }}">Tolak</label>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$foundDoc)
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small italic">Tidak ada lampiran dokumen baru.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- STICKY SAVE BAR --}}
        <div class="fixed-bottom bg-white border-top p-3 shadow-lg" style="z-index: 1030;">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-none d-md-block">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Field tanpa pilihan akan tetap berstatus <b>Pending</b>.
                    </span>
                </div>
                <button type="button" onclick="submitForm()" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="fas fa-save me-2"></i> SIMPAN HASIL VERIFIKASI
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Dropdown masal: Setuju semua atau Tolak semua
function bulkAction(type) {
    if (type === 'clear') {
        document.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
    } else {
        document.querySelectorAll(`input[value="${type}"]`).forEach(r => r.checked = true);
    }
}

// Kirim data ke controller
async function submitForm() {
    const form = document.getElementById('pdmForm');
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());
    
    // Karena kita butuh array 'actions', kita harus memproses ulang payload
    const actions = {};
    formData.forEach((value, key) => {
        if (key.startsWith('actions[')) {
            const fieldName = key.match(/\[(.*?)\]/)[1];
            actions[fieldName] = value;
        }
    });

    const confirm = await Swal.fire({
        title: 'Konfirmasi Simpan',
        text: "Data yang disetujui akan memperbarui database master karyawan.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    });

    if (confirm.isConfirmed) {
        try {
            const response = await fetch("{{ route('admin.pdm.bulkUpdate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: payload.id,
                    actions: actions
                })
            });

            const result = await response.json();
            if (result.success) {
                Swal.fire('Berhasil!', 'Data telah diperbarui.', 'success')
                .then(() => window.location.reload());
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memproses data.', 'error');
        }
    }
}
</script>

<style>
    body { padding-bottom: 100px; background-color: #f8f9fa; }
    .table-hover tbody tr:hover { background-color: rgba(13, 110, 253, 0.02); }
    .btn-check:checked + .btn-outline-success { background-color: #198754 !important; color: #fff !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545 !important; color: #fff !important; }
    .italic { font-style: italic; }
    .table th { font-size: 0.75rem; color: #6c757d; font-weight: 700; }
</style>
@endsection