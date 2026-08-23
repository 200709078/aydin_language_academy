    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">{{ __('dictt.loading') }}</span>
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
                        <a href="{{ route('frontend.branches.ortaca') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.ortaca') ? 'active' : '' }}">{{ __('dictt.branch_ortaca') }}</a>
                        <a href="{{ route('frontend.branches.dalaman') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.dalaman') ? 'active' : '' }}">{{ __('dictt.branch_dalaman') }}</a>
                        <a href="{{ route('frontend.branches.koycegiz') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.koycegiz') ? 'active' : '' }}">{{ __('dictt.branch_koycegiz') }}</a>
                    </div>
                </div>
                <a href="{{ route('frontend.placement-test') }}" class="nav-item nav-link {{ request()->routeIs('frontend.placement-test') ? 'active' : '' }}">{{ __('dictt.placement_test') }}</a>
                @guest
                    <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="nav-item nav-link d-lg-none">{{ __('dictt.login_now') }}</a>
                @else
                    <div class="nav-item dropdown av-dropdown d-lg-none">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-label="{{ __('dictt.user_menu') }}" aria-expanded="false">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width: 32px; height: 32px;">{{ $initials ?: __('dictt.user_initial') }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light rounded-0 m-0">
                            <a href="{{ route('changeLanguage', session('locale') === 'tr' ? 'en' : 'tr') }}" class="dropdown-item border-0 bg-transparent w-100 text-start">
                                    <span class="av-lang-badge me-2">{{ session('locale') === 'tr' ? 'EN' : 'TR' }}</span>{{ session('locale') === 'tr' ? 'English' : 'Türkçe' }}
                                
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"><i class="fa fa-sign-out-alt me-2" aria-hidden="true"></i>{{ __('dictt.logout') }}</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
            @guest
                <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">{{ __('dictt.login_now') }}<i class="fa fa-arrow-right ms-3"></i></a>
            @else
                <div class="nav-item dropdown av-dropdown d-none d-lg-flex align-items-stretch">
                    <a href="#" class="btn btn-primary rounded-0 py-4 px-lg-4 d-flex align-items-center dropdown-toggle border-0 shadow-none" style="box-shadow:none !important; border:0 !important; outline:none !important;" data-bs-toggle="dropdown" aria-label="{{ __('dictt.user_menu') }}" aria-expanded="false">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-primary fw-bold" style="width: 36px; height: 36px;">{{ $initials ?: __('dictt.user_initial') }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light rounded-0 m-0">
                        <a href="{{ route('changeLanguage', session('locale') === 'tr' ? 'en' : 'tr') }}" class="dropdown-item border-0 bg-transparent w-100 text-start">
                                <span class="av-lang-badge me-2">{{ session('locale') === 'tr' ? 'EN' : 'TR' }}</span>{{ session('locale') === 'tr' ? 'English' : 'Türkçe' }}
                            
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"><i class="fa fa-sign-out-alt me-2" aria-hidden="true"></i>{{ __('dictt.logout') }}</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
    </nav>
    <!-- Navbar End -->

    @include('frontend.partials.side-menu')
