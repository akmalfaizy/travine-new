@extends('layouts.app')

@section('content')
    @include('partials.hero')
    <!-- Mascot invitation for partner program -->
    <!-- <div class="text-center my-5">
        <img src="file:///C:/Users/asus/.gemini/antigravity-ide/brain/60d834d6-4d65-4285-9d26-084938a3fe29/mascot_invite_1788972621788.jpg" alt="Ayo jadi partner kami dan dapatkan komisi!" style="max-width: 300px; width: 100%; height: auto;">
    </div> -->
    @include('partials.about')
    @include('partials.services')

    {{-- Kategori Wisata Section --}}
    @include('partials.tours-categories')

    @include('partials.values')
    @include('partials.portfolio')
    @include('partials.why-choose')

    {{-- Berita Terbaru Section --}}
    @include('partials.news-preview')

    @include('partials.cta')
    @include('partials.contact')
@endsection
