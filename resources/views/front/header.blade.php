<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicon -->
  <link href="{{ asset('front/') }}/img/favicon.png" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap"
    rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="{{ asset('front/') }}/lib/animate/animate.min.css" rel="stylesheet">
  <link href="{{ asset('front/') }}/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="{{ asset('front/') }}/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="{{ asset('front/') }}/css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="{{ asset('front/') }}/css/style.css" rel="stylesheet">
</head>

<body>
  <!-- Topbar Start -->
  <div class="container-fluid bg-light p-0">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 text-start">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-map-marker-alt text-primary me-2"></small>
          <small>Karaçalı Mahallesi Şehit Hamza Atakul Caddesi No:39-A Dalaman/Muğla</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center py-3">
          <small class="far fa-clock text-primary me-2"></small>
          <small>Mon-Sun: 09.00 AM - 19.00 PM</small>
        </div>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-phone-alt text-primary me-2"></small>
          <small>Dalaman: (530) 828 4884 &nbsp;</small>
          <small class="fa fa-phone-alt text-primary me-2"></small>
          <small>Ortaca: (546) 828 4884</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href="#"><i
              class="fab fa-facebook-f"></i></a>
          <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href="#"><i
              class="fab fa-twitter"></i></a>
          <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href="#"><i
              class="fab fa-linkedin-in"></i></a>
          <a class="btn btn-sm-square rounded-circle bg-white text-primary me-0" href="#"><i
              class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>
  <!-- Topbar End -->
  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
<!--     <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-success">
        <img class="img-fluid bg-light rounded-circle" src="{{ asset('front/') }}/img/favicon.png"
          alt="AYDIN LANGUAGE ACADEMY" style="width: 70px; height: 70px;">
        ALA
      </h2>
    </a> -->
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="{{ route('home') }}"
          class="nav-item nav-link {{ Request::segment(1) === null ? 'active' : null }}">Home</a>
        @foreach ($levels as $level)
      <div class="nav-item dropdown">
        <a href="#"
        class="nav-link dropdown-toggle @isset ($themes) @if ($themes->first()!=null) {{$themes->first()->levels->slug === $level->slug ? 'active' : null}} @endif @endisset"
        data-bs-toggle="dropdown">{{$level->name}}</a>
        <div class="dropdown-menu rounded-0 rounded-bottom m-0">
        @foreach ($sub_levels as $sub_level)
      <a href="{{ route('themes', [$level->slug, $sub_level->slug]) }}"
        class="dropdown-item @isset ($themes) @if ($themes->first()!=null) {{$themes->first()->levels->slug === $level->slug ? ($themes->first()->sub_levels->slug === $sub_level->slug ? 'active' : null) : null}} @endif @endisset">{{$sub_level->name}}</a>
    @endforeach
        </div>
      </div>
    @endforeach
        <a href="{{ route('about') }}"
          class="nav-item nav-link {{ Request::segment(1) === 'about' ? 'active' : null }}">About</a>
        <a href="{{ route('contact') }}"
          class="nav-item nav-link {{ Request::segment(1) === 'contact' ? 'active' : null }}">Contact</a>
      </div>

      <!--       <a href="#" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Appointment<i
          class="fa fa-arrow-right ms-3"></i></a> -->

    </div>
  </nav>
  <!-- Navbar End -->