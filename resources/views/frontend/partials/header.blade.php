    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    @auth
        @php
            $nameParts = preg_split('/\s+/', trim((string) auth()->user()->name), -1, PREG_SPLIT_NO_EMPTY);
            $initials = collect($nameParts)
                ->take(2)
                ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8'))
                ->implode('');
        @endphp
    @endauth
    <style>
    .navbar .av-dropdown .btn:focus,
    .navbar .av-dropdown .btn:active,
    .navbar .av-dropdown .btn.show,
    .navbar .av-dropdown .btn:focus-visible,
    .navbar .av-dropdown .dropdown-toggle:focus {
        border-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }
    @media (min-width: 992px) {
        /* AV kullanıcı menüsü her zaman butonun alt-sağına hizalı açılsın ve sadece tıklama ile açılsın */
        .navbar .av-dropdown .dropdown-menu {
            right: 0 !important;
            left: auto !important;
        }
        .navbar .av-dropdown .dropdown-menu[data-bs-popper] {
            right: 0 !important;
            left: auto !important;
            transform: none !important;
            inset: 100% 0 auto auto !important;
        }
        /* Hover ile otomatik açılmayı kapat – sadece .show (tıklama) ile açılsın */
        .navbar .av-dropdown:hover > .dropdown-menu:not(.show) {
            top: 150% !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    }
    </style>
    <nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="{{ asset('frontend/images/logo-2.png') }}" alt="Aydın Language Academy" style="height: 56px; width: auto;">
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto p-4 p-lg-0">
                <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home', 'frontend.preview.home') ? 'active' : '' }}">{{ __('dictt.home') }}</a>
                <div class="nav-item dropdown frontend-courses-dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('frontend.courses.*') ? 'active' : '' }}" data-bs-toggle="dropdown">{{ __('dictt.ourcourses') }}</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.courses.ielts', 'frontend.courses.yks-dil') ? 'active' : '' }}">{{ __('dictt.featured_programs') }}</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.courses.ielts') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.ielts') ? 'active' : '' }}">{{ __('dictt.ielts_prep') }}</a>
                                <a href="{{ route('frontend.courses.yks-dil') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.yks-dil') ? 'active' : '' }}">{{ __('dictt.yks_dil_prep') }}</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.courses.yds-yokdil', 'frontend.courses.toefl', 'frontend.courses.pte-academic', 'frontend.courses.test-of-english', 'frontend.courses.sat') ? 'active' : '' }}">{{ __('dictt.academic_exam_prep') }}</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.courses.yds-yokdil') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.yds-yokdil') ? 'active' : '' }}">{{ __('dictt.yds_yokdil') }}</a>
                                <a href="{{ route('frontend.courses.toefl') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.toefl') ? 'active' : '' }}">{{ __('dictt.toefl') }}</a>
                                <a href="{{ route('frontend.courses.pte-academic') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.pte-academic') ? 'active' : '' }}">{{ __('dictt.pte_academic') }}</a>
                                <a href="{{ route('frontend.courses.test-of-english') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.test-of-english') ? 'active' : '' }}">{{ __('dictt.test_of_english') }}</a>
                                <a href="{{ route('frontend.courses.sat') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.sat') ? 'active' : '' }}">{{ __('dictt.sat') }}</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.courses.preschool', 'frontend.courses.primary-school', 'frontend.courses.middle-school', 'frontend.courses.high-school') ? 'active' : '' }}">{{ __('dictt.kids_teens_english') }}</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.courses.preschool') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.preschool') ? 'active' : '' }}">{{ __('dictt.preschool') }}</a>
                                <a href="{{ route('frontend.courses.primary-school') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.primary-school') ? 'active' : '' }}">{{ __('dictt.primary_school') }}</a>
                                <a href="{{ route('frontend.courses.middle-school') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.middle-school') ? 'active' : '' }}">{{ __('dictt.middle_school') }}</a>
                                <a href="{{ route('frontend.courses.high-school') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.high-school') ? 'active' : '' }}">{{ __('dictt.high_school') }}</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.courses.general-english', 'frontend.courses.speaking-clubs') ? 'active' : '' }}">{{ __('dictt.adult_english') }}</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.courses.general-english') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.general-english') ? 'active' : '' }}">{{ __('dictt.general_english') }}</a>
                                <a href="{{ route('frontend.courses.speaking-clubs') }}" class="dropdown-item {{ request()->routeIs('frontend.courses.speaking-clubs') ? 'active' : '' }}">{{ __('dictt.speaking_clubs') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('frontend.achievements') }}" class="nav-item nav-link {{ request()->routeIs('frontend.achievements') ? 'active' : '' }}">{{ __('dictt.achievements') }}</a>
                <a href="{{ route('frontend.campaigns') }}" class="nav-item nav-link {{ request()->routeIs('frontend.campaigns') ? 'active' : '' }}">{{ __('dictt.campaigns') }}</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('frontend.branches.*') ? 'active' : '' }}" data-bs-toggle="dropdown">{{ __('dictt.branches') }}</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        <a href="{{ route('frontend.branches.ortaca') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.ortaca') ? 'active' : '' }}">Ortaca</a>
                        <a href="{{ route('frontend.branches.dalaman') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.dalaman') ? 'active' : '' }}">Dalaman</a>
                        <a href="{{ route('frontend.branches.koycegiz') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.koycegiz') ? 'active' : '' }}">Köyceğiz</a>
                    </div>
                </div>
                <a href="{{ route('frontend.placement-test') }}" class="nav-item nav-link {{ request()->routeIs('frontend.placement-test') ? 'active' : '' }}">{{ __('dictt.placement_test') }}</a>
                @guest
                    <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="nav-item nav-link d-lg-none">Giriş Yap</a>
                @else
                    <div class="nav-item dropdown av-dropdown d-lg-none">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-label="Kullanıcı menüsü" aria-expanded="false">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width: 32px; height: 32px;">{{ $initials ?: 'Ü' }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light rounded-0 m-0">
                            <a href="{{ route('changeLanguage', session('locale') === 'tr' ? 'en' : 'tr') }}" class="dropdown-item border-0 bg-transparent w-100 text-start d-flex align-items-center gap-2">
                                @if (session('locale') === 'tr')
                                    <svg style="width: 24px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 26">
                                        <path fill="#00247D" d="M0 9.059V13h5.628zM4.664 31H13v-5.837zM23 25.164V31h8.335zM0 23v3.941L5.63 23zM31.337 5H23v5.837zM36 26.942V23h-5.631zM36 13V9.059L30.371 13zM13 5H4.664L13 10.837z"/>
                                        <path fill="#CF1B2B" d="M25.14 23l9.712 6.801c.471-.479.808-1.082.99-1.749L28.627 23H25.14zM13 23h-2.141l-9.711 6.8c.521.53 1.189.909 1.938 1.085L13 23.943V23zm10-10h2.141l9.711-6.8c-.521-.53-1.188-.909-1.937-1.085L23 12.057V13zm-12.141 0L1.148 6.2C.677 6.68.34 7.282.157 7.949L7.372 13h3.487z"/>
                                        <path fill="#EEE" d="M36 21H21v10h2v-5.836L31.335 31H32c1.117 0 2.126-.461 2.852-1.199L25.14 23h3.487l7.215 5.052c.093-.337.158-.686.158-1.052v-.058L30.369 23H36v-2zM0 21v2h5.63L0 26.941V27c0 1.091.439 2.078 1.148 2.8l9.711-6.8H13v.943l-9.914 6.941c.294.07.598.116.914.116h.664L13 25.163V31h2V21H0zM36 9c0-1.091-.439-2.078-1.148-2.8L25.141 13H23v-.943l9.915-6.942C32.62 5.046 32.316 5 32 5h-.663L23 10.837V5h-2v10h15v-2h-5.629L36 9.059V9zM13 5v5.837L4.664 5H4c-1.118 0-2.126.461-2.852 1.2l9.711 6.8H7.372L.157 7.949C.065 8.286 0 8.634 0 9v.059L5.628 13H0v2h15V5h-2z"/>
                                        <path fill="#CF1B2B" d="M21 15V5h-6v10H0v6h15v10h6V21h15v-6z"/>
                                    </svg>
                                @else
                                    <svg style="width: 24px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 26">
                                        <path fill="#E30917" d="M36 27c0 2.209-1.791 4-4 4H4c-2.209 0-4-1.791-4-4V9c0-2.209 1.791-4 4-4h28c2.209 0 4 1.791 4 4v18z"/>
                                        <path fill="#EEE" d="M16 24c-3.314 0-6-2.685-6-6 0-3.314 2.686-6 6-6 1.31 0 2.52.425 3.507 1.138-1.348-1.524-3.312-2.491-5.507-2.491-4.061 0-7.353 3.292-7.353 7.353 0 4.062 3.292 7.354 7.353 7.354 2.195 0 4.16-.967 5.507-2.492C18.521 23.575 17.312 24 16 24zm3.913-5.77l2.44.562.22 2.493 1.288-2.146 2.44.561-1.644-1.888 1.287-2.147-2.303.98-1.644-1.889.22 2.494z"/>
                                    </svg>
                                @endif
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">{{ __('dictt.logout') }}</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
            @guest
                <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block" style="margin-right: 6rem;">Giriş Yap<i class="fa fa-arrow-right ms-3"></i></a>
            @else
                <div class="nav-item dropdown av-dropdown d-none d-lg-flex align-items-stretch">
                    <a href="#" class="btn btn-primary rounded-0 py-4 px-lg-4 d-flex align-items-center dropdown-toggle border-0 shadow-none" style="box-shadow:none !important; border:0 !important; outline:none !important;" data-bs-toggle="dropdown" aria-label="Kullanıcı menüsü" aria-expanded="false">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-primary fw-bold" style="width: 36px; height: 36px;">{{ $initials ?: 'Ü' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light rounded-0 m-0">
                        <a href="{{ route('changeLanguage', session('locale') === 'tr' ? 'en' : 'tr') }}" class="dropdown-item border-0 bg-transparent w-100 text-start d-flex align-items-center gap-2">
                            @if (session('locale') === 'tr')
                                <svg style="width: 24px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 26">
                                    <path fill="#00247D" d="M0 9.059V13h5.628zM4.664 31H13v-5.837zM23 25.164V31h8.335zM0 23v3.941L5.63 23zM31.337 5H23v5.837zM36 26.942V23h-5.631zM36 13V9.059L30.371 13zM13 5H4.664L13 10.837z"/>
                                    <path fill="#CF1B2B" d="M25.14 23l9.712 6.801c.471-.479.808-1.082.99-1.749L28.627 23H25.14zM13 23h-2.141l-9.711 6.8c.521.53 1.189.909 1.938 1.085L13 23.943V23zm10-10h2.141l9.711-6.8c-.521-.53-1.188-.909-1.937-1.085L23 12.057V13zm-12.141 0L1.148 6.2C.677 6.68.34 7.282.157 7.949L7.372 13h3.487z"/>
                                    <path fill="#EEE" d="M36 21H21v10h2v-5.836L31.335 31H32c1.117 0 2.126-.461 2.852-1.199L25.14 23h3.487l7.215 5.052c.093-.337.158-.686.158-1.052v-.058L30.369 23H36v-2zM0 21v2h5.63L0 26.941V27c0 1.091.439 2.078 1.148 2.8l9.711-6.8H13v.943l-9.914 6.941c.294.07.598.116.914.116h.664L13 25.163V31h2V21H0zM36 9c0-1.091-.439-2.078-1.148-2.8L25.141 13H23v-.943l9.915-6.942C32.62 5.046 32.316 5 32 5h-.663L23 10.837V5h-2v10h15v-2h-5.629L36 9.059V9zM13 5v5.837L4.664 5H4c-1.118 0-2.126.461-2.852 1.2l9.711 6.8H7.372L.157 7.949C.065 8.286 0 8.634 0 9v.059L5.628 13H0v2h15V5h-2z"/>
                                    <path fill="#CF1B2B" d="M21 15V5h-6v10H0v6h15v10h6V21h15v-6z"/>
                                </svg>
                            @else
                                <svg style="width: 24px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 26">
                                    <path fill="#E30917" d="M36 27c0 2.209-1.791 4-4 4H4c-2.209 0-4-1.791-4-4V9c0-2.209 1.791-4 4-4h28c2.209 0 4 1.791 4 4v18z"/>
                                    <path fill="#EEE" d="M16 24c-3.314 0-6-2.685-6-6 0-3.314 2.686-6 6-6 1.31 0 2.52.425 3.507 1.138-1.348-1.524-3.312-2.491-5.507-2.491-4.061 0-7.353 3.292-7.353 7.353 0 4.062 3.292 7.354 7.353 7.354 2.195 0 4.16-.967 5.507-2.492C18.521 23.575 17.312 24 16 24zm3.913-5.77l2.44.562.22 2.493 1.288-2.146 2.44.561-1.644-1.888 1.287-2.147-2.303.98-1.644-1.889.22 2.494z"/>
                                </svg>
                            @endif
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                            <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">{{ __('dictt.logout') }}</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </nav>
    <!-- Navbar End -->
