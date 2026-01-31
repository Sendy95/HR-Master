function toggleStatusEmployee(status) {
    const localFields = document.querySelectorAll('.lokal-only');
    const expatLabel = document.getElementById('city-label');

    if (status === 'Expat') {
        localFields.forEach(el => el.style.display = 'none');
        expatLabel.innerText = "CITY";
        // Matikan required untuk field lokal agar tidak error saat submit
        document.getElementsByName('ktp_rt')[0].required = false;
    } else {
        localFields.forEach(el => el.style.display = 'block');
        expatLabel.innerText = "KOTA/KABUPATEN";
        document.getElementsByName('ktp_rt')[0].required = true;
    }
}