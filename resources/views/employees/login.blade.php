<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Master</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pdm-style.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/lang-handler.js') }}"></script>

    <style>
        #loginSection {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .password-requirements div {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>

<div id="loginSection">
    <div class="login-card text-center" id="mainCard">

        <div class="text-end mb-2">
            <select id="langSelect" class="form-select form-select-sm d-inline-block w-auto border-0 bg-transparent fw-bold">
                <option value="id">🇮🇩 ID</option>
                <option value="en">🇺🇸 EN</option>
            </select>
        </div>

        <i class="fas fa-user-shield text-primary mb-3" style="font-size: 3rem;"></i>
        <h4 class="fw-bold" data-i18n="title">Login Karyawan</h4>
        <hr>

        <form id="formLogin" method="POST" action="{{ route('login.proses') }}">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold" data-i18n="employee_no">NO. KARYAWAN</label>
                <input type="text" name="employee_no" id="logUser" class="form-control" data-i18n-placeholder="placeholder_emp" required autofocus>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label small fw-bold" data-i18n="password">PASSWORD</label>
                <div class="input-group">
                    <input type="password" name="password" id="logPass" class="form-control" placeholder="ddmmyyyy" required>
                    <span class="input-group-text" id="togglePass" style="cursor: pointer; background: white;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
                <div class="login-note mt-2 text-start" data-i18n="note">
                    <strong>Info:</strong> Gunakan 8 digit tanggal lahir (ddmmyyyy) jika Anda belum mengubah password.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnLogin" data-i18n="btn_login">
                <i class="fas fa-sign-in-alt"></i> MASUK
            </button>

            <div class="mt-3 text-start">
                <a href="javascript:void(0)" id="btnForgotPass" class="text-decoration-none small fw-bold" data-i18n="btn_forgot">Lupa Password?</a>
            </div>
        </form>
    </div>
</div>

<template id="changePasswordTemplate">
    <div class="text-start">
        <p class="small text-muted mb-3 border-bottom pb-2 text-center" data-i18n="modal_subtitle">
            Verifikasi identitas diperlukan untuk keamanan akun baru.
        </p>
        
        <div class="mb-3">
            <label class="small fw-bold mb-1" data-i18n="employee_no">NO. KARYAWAN</label>
            <input type="text" id="swal_emp_no" class="form-control bg-light" readonly>
        </div>

        <div class="mb-3">
            <label class="small fw-bold mb-1" data-i18n="modal_dob_label">TANGGAL LAHIR (DDMMYYYY)</label>
            <input type="text" id="swal_dob" class="form-control" data-i18n-placeholder="modal_dob_placeholder" placeholder="Contoh: 16091995">
        </div>

        <div class="mb-3">
            <label class="small fw-bold mb-1" data-i18n="modal_email_label">EMAIL KONFIRMASI</label>
            <input type="text" id="swal_email_masked" class="form-control bg-light" readonly>
        </div>
    
        <div class="mb-3">
            <label class="small fw-bold mb-1" data-i18n="modal_otp_label">KODE OTP (CEK EMAIL)</label>
            <div class="input-group">
                <input type="text" id="swal_otp" class="form-control" data-i18n-placeholder="modal_otp_placeholder" placeholder="6 digit kode" maxlength="6">
                <button class="btn btn-primary btn-sm" type="button" id="btnResendOtp" data-i18n="modal_btn_send_otp">Klik untuk Kirim OTP</button>
            </div>
            <div class="form-text" style="font-size: 0.7rem;" data-i18n="modal_otp_note">OTP berlaku 5 menit.</div>
        </div>

        <hr>

        <div class="mb-3">
            <label class="small fw-bold mb-1" data-i18n="modal_new_pass">PASSWORD BARU</label>
            <input type="password" id="swal_new_password" class="form-control" data-i18n-placeholder="modal_new_pass_placeholder" placeholder="Minimal 8 karakter">
            <div class="password-requirements mt-2" style="font-size: 0.75rem;">
                <div id="req_min" class="text-danger" data-i18n="req_min"><i class="fas fa-times-circle"></i> Minimal 8 Karakter</div>
                <div id="req_caps" class="text-danger" data-i18n="req_caps"><i class="fas fa-times-circle"></i> Minimal 1 Huruf Kapital</div>
                <div id="req_num" class="text-danger" data-i18n="req_num"><i class="fas fa-times-circle"></i> Minimal 1 Angka</div>
                <div id="req_sym" class="text-danger" data-i18n="req_sym"><i class="fas fa-times-circle"></i> Minimal 1 Simbol</div>
            </div>
        </div>
        
        <div class="mb-2">
            <label class="small fw-bold mb-1" data-i18n="modal_conf_pass">KONFIRMASI PASSWORD</label>
            <input type="password" id="swal_confirm_password" class="form-control" data-i18n-placeholder="modal_conf_pass_placeholder" placeholder="Ulangi password">
            <div id="req_match" class="text-danger mt-1" style="font-size: 0.75rem; display: none;" data-i18n="req_match">
                <i class="fas fa-exclamation-triangle"></i> Password tidak cocok!
            </div>
        </div>
    </div>
