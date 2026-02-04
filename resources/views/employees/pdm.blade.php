<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembaruan Data Mandiri - HR Master</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pdm-style.css') }}">
    <style>
        #loginSection { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .view-container {
            margin-top: 10px;
            display: flex;
            justify-content: center; /* Membuat tombol ke tengah */
            align-items: center;
        }

        .btn-view-center {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 20px; /* Membuat lonjong seperti di foto */
            padding: 5px 15px;
            font-size: 12px;
            color: #333;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }

        .btn-view-center:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div id="formSection">
        <button type="button" onclick="logoutKaryawan()" class="btn-logout-fixed">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </button>

        <div class="main-container">
            <div class="form-header">
                <i class="fas fa-id-badge fa-3x text-primary mb-3"></i>
                <h2>Pembaruan Data Mandiri (PDM) Karyawan</h2>
                <p class="text-muted">Pastikan data Anda mutakhir untuk keperluan administrasi perusahaan.</p>
            </div>

                <form id="pdmForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="section-card">
                    <div class="section-title"><i class="fas fa-user-circle"></i> I. Data Pribadi Karyawan</div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="label-req">PERUSAHAAN</label>
                            <input type="text" name="company_name" class="form-control bg-light" value="{{ $userData['company_name'] }}" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="label-req">STATUS KARYAWAN</label>
                            <select name="status" id="empStatus" class="form-select bg-light" 
                                    style="pointer-events: none; touch-action: none;" 
                                    onchange="toggleExpatLogic()" tabindex="-1" aria-disabled="true" required>
                                <option value="" disabled {{ empty($userData['status']) ? 'selected' : '' }}>>- Pilih -</option>    
                                <option value="Lokal" {{ $userData['status'] == 'Lokal' ? 'selected' : '' }}>Lokal</option>
                                <option value="Expat" {{ $userData['status'] == 'Expat' ? 'selected' : '' }}>Expat</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req">NO KARYAWAN</label>
                            <input type="text" name="employee_no" id="displayemployee_no" class="form-control bg-light" value="{{ $userData['employee_no'] }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req">NAMA LENGKAP</label>
                            <input type="text" name="employee_name" class="form-control" value="{{ $userData['employee_name'] }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req">JENIS KELAMIN</label>
                            <select name="gender" class="form-select" required>
                                <option value="" disabled {{ empty($userData['gender']) ? 'selected' : '' }}>- Pilih -</option>
                                <option value="Laki-laki" {{ $userData['gender'] == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $userData['gender'] == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req">TEMPAT LAHIR</label>
                            <input type="text" name="pob" class="form-control" value="{{ $userData['pob'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req">TANGGAL LAHIR</label>
                            <input type="date" name="dob" class="form-control" value="{{ $userData['dob'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req">GOLONGAN DARAH</label>
                            <select name="blood_type" class="form-select" required>
                                <option value="" disabled {{ empty($userData['blood_type']) ? 'selected' : '' }}>- Pilih -</option>
                                @foreach(['A', 'A+', 'A-', 'B', 'B+', 'B-', 'AB', 'AB+', 'AB-', 'O', 'O+', 'O-'] as $type)
                                    <option value="{{ $type }}" {{ ($userData['blood_type'] ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req">AGAMA</label>
                            <select name="religion" class="form-select" required>
                                <option value="" disabled {{ empty($userData['religion']) ? 'selected' : '' }}>- Pilih -</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Buddha', 'Hindu', 'Khonghucu'] as $rel)
                                    <option value="{{ $rel }}" {{ ($userData['religion'] ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req">EMAIL PRIBADI</label>
                            <input type="email" name="personal_email" class="form-control" value="{{ $userData['personal_email'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>SUKU</label>
                            <input type="text" name="tribe" class="form-control" value="{{ $userData['tribe'] ?? '' }}">
                        </div>
                    </div>

                    @php
                        $phoneStatuses = ['WhatsApp & Telepon', 'WhatsApp', 'Telepon'];
                    @endphp

                    <div class="row">
                        {{-- NO TELEPON 1 --}}
                        <div class="col-md-3 mb-3">
                            <label class="label-req">NO TELEPON 1</label>
                            <input type="text"
                                name="phone_1"
                                value="{{ $userData['phone_1'] ?? '' }}"
                                onkeypress="return hanyaAngka(event)"
                                oninput="cleanNonNumber(this)"
                                class="form-control"
                                placeholder="08..."
                                maxlength="14"
                                required>
                        </div>

                        {{-- STATUS NO TELP 1 --}}
                        <div class="col-md-3 mb-3">
                            <label class="label-req">STATUS NO TELP 1</label>
                            <select name="phone_1_status" class="form-select" required>
                                <option value="" disabled {{ empty($userData['phone_1_status']) ? 'selected' : '' }}>
                                    - Pilih -
                                </option>
                                @foreach($phoneStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ ($userData['phone_1_status'] ?? '') === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NO TELEPON 2 --}}
                        <div class="col-md-3 mb-3">
                            <label>NO TELEPON 2</label>
                            <input type="text"
                                name="phone_2"
                                value="{{ $userData['phone_2'] ?? '' }}"
                                onkeypress="return hanyaAngka(event)"
                                oninput="cleanNonNumber(this)"
                                class="form-control"
                                placeholder="08..."
                                maxlength="14">
                        </div>

                        {{-- STATUS NO TELP 2 --}}
                        <div class="col-md-3 mb-3">
                            <label>STATUS NO TELP 2</label>
                            <select name="phone_2_status" class="form-select">
                                <option value="" disabled {{ empty($userData['phone_2_status']) ? 'selected' : '' }}>
                                    - Pilih -
                                </option>
                                @foreach($phoneStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ ($userData['phone_2_status'] ?? '') === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title">🎓 PENDIDIKAN</div>
                                <label class="label-req">PENDIDIKAN TERAKHIR</label>
                                <select name="education_level" class="form-select" required>
                                    <option value="" disabled {{ empty($userData['education_level']) ? 'selected' : '' }}>- Pilih -</option>
                                    @foreach(['SD', 'SMP', 'SMK', 'SMA', 'STM', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2'] as $edu)
                                        <option value="{{ $edu }}" {{ ($userData['education_level'] ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                    @endforeach
                                </select>
                                <label class="label-req mt-3">UPLOAD IJAZAH</label>
                                <input type="file" id="education_certificate_url" name="education_certificate_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'ijazah')">
                                <div id="count_ijazah" style="font-size: 11px; color: #6c757d; margin-top: 4px;">Batas Upload: 0/2</div>
                                <div id="view_ijazah_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('ijazah')" class="btn-view-center">👁️ Lihat Ijazah</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title">🏦 REKENING BANK</div>
                                <label class="label-req">NO REKENING (13 DIGIT)</label>
                                <input type="text" name="bank_account_number" value="{{ $userData['bank_account_number'] ?? '' }}" class="form-control" maxlength="13" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 13, 'No Rekening Mandiri'); cleanNonNumber(this)" placeholder="13 digit angka" required>
                                <label class="label-req mt-3">UPLOAD BUKU TABUNGAN</label>
                                <input type="file" id="bank_book_url" name="bank_book_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'bank')">
                                <div id="count_bank" style="font-size: 11px; color: #6c757d; margin-top: 4px;">Batas Upload: 0/2</div>
                                <div id="view_bank_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('bank')" class="btn-view-center">👁️ Lihat Tabungan</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title">📄 PAJAK (NPWP)</div>
                                <label class="label-req">NO NPWP (16 DIGIT)</label>
                                <input type="text" name="npwp_number" value="{{ $userData['npwp_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'No NPWP'); cleanNonNumber(this)" placeholder="16 digit angka" required>
                                <label class="label-req mt-3">UPLOAD KARTU NPWP</label>
                                <input type="file" id="npwp_url" name="npwp_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'npwp')">
                                <div id="count_npwp" style="font-size: 11px; color: #6c757d; margin-top: 4px;">Batas Upload: 0/2</div>
                                <div id="view_npwp_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('npwp')" class="btn-view-center">👁️ Lihat NPWP</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-title">🆔 IDENTITAS KTP</div>
                                <label class="label-req">NO KTP (NIK)</label>
                                <input type="text" name="identity_number" value="{{ $userData['identity_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'No KTP'); cleanNonNumber(this)" placeholder="16 digit angka" required>
                                
                                <div class="input-group mt-3 mb-3">
                                    <div class="input-group-text w-50">
                                        <input type="checkbox" id="identity_expiry_forever"
                                            {{ ($userData['identity_expiry'] ?? 'Seumur Hidup') == 'Seumur Hidup' ? 'checked' : '' }} 
                                            onchange="toggleidentity_expiry()" class="me-2"> 
                                        <span class="small fw-bold">Seumur Hidup</span>
                                    </div>
                                    <input type="date" id="identity_expiry" name="identity_expiry" class="form-control w-50" 
                                        value="{{ ($userData['identity_expiry'] ?? '') != 'Seumur Hidup' ? ($userData['identity_expiry'] ?? '') : '' }}" 
                                        {{ ($userData['identity_expiry'] ?? 'Seumur Hidup') == 'Seumur Hidup' ? 'disabled' : '' }}>
                                </div>

                                <label class="label-req">UPLOAD KTP</label>
                                <input type="file" id="identity_url" name="identity_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'ktp')">
                                <div id="count_ktp" style="font-size: 11px; color: #6c757d; margin-top: 4px;">Batas Upload: 0/2</div>
                                <div id="view_ktp_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('ktp')" class="btn-view-center">👁️ Lihat KTP</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-title">👨‍👩‍👧‍👦 KARTU KELUARGA</div>
                                <label class="label-req">NO KARTU KELUARGA</label>
                                <input type="text" name="family_card_number" value="{{ $userData['family_card_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'No KK'); cleanNonNumber(this)" placeholder="16 digit angka" required>
                                <div class="d-none d-md-block" style="height: 48px;"></div>
                                <label class="label-req">UPLOAD KK</label>
                                <input type="file" id="family_card_url" name="family_card_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'kk')">
                                <div id="count_kk" style="font-size: 11px; color: #6c757d; margin-top: 4px;">Batas Upload: 0/2</div>
                                <div id="view_kk_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('kk')" class="btn-view-center">👁️ Lihat KK</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-title"><i class="fas fa-ring"></i> Status Pernikahan & Pajak</div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="label-req">STATUS PERNIKAHAN</label>
                                <select id="marital_status" name="marital_status" class="form-select" onchange="updateStatusLogic()" required>
                                    <option value="" disabled {{ empty($userData['marital_status']) ? 'selected' : '' }}>- Pilih -</option>
                                    <option value="Lajang" {{ ($userData['marital_status'] ?? '') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                                    <option value="Menikah" {{ ($userData['marital_status'] ?? '') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Cerai" {{ ($userData['marital_status'] ?? '') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>STATUS PAJAK (PTKP)</label>
                                <input type="text" id="ptkp_status" name="ptkp_status" class="form-control ptkp-badge" readonly value="{{ $userData['ptkp_status'] ?? '-' }}">
                                <input type="hidden" name="family_status" id="family_status">
                            </div>
                        </div>

                        <div id="tanggungan_checkbox_container" class="form-check mb-3" style="display: {{ empty($userData['marital_status']) ? 'none' : 'block' }};">
                            <input class="form-check-input" type="checkbox" id="has_children" name="has_children" 
                                onchange="updateStatusLogic()" {{ ($userData['has_children'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_children" style="cursor:pointer">Memiliki Tanggungan Anak?</label>
                        </div>

                        <div id="tanggungan_section" style="display:none; background: #f8fbff; padding: 15px; border-radius: 8px; border: 1px solid #e1e8f0;">
                            
                            <div id="familySection" style="display:none;" class="mb-4">
                                <label class="section-title" style="border-bottom: 2px solid #0d6efd;">DATA PASANGAN</label>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="label-req" style="font-size: 10px;">NAMA PASANGAN</label>
                                        <input type="text" id="spouse_name" name="spouse_name" value="{{ $userData['spouse_name'] ?? '' }}" onchange="updateCache(this)" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="label-req" style="font-size: 10px;">HUBUNGAN</label>
                                        <select id="spouse_relation" name="spouse_relation" class="form-select form-select-sm bg-light" style="pointer-events: none; touch-action: none;" tabindex="-1" aria-disabled="true">
                                            <option value="" disabled>- Pilih -</option>
                                            <option value="Suami" {{ ($userData['spouse_relation'] ?? '') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                            <option value="Istri" {{ ($userData['spouse_relation'] ?? '') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="label-req" style="font-size: 10px;">TANGGAL LAHIR PASANGAN</label>
                                        <input type="date" id="spouse_dob" name="spouse_dob" value="{{ $userData['spouse_dob'] ?? '' }}" onchange="updateCache(this)" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div id="anak_selection_area" style="display:none;">
                                <label class="section-title" style="border-bottom: 2px solid #0d6efd;">DATA TANGGUNGAN</label>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label style="font-size: 10px;">JUMLAH TANGGUNGAN (TOTAL)</label>
                                        <select id="child_count" name="child_count" class="form-select form-select-sm" onchange="updateStatusLogic()">
                                            @for($i=0; $i<=5; $i++)
                                                <option value="{{ $i }}" {{ ($userData['child_count'] ?? 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div id="detail_tanggungan_area"></div>
                            </div>
                        </div>
                    </div>

                <div class="mt-4 mb-3 p-3" style="background-color: #fff9db; border-radius: 8px; border: 1px solid #ffe066;">
                    <div class="form-check d-flex align-items-start gap-2">
                        <input class="form-check-input" type="checkbox" id="pernyataan_benar" name="pernyataan_benar" required>
                        <label class="form-check-label" for="pernyataan_benar" style="font-size: 13px;">
                            Dengan ini saya menyatakan bahwa seluruh data yang saya isikan adalah benar dan telah saya periksa dengan seksama. Apabila di kemudian hari terdapat kesalahan pada data yang saya input, maka hal tersebut sepenuhnya menjadi tanggung jawab saya.                        </label>
                    </div>
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg w-100 shadow mb-5">
                    <i class="fas fa-paper-plane"></i> UPDATE DATA
                </button>
                </div>
            </form>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * ============================================================
 * 1. KONFIGURASI GLOBAL & STATE
 * ============================================================
 */
const NO_WA_HR = "6285765369012";
const DURASI_MENIT = 30;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Penanganan URL agar fleksibel (Absolut)
const BASE_URL = "{{ url('/') }}";

const counts = @json($uploadCounts);

let sessionTimeout;
let fileURLs = {}; // Menyimpan URL file lama dari server

// Inisialisasi URL yang sudah ada di database (Server-side data)
window.fileURLs = {
    ktp: "{{ $userData['identity_url'] ?? '' }}",
    ijazah: "{{ $userData['education_certificate_url'] ?? '' }}",
    bank: "{{ $userData['bank_book_url'] ?? '' }}",
    npwp: "{{ $userData['npwp_url'] ?? '' }}",
    kk: "{{ $userData['family_card_url'] ?? '' }}"
};

function logoutKaryawan() {
    Swal.fire({
        title: 'Keluar?',
        text: "Pastikan data telah disimpan sebelum keluar.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar'
    }).then((result) => {
        if (result.isConfirmed) {
            sessionStorage.clear();
            window.location.href = "{{ route('logout') }}";
        }
    });
}

/**
 * ============================================================
 * 3. DATA FETCHING (GET DATA FROM SERVER)
 * function loadUserData(employee_no)
 * function fillFormWithData(data)
 * function updateFilePreviewUI()
 * function openFileInNewTab(type)
 * ============================================================
 */
async function loadUserData(employee_no) {
    if (!employee_no) return;

    // Tampilkan Loading Overlay
    Swal.fire({
        title: 'Mengambil Data...',
        text: 'Sinkronisasi dengan server HR.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try { 
        // 1. Persiapkan URL menggunakan Laravel Route Helper
        const urlFetch = "{{ route('pdm.get-data', ':no') }}".replace(':no', employee_no);
        console.log("Fetching dari URL:", urlFetch);
        
        // 2. Lakukan Request ke Server
        const response = await fetch(urlFetch, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        // 3. Validasi Response HTTP
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Server status: ${response.status}`);
        }

        const res = await response.json();
        
        // Tutup loading
        Swal.close();

        if (res.success) {
            console.log("Data diterima:", res.userData);
            
            // 4. ISI FORM DENGAN DATA
            // Kita panggil fungsi fillFormWithData yang sudah kita buat sebelumnya
            fillFormWithData(res.userData);

            // Opsional: Jika ada bagian form yang disembunyikan sebelumnya, bisa ditampilkan di sini
            const formSection = document.getElementById('pdmFormSection');
            if (formSection) formSection.classList.remove('d-none');
            
        } else {
            Swal.fire('Informasi', res.message || 'Data tidak ditemukan', 'info');
        }

    } catch (err) {
        console.error("Load Data Error Detail:", err);
        Swal.close();
        
        // Tampilkan pesan error yang user-friendly
        Swal.fire({
            icon: 'error',
            title: 'Gagal Sinkronisasi',
            text: 'Terjadi kesalahan: ' + err.message
        });
    }
}

/**
 * Fungsi Utama: Mengisi form dan mengatur logika UI Dokumen
 * @param {Object} data - Objek userData dari response server
 */
// 1. Inisialisasi Cache Global (Ambil data awal dari server)
const serverData = {!! $logDataJson !!};

let currentGender = serverData.gender || '';
let defaultRelation = '';

if (currentGender === "Laki-laki") defaultRelation = "Istri";
else if (currentGender === "Perempuan") defaultRelation = "Suami";

let tanggunganCache = {
    spouse_name: serverData.spouse_name || '',
    spouse_relation: serverData.spouse_relation || defaultRelation, // Gunakan default yang dinamis
    spouse_dob: serverData.spouse_dob || '',
    child_1_name: serverData.child_1_name || '',
    child_1_relation: serverData.child_1_relation || 'Anak Kandung',
    child_1_dob: serverData.child_1_dob || '',
    child_2_name: serverData.child_2_name || '',
    child_2_relation: serverData.child_2_relation || 'Anak Kandung',
    child_2_dob: serverData.child_2_dob || '',
    child_3_name: serverData.child_3_name || '',
    child_3_relation: serverData.child_3_relation || 'Anak Kandung',
    child_3_dob: serverData.child_3_dob || ''
};

// 2. Fungsi untuk Update Cache setiap kali user mengetik
function updateCache(el) {
    tanggunganCache[el.name] = el.value;
    console.log(`Updated Cache [${el.name}]: ${el.value}`);
}

// 3. Fungsi untuk menyimpan input yang sedang tampil sebelum HTML di-reset
function saveCurrentInputToCache() {
    console.log("Mengamankan data input ke cache...");

    // 1. Amankan Data Pasangan (Spouse)
    // Menggunakan querySelector agar lebih fleksibel mencari berdasarkan name
    const sName = document.querySelector('input[name="spouse_name"]');
    const sRel  = document.querySelector('select[name="spouse_relation"]') || document.querySelector('input[name="spouse_relation"]');
    const sDob  = document.querySelector('input[name="spouse_dob"]');

    if (sName) tanggunganCache.spouse_name = sName.value;
    if (sRel)  tanggunganCache.spouse_relation = sRel.value;
    if (sDob)  tanggunganCache.spouse_dob = sDob.value;

    // 2. Amankan Data Anak (1-3)
    for (let i = 1; i <= 3; i++) {
        const nameEl = document.querySelector(`input[name="child_${i}_name"]`);
        const relEl  = document.querySelector(`select[name="child_${i}_relation"]`);
        const dobEl  = document.querySelector(`input[name="child_${i}_dob"]`);
        
        // Simpan hanya jika elemen tersebut ada di layar (sedang ditampilkan)
        if (nameEl) {
            tanggunganCache[`child_${i}_name`] = nameEl.value;
        }
        if (relEl) {
            tanggunganCache[`child_${i}_relation`] = relEl.value;
        }
        if (dobEl) {
            tanggunganCache[`child_${i}_dob`] = dobEl.value;
        }
    }

    console.log("Data berhasil diamankan di cache:", tanggunganCache);
}

function fillFormWithData(data) {
    if (!data) return;

    console.log("Memulai pengisian form dengan data:", data);

    // 1. MAPPING DATA UTAMA
    const mapping = {
        'company_name': data.company_name,
        'status': data.status, 
        'employee_no': data.employee_no,
        'employee_name': data.employee_name,
        'gender': data.gender,
        'pob': data.pob,
        'dob': data.dob, 
        'blood_type': data.blood_type,
        'religion': data.religion,
        'personal_email': data.personal_email,
        'tribe': data.tribe,
        'phone_1': data.phone_1,
        'phone_1_status': data.phone_1_status,
        'phone_2': data.phone_2,
        'phone_2_status': data.phone_2_status,
        'education_level': data.education_level,
        'bank_account_number': data.bank_account_number,
        'npwp_number': data.npwp_number,
        'identity_number': data.identity_number,
        'family_card_number': data.family_card_number,
        'marital_status': data.marital_status,
        'ptkp_status': data.ptkp_status,
        'family_status': data.family_status,
        'identity_expiry': data.identity_expiry,
        'spouse_name': data.spouse_name,
        'spouse_relation': data.spouse_relation,
        'spouse_dob': data.spouse_dob,
        'child_count': data.child_count || 0
    };

    Object.keys(mapping).forEach(fieldName => {
        let el = document.getElementsByName(fieldName)[0] || document.getElementById(fieldName);
        if (el) {
            let value = mapping[fieldName] || "";
            if (el.type === 'date') {
                el.value = (value && value !== "0000-00-00") ? value.substring(0, 10) : "";
            } else {
                el.value = value;
            }
        }
    });

    // 2. LOGIKA IDENTITY EXPIRY
    const cbForever = document.getElementById('identity_expiry_forever');
    const dateExpiry = document.getElementById('identity_expiry');
    if (data.identity_expiry === 'Seumur Hidup' || !data.identity_expiry) {
        if (cbForever) cbForever.checked = true;
        if (dateExpiry) {
            dateExpiry.type = "text"; dateExpiry.placeholder = "SEUMUR HIDUP";
            dateExpiry.disabled = true; dateExpiry.style.backgroundColor = "#e9ecef";
        }
    }

    // 3. LOGIKA DINAMIS: ANAK (BAGIAN KRUSIAL)
    if (typeof generateDetailTanggungan === 'function') {
        const jmlAnak = parseInt(data.child_count) || 0;
        const hasTanggunganEl = document.getElementById('has_children');
        const childCountSelect = document.getElementById('child_count');
        
        // Set UI State
        if (hasTanggunganEl) hasTanggunganEl.checked = jmlAnak > 0;
        if (childCountSelect) childCountSelect.value = jmlAnak;

        // Cegah updateStatusLogic mereset area ini saat inisialisasi
        window.isInitializing = true; 

        // Generate Baris HTML
        generateDetailTanggungan(jmlAnak);

        // Gunakan interval untuk memastikan elemen input SUDAH ada di DOM sebelum diisi
        let attempts = 0;
        const checkChildElements = setInterval(() => {
            attempts++;
            const firstChildInput = document.getElementsByName('child_1_name')[0];

            if (firstChildInput || jmlAnak === 0 || attempts > 20) {
                clearInterval(checkChildElements);
                
                // Isi Value Nama, Relasi, dan DOB Anak
                for (let i = 1; i <= jmlAnak; i++) {
                    const n = document.getElementsByName(`child_${i}_name`)[0];
                    const r = document.getElementsByName(`child_${i}_relation`)[0];
                    const d = document.getElementsByName(`child_${i}_dob`)[0];
                    
                    if (n) n.value = data[`child_${i}_name`] || "";
                    if (r) r.value = data[`child_${i}_relation`] || "Anak Kandung";
                    if (d) {
                        let dobVal = data[`child_${i}_dob`] || "";
                        if (dobVal && dobVal !== "0000-00-00") d.value = dobVal.substring(0, 10);
                    }
                }
                
                window.isInitializing = false; // Buka kunci inisialisasi
                if (typeof updateStatusLogic === 'function') updateStatusLogic();
                console.log("Data Anak Berhasil Diisi.");
            }
        }, 100);
    }

    // 4. LOGIKA FILE PREVIEW
    const fileKeys = { ijazah: "ijazah", ktp: "ktp", npwp: "npwp", bank: "bank", kk: "kk" };
    Object.keys(fileKeys).forEach(key => {
        const url = (data.fileURLs && data.fileURLs[key]) ? data.fileURLs[key] : (data[key + '_url'] || null);
        const container = document.getElementById("view_" + key + "_container");
        if (url && (url.startsWith('http') || url.length > 5)) {
            if (container) container.style.setProperty('display', 'flex', 'important');
            const input = document.getElementById("f" + key.charAt(0).toUpperCase() + key.slice(1));
            if (input) input.removeAttribute('required');
        }
    });

    // 5. FINALISASI
    const displayNoK = document.getElementById('displayemployee_no');
    if (displayNoK) displayNoK.value = data.employee_no;
    
    if (typeof toggleExpatLogic === 'function') toggleExpatLogic();
    
    console.log("Sinkronisasi Form Selesai.");
}

/**
 * Update UI untuk menampilkan tombol "Lihat File" jika file sudah ada di server
 */
function updateFilePreviewUI() {
    const types = ['ktp', 'ijazah', 'bank', 'npwp', 'kk'];
    
    types.forEach(type => {
        const container = document.getElementById(`view_${type}_container`);
        if (container) {
            if (window.fileURLs && window.fileURLs[type]) {
                container.style.display = 'flex'; // Munculkan tombol lihat
                
                // Cari button di dalamnya untuk update event click
                const btn = container.querySelector('button');
                if (btn) {
                    btn.onclick = (e) => {
                        e.preventDefault();
                        window.open(window.fileURLs[type], '_blank');
                    };
                }
            } else {
                container.style.display = 'none'; // Sembunyikan jika belum ada file
            }
        }
    });
}

/**
 * Membuka file di tab baru (baik file lokal maupun file dari Drive)
 * @param {string} type - 'ktp', 'ijazah', 'bank', 'npwp', 'kk'
 */
function openFileInNewTab(type) {
    // 1. Identifikasi ID input file (identity_url, education_certificate_url, dll)
    const inputId = "f" + type.charAt(0).toUpperCase() + type.slice(1);
    const inputFile = document.getElementById(inputId);
    let targetUrl = null;

    // 2. CEK FILE BARU: Jika user baru saja memilih file di komputer mereka
    if (inputFile && inputFile.files && inputFile.files[0]) {
        const file = inputFile.files[0];
        targetUrl = URL.createObjectURL(file); 
        console.log("Membuka preview file baru (local):", targetUrl);
    } 
    // 3. CEK FILE LAMA: Jika tidak ada file baru, ambil dari database (window.fileURLs)
    else if (window.fileURLs && window.fileURLs[type]) {
        targetUrl = window.fileURLs[type];
        console.log("Membuka file dari database (server):", targetUrl);
    }

    // 4. EKSEKUSI BUKA TAB BARU
    if (targetUrl) {
        const newTab = window.open(targetUrl, '_blank');
        
        // Penanganan Pop-up Blocker
        if (!newTab || newTab.closed || typeof newTab.closed == 'undefined') {
            Swal.fire('Pop-up Blocked', 'Mohon izinkan pop-up untuk melihat dokumen.', 'warning');
            return;
        }

        // Jika ini adalah local URL (blob), bersihkan memori setelah tab terbuka
        if (targetUrl.startsWith('blob:')) {
            // Beri waktu 10 detik agar browser sempat memuat data sebelum link dihancurkan
            setTimeout(() => URL.revokeObjectURL(targetUrl), 10000);
        }
    } else {
        Swal.fire('Informasi', 'Belum ada file yang dipilih atau diunggah untuk bagian ini.', 'info');
    }
}

/**
 * Listener saat input file berubah
 * Mengubah UI tombol preview secara real-time
 */
function handleFileSelection(input, type) {
    const viewContainer = document.getElementById("view_" + type + "_container");
    const btnView = viewContainer ? viewContainer.querySelector('button') : null;
    
    if (input.files && input.files[0]) {
        // Tampilkan kontainer (menggunakan flex untuk alignment)
        if (viewContainer) {
            viewContainer.style.setProperty('display', 'flex', 'important');
        }
        
        if(btnView) {
            btnView.innerHTML = '👁️ Lihat Berkas Terpilih';
        }
        
        // Memberi feedback visual pada input
        input.style.borderColor = "#0d6efd";
        input.style.backgroundColor = "#f0f7ff";
    }
}

/**
 * ============================================================
 * 4. FORM SUBMISSION (UPDATE)
 * document.getElementById('masterForm')
 * ============================================================
 */
// Gabungkan logika submit menjadi satu
document.getElementById('pdmForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // 1. Validasi Pernyataan
    const checkPernyataan = document.getElementById('pernyataan_benar');
    if (checkPernyataan && !checkPernyataan.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan centang kotak pernyataan kebenaran data.'
        });
        return;
    }

    const btn = document.getElementById('btnSubmit');
    const originalContent = btn.innerHTML;
    const cbForever = document.getElementById('identity_expiry_forever');

    // 2. Loading State pada Tombol
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    // 3. Persiapan Data
    const formData = new FormData(this);
    if (cbForever && cbForever.checked) {
        formData.set('identity_expiry', 'Seumur Hidup');
    }

    try {
        const response = await fetch("{{ route('pdm.index') }}", {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const res = await response.json();

        if (response.ok && res.success) {
            // FITUR FREEZE: Menampilkan loading yang tidak bisa ditutup (Modal Blocking)
            Swal.fire({
                title: 'Berhasil Disimpan!',
                text: 'Mohon tunggu, sedang menyinkronkan data...',
                icon: 'success',
                allowOutsideClick: false, // Freeze: user tidak bisa klik di luar
                allowEscapeKey: false,    // Freeze: user tidak bisa tekan Esc
                showConfirmButton: false, // Freeze: tidak ada tombol untuk diklik
                didOpen: () => {
                    Swal.showLoading(); // Menampilkan spinner loading
                }
            });

            // Beri jeda 1.5 detik agar user sempat membaca, lalu refresh
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } else {
            // Jika Gagal: Kembalikan kontrol ke user
            let errorMsg = res.message || 'Gagal menyimpan data.';
            if (res.errors) errorMsg = Object.values(res.errors).flat().join('<br>');
            
            Swal.fire({ 
                icon: 'error', 
                title: 'Gagal', 
                html: errorMsg 
            });
            
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }

    } catch (err) {
        console.error("Submit Error:", err);
        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
});

/**
 * ============================================================
 * 5. LOGIKA UI & VALIDASI
 * function toggleidentity_expiry()
 * function toggleExpatLogic()
 * function validateAge()
 * document.getElementById('togglePass')
 * function handleFileSelection(input, type)
 * ============================================================
 */
function toggleidentity_expiry() {
    const cbForever = document.getElementById('identity_expiry_forever');
    const dateInput = document.getElementById('identity_expiry');

    if (!cbForever || !dateInput) return;

    if (cbForever.checked) {
        dateInput.type = "text"; 
        dateInput.value = ""; 
        dateInput.placeholder = "SEUMUR HIDUP"; // Sekarang placeholder ini BISA muncul
        dateInput.disabled = true;
        dateInput.style.backgroundColor = "#e9ecef"; // Warna abu-abu (disabled)
    } else {
        // KEMBALIKAN KE DATE: Agar muncul picker tanggal kembali
        dateInput.type = "date";
        dateInput.disabled = false;
        dateInput.placeholder = ""; 
        dateInput.style.backgroundColor = "#fff";
    }
}

function toggleExpatLogic() {
    const status = document.getElementById('empStatus')?.value;
    const lokalFields = document.querySelectorAll('.lokal-field');
    const cityLabel = document.getElementById('cityLabel');

    if(status === 'Expat') {
        lokalFields.forEach(f => f.style.display = 'none');
        if(cityLabel) cityLabel.innerText = "CITY (INTERNATIONAL)";
    } else {
        lokalFields.forEach(f => f.style.display = 'block');
        if(cityLabel) cityLabel.innerText = "KOTA / KABUPATEN";
    }
}

function validateAge() {
    const dob = document.getElementsByName('dob')[0];
    if (dob?.value) {
        const birthDate = new Date(dob.value);
        if (birthDate > new Date()) {
            Swal.fire('Peringatan', 'Tanggal lahir tidak valid', 'warning');
            dob.value = '';
        }
    }
}

// // Event Listener untuk Show/Hide Password
// document.getElementById('togglePass')?.addEventListener('click', function() {
//     const passInput = document.getElementById('logPass');
//     const eyeIcon = document.getElementById('eyeIcon');
//     if(passInput.type === 'password') {
//         passInput.type = 'text';
//         eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
//     } else {
//         passInput.type = 'password';
//         eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
//     }
// });



/**
 * ============================================================
 * 6. LIFECYCLE & SESSION
 * function startSessionTimer()
 * document.addEventListener
 * function handleUploadError(res)
 * ============================================================
 */
function startSessionTimer() {
    if (sessionTimeout) clearTimeout(sessionTimeout);
    sessionTimeout = setTimeout(() => {
        sessionStorage.clear();
        Swal.fire('Sesi Habis', 'Sesi Anda telah berakhir. Silakan login kembali.', 'info')
            .then(() => location.reload());
    }, DURASI_MENIT * 60 * 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded and parsed");

    // 1. LOGIKA RESTORE SESSION & TAMPILAN AWAL
    if (sessionStorage.getItem('isLoggedIn') === 'true') {
        const loginSection = document.getElementById('loginSection');
        const formSection = document.getElementById('formSection');
        
        if (loginSection) loginSection.style.display = 'none';
        if (formSection) formSection.style.display = 'block';
        
        const savedData = JSON.parse(sessionStorage.getItem('userData'));
        if (savedData && savedData.employee_no) {
            loadUserData(savedData.employee_no);
        }
        
        if (typeof startSessionTimer === 'function') {
            startSessionTimer();
        }
    }

    // 2. LOGIKA PENGISIAN FORM (DARI SERVER DATA)
    // Pastikan fillFormWithData jalan SEBELUM updateStatusLogic
    if (typeof serverData !== 'undefined' && serverData) {
        fillFormWithData(serverData);
    }

    // 3. INISIALISASI EVENT LISTENERS
    const genderSelect = document.querySelector('select[name="gender"]');
    const maritalStatusSelect = document.getElementById('marital_status');
    const childCountSelect = document.getElementById('child_count');
    const hasChildrenCheck = document.getElementById('has_children');

    // Trigger updateStatusLogic pertama kali untuk mengatur UI awal
    updateStatusLogic();

    // Listener untuk Gender (Otomatisasi Suami/Istri)
    if (genderSelect) {
        genderSelect.addEventListener('change', function() {
            if (typeof serverData !== 'undefined') serverData.gender = this.value; 
            updateStatusLogic();
        });
    }

    // Listener untuk Status Nikah
    if (maritalStatusSelect) {
        maritalStatusSelect.addEventListener('change', updateStatusLogic);
    }

    // Listener untuk Checkbox Anak
    if (hasChildrenCheck) {
        hasChildrenCheck.addEventListener('change', updateStatusLogic);
    }

    // Listener untuk Jumlah Anak
    if (childCountSelect) {
        childCountSelect.addEventListener('change', updateStatusLogic);
    }

    updateUploadCountsUI();
});

function handleUploadError(res) {
    if (res.message?.includes("Limit Upload")) {
        Swal.fire({
            title: "Batas Upload!",
            text: res.message,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hubungi HR"
        }).then((result) => {
            if (result.isConfirmed) {
                const msg = `Halo HR, saya mengalami limit upload. No Karyawan: ${document.getElementById('displayemployee_no').value}`;
                window.open(`https://wa.me/${NO_WA_HR}?text=${encodeURIComponent(msg)}`);
            }
        });
    } else {
        Swal.fire('Gagal', res.message || 'Error saat menyimpan data.', 'error');
    }
}

    // Inisialisasi variabel global untuk mengunci jumlah tanggungan
    let lastTanggunganCount = -1;

    /**
     * 3. LOGIKA FORM DINAMIS (PASANGAN & ANAK)
     */
function updateStatusLogic() {
    // 1. Inisialisasi Elemen dengan Guard Clause
    const genderEl = document.querySelector('select[name="gender"]');
    const statusSelectionEl = document.getElementById('marital_status');
    const hasTanggunganEl = document.getElementById('has_children');
    const childCountEl = document.getElementById('child_count');
    
    if (!genderEl || !statusSelectionEl || !hasTanggunganEl || !childCountEl) {
        console.warn("Beberapa elemen kontrol status tidak ditemukan di DOM.");
        return; 
    }

    const gender = genderEl.value; 
    const statusSelection = statusSelectionEl.value;
    const hasTanggungan = hasTanggunganEl.checked;
    const jmlTanggungan = parseInt(childCountEl.value) || 0;
    
    const familySection = document.getElementById('familySection');
    const tanggunganSection = document.getElementById('tanggungan_section');
    const tanggunganCheckboxContainer = document.getElementById('tanggungan_checkbox_container');
    const anakArea = document.getElementById('anak_selection_area');
    const detailArea = document.getElementById('detail_tanggungan_area');
    const statusPajakInput = document.getElementById('ptkp_status');
    const familyStatusInput = document.getElementById('family_status');

    // Input Pasangan
    const namaPasangan = document.getElementById('spouse_name');
    const hubPasangan = document.getElementById('spouse_relation');
    const tglPasangan = document.getElementById('spouse_dob');

    const isMarried = (statusSelection === 'Menikah');

    // --- A. LOGIKA TAMPILAN SECTION UTAMA ---
    if (tanggunganCheckboxContainer) {
        tanggunganCheckboxContainer.style.display = (statusSelection === "") ? 'none' : 'block';
    }
    
    if (statusSelection === "") {
        hasTanggunganEl.checked = false;
    }

    if (tanggunganSection) {
        tanggunganSection.style.display = (isMarried || hasTanggungan) ? 'block' : 'none';
    }

    // --- B. LOGIKA DATA PASANGAN (DIPERBAIKI) ---
    if (familySection) {
        if (isMarried) {
            familySection.style.display = 'block';
            
            // Set Required
            if (namaPasangan) namaPasangan.required = true;
            if (hubPasangan) hubPasangan.required = true;
            if (tglPasangan) tglPasangan.required = true;

            // RESTORE DATA DARI CACHE (Agar tidak kosong saat balik ke Menikah)
            if (namaPasangan && !namaPasangan.value) {
                namaPasangan.value = tanggunganCache.spouse_name || "";
            }
            if (tglPasangan && !tglPasangan.value) {
                tglPasangan.value = tanggunganCache.spouse_dob || "";
            }

            // Otomatisasi Hubungan Pasangan
            if (hubPasangan) {
                if (gender === "Laki-laki") hubPasangan.value = "Istri";
                else if (gender === "Perempuan") hubPasangan.value = "Suami";
                // Simpan ke cache juga
                tanggunganCache.spouse_relation = hubPasangan.value;
            }
        } else {
            // SEBELUM SEMBUNYI: Amankan data yang ada ke cache
            if (namaPasangan && namaPasangan.value !== "") {
                tanggunganCache.spouse_name = namaPasangan.value;
            }
            if (tglPasangan && tglPasangan.value !== "") {
                tanggunganCache.spouse_dob = tglPasangan.value;
            }

            familySection.style.display = 'none';
            
            // JANGAN gunakan namaPasangan.value = "" karena akan menghapus data selamanya
            if (namaPasangan) namaPasangan.required = false;
            if (hubPasangan) hubPasangan.required = false;
            if (tglPasangan) tglPasangan.required = false;
        }
    }

    // --- C. LOGIKA AREA INPUT DETAIL ANAK ---
    if (anakArea) {
        if (hasTanggungan) {
            anakArea.style.display = 'block';
            
            if (typeof generateDetailTanggungan === 'function') {
                // Gunakan flag window.isInitializing agar tidak render ulang saat startup
                if (!window.isInitializing && jmlTanggungan !== (window.lastTanggunganCount)) {
                    generateDetailTanggungan(jmlTanggungan);
                    window.lastTanggunganCount = jmlTanggungan; 
                }
            }
        } else {
            // Amankan data anak ke cache sebelum area dibersihkan
            if (typeof saveCurrentInputToCache === 'function') {
                saveCurrentInputToCache();
            }
            
            anakArea.style.display = 'none';
            if (detailArea) detailArea.innerHTML = ""; 
            window.lastTanggunganCount = -1; 
            // childCountEl.value = "0"; // Opsional: tetap biarkan jumlahnya di select
        }
    }

    // --- D. LOGIKA KODE PAJAK (PTKP) ---
    if (statusPajakInput) {
        if (statusSelection === "" || gender === "") {
            statusPajakInput.value = "-";
        } 
        else if (gender === "Perempuan") {
            statusPajakInput.value = "TK/0";
        } 
        else {
            let kode = isMarried ? "K" : "TK";
            let nilaiTanggungan = Math.min(jmlTanggungan, 3); 
            statusPajakInput.value = kode + "/" + nilaiTanggungan;
        }
    }

    // --- E. LOGIKA STATUS KELUARGA ---
    if (familyStatusInput) {
        if (statusSelection === "") {
            familyStatusInput.value = "-";
        } else if (isMarried) {
            familyStatusInput.value = "M" + jmlTanggungan; 
        } else {
            familyStatusInput.value = "S" + jmlTanggungan;
        }
    }
}

    let existingChildren = [];

    function generateDetailTanggungan(jml) {
        const container = document.getElementById('detail_tanggungan_area');
        if (!container) return;
        
        // Simpan data lama ke cache sebelum container dikosongkan
        saveCurrentInputToCache();
        
        container.innerHTML = "";
        const limit = Math.min(jml, 3); 
        
        for(let i = 1; i <= limit; i++) {
            // Ambil data dari cache agar saat pindah jumlah anak, data lama tidak hilang
            const valName = tanggunganCache[`child_${i}_name`] || "";
            const valRel = tanggunganCache[`child_${i}_relation`] || "Anak Kandung";
            const valDob = tanggunganCache[`child_${i}_dob`] || "";

            container.innerHTML += `
                <div class="tanggungan-item mb-3 p-2" style="border: 1px dashed #ced4da; border-radius: 5px; background: #fff;">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="label-req" style="font-size: 10px; color: #0d6efd; font-weight:700;">NAMA ANAK ${i}</label>
                            <input type="text" name="child_${i}_name" class="form-control form-control-sm" 
                                placeholder="Nama Lengkap Anak" value="${valName}" 
                                onchange="updateCache(this)" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label style="font-size: 10px;">HUBUNGAN</label>
                            <select name="child_${i}_relation" class="form-select form-select-sm" onchange="updateCache(this)">
                                <option value="Anak Kandung" ${valRel === 'Anak Kandung' ? 'selected' : ''}>Anak Kandung</option>
                                <option value="Anak Angkat" ${valRel === 'Anak Angkat' ? 'selected' : ''}>Anak Angkat</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="label-req" style="font-size: 10px;">TGL LAHIR</label>
                            <input type="date" name="child_${i}_dob" class="form-control form-control-sm" 
                                value="${valDob}" onchange="updateCache(this)" required>
                        </div>
                    </div>
                </div>`;
        }
    }

    // Fungsi untuk membatasi hanya input angka
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // Fungsi untuk mengecek panjang karakter (misal KTP harus 16 digit)
    function checkExactLength(el) {
        const maxLength = el.getAttribute('maxlength');
        if (maxLength && el.value.length > maxLength) {
            el.value = el.value.slice(0, maxLength);
        }
    }

    // Tambahkan ini di dalam tag <script> Anda
    function cleanNonNumber(el) {
        // Menghapus semua karakter yang bukan angka
        el.value = el.value.replace(/\D/g, '');
        
        // Jika Anda juga menggunakan checkExactLength, panggil di sini
        if (typeof checkExactLength === "function") {
            checkExactLength(el);
        }
    }

function updateUploadCountsUI() {
    console.log("Memperbarui UI Batas Upload...", counts);
    
    const statusMapping = {
        ktp: "ktp",
        ijazah: "ijazah",
        bank: "bank", 
        npwp: "npwp",
        kk: "kk"
    };

    Object.keys(statusMapping).forEach(id => {
        const el = document.getElementById("count_" + id);
        if (el) {
            const label = statusMapping[id];
            // Pastikan variabel 'counts' global sudah terisi dari @json($uploadCounts)
            const currentCount = (typeof counts !== 'undefined' && counts[label]) ? counts[label] : 0;
            
            el.innerHTML = `Batas Upload: ${currentCount}/2`;
            
            if (currentCount >= 2) {
                el.style.color = "red";
                el.style.fontWeight = "bold";
                el.innerHTML += " (Limit Tercapai)";
                
                const inputId = "f" + id.charAt(0).toUpperCase() + id.slice(1);
                const inputEl = document.getElementById(inputId);
                if(inputEl) {
                    inputEl.disabled = true;
                    inputEl.style.backgroundColor = "#e9ecef";
                }
            }
        }
    });
}
</script>
</body>
</html>