<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update Status Penarikan Komisi</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #14B8A6; }
        .content { padding: 30px; }
        .details-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .details-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #E2E8F0; }
        .details-row:last-child { border-bottom: none; }
        .footer { background: #F8FAFC; padding: 20px; text-align: center; font-size: 13px; color: #64748B; border-top: 1px solid #E2E8F0; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; }
        .badge-paid { background: #DCFCE7; color: #15803D; }
        .badge-rejected { background: #FEE2E2; color: #B91C1C; }
        .badge-approved { background: #E0F2FE; color: #0369A1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>travine.id Partner</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $withdrawal->partner->name }}!</h2>
            <p>Pemberitahuan perubahan status pengajuan pencairan komisi Anda dengan rincian berikut:</p>

            <div class="details-box">
                <div style="margin-bottom:10px;">
                    <strong>Kode Penarikan:</strong> {{ $withdrawal->withdrawal_code }}
                </div>
                <div style="margin-bottom:10px;">
                    <strong>Nominal:</strong> {{ $withdrawal->amount_formatted }}
                </div>
                <div style="margin-bottom:10px;">
                    <strong>Tujuan Bank:</strong> {{ $withdrawal->bank_name }} - {{ $withdrawal->bank_account_number }} a.n {{ $withdrawal->bank_account_name }}
                </div>
                <div>
                    <strong>Status:</strong>
                    @if($withdrawal->status === 'paid')
                        <span class="badge badge-paid">Dibayar</span>
                    @elseif($withdrawal->status === 'rejected')
                        <span class="badge badge-rejected">Ditolak</span>
                    @else
                        <span class="badge badge-approved">{{ $withdrawal->status_label }}</span>
                    @endif
                </div>
            </div>

            @if($withdrawal->status === 'paid')
                <p>Transfer komisi sebesar <strong>{{ $withdrawal->amount_formatted }}</strong> telah berhasil diproses ke rekening Anda.</p>
                <p>Bukti transfer terlampir dalam email ini atau dapat diunduh di dashboard Partner Anda.</p>
            @elseif($withdrawal->status === 'rejected')
                <p style="color: #B91C1C;"><strong>Alasan Penolakan:</strong></p>
                <blockquote style="background:#FFF5F5; border-left:4px solid #EF4444; padding:12px; margin:0 0 16px 0; color:#7F1D1D;">
                    {{ $withdrawal->rejection_reason ?? 'Tidak ada catatan alasan penolakan.' }}
                </blockquote>
                <p>Saldo komisi Anda telah dikembalikan ke saldo <em>Available</em>. Anda dapat mengajukan pencairan ulang setelah memperbarui data rekening atau memenuhi ketentuan.</p>
            @else
                <p>Pengajuan pencairan Anda sedang diproses oleh Tim Finance travine.id.</p>
            @endif

            <div style="text-align: center; margin-top:25px;">
                <a href="{{ route('partner.withdrawals') }}" style="background:#0D9488; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:6px; font-weight:bold;">Lihat Portal Partner</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} travine.id. All rights reserved.
        </div>
    </div>
</body>
</html>
