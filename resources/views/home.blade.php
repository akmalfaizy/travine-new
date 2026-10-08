@extends('layouts.app')

@section('content')
    @include('partials.hero')

    {{-- Promo & Open Trip Banner Slider --}}
    @include('partials.banner-slider')
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
