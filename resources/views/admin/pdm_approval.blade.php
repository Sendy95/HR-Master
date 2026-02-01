@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    {{-- JIKA SEDANG MELIHAT DETAIL KARYAWAN --}}
    @if(request()->has('id') && isset($update))
        <div class="mb-3">
            <a href="{{ url('/admin/pdm-approval') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Detail Perubahan: <span class="text-primary">{{ $master->employee_name }}</span></h5>
                <small class="text-muted">NIK: {{ $update->employee_no }}</small>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 20%;">Nama Field</th>
                                <th style="width: 25%;">Data Saat Ini (Master)</th>
                                <th style="width: 25%;">Usulan Perubahan (Baru)</th>
                                <th class="text-center" style="width: 30%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $fields = [
                                    'bank_account_number' => 'No. Rekening',
                                    'npwp_number' => 'Nomor NPWP',
                                    'marital_status' => 'Status Nikah',
                                    'ptkp_status' => 'Status Pajak',
                                    'personal_email' => 'Email Pribadi',
                                    'phone_1' => 'No. Telepon'
                                ];
                                $found = false;
                            @endphp

                            @foreach($fields as $key => $label)
                                @php
                                    $old = $master->$key ?? '';
                                    $new = $update->$key ?? '';
                                @endphp

                                @if($old != $new && !empty($new))
                                    @php $found = true; @endphp
                                    <tr id="row-{{ $key }}">
                                        <td class="px-4 fw-bold text-muted small text-uppercase">{{ $label }}</td>
                                        <td>
                                            <span class="text-muted italic small">{{ $old ?: '(Kosong)' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">{{ $new }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div id="action-{{ $key }}" class="d-flex justify-content-center gap-2">
                                                <button onclick="updateField({{ $update->id }}, '{{ $key }}', 'approve')" 
                                                        class="btn btn-sm btn-success px-3 rounded-pill shadow-sm">
                                                    <i class="fas fa-check me-1"></i> Setuju
                                                </button>
                                                <button onclick="updateField({{ $update->id }}, '{{ $key }}', 'reject')" 
                                                        class="btn btn-sm btn-outline-danger px-3 rounded-pill shadow-sm">
                                                    <i class="fas fa-times me-1"></i> Tolak
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(!$found)
                    <div class="p-5 text-center text-muted">Tidak ada perubahan field teks terdeteksi.</div>
                @endif
            </div>

            <div class="card-footer bg-light p-4">
                <h6 class="fw-bold mb-3 small">DOKUMEN PENDUKUNG</h6>
                <div class="d-flex gap-2">
                    @if($update->identity_url) <a href="{{ asset('storage/'.$update->identity_url) }}" target="_blank" class="btn btn-sm btn-primary">Lihat KTP</a> @endif
                    @if($update->family_card_url) <a href="{{ asset('storage/'.$update->family_card_url) }}" target="_blank" class="btn btn-sm btn-primary">Lihat KK</a> @endif
                    @if($update->npwp_url) <a href="{{ asset('storage/'.$update->npwp_url) }}" target="_blank" class="btn btn-sm btn-primary">Lihat NPWP</a> @endif
                </div>
            </div>
        </div>

    {{-- VIEW DAFTAR ANTREAN --}}
    @else
        <h3 class="mb-4 fw-bold">Daftar Antrean PDM</h3>
        <div class="card shadow border-0">
            <div class="list-group list-group-flush">
                @forelse($requests as $r)
                    <a href="{{ url('/admin/pdm-approval?id='.$r->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="fw-bold text-dark">{{ $r->employee_name }}</span>
                            <div class="small text-muted">NIK: {{ $r->employee_no }}</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                @empty
                    <div class="p-5 text-center text-muted">Antrean kosong.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function updateField(id, fieldName, type) {
    const isApprove = type === 'approve';
    
    const result = await Swal.fire({
        title: isApprove ? 'Setujui?' : 'Tolak?',
        text: `Konfirmasi tindakan untuk field ini.`,
        icon: isApprove ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        confirmButtonColor: isApprove ? '#198754' : '#dc3545',
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch("{{ route('admin.pdm.updateField') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, field: fieldName, action: type })
            });

            const data = await response.json();
            if (data.success) {
                const row = document.getElementById(`row-${fieldName}`);
                const actionBox = document.getElementById(`action-${fieldName}`);
                
                row.style.backgroundColor = isApprove ? '#f0fff4' : '#fff5f5';
                actionBox.innerHTML = isApprove 
                    ? '<span class="text-success fw-bold small"><i class="fas fa-check-double"></i> BERHASIL DIUPDATE</span>' 
                    : '<span class="text-danger fw-bold small"><i class="fas fa-ban"></i> DITOLAK</span>';
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memproses data.', 'error');
        }
    }
}
</script>
@endsection