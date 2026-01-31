@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ url('/admin/pdm-approval') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Detail Perubahan: <strong>{{ $master->employee_name }}</strong></h5>
            <small class="text-muted">NIK: {{ $update->employee_no }}</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Nama Field</th>
                            <th>Data Saat Ini (Master)</th>
                            <th>Usulan Perubahan (Baru)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fields = [
                                'company_name' => 'Perusahaan', 'personal_email' => 'Email Pribadi',
                                'phone_1' => 'No. Telepon 1', 'bank_account_number' => 'No. Rekening',
                                'npwp_number' => 'Nomor NPWP', 'marital_status' => 'Status Nikah',
                                'ptkp_status' => 'Status Pajak', 'child_count' => 'Jumlah Anak'
                                // Tambahkan field lainnya di sini sesuai kebutuhan
                            ];
                            $foundChange = false;
                        @endphp

                        @foreach($fields as $key => $label)
                            @php
                                $valOld = $master->$key ?? '';
                                $valNew = $update->$key ?? '';
                            @endphp

                            {{-- Tampilkan hanya jika data berbeda DAN data baru tidak kosong --}}
                            @if($valOld != $valNew && !empty($valNew))
                                @php $foundChange = true; @endphp
                                <tr>
                                    <td class="px-4 fw-bold small text-muted text-uppercase">{{ $label }}</td>
                                    <td>
                                        @if(empty($valOld))
                                            <span class="badge bg-light text-muted fw-normal italic">(Kosong)</span>
                                        @else
                                            <span class="text-danger text-decoration-line-through">{{ $valOld }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $valNew }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$foundChange)
            <div class="p-4 text-center text-muted">
                Tidak ada perbedaan data teks terdeteksi. Silakan periksa dokumen terlampir di bawah.
            </div>
            @endif
        </div>

        {{-- Bagian Lampiran --}}
        <div class="card-footer bg-light p-4">
            <h6 class="fw-bold mb-3 text-uppercase small">Dokumen Pendukung</h6>
            <div class="d-flex gap-3 mb-4">
                @foreach(['identity_url' => 'KTP', 'family_card_url' => 'KK', 'npwp_url' => 'NPWP'] as $f => $l)
                    @if($update->$f)
                        <a href="{{ asset('storage/'.$update->$f) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-download me-1"></i> Lihat {{ $l }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <button onclick="processAction({{ $update->id }}, 'reject')" class="btn btn-outline-danger px-4">Tolak</button>
                <button onclick="processAction({{ $update->id }}, 'approve')" class="btn btn-success px-5 shadow-sm">Setujui Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function processAction(id, type) {
    const isApprove = type === 'approve';
    const result = await Swal.fire({
        title: isApprove ? 'Setujui Perubahan?' : 'Tolak Pengajuan?',
        text: isApprove ? "Data master karyawan akan langsung diperbarui." : "Berikan alasan jika menolak.",
        input: isApprove ? null : 'text',
        icon: isApprove ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        // Logika Fetch API ke /admin/pdm-action/${id} Anda di sini
        // ... (Gunakan script fetch yang sudah kita buat sebelumnya)
    }
}
</script>
@endsection