@extends('admin.layout')
@section('title', 'Kelola Media Sosial')
@section('content')
<div class="admin-topbar">
    <h1>Kelola Media Sosial</h1>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="admin-alert admin-alert--error">✗ {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="admin-alert admin-alert--error">
        @foreach($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.social-links.update') }}" class="admin-card">
    @csrf
    <div class="form-group">
        <label class="form-label" for="instagram_url">Instagram URL</label>
        <input name="instagram_url" type="url" id="instagram_url" class="form-control" value="{{ old('instagram_url', $link->instagram_url) }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="tiktok_url">TikTok URL</label>
        <input name="tiktok_url" type="url" id="tiktok_url" class="form-control" value="{{ old('tiktok_url', $link->tiktok_url) }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="youtube_url">YouTube URL</label>
        <input name="youtube_url" type="url" id="youtube_url" class="form-control" value="{{ old('youtube_url', $link->youtube_url) }}">
    </div>
    <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center; padding:14px; font-size:1rem;">Simpan Perubahan</button>
</form>
@endsection