</template>

<script>
    // --- KONFIGURASI GLOBAL & HELPER ---
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper untuk mengambil bahasa yang sedang aktif
    const getLang = () => localStorage.getItem('selectedLang') || 'id';

    // Helper untuk mengambil teks dari kamus (translations.js)
    const getText = (key) => {
        const lang = getLang();
        return (translations[lang] && translations[lang][key]) ? translations[lang][key] : key;
    };

    const formLogin = document.getElementById('formLogin');
    let isPasswordSuccessfullyChanged = false;

    // --- 1. PROSES LOGIN UTAMA ---
    formLogin.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const user = document.getElementById('logUser').value;
        const pass = document.getElementById('logPass').value;
        const btn  = document.getElementById('btnLogin');

        // Tampilkan Status Loading (Sesuai Bahasa)
        btn.disabled = true;
        // Mengambil kunci 'verify' dari kamus (Verifikasi... / Verifying...)
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${getText('verify')}`;

        try {
            const response = await fetch("{{ route('login.proses') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ employee_no: user, password: pass })
            });

            const res = await response.json();
            
            // Kembalikan tombol ke teks awal (MASUK / LOGIN)
            btn.disabled = false;
            btn.innerHTML = getText('btn_login');

            if (res.success) {
                // Update CSRF jika dikirimkan oleh server
                if (res.new_csrf_token) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', res.new_csrf_token);
                }

                // Simpan data user ke session storage
                sessionStorage.setItem('userData', JSON.stringify({
                    employee_no: res.employee_no,
                    email: res.email
                }));

                // Cek apakah butuh ganti password atau langsung ke dashboard
                if (res.force_password_change) {
                    setTimeout(() => { showChangePasswordModal(); }, 500);
                } else {
                    Swal.fire({ 
                        icon: 'success', 
                        title: getText('success') || 'Berhasil', 
                        text: getText('welcome') || 'Selamat Datang!', 
                        timer: 1500, 
                        showConfirmButton: false 
                    })
                    .then(() => { 
                        window.location.replace("{{ route('dashboard') }}"); 
                    });
                }
            } else {
                // --- BAGIAN ELSE UNTUK PASSWORD SALAH ---
                const errorMessage = res.message_key ? getText(res.message_key) : (res.message || getText('failed'));

                Swal.fire({
                    icon: 'error',
                    title: getText('failed'), 
                    text: errorMessage,
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    // Opsional: Kosongkan password & fokuskan kembali jika salah
                    document.getElementById('logPass').value = '';
                    document.getElementById('logPass').focus();
                });
            }
        } catch (err) {
            // Handle error jaringan atau sistem
            btn.disabled = false;
            btn.innerHTML = getText('btn_login');
                Swal.fire({ icon: 'error', title: 'Error', text: getText('sys_error')
            });
        }
    });

    // 2. MODAL GANTI PASSWORD (MULTILINGUAL)
    function showChangePasswordModal(isForgot = false) {
        const userData = JSON.parse(sessionStorage.getItem('userData'));
        if (!userData) return location.reload();

        const maskedEmail = userData.email.replace(/^(..)(.*)(?=@)/, (match, p1, p2) => p1 + '*'.repeat(p2.length));

        Swal.fire({
            title: getText('modal_title') || 'Verifikasi & Ganti Password',
            html: document.getElementById('changePasswordTemplate').innerHTML,
            confirmButtonText: getText('modal_btn_save') || 'Verifikasi & Simpan',
            showCancelButton: true,
            cancelButtonText: getText('btn_cancel') || 'Batal',
            confirmButtonColor: '#0d6efd',
            allowOutsideClick: false,
            didOpen: () => {
                const popup = Swal.getPopup();
                popup.querySelector('#swal_emp_no').value = userData.employee_no;
                popup.querySelector('#swal_email_masked').value = maskedEmail;

                // --- FITUR ENTER TO NEXT / SUBMIT ---
                const inputDob = popup.querySelector('#swal_dob');
                const inputOtp = popup.querySelector('#swal_otp');
                const inputNewPass = popup.querySelector('#swal_new_password');
                const inputConfPass = popup.querySelector('#swal_confirm_password');

                const enterToNext = (current, next) => {
                    current.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            next.focus();
                        }
                    });
                };

                enterToNext(inputDob, inputOtp);
                enterToNext(inputOtp, inputNewPass);
                enterToNext(inputNewPass, inputConfPass);

                inputConfPass.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        Swal.clickConfirm();
                    }
                });

                // Validasi Password Real-time
                inputNewPass.addEventListener('input', (e) => validatePassword(e.target.value));

                // Tombol Kirim OTP (Diterjemahkan)
                const btnResend = popup.querySelector('#btnResendOtp');
                btnResend.onclick = async () => {
                    btnResend.disabled = true;
                    btnResend.innerText = getText('sending') || 'Mengirim...';
                    
                    await sendOtpRequest(userData.employee_no);
                    
                    btnResend.disabled = false;
                    btnResend.innerText = getText('resend_otp') || 'Kirim Ulang';
                    btnResend.classList.replace('btn-primary', 'btn-outline-secondary');
                };
            },
            
            preConfirm: () => {
                const popup = Swal.getPopup();
                const dob = popup.querySelector('#swal_dob').value;
                const otp = popup.querySelector('#swal_otp').value;
                const newPass = popup.querySelector('#swal_new_password').value;
                const confPass = popup.querySelector('#swal_confirm_password').value;

                // Pesan Validasi (Diterjemahkan)
                if (!dob || otp.length < 6) {
                    Swal.showValidationMessage(getText('val_dob_otp') || 'Input Tanggal Lahir & OTP!');
                    return false;
                }
                if (!checkAllRequirements(newPass) || newPass !== confPass) {
                    Swal.showValidationMessage(getText('val_pass_mismatch') || 'Password baru tidak sesuai kriteria atau tidak cocok!');
                    return false;
                }
                return { dob, otp, password: newPass, employee_no: userData.employee_no };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                saveNewPassword(result.value);
            } else {
                if (!isForgot && !isPasswordSuccessfullyChanged) {
                    window.location.href = "{{ route('logout') }}";
                }
            }
        });
    }

    // 3. FUNGSI KIRIM OTP (MULTILINGUAL - FRONTEND)
    async function sendOtpRequest(empNo) {
        // Ambil bahasa yang sedang aktif (id/en)
        const currentLang = localStorage.getItem('selectedLang') || 'id';

        try {
            const response = await fetch("{{ route('otp.resend') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(), 
                    'X-Requested-With': 'XMLHttpRequest'
                },
                // Tambahkan parameter lang ke dalam body
                body: JSON.stringify({ 
                    employee_no: empNo,
                    lang: currentLang 
                })
            });

            const data = await response.json();

            if (data.success) {
                // Tampilkan pesan sukses di modal Swal
                const successMsg = getText('otp_sent_success') || 'Sukses! Kode OTP telah dikirim ke email.';
                Swal.showValidationMessage(successMsg);
                
                // Hilangkan notifikasi merah/hijau setelah 4 detik agar modal bersih kembali
                setTimeout(() => { 
                    if (Swal.isVisible()) Swal.resetValidationMessage(); 
                }, 4000);
            } else {
                // Jika server kirim message_key, gunakan terjemahan. Jika tidak, pakai fallback message.
                const errorMsg = data.message_key ? getText(data.message_key) : (data.message || getText('failed'));
                Swal.showValidationMessage(`${getText('failed')}: ${errorMsg}`);
            }
        } catch (err) { 
            // Error koneksi internet atau server down
            Swal.showValidationMessage(getText('sys_error') || 'Gagal menghubungi server.');
        }
    }

    // 4. SIMPAN PASSWORD BARU
    async function saveNewPassword(payload) {
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        try {
            const res = await fetch("{{ route('password.update.first') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload) 
            });
            
            const data = await res.json();
            if (data.success) {
                isPasswordSuccessfullyChanged = true;
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Password diperbarui!', timer: 2000, showConfirmButton: false })
                .then(() => { window.location.replace("{{ route('dashboard') }}"); });
            } else {
                Swal.fire('Gagal', data.message, 'error').then(() => showChangePasswordModal());
            }
        } catch (error) {
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error').then(() => location.reload());
        }
    }

    // --- LOGIKA LUPA PASSWORD ---
    document.getElementById('btnForgotPass').addEventListener('click', async function() {
        // 1. Pop-up pertama: Minta No. Karyawan (Multilingual)
        const { value: userNo } = await Swal.fire({
            title: getText('btn_forgot'), // Lupa Password? / Forgot Password?
            input: 'text',
            inputLabel: getText('label_input_emp') || 'Masukkan Nomor Karyawan Anda',
            inputPlaceholder: getText('placeholder_emp') || 'Contoh: 2023001',
            showCancelButton: true,
            confirmButtonText: getText('verify_only') || 'Verifikasi',
            cancelButtonText: getText('btn_cancel') || 'Batal',
            confirmButtonColor: '#0d6efd',
            inputValidator: (value) => {
                if (!value) return getText('val_emp_required') || 'Nomor Karyawan wajib diisi!';
            }
        });

        if (userNo) {
            // Tampilkan loading saat cek ke database
            Swal.fire({
                title: getText('verify') || 'Verifikasi...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                // 2. Cek ke Database via Controller
                const response = await fetch("{{ route('user.get-email') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken() 
                    },
                    body: JSON.stringify({ employee_no: userNo })
                });
                
                const res = await response.json();
                Swal.close();

                if (res.success) {
                    // 3. Simpan data ke session & buka modal ganti password
                    sessionStorage.setItem('userData', JSON.stringify({
                        employee_no: userNo,
                        email: res.email
                    }));
                    
                    // Panggil modal ganti password yang sudah ditranslate sebelumnya
                    showChangePasswordModal(true); 
                } else {
                    // Gunakan message_key dari server jika ada (seperti err_user_not_found)
                    const errorMsg = res.message_key ? getText(res.message_key) : (res.message || getText('user_not_found'));
                    Swal.fire(getText('failed'), errorMsg, 'error');
                }
            } catch (err) {
                Swal.fire('Error', getText('sys_error'), 'error');
            }
        }
    });

    // --- FUNGSI VALIDASI ---
    function validatePassword(val) {
        const popup = Swal.getPopup();
        const rules = [
            { id: '#req_min', regex: /.{8,}/ },
            { id: '#req_caps', regex: /[A-Z]/ },
            { id: '#req_num', regex: /[0-9]/ },
            { id: '#req_sym', regex: /[^A-Za-z0-9]/ }
        ];

        rules.forEach(rule => {
            const el = popup.querySelector(rule.id);
            if (!el) return;

            // Cek apakah syarat terpenuhi
            const isValid = rule.regex.test(val);
            
            // Ambil teks asli (tanpa ikon) untuk menjaga translasi tetap ada
            const currentText = getText(el.getAttribute('data-i18n'));

            if (isValid) {
                el.classList.replace('text-danger', 'text-success');
                el.innerHTML = `<i class="fas fa-check-circle"></i> ${currentText}`;
            } else {
                el.classList.replace('text-success', 'text-danger');
                el.innerHTML = `<i class="fas fa-times-circle"></i> ${currentText}`;
            }
        });
    }

    function checkAllRequirements(val) {
        return /.{8,}/.test(val) && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val);
    }

    // --- TOGGLE PASSWORD LOGIN ---
    document.getElementById('togglePass').addEventListener('click', function () {
        const passInput = document.getElementById('logPass');
        const icon      = document.getElementById('eyeIcon');
        const isPass    = passInput.type === 'password';
        passInput.type  = isPass ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isPass);
        icon.classList.toggle('fa-eye-slash', isPass);
    });

    // Navigasi Enter untuk Form Login Utama
    document.getElementById('logUser').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('logPass').focus();
        }
    });
</script>
</body>
</html>