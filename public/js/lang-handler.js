// function applyLanguage(lang) {
//     const t = translations[lang];
//     if (!t) return;

//     localStorage.setItem('selectedLang', lang);

//     // Update Elemen Login
//     const elements = {
//         'txt_login_title': t.title,
//         'lbl_employee_no': t.employee_no,
//         'lbl_password': t.password,
//         'btnForgotPass': t.btn_forgot
//     };

//     for (let id in elements) {
//         const el = document.getElementById(id);
//         if (el) el.innerText = elements[id];
//     }

//     // Update HTML Content (yang pakai tag <strong> atau icon)
//     if (document.getElementById('txt_note')) document.getElementById('txt_note').innerHTML = t.note;
//     if (document.getElementById('btnLogin')) document.getElementById('btnLogin').innerHTML = t.btn_login;
//     if (document.getElementById('logUser')) document.getElementById('logUser').placeholder = t.placeholder_emp;

//     // Update Template Modal
//     const template = document.getElementById('changePasswordTemplate');
//     if (template) {
//         const content = template.content;
//         content.querySelector('p').innerText = t.modal_subtitle;
//         const labels = content.querySelectorAll('label');
//         if (labels.length >= 6) {
//             labels[0].innerText = t.employee_no;
//             labels[1].innerText = t.modal_dob_label;
//             labels[2].innerText = t.modal_email_label;
//             labels[3].innerText = t.modal_otp_label;
//             labels[4].innerText = t.modal_new_pass;
//             labels[5].innerText = t.modal_conf_pass;
//         }
//         content.querySelector('#btnResendOtp').innerText = t.modal_btn_send_otp;
//         content.querySelector('.form-text').innerText = t.modal_otp_note;
//         content.querySelector('#req_min').innerHTML = `<i class="fas fa-times-circle"></i> ${t.req_min}`;
//         content.querySelector('#req_caps').innerHTML = `<i class="fas fa-times-circle"></i> ${t.req_caps}`;
//         content.querySelector('#req_num').innerHTML = `<i class="fas fa-times-circle"></i> ${t.req_num}`;
//         content.querySelector('#req_sym').innerHTML = `<i class="fas fa-times-circle"></i> ${t.req_sym}`;
//         content.querySelector('#req_match').innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${t.req_match}`;
//     }
// }

// // --- LOGIKA INISIALISASI BAHASA ---
// document.addEventListener('DOMContentLoaded', () => {
//     const langSelect = document.getElementById('langSelect');
//     const mainCard = document.getElementById('mainCard');
    
//     // 1. Ambil bahasa tersimpan, default ke 'id'
//     const savedLang = localStorage.getItem('selectedLang') || 'id';
    
//     // 2. Sinkronkan Dropdown Select
//     if (langSelect) {
//         langSelect.value = savedLang;
//         // Tambahkan event listener agar saat user ganti bahasa, teks langsung berubah
//         langSelect.addEventListener('change', (e) => applyLanguage(e.target.value));
//     }
    
//     // 3. Jalankan translasi SEGERA
//     applyLanguage(savedLang);

//     // 4. (Opsional) Tampilkan card setelah teks siap agar mulus
//     if (mainCard) {
//         mainCard.classList.add('ready');
//     }
// });

/**
 * HR-Master Language Handler
 * Mengelola translasi UI secara client-side menggunakan translations.js
 */

function applyLanguage(lang) {
    // 1. Validasi Kamus (Memastikan variabel 'translations' dari translations.js tersedia)
    const t = typeof translations !== 'undefined' ? translations[lang] : null;
    if (!t) {
        console.error(`Kamus untuk bahasa "${lang}" tidak ditemukan.`);
        return;
    }

    // 2. Simpan pilihan bahasa ke localStorage agar persisten saat refresh
    localStorage.setItem('selectedLang', lang);

    // 3. Update Teks Berdasarkan atribut [data-i18n]
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key]) {
            // Gunakan innerHTML untuk elemen yang mengandung Icon atau tag HTML (seperti <strong>)
            // Cek berdasarkan tag atau class tertentu
            const needsHtml = el.tagName === 'BUTTON' || 
                             el.classList.contains('login-note') || 
                             el.tagName === 'A' ||
                             t[key].includes('<');

            if (needsHtml) {
                el.innerHTML = t[key];
            } else {
                el.innerText = t[key];
            }
        }
    });

    // 4. Update Placeholder Input [data-i18n-placeholder]
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (t[key]) {
            el.placeholder = t[key];
        }
    });

    // 5. Update Elemen di Dalam Template (PENTING untuk Modal)
    const template = document.getElementById('changePasswordTemplate');
    if (template) {
        // Kita mengupdate content dari template agar saat modal dibuka, teksnya sudah benar
        const content = template.content;
        
        // Cari elemen berdasarkan atribut data-i18n di dalam template
        content.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (t[key]) {
                el.innerHTML = t[key];
            }
        });

        content.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            if (t[key]) {
                el.placeholder = t[key];
            }
        });
    }

    // 6. ANTI-FLASH: Beri tanda bahwa translasi selesai
    const mainCard = document.getElementById('mainCard');
    if (mainCard) {
        mainCard.classList.add('lang-ready');
        mainCard.style.opacity = '1'; // Memastikan card muncul
    }
}

/**
 * Inisialisasi Saat Halaman Dimuat
 */
document.addEventListener('DOMContentLoaded', () => {
    const langSelect = document.getElementById('langSelect');
    
    // Ambil bahasa terakhir dari storage, default ke Indonesia ('id')
    const savedLang = localStorage.getItem('selectedLang') || 'id';

    // Sinkronkan nilai Dropdown dengan bahasa yang aktif
    if (langSelect) {
        langSelect.value = savedLang;
        langSelect.addEventListener('change', (e) => {
            applyLanguage(e.target.value);
            // Opsional: Refresh halaman jika ada logika backend yang bergantung pada locale
            // location.reload(); 
        });
    }

    // Jalankan translasi pertama kali saat halaman siap
    applyLanguage(savedLang);
});