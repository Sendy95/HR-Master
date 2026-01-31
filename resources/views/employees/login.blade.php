<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Master</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pdm-style.css') }}">    
    <style>
        #loginSection { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    </style>
</head>
<body>

<div id="loginSection">
    <div class="login-card text-center">
        <i class="fas fa-user-shield text-primary mb-3" style="font-size: 3rem;"></i>
        <h4 class="fw-bold">Login Karyawan</h4>
        <hr>

        @if($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">NO. KARYAWAN</label>
                <input type="text" name="employee_no" id="logUser" class="form-control" placeholder="Input No. Karyawan" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">PASSWORD</label>
                <div class="input-group">
                    <input type="password" name="password" id="logPass" class="form-control" placeholder="ddmmyyyy" required>
                    <span class="input-group-text" id="togglePass" style="cursor: pointer; background: white;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
                <div class="login-note mt-2 text-start">
                    <strong>Info:</strong> Gunakan 8 digit tanggal lahir (ddmmyyyy) sebagai password awal.
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="fas fa-sign-in-alt"></i> MASUK
            </button>
        </form>
    </div>
</div>

<script>
async function prosesLogin() {
    const user = document.getElementById('logUser').value;
    const pass = document.getElementById('logPass').value;
    const btn = document.getElementById('btnLogin');

    if (!user || !pass) {
        Swal.fire('Error', 'No. Karyawan dan Password wajib diisi', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifikasi...';

    try {
        const response = await fetch("{{ route('login.proses') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ user, pass })
        });

        const res = await response.json();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> MASUK';

        if (res.success) {
            sessionStorage.setItem('isLoggedIn', 'true');
            sessionStorage.setItem('userData', JSON.stringify(res.userData));

            // UI Transition
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('formSection').style.display = 'block';
            
            // Ambil data detail terbaru dari server
            await loadUserData(user);
            startSessionTimer();

            // Swal.fire({ icon: 'success', title: 'Berhasil Masuk', timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire('Gagal', res.message || 'Kredensial salah', 'error');
        }
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> MASUK';
        console.error("Login Error:", err);
        Swal.fire('Error', 'Gagal terhubung ke server. Cek koneksi atau rute Laravel.', 'error');
    }
}

    document.getElementById('togglePass').addEventListener('click', function() {
        const passInput = document.getElementById('logPass');
        const icon = document.getElementById('eyeIcon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
</body>
</html>