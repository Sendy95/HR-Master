@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Antrean Verifikasi Data (PDM)</h2>
        <span class="badge bg-primary px-3 py-2">{{ count($requests) }} Pending</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush">
            @forelse($requests as $row)
            <a href="{{ url('/admin/pdm-approval?id='.$row->id) }}" class="list-group-item list-group-item-action py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">{{ $row->employee_name }}</div>
                        <small class="text-muted">{{ $row->employee_no }} • Diajukan: {{ date('d M Y H:i', strtotime($row->created_at)) }}</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
            @empty
            <div class="p-5 text-center text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 opacity-20"></i>
                <p>Tidak ada pengajuan perubahan data saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection