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
</head>
<body>

    <div id="formSection">
    <button type="button" onclick="logoutKaryawan()" class="btn-logout-fixed">
        <i class="fas fa-sign-out-alt"></i> <span data-id="Keluar" data-en="Logout">Keluar</span>
    </button>

    <div class="main-container">
        <div class="form-header">
            <i class="fas fa-id-badge fa-3x text-primary mb-3"></i>
            <h2 data-id="Pembaruan Data Mandiri (PDM) Karyawan" data-en="Employee Self-Data Update (SDU)">Pembaruan Data Mandiri (PDM) Karyawan</h2>
            <p class="text-muted" data-id="Pastikan data Anda mutakhir untuk keperluan administrasi perusahaan." data-en="Ensure your data is up to date for company administrative purposes.">Pastikan data Anda mutakhir untuk keperluan administrasi perusahaan.</p>
        </div>

        <form id="pdmForm" method="POST" enctype="multipart/form-data" onsubmit="return validatePdmForm(event)">
            @csrf
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-user-circle"></i> <span data-id="Data Pribadi Karyawan" data-en="Employee Personal Data">Data Pribadi Karyawan</span>
                </div>
                    
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="label-req" data-id="PERUSAHAAN" data-en="COMPANY">PERUSAHAAN</label>
                        <input type="text" name="company_name" class="form-control bg-light" value="{{ $userData['company_name'] }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="label-req" data-id="STATUS KARYAWAN" data-en="EMPLOYEE STATUS">STATUS KARYAWAN</label>
                        <select name="status" id="empStatus" class="form-select bg-light" style="pointer-events: none;" required>
                            <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['status']) ? 'selected' : '' }}>- Pilih -</option>
                            <option value="Local" data-id="Lokal" data-en="Local" {{ $userData['status'] == 'Local' ? 'selected' : '' }}>Lokal</option>
                            <option value="Expat" data-id="Expat" data-en="Expatriate" {{ $userData['status'] == 'Expat' ? 'selected' : '' }}>Expat</option>
                        </select>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="NO KARYAWAN" data-en="EMPLOYEE ID">NO KARYAWAN</label>
                        <input type="text" name="employee_no" id="displayemployee_no" class="form-control bg-light" value="{{ $userData['employee_no'] }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="NAMA LENGKAP" data-en="FULL NAME">NAMA LENGKAP</label>
                            <input type="text" 
                                name="employee_name" 
                                class="form-control" 
                                data-id-placeholder="Nama Lengkap" 
                                data-en-placeholder="Full Name" 
                                value="{{ $userData['employee_name'] }}" 
                                required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="JENIS KELAMIN" data-en="GENDER">JENIS KELAMIN</label>
                            <select name="gender" class="form-select" required>
                                <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['gender']) ? 'selected' : '' }}>- Pilih -</option>
                                <option value="Male" data-id="Laki-laki" data-en="Male" {{ $userData['gender'] == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Female" data-id="Perempuan" data-en="Female" {{ $userData['gender'] == 'Female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="TEMPAT LAHIR" data-en="PLACE OF BIRTH">TEMPAT LAHIR</label>
                            <input type="text" name="pob" class="form-control" value="{{ $userData['pob'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="TANGGAL LAHIR" data-en="DATE OF BIRTH">TANGGAL LAHIR</label>
                            <input type="date" id="dob" name="dob" class="form-control" value="{{ $userData['dob'] ?? '' }}" onchange="validateAge()" max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="GOLONGAN DARAH" data-en="BLOOD TYPE">GOLONGAN DARAH</label>
                            <select name="blood_type" class="form-select" required>
                                <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['blood_type']) ? 'selected' : '' }}>- Pilih -</option>
                                @foreach(['A', 'A+', 'A-', 'B', 'B+', 'B-', 'AB', 'AB+', 'AB-', 'O', 'O+', 'O-'] as $type)
                                    <option value="{{ $type }}" {{ ($userData['blood_type'] ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="AGAMA" data-en="RELIGION">AGAMA</label>
                            <select name="religion" class="form-select" required>
                                <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['religion']) ? 'selected' : '' }}>- Pilih -</option>
                                
                                {{-- Kita buat mapping sederhana untuk label agama --}}
                                @php
                                    $religions = [
                                        'Islam' => ['id' => 'Islam', 'en' => 'Islam'],
                                        'Christian' => ['id' => 'Kristen', 'en' => 'Christian'],
                                        'Catholic' => ['id' => 'Katolik', 'en' => 'Catholic'],
                                        'Buddhism' => ['id' => 'Buddha', 'en' => 'Buddhism'],
                                        'Hindu' => ['id' => 'Hindu', 'en' => 'Hindu'],
                                        'Confucianism' => ['id' => 'Khonghucu', 'en' => 'Confucianism'],
                                        'Other' => ['id' => 'Lainnya', 'en' => 'Other'],
                                    ];
                                @endphp

                                @foreach($religions as $val => $label)
                                    <option value="{{ $val }}" 
                                            data-id="{{ $label['id'] }}" 
                                            data-en="{{ $label['en'] }}"
                                            {{ ($userData['religion'] ?? '') == $val ? 'selected' : '' }}>
                                        {{ $label['id'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="label-req" data-id="EMAIL PRIBADI" data-en="PERSONAL EMAIL">EMAIL PRIBADI</label>
                            <input type="email" name="personal_email" class="form-control" value="{{ $userData['personal_email'] ?? '' }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label data-id="SUKU" data-en="TRIBE/ETHNICITY">SUKU</label>
                            <input type="text" name="tribe" class="form-control" value="{{ $userData['tribe'] ?? '' }}" 
                                data-id-placeholder="Misal: Jawa, Batak, Tionghoa" 
                                data-en-placeholder="Example: Javanese, Batak, Chinese">
                        </div>
                    </div>

                    @php
                        // Mapping untuk mendukung translasi label tanpa mengubah value database
                        $phoneStatuses = [
                            'WhatsApp & Phone' => ['id' => 'WhatsApp & Telepon', 'en' => 'WhatsApp & Phone'],
                            'WhatsApp Only'           => ['id' => 'WhatsApp Saja', 'en' => 'WhatsApp Only'],
                            'Phone Only'            => ['id' => 'Telepon Saja', 'en' => 'Phone Only'],
                        ];
                    @endphp

                    <div class="row">
                        {{-- NO TELEPON 1 --}}
                        <div class="col-md-3 mb-3">
                            <label class="label-req" data-id="NO TELEPON 1" data-en="PHONE NUMBER 1">NO TELEPON 1</label>
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
                            <label class="label-req" data-id="STATUS NO TELP 1" data-en="PHONE 1 STATUS">STATUS NO TELP 1</label>
                            <select name="phone_1_status" class="form-select" required>
                                <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['phone_1_status']) ? 'selected' : '' }}>
                                    - Pilih -
                                </option>
                                @foreach($phoneStatuses as $val => $label)
                                    <option value="{{ $val }}"
                                        data-id="{{ $label['id'] }}"
                                        data-en="{{ $label['en'] }}"
                                        {{ ($userData['phone_1_status'] ?? '') === $val ? 'selected' : '' }}>
                                        {{ $label['id'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NO TELEPON 2 --}}
                        <div class="col-md-3 mb-3">
                            <label data-id="NO TELEPON 2" data-en="PHONE NUMBER 2">NO TELEPON 2</label>
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
                            <label data-id="STATUS NO TELP 2" data-en="PHONE 2 STATUS">STATUS NO TELP 2</label>
                            <select name="phone_2_status" class="form-select">
                                <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['phone_2_status']) ? 'selected' : '' }}>
                                    - Pilih -
                                </option>
                                @foreach($phoneStatuses as $val => $label)
                                    <option value="{{ $val }}"
                                        data-id="{{ $label['id'] }}"
                                        data-en="{{ $label['en'] }}"
                                        {{ ($userData['phone_2_status'] ?? '') === $val ? 'selected' : '' }}>
                                        {{ $label['id'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        {{-- PENDIDIKAN --}}
                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title" data-id="🎓 PENDIDIKAN" data-en="🎓 EDUCATION">🎓 PENDIDIKAN</div>
                                
                                <label class="label-req" data-id="PENDIDIKAN TERAKHIR" data-en="LATEST EDUCATION">PENDIDIKAN TERAKHIR</label>
                                @php
                                    $educationMap = [
                                        'SD'  => ['id' => 'SD', 'en' => 'Elementary School (SD)'],
                                        'SMP' => ['id' => 'SMP', 'en' => 'Junior High School (SMP)'],
                                        'SMK' => ['id' => 'SMK', 'en' => 'Vocational High School (SMK)'],
                                        'STM' => ['id' => 'STM', 'en' => 'Technical High School (STM)'],
                                        'SMA' => ['id' => 'SMA', 'en' => 'Senior High School (SMA)'],
                                        'D1'  => ['id' => 'D1', 'en' => 'Associate Degree (D1)'],
                                        'D2'  => ['id' => 'D2', 'en' => 'Associate Degree (D2)'],
                                        'D3'  => ['id' => 'D3', 'en' => 'Associate Degree (D3)'],
                                        'D4'  => ['id' => 'D4', 'en' => 'Applied Bachelor (D4)'],
                                        'S1'  => ['id' => 'S1', 'en' => 'Bachelor\'s Degree (S1)'],
                                        'S2'  => ['id' => 'S2', 'en' => 'Master\'s Degree (S2)'],
                                    ];
                                @endphp

                                <select name="education_level" class="form-select" required>
                                    <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['education_level']) ? 'selected' : '' }}>- Pilih -</option>
                                    @foreach($educationMap as $val => $label)
                                        <option value="{{ $val }}" 
                                                data-id="{{ $label['id'] }}" 
                                                data-en="{{ $label['en'] }}"
                                                {{ ($userData['education_level'] ?? '') == $val ? 'selected' : '' }}>
                                            {{ $label['id'] }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="label-req mt-3" data-id="UPLOAD IJAZAH" data-en="UPLOAD CERTIFICATE">UPLOAD IJAZAH</label>
                                <input type="file" id="education_certificate_url" name="education_certificate_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'ijazah')" {{ ($uploadCounts['ijazah'] ?? 0) >= 2 ? 'disabled' : '' }}>
                                
                                <div id="count_ijazah" class="upload-limit-text" 
                                    data-id="Batas Upload: {{ $uploadCounts['ijazah'] ?? 0 }}/2" 
                                    data-en="Upload Limit: {{ $uploadCounts['ijazah'] ?? 0 }}/2">
                                    Batas Upload: {{ $uploadCounts['ijazah'] ?? 0 }}/2
                                </div>

                                <div id="view_ijazah_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('ijazah')" class="btn-view-center">
                                        <span data-id="👁️ Lihat Ijazah" data-en="👁️ View Certificate">👁️ Lihat Ijazah</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- REKENING BANK --}}
                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title" data-id="🏦 REKENING BANK" data-en="🏦 BANK ACCOUNT">🏦 REKENING BANK</div>
                                
                                <label id="label_bank" class="label-req" data-id="NO REKENING MANDIRI (13 DIGIT)" data-en="MANDIRI ACCOUNT NUMBER (13 DIGIT)" data-msg-id="No Rekening Bank Mandiri" data-msg-en="Mandiri Bank Account Number">NO REKENING MANDIRI (13 DIGIT)</label>
                                <input type="text" id="bank_account_number" name="bank_account_number" value="{{ $userData['bank_account_number'] ?? '' }}" class="form-control" maxlength="13" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 13, 'label_bank'); cleanNonNumber(this)" placeholder="13 digit" required>
                                
                                <label class="label-req mt-3" data-id="UPLOAD BUKU TABUNGAN" data-en="UPLOAD BANK BOOK">UPLOAD BUKU TABUNGAN</label>
                                <input type="file" id="bank_book_url" name="bank_book_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'bank')" {{ ($uploadCounts['bank'] ?? 0) >= 2 ? 'disabled' : '' }}>
                                
                                <div id="count_bank" class="upload-limit-text"
                                    data-id="Batas Upload: {{ $uploadCounts['bank'] ?? 0 }}/2" 
                                    data-en="Upload Limit: {{ $uploadCounts['bank'] ?? 0 }}/2">
                                    Batas Upload: {{ $uploadCounts['bank'] ?? 0 }}/2
                                </div>

                                <div id="view_bank_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('bank')" class="btn-view-center">
                                        <span data-id="👁️ Lihat Tabungan" data-en="👁️ View Bank Book">👁️ Lihat Tabungan</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- PAJAK (NPWP) --}}
                        <div class="col-md-4">
                            <div class="section-card">
                                <div class="section-title" data-id="📄 PAJAK (NPWP)" data-en="📄 TAX (NPWP)">📄 PAJAK (NPWP)</div>
                                
                                <label id="label_npwp" class="label-req" data-id="NO NPWP (16 DIGIT)" data-en="TAX ID / NPWP (16 DIGIT)" data-msg-id="No NPWP" data-msg-en="Tax ID / NPWP Number">NO NPWP (16 DIGIT)</label>
                                <input type="text" id="npwp_number" name="npwp_number" value="{{ $userData['npwp_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'label_npwp'); cleanNonNumber(this)" placeholder="16 digit" required>
                                
                                <label class="label-req mt-3" data-id="UPLOAD KARTU NPWP" data-en="UPLOAD TAX CARD">UPLOAD KARTU NPWP</label>
                                <input type="file" id="npwp_url" name="npwp_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'npwp')" {{ ($uploadCounts['npwp'] ?? 0) >= 2 ? 'disabled' : '' }}>
                                
                                <div id="count_npwp" class="upload-limit-text"
                                    data-id="Batas Upload: {{ $uploadCounts['npwp'] ?? 0 }}/2" 
                                    data-en="Upload Limit: {{ $uploadCounts['npwp'] ?? 0 }}/2">
                                    Batas Upload: {{ $uploadCounts['npwp'] ?? 0 }}/2
                                </div>

                                <div id="view_npwp_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('npwp')" class="btn-view-center">
                                        <span data-id="👁️ Lihat NPWP" data-en="👁️ View Tax Card">👁️ Lihat NPWP</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- IDENTITAS KTP --}}
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-title" data-id="🆔 IDENTITAS KTP" data-en="🆔 ID CARD / KTP">🆔 IDENTITAS KTP</div>
                                
                                <label id="label_ktp" class="label-req" data-id="NO KTP (NIK)" data-en="ID NUMBER (NIK)" data-msg-id="No KTP" data-msg-en="ID Number (NIK)">NO KTP (NIK)</label>
                                <input type="text" id="identity_number" name="identity_number" value="{{ $userData['identity_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'label_ktp'); cleanNonNumber(this)" placeholder="16 digit" required>
                                
                                <div class="input-group mt-3 mb-3">
                                    <div class="input-group-text w-50">
                                        <input type="checkbox" id="identity_expiry_forever"
                                            {{ ($userData['identity_expiry'] ?? 'Seumur Hidup') == 'Seumur Hidup' ? 'checked' : '' }} 
                                            onchange="toggleidentity_expiry()" class="me-2"> 
                                        <span class="small fw-bold" data-id="Seumur Hidup" data-en="Lifetime">Seumur Hidup</span>
                                    </div>
                                    <input type="date" id="identity_expiry" name="identity_expiry" class="form-control w-50" max="{{ date('Y-m-d') }}"
                                        value="{{ ($userData['identity_expiry'] ?? '') != 'Seumur Hidup' ? ($userData['identity_expiry'] ?? '') : '' }}" 
                                        {{ ($userData['identity_expiry'] ?? 'Seumur Hidup') == 'Seumur Hidup' ? 'disabled' : '' }}>
                                </div>

                                <label class="label-req" data-id="UPLOAD KTP" data-en="UPLOAD ID CARD">UPLOAD KTP</label>
                                <input type="file" id="identity_url" name="identity_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'ktp')" {{ ($uploadCounts['ktp'] ?? 0) >= 2 ? 'disabled' : '' }}>
                                
                                <div id="count_ktp" class="upload-limit-text" 
                                    data-id="Batas Upload: {{ $uploadCounts['ktp'] ?? 0 }}/2" 
                                    data-en="Upload Limit: {{ $uploadCounts['ktp'] ?? 0 }}/2">
                                    Batas Upload: {{ $uploadCounts['ktp'] ?? 0 }}/2
                                </div>

                                <div id="view_ktp_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('ktp')" class="btn-view-center">
                                        <span data-id="👁️ Lihat KTP" data-en="👁️ View ID Card">👁️ Lihat KTP</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- KARTU KELUARGA --}}
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-title" data-id="👨‍👩‍👧‍👦 KARTU KELUARGA" data-en="👨‍👩‍👧‍👦 FAMILY CARD">👨‍👩‍👧‍👦 KARTU KELUARGA</div>
                                
                                <label id="label_kk" class="label-req" data-id="NO KARTU KELUARGA (KK)" data-en="FAMILY CARD NUMBER (KK)" data-msg-id="No KK" data-msg-en="Family Card Number (KK)">NO KARTU KELUARGA</label>
                                <input type="text" id="family_card_number" name="family_card_number" value="{{ $userData['family_card_number'] ?? '' }}" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)" oninput="checkExactLength(this, 16, 'label_kk'); cleanNonNumber(this)" placeholder="16 digit" required>
                                
                                {{-- Spacer agar tinggi card tetap seimbang --}}
                                <div class="d-none d-md-block" style="height: 48px;"></div>
                                
                                <label class="label-req" data-id="UPLOAD KK" data-en="UPLOAD FAMILY CARD">UPLOAD KK</label>
                                <input type="file" id="family_card_url" name="family_card_url" class="form-control" accept=".pdf,image/*" onchange="handleFileSelection(this, 'kk')" {{ ($uploadCounts['kk'] ?? 0) >= 2 ? 'disabled' : '' }}>
                                
                                <div id="count_kk" class="upload-limit-text" 
                                    data-id="Batas Upload: {{ $uploadCounts['kk'] ?? 0 }}/2" 
                                    data-en="Upload Limit: {{ $uploadCounts['kk'] ?? 0 }}/2">
                                    Batas Upload: {{ $uploadCounts['kk'] ?? 0 }}/2
                                </div>

                                <div id="view_kk_container" class="view-container">
                                    <button type="button" onclick="openFileInNewTab('kk')" class="btn-view-center">
                                        <span data-id="👁️ Lihat KK" data-en="👁️ View Family Card">👁️ Lihat KK</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-ring"></i> 
                            <span data-id="Status Pernikahan & Pajak" data-en="Marital Status & Tax">Status Pernikahan & Pajak</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="label-req" data-id="STATUS PERNIKAHAN" data-en="MARITAL STATUS">STATUS PERNIKAHAN</label>
                                <select id="marital_status" name="marital_status" class="form-select" onchange="updateStatusLogic()" required>
                                    <option value="" disabled data-id="- Pilih -" data-en="- Select -" {{ empty($userData['marital_status']) ? 'selected' : '' }}>- Pilih -</option>
                                    <option value="Single" data-id="Lajang" data-en="Single" {{ ($userData['marital_status'] ?? '') == 'Single' ? 'selected' : '' }}>Lajang</option>
                                    <option value="Married" data-id="Menikah" data-en="Married" {{ ($userData['marital_status'] ?? '') == 'Married' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Divorced" data-id="Cerai" data-en="Divorced" {{ ($userData['marital_status'] ?? '') == 'Divorced' ? 'selected' : '' }}>Cerai</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label data-id="STATUS PAJAK (PTKP)" data-en="TAX STATUS (PTKP)">STATUS PAJAK (PTKP)</label>
                                <input type="text" id="ptkp_status" name="ptkp_status" class="form-control ptkp-badge" readonly value="{{ $userData['ptkp_status'] ?? '-' }}">
                                <input type="hidden" name="family_status" id="family_status">
                            </div>
                        </div>

                        <div id="tanggungan_checkbox_container" class="form-check mb-3" style="display: {{ empty($userData['marital_status']) ? 'none' : 'block' }};">
                            <input class="form-check-input" type="checkbox" id="has_children" name="has_children" 
                                onchange="updateStatusLogic()" {{ ($userData['has_children'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_children" style="cursor:pointer" 
                                data-id="Memiliki Tanggungan Anak?" data-en="Have dependents/children?">
                                Memiliki Tanggungan Anak?
                            </label>
                        </div>

                        <div id="tanggungan_section" style="display:none; background: #f8fbff; padding: 15px; border-radius: 8px; border: 1px solid #e1e8f0;">
                            <div id="familySection" style="display:none;" class="mb-4">
                                <label class="section-title" style="border-bottom: 2px solid #0d6efd;" data-id="DATA PASANGAN" data-en="SPOUSE DATA">DATA PASANGAN</label>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="label-req" style="font-size: 10px;" data-id="NAMA PASANGAN" data-en="SPOUSE NAME">NAMA PASANGAN</label>
                                        <input type="text" id="spouse_name" name="spouse_name" value="{{ $userData['spouse_name'] ?? '' }}" onchange="updateCache(this)" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="label-req" style="font-size: 10px;" data-id="HUBUNGAN" data-en="RELATION">HUBUNGAN</label>
                                        <select id="spouse_relation" name="spouse_relation" class="form-select form-select-sm bg-light" style="pointer-events: none; touch-action: none;" tabindex="-1" aria-disabled="true">
                                            <option value="" disabled data-id="- Pilih -" data-en="- Select -">- Pilih -</option>
                                            <option value="Suami" data-id="Suami" data-en="Husband" {{ ($userData['spouse_relation'] ?? '') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                            <option value="Istri" data-id="Istri" data-en="Wife" {{ ($userData['spouse_relation'] ?? '') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="label-req" style="font-size: 10px;" data-id="TANGGAL LAHIR PASANGAN" data-en="SPOUSE DATE OF BIRTH">TANGGAL LAHIR PASANGAN</label>
                                        <input type="date" id="spouse_dob" name="spouse_dob" value="{{ $userData['spouse_dob'] ?? '' }}" onchange="updateCache(this)" max="{{ date('Y-m-d') }}" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div id="anak_selection_area" style="display:none;">
                                <label class="section-title" style="border-bottom: 2px solid #0d6efd;" data-id="DATA TANGGUNGAN" data-en="DEPENDENTS DATA">DATA TANGGUNGAN</label>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label style="font-size: 10px;" data-id="JUMLAH TANGGUNGAN (TOTAL)" data-en="NUMBER OF DEPENDENTS">JUMLAH TANGGUNGAN (TOTAL)</label>
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

                    {{-- PERNYATAAN --}}
                    <div class="mt-4 mb-3 p-3" style="background-color: #fff9db; border-radius: 8px; border: 1px solid #ffe066;">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input class="form-check-input check-pernyataan" type="checkbox" id="pernyataan_benar" name="pernyataan_benar" required>
                            <label class="form-check-label ms-1" for="pernyataan_benar" style="font-size: 13px;"
                                data-id="Dengan ini saya menyatakan bahwa seluruh data yang saya isikan adalah benar dan telah saya periksa dengan seksama. Apabila di kemudian hari terdapat kesalahan pada data yang saya input, maka hal tersebut sepenuhnya menjadi tanggung jawab saya."
                                data-en="I hereby declare that all the data I have entered is correct and has been carefully checked. If in the future there are errors in the data I input, it will be entirely my responsibility.">
                                Dengan ini saya menyatakan bahwa seluruh data yang saya isikan adalah benar dan telah saya periksa dengan seksama. 
                                Apabila di kemudian hari terdapat kesalahan pada data yang saya input, maka hal tersebut sepenuhnya menjadi tanggung jawab saya.
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg w-100 shadow mb-5">
                        <i class="fas fa-paper-plane"></i> 
                        <span data-id="UPDATE DATA" data-en="UPDATE DATA">UPDATE DATA</span>
                    </button>

<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
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

// 1. Ambil data jumlah upload dari Controller (Gunakan LET agar bisa diupdate via AJAX nanti)
let counts = @json($uploadCounts ?? []); 

let sessionTimeout;

/**
 * Catatan: fileURLs lama (identity_url, dsb) dihapus dari sini 
 * karena data file sekarang ada di tabel employee_documents, 
 * bukan lagi di kolom tabel employees.
 */

function logoutKaryawan() {
    const lang = localStorage.getItem('selectedLang') || 'id'; // Deteksi bahasa aktif

    Swal.fire({
        title: (lang === 'en') ? 'Logout?' : 'Keluar?',
        text: (lang === 'en') 
            ? "Ensure all data is saved before logging out." 
            : "Pastikan data telah disimpan sebelum keluar.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: (lang === 'en') ? 'Yes, Logout' : 'Ya, Keluar',
        cancelButtonText: (lang === 'en') ? 'Cancel' : 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Bersihkan session storage sebelum redirect
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

if (currentGender === "Male") defaultRelation = "Istri";
else if (currentGender === "Female") defaultRelation = "Suami";

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

    // --- UPDATE GLOBAL CACHE DARI DATA SERVER ---
    if (window.tanggunganCache) {
        window.tanggunganCache.child_count = parseInt(data.child_count) || 0;
        window.tanggunganCache.spouse_name = data.spouse_name || "";
        window.tanggunganCache.spouse_dob = data.spouse_dob || "";
    }

    // 2. LOGIKA IDENTITY EXPIRY
    const cbForever = document.getElementById('identity_expiry_forever');
    const dateExpiry = document.getElementById('identity_expiry');

    if (data.identity_expiry === 'Seumur Hidup' || !data.identity_expiry) {
        if (cbForever) cbForever.checked = true;
        if (dateExpiry) {
            dateExpiry.type = "text"; 
            
            // Tambahkan atribut translasi untuk placeholder
            dateExpiry.setAttribute('data-id-placeholder', 'SEUMUR HIDUP');
            dateExpiry.setAttribute('data-en-placeholder', 'LIFETIME');
            
            // Tentukan placeholder awal berdasarkan bahasa saat ini
            const currentLang = localStorage.getItem('selectedLang') || 'id';
            dateExpiry.placeholder = currentLang === 'en' ? 'LIFETIME' : 'SEUMUR HIDUP';
            
            dateExpiry.disabled = true; 
            dateExpiry.style.backgroundColor = "#e9ecef";
        }
    }

    // 3. LOGIKA DINAMIS: ANAK (DIPERBAIKI)
    if (typeof generateDetailTanggungan === 'function') {
        const jmlAnak = parseInt(data.child_count) || 0;
        const hasTanggunganEl = document.getElementById('has_children');
        const childCountSelect = document.getElementById('child_count');
        
        // Set UI State berdasarkan data database
        if (hasTanggunganEl) hasTanggunganEl.checked = jmlAnak > 0;
        if (childCountSelect) childCountSelect.value = jmlAnak.toString();

        // Kunci agar updateStatusLogic tidak melakukan intervensi saat loading
        window.isInitializing = true; 

        if (jmlAnak > 0) {
            generateDetailTanggungan(jmlAnak);

            let attempts = 0;
            const checkChildElements = setInterval(() => {
                attempts++;
                const firstChildInput = document.getElementsByName('child_1_name')[0];

                if (firstChildInput || attempts > 20) {
                    clearInterval(checkChildElements);
                    
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
                    
                    window.isInitializing = false; 
                    if (typeof updateStatusLogic === 'function') updateStatusLogic();
                }
            }, 100);
        } else {
            // Jika jmlAnak == 0, langsung buka kunci
            window.isInitializing = false;
            if (typeof updateStatusLogic === 'function') updateStatusLogic();
        }
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
    const lang = localStorage.getItem('selectedLang') || 'id'; // Ambil bahasa aktif
    
    types.forEach(type => {
        const container = document.getElementById(`view_${type}_container`);
        if (container) {
            // Cek apakah di variabel 'counts' (dari database) sudah ada file
            const hasFileInServer = (typeof counts !== 'undefined' && counts[type] > 0);
            
            if (hasFileInServer) {
                container.style.setProperty('display', 'flex', 'important');
                const btn = container.querySelector('button');
                
                if (btn) {
                    // Logika Teks Multibahasa
                    const btnText = (lang === 'en') ? '👁️ View File' : '👁️ Lihat Berkas';
                    btn.innerHTML = btnText;
                    
                    // Pastikan warnanya kembali ke default (biru/center) jika sebelumnya berwarna warning (kuning)
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-view-center');
                }
            } else {
                // Tetap sembunyikan jika benar-benar belum ada file sama sekali
                container.style.display = 'none';
            }
        }
    });
}

/**
 * Membuka file di tab baru (baik file lokal maupun file dari Drive)
 * @param {string} type - 'ktp', 'ijazah', 'bank', 'npwp', 'kk'
 */
function openFileInNewTab(type) {
    // 1. Mapping ID Input sesuai HTML Anda
    const inputMapping = {
        ktp: 'identity_url',
        ijazah: 'education_certificate_url',
        bank: 'bank_book_url',
        npwp: 'npwp_url',
        kk: 'family_card_url'
    };

    const inputId = inputMapping[type];
    const inputFile = document.getElementById(inputId);
    let targetUrl = null;

    // 2. CEK FILE BARU (Local Preview)
    if (inputFile && inputFile.files && inputFile.files[0]) {
        const file = inputFile.files[0];
        targetUrl = URL.createObjectURL(file); 
        console.log("Preview file baru:", targetUrl);
    } 
    // 3. CEK FILE LAMA (Server Side)
    else if (typeof counts !== 'undefined' && counts[type] > 0) {
        // Arahkan ke Route Laravel yang kita buat sebelumnya
        targetUrl = `${BASE_URL}/pdm/view-document/${type}`;
        console.log("Membuka file dari server:", targetUrl);
    }

    // 4. EKSEKUSI
    if (targetUrl) {
        const newTab = window.open(targetUrl, '_blank');
        if (!newTab) {
            Swal.fire('Pop-up Blocked', 'Mohon izinkan pop-up browser Anda.', 'warning');
            return;
        }

        if (targetUrl.startsWith('blob:')) {
            setTimeout(() => URL.revokeObjectURL(targetUrl), 10000);
        }
    } else {
        Swal.fire('Informasi', 'Belum ada berkas yang diunggah.', 'info');
    }
}

/**
 * Listener saat input file berubah
 * Mengubah UI tombol preview secara real-time
 */
function handleFileSelection(input, type) {
    const viewContainer = document.getElementById("view_" + type + "_container");
    const btnView = viewContainer ? viewContainer.querySelector('button') : null;
    const lang = localStorage.getItem('selectedLang') || 'id'; // Ambil bahasa aktif

    if (input.files && input.files[0]) {
        if (viewContainer) {
            // Gunakan flex agar tombol tetap di tengah sesuai CSS view-container
            viewContainer.style.setProperty('display', 'flex', 'important');
        }
        
        if (btnView) {
            // Logika Teks Multibahasa
            const btnText = (lang === 'en') 
                ? '👁️ View File (Not Saved Yet)' 
                : '👁️ Lihat Berkas (Belum Simpan)';
            
            btnView.innerHTML = btnText;
            
            // Tambahkan class warning (kuning) tanpa menghapus class dasar (btn-view-center)
            // Ini memastikan CSS layout tombol Anda tidak berantakan
            btnView.classList.add('btn-warning'); 
        }
        
        // Highlight pada input file
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
    const lang = localStorage.getItem('selectedLang') || 'id';

    // --- 1. VALIDASI INTERNAL ---
    
    // A. Cek Input yang Masih Merah (Invalid Digit)
    const firstInvalid = document.querySelector('.is-invalid');
    if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus();
        return; 
    }

    // B. Cek Field Wajib (*) yang Kosong
    const requiredFields = document.querySelectorAll('input[required]:not([type="checkbox"]), select[required]');
    for (let field of requiredFields) {
        if (!field.value || field.value.trim() === "") {
            field.classList.add('is-invalid'); 
            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            field.focus();
            return; 
        }
    }

    // C. Validasi Logika Upload (Minimal 1/2)
    const docTypes = [
        { id: 'ijazah', label_id: 'Ijazah', label_en: 'Certificate' },
        { id: 'bank', label_id: 'Buku Tabungan', label_en: 'Bank Statement' },
        { id: 'npwp', label_id: 'Kartu NPWP', label_en: 'Tax Card (NPWP)' },
        { id: 'ktp', label_id: 'KTP', label_en: 'ID Card (KTP)' },
        { id: 'kk', label_id: 'Kartu Keluarga', label_en: 'Family Card (KK)' }
    ];

    for (let doc of docTypes) {
        const countDiv = document.getElementById('count_' + doc.id);
        if (countDiv) {
            const text = countDiv.innerText; 
            const matches = text.match(/\d+/);
            const currentUploaded = matches ? parseInt(matches[0]) : 0;
            const fileInput = document.querySelector(`input[onchange*="'${doc.id}'"]`);
            
            if (currentUploaded === 0 && (!fileInput || fileInput.files.length === 0)) {
                countDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const label = (lang === 'en') ? doc.label_en : doc.label_id;
                Swal.fire({
                    icon: 'warning',
                    title: (lang === 'en') ? 'Required Document' : 'Dokumen Wajib',
                    text: (lang === 'en') ? `Please upload your ${label} first.` : `Silakan upload dokumen ${label} terlebih dahulu.`
                });
                return;
            }
        }
    }

    // D. Cek Pernyataan (Checkbox)
    const checkPernyataan = document.getElementById('pernyataan_benar');
    if (checkPernyataan && !checkPernyataan.checked) {
        checkPernyataan.scrollIntoView({ behavior: 'smooth', block: 'center' });
        checkPernyataan.focus();
        return;
    }

    // --- 2. KONFIRMASI AKHIR ---
    const confirmSave = await Swal.fire({
        title: (lang === 'en') ? 'Confirmation' : 'Konfirmasi',
        text: (lang === 'en') ? 'Is the data correct and ready to be saved?' : 'Apakah data sudah benar dan siap disimpan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: (lang === 'en') ? 'Yes, Save it!' : 'Ya, Simpan!',
        cancelButtonText: (lang === 'en') ? 'Cancel' : 'Batal'
    });

    if (!confirmSave.isConfirmed) return;

    // --- 3. PROSES LOADING & FETCH (AJAX) ---
    const btn = document.getElementById('btnSubmit');
    const originalContent = btn.innerHTML;
    const cbForever = document.getElementById('identity_expiry_forever');

    btn.disabled = true;
    btn.innerHTML = (lang === 'en') ? '<i class="fas fa-spinner fa-spin"></i> Saving...' : '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(this);
    
    // PENTING: Kirim bahasa ke Controller agar pesan "Data Sama" bisa diterjemahkan di sana
    formData.append('selectedLang', lang);

    if (cbForever && cbForever.checked) {
        formData.set('identity_expiry', 'Seumur Hidup');
    }

    try {
        const response = await fetch("{{ route('pdm.index') }}", {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        // Baca JSON sebelum cek response.ok agar data error (422) terbaca
        const res = await response.json();

        if (response.ok && res.success) {
            Swal.fire({
                title: (lang === 'en') ? 'Saved Successfully!' : 'Berhasil Disimpan!',
                text: (lang === 'en') ? 'Please wait, synchronizing data...' : 'Mohon tunggu, sedang menyinkronkan data...',
                icon: 'success',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
            setTimeout(() => { window.location.reload(); }, 1500);

        } else {
            // Menampilkan pesan langsung dari res.message (Controller)
            let errorMsg = res.message || (lang === 'en' ? 'Failed to save data.' : 'Gagal menyimpan data.');
            
            // Jika ada detail error validasi dari Laravel
            if (res.errors) errorMsg = Object.values(res.errors).flat().join('<br>');
            
            Swal.fire({ 
                icon: 'error', 
                title: (lang === 'en') ? 'Failed' : 'Gagal', 
                html: errorMsg 
            });
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }

    } catch (err) {
        console.error("Submit Error:", err);
        Swal.fire(
            (lang === 'en') ? 'Error' : 'Error', 
            (lang === 'en') ? 'Connection error occurred.' : 'Terjadi kesalahan koneksi.', 
            'error'
        );
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
    const lang = localStorage.getItem('selectedLang') || 'id'; // Ambil bahasa aktif

    if (!cbForever || !dateInput) return;

    if (cbForever.checked) {
        dateInput.type = "text"; 
        dateInput.value = ''; 
        
        // Logika Teks Multibahasa untuk Placeholder
        dateInput.placeholder = (lang === 'en') ? "LIFETIME" : "SEUMUR HIDUP";
        
        dateInput.disabled = true;
        dateInput.style.backgroundColor = "#e9ecef"; 
    } else {
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
    const dobInput = document.getElementById('dob');
    if (!dobInput || !dobInput.value) return;

    const birthDate = new Date(dobInput.value);
    const today = new Date();
    
    // Hitung batas minimal (Hari ini dikurangi 18 tahun)
    const minAgeDate = new Date();
    minAgeDate.setFullYear(today.getFullYear() - 18);

    // Jika birthDate lebih besar dari minAgeDate, berarti belum 18 tahun
    if (birthDate > minAgeDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Usia Tidak Mencukupi',
            text: 'Sesuai UU Ketenagakerjaan, usia minimal karyawan adalah 18 tahun.',
            confirmButtonColor: '#d33'
        });
        dobInput.value = ''; // Reset input
    }
}

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

document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 PDM Dashboard Ready");
    
    window.isInitializing = true; // Kunci logic agar tidak konflik saat fill data

    updateUploadCountsUI();
    updateFilePreviewUI();

    if (typeof resetSessionTimeout === "function") resetSessionTimeout();

    handleSessionLogin();

    // LOAD DATA SERVER
    if (typeof serverData !== 'undefined' && serverData) {
        // Pastikan variabel cache diisi dari data server sebelum form di-fill
        window.tanggunganCache.child_count = serverData.child_count || 0;
        
        if (typeof fillFormWithData === 'function') {
            fillFormWithData(serverData);
        }
    }

    initEventListeners();

    // INIT UI STATE
    updateStatusLogic();
    
    window.isInitializing = false; // Buka kunci setelah semua selesai
    console.log("✅ Init selesai");
});

function handleSessionLogin() {
    const isLoggedIn = sessionStorage.getItem("isLoggedIn");

    if (isLoggedIn !== "true") return;

    const loginSection = document.getElementById("loginSection");
    const formSection = document.getElementById("formSection");

    if (loginSection) loginSection.style.display = "none";
    if (formSection) formSection.style.display = "block";

    const savedData = JSON.parse(sessionStorage.getItem("userData") || "{}");

    if (savedData?.employee_no) {
        loadUserData(savedData.employee_no);
    }

    if (typeof startSessionTimer === "function") {
        startSessionTimer();
    }
}

function initEventListeners() {
    const genderSelect = document.querySelector('select[name="gender"]');
    const maritalStatusSelect = document.getElementById('marital_status');
    const childCountSelect = document.getElementById('child_count');
    const hasChildrenCheck = document.getElementById('has_children');

    if (genderSelect) {
        genderSelect.addEventListener('change', function () {
            if (typeof serverData !== 'undefined') {
                serverData.gender = this.value;
            }
            updateStatusLogic();
        });
    }

    if (maritalStatusSelect) {
        maritalStatusSelect.addEventListener('change', updateStatusLogic);
    }

    if (hasChildrenCheck) {
        hasChildrenCheck.addEventListener('change', updateStatusLogic);
    }

    if (childCountSelect) {
        childCountSelect.addEventListener('change', updateStatusLogic);
    }

    console.log("🎯 Event listeners aktif");
}

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

/**
 * ============================================================
 * 1. DEFINISI GLOBAL & CACHE (Paling Atas)
 * ============================================================
 */
if (typeof window.tanggunganCache === 'undefined') {
    window.tanggunganCache = {
        child_count: {{ $employee->child_count ?? 0 }},
        spouse_name: "{{ $employee->spouse_name ?? '' }}",
        spouse_dob: "{{ $employee->spouse_dob ?? '' }}",
        spouse_relation: "{{ $employee->spouse_relation ?? '' }}"
    };
}
window.lastTanggunganCount = -1;
window.isInitializing = false;

/**
 * ============================================================
 * 2. FUNGSI UTAMA: updateStatusLogic
 * ============================================================
 */
function updateStatusLogic() {
    const genderEl = document.querySelector('select[name="gender"]');
    const statusSelectionEl = document.getElementById('marital_status');
    const hasTanggunganCheck = document.getElementById('has_children');
    const childCountSelect = document.getElementById('child_count');
    
    if (!genderEl || !statusSelectionEl || !hasTanggunganCheck || !childCountSelect) return;

    const gender = genderEl.value; 
    const isMarried = (statusSelectionEl.value === 'Married');
    const hasTanggungan = hasTanggunganCheck.checked;
    let jmlAnak = parseInt(childCountSelect.value) || 0;

    // Elemen Container
    const tanggunganSection = document.getElementById('tanggungan_section'); // Box Biru Utama
    const familySection = document.getElementById('familySection');         // Box Data Pasangan
    const anakSelectionArea = document.getElementById('anak_selection_area'); // Box Dropdown & Detail Anak
    const detailArea = document.getElementById('detail_tanggungan_area');    // Tempat Input Nama Anak
    
    // Elemen Output Otomatis
    const statusPajakInput = document.getElementById('ptkp_status');
    const familyStatusInput = document.getElementById('family_status');

    // 1. Logika Box Biru Utama (Tanggungan Section)
    // Muncul jika Menikah (isi pasangan) ATAU Centang Tanggungan Aktif (isi anak)
    if (tanggunganSection) {
        tanggunganSection.style.display = (isMarried || hasTanggungan) ? 'block' : 'none';
    }

    // 2. Logika Data Pasangan
    if (familySection) {
        familySection.style.display = isMarried ? 'block' : 'none';
        if (isMarried) {
            const hubPasangan = document.getElementById('spouse_relation');
            if (hubPasangan) {
                hubPasangan.value = (gender === "Male") ? "Istri" : "Suami";
            }
        }
    }

    // 3. Logika Dropdown Jumlah Anak & Form Detail
    if (anakSelectionArea) {
        if (hasTanggungan) {
            // Tampilkan dropdown jumlah anak jika centang aktif
            anakSelectionArea.style.display = 'block';
            childCountSelect.disabled = false;

            // Jika jumlah > 0, buatkan form nama anak
            if (jmlAnak > 0) {
                if (!window.isInitializing && (jmlAnak !== window.lastTanggunganCount || detailArea.innerHTML.trim() === "")) {
                    generateDetailTanggungan(jmlAnak);
                    window.lastTanggunganCount = jmlAnak;
                }
            } else {
                // Jika centang aktif tapi jumlah 0 (Bisa dipilih sekarang!)
                detailArea.innerHTML = "";
                window.lastTanggunganCount = 0;
            }
        } else {
            // Sembunyikan dropdown anak & reset jumlah jika centang mati
            anakSelectionArea.style.display = 'none';
            childCountSelect.value = "0";
            childCountSelect.disabled = true;
            detailArea.innerHTML = "";
            window.lastTanggunganCount = -1;
            jmlAnak = 0; // Pastikan jmlAnak di-reset untuk hitungan pajak bawah
        }
    }

    // 4. Update Kode Pajak & Status Internal
    if (statusPajakInput) {
        if (statusSelectionEl.value === "" || gender === "") {
            statusPajakInput.value = "-";
        } else if (gender === "Perempuan") {
            statusPajakInput.value = "TK/0"; 
        } else {
            let kode = isMarried ? "K" : "TK";
            statusPajakInput.value = kode + "/" + Math.min(jmlAnak, 3);
        }
    }

    if (familyStatusInput) {
        let kodeStatus = isMarried ? "M" : "S";
        familyStatusInput.value = (statusSelectionEl.value === "") ? "-" : kodeStatus + jmlAnak;
    }

    if (typeof updateContent === 'function') updateContent();
}

let existingChildren = [];

function generateDetailTanggungan(jml) {
    const container = document.getElementById('detail_tanggungan_area');
    if (!container) return;
    
    // Pastikan fungsi cache Anda sudah terdefinisi
    if (typeof saveCurrentInputToCache === 'function') saveCurrentInputToCache();
    
    container.innerHTML = "";
    
    // Gunakan limit 3 atau sesuai kebutuhan Anda
    const limit = Math.min(jml, 3); 
    
    // Ambil status bahasa (cek apakah variabel lang ada, jika tidak default ke 'id')
    const currentLang = (typeof lang !== 'undefined') ? lang : 'id';

    for(let i = 1; i <= limit; i++) {
        const valName = tanggunganCache[`child_${i}_name`] || "";
        const valRel = tanggunganCache[`child_${i}_relation`] || "Anak Kandung";
        const valDob = tanggunganCache[`child_${i}_dob`] || "";

        // Logika Placeholder Dinamis
        const namePlaceholder = (currentLang === 'en') ? "Full Name" : "Nama Lengkap";

        container.innerHTML += `
            <div class="tanggungan-item mb-3 p-2" style="border: 1px dashed #ced4da; border-radius: 5px; background: #fff;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="label-req" style="font-size: 10px; color: #0d6efd; font-weight:700;" 
                            data-id="NAMA ANAK ${i}" data-en="CHILD NAME ${i}">NAMA ANAK ${i}</label>
                        <input type="text" name="child_${i}_name" 
           class="form-control form-control-sm" 
           data-id-placeholder="Nama Lengkap" 
           data-en-placeholder="Full Name" 
           value="${valName}"
                            onchange="updateCache(this)" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label style="font-size: 10px;" data-id="HUBUNGAN" data-en="RELATION">HUBUNGAN</label>
                        <select name="child_${i}_relation" class="form-select form-select-sm" onchange="updateCache(this)">
                            <option value="Anak Kandung" data-id="Anak Kandung" data-en="Biological Child" ${valRel === 'Anak Kandung' ? 'selected' : ''}>Anak Kandung</option>
                            <option value="Anak Angkat" data-id="Anak Angkat" data-en="Adopted Child" ${valRel === 'Anak Angkat' ? 'selected' : ''}>Anak Angkat</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="label-req" style="font-size: 10px;" data-id="TGL LAHIR" data-en="BIRTH DATE">TGL LAHIR</label>
                        <input type="date" name="child_${i}_dob" class="form-control form-control-sm" 
                            value="${valDob}" onchange="updateCache(this)" required>
                    </div>
                </div>
            </div>`;
    }

    // Jalankan translasi untuk elemen dengan data-id/data-en
    if (typeof updateContent === 'function') updateContent();
}

    // Fungsi untuk membatasi hanya input angka
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // Fungsi untuk mengecek panjang karakter (misal KTP harus 16 digit)
function checkExactLength(input, targetLength, labelId) {
    const val = input.value;
    const lang = localStorage.getItem('selectedLang') || 'id';
    const labelEl = document.getElementById(labelId);
    
    // --- LOGIKA AUTO-LOOKUP LABEL (OPTIMIZED) ---
    let finalLabel = labelId; // Fallback jika tidak ada label

    if (labelEl) {
        // Prioritas 1: Ambil dari data-msg (untuk pesan error yang bersih)
        // Prioritas 2: Ambil dari data-id/en (jika data-msg tidak ada)
        // Prioritas 3: Ambil dari innerText (jika atribut data kosong)
        finalLabel = labelEl.getAttribute(`data-msg-${lang}`) || 
                     labelEl.getAttribute(`data-${lang}`) || 
                     labelEl.innerText.replace(/\(\d+ DIGIT\)/gi, '').trim(); 
                     // .replace di atas adalah backup untuk menghapus "(13 DIGIT)" otomatis jika lupa pasang data-msg
    }

    // --- IDENTIFIKASI ELEMEN ERROR ---
    let errorId = 'err-' + (input.id || input.name);
    let errorSpan = document.getElementById(errorId);
    
    if (!errorSpan) {
        errorSpan = document.createElement('div');
        errorSpan.id = errorId;
        errorSpan.className = 'error-message text-danger fw-bold';
        errorSpan.style.fontSize = '11px';
        errorSpan.style.marginTop = '4px';
        input.insertAdjacentElement('afterend', errorSpan);
    }

    // --- LOGIKA VALIDASI ---
    if (val.length === 0) {
        input.classList.remove('is-invalid', 'is-valid');
        errorSpan.style.display = 'none';
    } 
    else if (val.length === targetLength) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid'); 
        errorSpan.style.display = 'none'; 
        errorSpan.innerHTML = '';
    } 
    else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        
        const msg = (lang === 'en') 
            ? `⚠️ ${finalLabel} must be ${targetLength} digits (Current: ${val.length})`
            : `⚠️ ${finalLabel} harus ${targetLength} digit (Saat ini: ${val.length})`;

        errorSpan.innerHTML = msg;
        errorSpan.style.display = 'block';
    }
}

    // Tambahkan ini di dalam tag <script> Anda
    function cleanNonNumber(el) {
        // Menghapus semua karakter yang bukan angka
        el.value = el.value.replace(/\D/g, '');
    }

    function updateUploadCountsUI() {
    // Pastikan variabel 'counts' tersedia secara global
    // Anda bisa menaruh ini di bagian atas script Blade: 
    // const counts = @json($uploadCounts);

    console.log("Memperbarui UI Batas Upload...", typeof counts !== 'undefined' ? counts : 'Data tidak ditemukan');
    
    // Mapping: ID Elemen Teks -> Key di Database -> ID Elemen Input File
    const statusMapping = {
        ktp:    { key: 'ktp',    inputId: 'identity_url' },
        ijazah: { key: 'ijazah', inputId: 'education_certificate_url' },
        bank:   { key: 'bank',   inputId: 'bank_book_url' },
        npwp:   { key: 'npwp',   inputId: 'npwp_url' },
        kk:     { key: 'kk',     inputId: 'family_card_url' }
    };

    Object.keys(statusMapping).forEach(id => {
        const config = statusMapping[id];
        const elStatus = document.getElementById("count_" + id);
        
        if (elStatus) {
            // Ambil angka dari variabel global 'counts'
            const currentCount = (typeof counts !== 'undefined' && counts[config.key]) ? counts[config.key] : 0;
            
            elStatus.innerHTML = `Batas Upload: ${currentCount}/2`;
            
            // Jika sudah mencapai limit 2
            if (currentCount >= 2) {
                elStatus.style.color = "#dc3545"; // Merah bootstrap
                elStatus.style.fontWeight = "bold";
                elStatus.innerHTML += " <span class='badge bg-danger ms-1'>Limit Tercapai</span>";
                
                // Cari elemen input file berdasarkan ID aslinya
                const inputEl = document.getElementById(config.inputId);
                if (inputEl) {
                    inputEl.disabled = true;
                    inputEl.style.backgroundColor = "#e9ecef";
                    inputEl.placeholder = "Batas upload tercapai";
                }
            } else {
                // Reset jika masih di bawah limit (penting jika ada fitur delete nantinya)
                elStatus.style.color = "#6c757d";
                elStatus.style.fontWeight = "normal";
                const inputEl = document.getElementById(config.inputId);
                if (inputEl) inputEl.disabled = false;
            }
        }
    });
}

/**
 * ============================================================
 * LOGIKA TRANSLASI (MULTILANGUAGE)
 * ============================================================
 */

    function updateContent() {
        // Ambil bahasa dari localStorage, default ke 'id'
        const lang = localStorage.getItem('selectedLang') || 'id'; 

        // 1. Update Label, Span, dan Option
        document.querySelectorAll('[data-id]').forEach(el => {
            const text = el.getAttribute(`data-${lang}`);
            if (text) {
                if (el.tagName === 'OPTION') {
                    el.text = text;
                } else {
                    // Gunakan innerText agar tidak merusak elemen icon di dalamnya jika ada
                    el.innerText = text;
                }
            }
        });

        // 2. Update Placeholder Input
        // Konsisten menggunakan atribut: data-id-placeholder dan data-en-placeholder
        document.querySelectorAll('input[data-id-placeholder], textarea[data-id-placeholder]').forEach(input => {
            const placeholder = input.getAttribute(`data-${lang}-placeholder`);
            if (placeholder) {
                input.placeholder = placeholder;
            }
        });

        // 3. Update Text Khusus (seperti Limit Upload)
        document.querySelectorAll('.upload-limit-text').forEach(el => {
            const text = el.getAttribute(`data-${lang}`);
            if (text) el.textContent = text;
        });

        console.log(`🌐 UI Switched to: ${lang.toUpperCase()}`);

        // Validasi ulang semua field yang sedang error saat ganti bahasa
        document.querySelectorAll('.is-invalid').forEach(input => {
            // Memicu event input secara manual agar pesan error ter-update bahasanya
            input.dispatchEvent(new Event('input'));
        });

        document.querySelectorAll('.btn-warning').forEach(btn => {
            // Cek apakah ini tombol preview file
            if (btn.closest('.view-container')) {
                const icon = '👁️ ';
                btn.innerText = (lang === 'en') 
                    ? icon + 'View File (Not Saved Yet)' 
                    : icon + 'Lihat Berkas (Belum Simpan)';
            }
        });

        const cbForever = document.getElementById('identity_expiry_forever');
        if (cbForever && cbForever.checked) {
            toggleidentity_expiry(); // Panggil ulang fungsi untuk memperbarui placeholder
        }

        updateFilePreviewUI();
    }

    function switchLanguage(lang) {
        localStorage.setItem('selectedLang', lang);
        
        // Update teks statis
        updateContent();

        // Update logika status (karena ada teks 'Menikah' atau 'Single' yang mungkin divalidasi)
        if (typeof updateStatusLogic === 'function') {
            updateStatusLogic(); 
        }
    }

// Jalankan saat pertama kali load
document.addEventListener('DOMContentLoaded', () => {
    updateContent();
});

/**
 * Catatan: 
 * Kode di bawah ini sebelumnya menyebabkan error karena variabel 'lang' tidak dikenal.
 * Sekarang dipindahkan ke dalam updateContent() atau dibungkus fungsi.
 */

/**
 * Fungsi Validasi Universal Multibahasa
 * @param {HTMLElement} input - Elemen input (this)
 * @param {number} targetLength - Panjang digit yang diinginkan
 * @param {string} labelId - ID dari elemen <label> terkait
 */
function handleValidation(input, targetLength, labelId) {
    const lang = localStorage.getItem('selectedLang') || 'id';
    const labelEl = document.getElementById(labelId);
    
    // AMBIL TEKS DARI ATRIBUT data-id ATAU data-en
    // Jika elemen label tidak ditemukan, baru pakai fallback ID-nya
    let labelText = labelEl ? labelEl.getAttribute(`data-${lang}`) : labelId;

    // Pastikan labelText tidak null atau undefined
    if (!labelText) {
        labelText = labelEl ? labelEl.innerText : labelId;
    }

    // Panggil fungsi pengecekan
    checkExactLength(input, targetLength, labelText);

    // Bersihkan karakter non-angka
    if (typeof cleanNonNumber === 'function') {
        cleanNonNumber(input);
    }
}

</script>
</body>
</html>