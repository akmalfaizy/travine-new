@extends('admin.layout')
@section('title', 'Proses Withdraw ' . $withdrawal->withdrawal_code)
@section('content')
<div class="admin-topbar">
    <div>
        <h1>💸 Proses Penarikan Komisi #{{ $withdrawal->withdrawal_code }}</h1>
        <small style="color:var(--text-muted);">Tujuan Mitra: <strong>{{ $withdrawal->partner?->name }}</strong> ({{ $withdrawal->partner?->email }})</small>
    </div>
    <a href="{{ route('admin.withdrawals.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="admin-alert admin-alert--error">⚠️ {{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="admin-alert admin-alert--error">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display:grid; grid-template-columns:1fr 400px; gap:24px; align-items:start;">
    {{-- Left: Details --}}
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:20px; color:var(--text-primary);">Rincian Rekening & Nominal</h3>
            <div style="background:rgba(20,184,166,0.08); border:1px solid rgba(20,184,166,0.2); padding:20px; border-radius:12px; margin-bottom:20px;">
                <span style="font-size:0.85rem; color:var(--text-muted); display:block;">Nominal Wajib Ditransfer:</span>
                <div style="font-size:2.2rem; font-weight:850; color:var(--orange,#F97316); margin-top:2px;">
                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                </div>
            </div>

            <table class="admin-table" style="font-size:0.9rem;">
                <tr>
                    <td style="font-weight:600; width:180px;">Bank Tujuan</td>
                    <td style="font-weight:700; color:var(--teal);">{{ $withdrawal->bank_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Nomor Rekening</td>
                    <td style="font-weight:700; font-family:monospace; font-size:1.1rem; color:#fff;">{{ $withdrawal->bank_account_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Nama Pemilik Rekening</td>
                    <td style="font-weight:700;">{{ $withdrawal->bank_account_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Tanggal Pengajuan</td>
                    <td style="color:var(--text-muted);">{{ $withdrawal->created_at->format('d M Y, H:i:s') }}</td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Status Saat Ini</td>
                    <td>
                        @if($withdrawal->status === 'paid')
                            <span class="badge badge-success">✓ Sudah Dibayar</span>
                        @elseif($withdrawal->status === 'requested')
                            <span class="badge badge-warning">⌛ Menunggu Diproses</span>
                        @elseif($withdrawal->status === 'rejected')
                            <span class="badge badge-danger">❌ Ditolak</span>
                        @else
                            <span class="badge badge-info">{{ $withdrawal->status_label }}</span>
                        @endif
                    </td>
                </tr>
                @if($withdrawal->transfer_proof)
                    <tr>
                        <td style="font-weight:600;">Bukti Transfer</td>
                        <td>
                            <a href="{{ Storage::url($withdrawal->transfer_proof) }}" target="_blank" class="admin-btn admin-btn--secondary admin-btn--sm">
                                📄 Lihat Bukti Transfer
                            </a>
                        </td>
                    </tr>
                @endif
                @if($withdrawal->rejection_reason)
                    <tr>
                        <td style="font-weight:600;">Alasan Penolakan</td>
                        <td style="color:#ef4444;">{{ $withdrawal->rejection_reason }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Partner Profile Box --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Profil Pemohon Mitra</h3>
            <div style="font-size:0.875rem; line-height:1.6;">
                <div><strong>Nama Mitra:</strong> {{ $withdrawal->partner?->name }}</div>
                <div><strong>Email:</strong> {{ $withdrawal->partner?->email }}</div>
                <div><strong>WhatsApp:</strong> {{ $withdrawal->partner?->phone }}</div>
                <div><strong>Saldo Komisi Tersedia Mitra Saat Ini:</strong> <span style="color:var(--teal); font-weight:700;">Rp {{ number_format($withdrawal->partner?->available_commission ?? 0, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    {{-- Right: Update Status Form --}}
    <div class="admin-card">
        <h3 style="font-weight:700; margin-bottom:20px;">Konfirmasi & Prosedur Admin</h3>

        <form method="POST" action="{{ route('admin.withdrawals.status', $withdrawal) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Pilih Action Status *</label>
                <select name="status" id="withdraw_status" class="form-control" onchange="toggleWithdrawForm(this.value)">
                    <option value="requested" {{ $withdrawal->status === 'requested' ? 'selected' : '' }}>⌛ Menunggu Diproses</option>
                    <option value="processing" {{ $withdrawal->status === 'processing' ? 'selected' : '' }}>🔄 Sedang Dalam Transfer</option>
                    <option value="paid" {{ $withdrawal->status === 'paid' ? 'selected' : '' }}>✅ Dibayar (Transfer Selesai)</option>
                    <option value="rejected" {{ $withdrawal->status === 'rejected' ? 'selected' : '' }}>❌ Tolak Pengajuan</option>
                </select>
            </div>

            <div id="proof-group" class="form-group" style="margin-bottom:20px; display:none;">
                <label class="form-label">Upload Bukti Transfer (Resi/Struk) *</label>
                <input type="file" name="transfer_proof" class="form-control" accept="image/*,.pdf">
                <small style="color:var(--text-muted); font-size:0.78rem;">Upload foto resi/bukti transfer bank dalam format JPG, PNG, atau PDF.</small>
            </div>

            <div id="rejection-group" class="form-group" style="margin-bottom:20px; display:none;">
                <label class="form-label" style="color:#ef4444;">Alasan Penolakan *</label>
                <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Contoh: Nomor rekening bank tidak valid / nama tidak sesuai.">{{ old('rejection_reason', $withdrawal->rejection_reason) }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Catatan Admin (Opsional)</label>
                <textarea name="admin_note" class="form-control" rows="2" placeholder="Catatan internal atau pesan tambahan untuk mitra...">{{ old('admin_note', $withdrawal->admin_note) }}</textarea>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center; padding:12px; font-size:1rem;">
                Simpan Status Penarikan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleWithdrawForm(val) {
    const proofGroup = document.getElementById('proof-group');
    const rejectionGroup = document.getElementById('rejection-group');
    
    proofGroup.style.display = (val === 'paid') ? 'block' : 'none';
    rejectionGroup.style.display = (val === 'rejected') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', () => {
    toggleWithdrawForm(document.getElementById('withdraw_status').value);
});
</script>
@endpush
@endsection
