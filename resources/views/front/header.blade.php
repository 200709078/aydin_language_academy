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
  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0" style="color:#25bebc;">
        <img class="img-fluid bg-light rounded-circle" src="{{ asset('front/') }}/img/favicon.png"
          alt="{{ __('dictt.ala') }}" style="width: 70px; height: 70px; color:'#25bebc'">
        ALA
      </h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="{{ route('home') }}"
          class="nav-item nav-link {{ Request::segment(1) === null ? 'active' : null }}">{{ __('dictt.home') }}</a>
        @foreach ($levels as $level)
        <div class="nav-item dropdown">
          <a href="#"
          class="nav-link dropdown-toggle @isset ($themes) @if ($themes->first() != null) {{$themes->first()->levels->slug === $level->slug ? 'active' : null}} @endif @endisset"
          data-bs-toggle="dropdown">{{$level->name}}</a>
          <div class="dropdown-menu rounded-0 rounded-bottom m-0">
          @foreach ($sub_levels as $sub_level)
        <a href="{{ route('themes', [$level->slug, $sub_level->slug]) }}"
          class="dropdown-item @isset ($themes) @if ($themes->first() != null) {{$themes->first()->levels->slug === $level->slug ? ($themes->first()->sub_levels->slug === $sub_level->slug ? 'active' : null) : null}} @endif @endisset">{{$sub_level->name}}</a>
        @endforeach
          </div>
        </div>
    @endforeach
        <a href="{{ route('about') }}"
          class="nav-item nav-link {{ Request::segment(1) === 'about' ? 'active' : null }}">{{ __('dictt.about') }}</a>
        <a href="{{ route('contact') }}"
          class="nav-item nav-link {{ Request::segment(1) === 'contact' ? 'active' : null }}">{{ __('dictt.contact') }}</a>

        @if (Session::get('locale') == 'tr')
      <a href="{{ route('changeLanguage', 'en') }}" class="nav-item nav-link">
        <svg style="width:25px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 5 36 26">
        <path fill="#00247D"
          d="M0 9.059V13h5.628zM4.664 31H13v-5.837zM23 25.164V31h8.335zM0 23v3.941L5.63 23zM31.337 5H23v5.837zM36 26.942V23h-5.631zM36 13V9.059L30.371 13zM13 5H4.664L13 10.837z">
        </path>
        <path fill="#CF1B2B"
          d="M25.14 23l9.712 6.801c.471-.479.808-1.082.99-1.749L28.627 23H25.14zM13 23h-2.141l-9.711 6.8c.521.53 1.189.909 1.938 1.085L13 23.943V23zm10-10h2.141l9.711-6.8c-.521-.53-1.188-.909-1.937-1.085L23 12.057V13zm-12.141 0L1.148 6.2C.677 6.68.34 7.282.157 7.949L7.372 13h3.487z">
        </path>
        <path fill="#EEE"
          d="M36 21H21v10h2v-5.836L31.335 31H32c1.117 0 2.126-.461 2.852-1.199L25.14 23h3.487l7.215 5.052c.093-.337.158-.686.158-1.052v-.058L30.369 23H36v-2zM0 21v2h5.63L0 26.941V27c0 1.091.439 2.078 1.148 2.8l9.711-6.8H13v.943l-9.914 6.941c.294.07.598.116.914.116h.664L13 25.163V31h2V21H0zM36 9c0-1.091-.439-2.078-1.148-2.8L25.141 13H23v-.943l9.915-6.942C32.62 5.046 32.316 5 32 5h-.663L23 10.837V5h-2v10h15v-2h-5.629L36 9.059V9zM13 5v5.837L4.664 5H4c-1.118 0-2.126.461-2.852 1.2l9.711 6.8H7.372L.157 7.949C.065 8.286 0 8.634 0 9v.059L5.628 13H0v2h15V5h-2z">
        </path>
        <path fill="#CF1B2B" d="M21 15V5h-6v10H0v6h15v10h6V21h15v-6z"></path>
        </svg>
      </a>
    @else
      <a href="{{ route('changeLanguage', 'tr') }}" class="nav-item nav-link">
        <svg style="width:25px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 5 36 26">
        <path fill="#E30917"
          d="M36 27c0 2.209-1.791 4-4 4H4c-2.209 0-4-1.791-4-4V9c0-2.209 1.791-4 4-4h28c2.209 0 4 1.791 4 4v18z">
        </path>
        <path fill="#EEE"
          d="M16 24c-3.314 0-6-2.685-6-6 0-3.314 2.686-6 6-6 1.31 0 2.52.425 3.507 1.138-1.348-1.524-3.312-2.491-5.507-2.491-4.061 0-7.353 3.292-7.353 7.353 0 4.062 3.292 7.354 7.353 7.354 2.195 0 4.16-.967 5.507-2.492C18.521 23.575 17.312 24 16 24zm3.913-5.77l2.44.562.22 2.493 1.288-2.146 2.44.561-1.644-1.888 1.287-2.147-2.303.98-1.644-1.889.22 2.494z">
        </path>
        </svg>
      </a>
    @endif

      </div>
    </div>
  </nav>
  <!-- Navbar End -->