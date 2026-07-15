@extends('admin.layout')
@section('title', 'Pengaturan Website')
@section('content')
<div class="admin-topbar">
    <h1>Pengaturan Website</h1>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:20px;">Informasi Website</h3>
            @foreach(['site_name'=>'Nama Website','site_tagline'=>'Tagline','email_contact'=>'Email Kontak','phone_whatsapp'=>'No. WhatsApp (format: 628xxx)','address'=>'Alamat'] as $key=>$label)
            <div class="form-group">
                <label class="form-label">{{ $label }}</label>
                <input name="{{ $key }}" type="text" class="form-control" value="{{ $settings[$key] ?? '' }}">
            </div>
            @endforeach
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Media Sosial</h3>
                @foreach(['facebook'=>'Facebook URL','instagram'=>'Instagram URL'] as $key=>$label)
                <div class="form-group">
                    <label class="form-label">{{ $label }}</label>
                    <input name="{{ $key }}" type="text" class="form-control" value="{{ $settings[$key] ?? '' }}">
                </div>
                @endforeach
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Pengaturan Pembayaran</h3>
                <div class="form-group">
                    <label class="form-label">DP Minimum (%)</label>
                    <input name="dp_percentage" type="number" class="form-control" value="{{ $settings['dp_percentage'] ?? '30' }}" min="0" max="100">
                </div>
                @foreach(['bank_name'=>'Nama Bank','bank_account'=>'No. Rekening','bank_holder'=>'Atas Nama'] as $key=>$label)
                <div class="form-group">
                    <label class="form-label">{{ $label }}</label>
                    <input name="{{ $key }}" type="text" class="form-control" value="{{ $settings[$key] ?? '' }}">
                </div>
                @endforeach
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">WhatsApp API</h3>
                <div class="form-group">
                    <label class="form-label">API URL</label>
                    <input name="whatsapp_api_url" type="text" class="form-control" value="{{ $settings['whatsapp_api_url'] ?? '' }}" placeholder="https://api.whatsapp.com/...">
                    <small style="color:var(--text-muted); font-size:0.78rem;">Opsional. Kosongkan untuk mode simulasi (log saja).</small>
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center; padding:14px; font-size:1rem;">
                Simpan Semua Pengaturan
            </button>
        </div>
    </div>
</form>
@endsection
