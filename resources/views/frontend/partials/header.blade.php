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
    <nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="{{ asset('frontend/images/logo-2.png') }}" alt="Aydın Language Academy" style="height: 56px; width: auto;">
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home', 'frontend.preview.home') ? 'active' : '' }}">Ana Sayfa</a>
                <div class="nav-item dropdown frontend-courses-dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('frontend.trainings.*') ? 'active' : '' }}" data-bs-toggle="dropdown">Kurslarımız</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.trainings.ielts', 'frontend.trainings.yks-dil') ? 'active' : '' }}">Öne Çıkan Programlar</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.trainings.ielts') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.ielts') ? 'active' : '' }}">IELTS Hazırlık</a>
                                <a href="{{ route('frontend.trainings.yks-dil') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.yks-dil') ? 'active' : '' }}">YKS-DİL Hazırlık</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.trainings.yds-yokdil', 'frontend.trainings.toefl', 'frontend.trainings.pte-academic', 'frontend.trainings.test-of-english', 'frontend.trainings.sat') ? 'active' : '' }}">Akademik Sınav Hazırlık</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.trainings.yds-yokdil') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.yds-yokdil') ? 'active' : '' }}">YDS / YÖKDİL</a>
                                <a href="{{ route('frontend.trainings.toefl') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.toefl') ? 'active' : '' }}">TOEFL</a>
                                <a href="{{ route('frontend.trainings.pte-academic') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.pte-academic') ? 'active' : '' }}">PTE Academic</a>
                                <a href="{{ route('frontend.trainings.test-of-english') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.test-of-english') ? 'active' : '' }}">Test of English</a>
                                <a href="{{ route('frontend.trainings.sat') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.sat') ? 'active' : '' }}">SAT</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.trainings.preschool', 'frontend.trainings.primary-school', 'frontend.trainings.middle-school', 'frontend.trainings.high-school') ? 'active' : '' }}">Çocuk &amp; Genç İngilizce</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.trainings.preschool') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.preschool') ? 'active' : '' }}">Okul Öncesi</a>
                                <a href="{{ route('frontend.trainings.primary-school') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.primary-school') ? 'active' : '' }}">İlkokul</a>
                                <a href="{{ route('frontend.trainings.middle-school') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.middle-school') ? 'active' : '' }}">Ortaokul</a>
                                <a href="{{ route('frontend.trainings.high-school') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.high-school') ? 'active' : '' }}">Lise</a>
                            </div>
                        </div>
                        <div class="dropdown-submenu">
                            <span class="dropdown-item dropdown-submenu-toggle {{ request()->routeIs('frontend.trainings.general-english', 'frontend.trainings.speaking-clubs') ? 'active' : '' }}">Yetişkin İngilizce</span>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('frontend.trainings.general-english') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.general-english') ? 'active' : '' }}">Genel İngilizce</a>
                                <a href="{{ route('frontend.trainings.speaking-clubs') }}" class="dropdown-item {{ request()->routeIs('frontend.trainings.speaking-clubs') ? 'active' : '' }}">Konuşma Kulüpleri</a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('frontend.achievements') }}" class="nav-item nav-link {{ request()->routeIs('frontend.achievements') ? 'active' : '' }}">Başarılarımız</a>
                <a href="{{ route('frontend.campaigns') }}" class="nav-item nav-link {{ request()->routeIs('frontend.campaigns') ? 'active' : '' }}">Kampanyalarımız</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('frontend.branches.*') ? 'active' : '' }}" data-bs-toggle="dropdown">Şubelerimiz</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        <a href="{{ route('frontend.branches.ortaca') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.ortaca') ? 'active' : '' }}">Ortaca</a>
                        <a href="{{ route('frontend.branches.dalaman') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.dalaman') ? 'active' : '' }}">Dalaman</a>
                        <a href="{{ route('frontend.branches.koycegiz') }}" class="dropdown-item {{ request()->routeIs('frontend.branches.koycegiz') ? 'active' : '' }}">Köyceğiz</a>
                    </div>
                </div>
                <a href="{{ route('frontend.placement-test') }}" class="nav-item nav-link {{ request()->routeIs('frontend.placement-test') ? 'active' : '' }}">Seviye Tespit Sınavı</a>
                @guest
                    <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="nav-item nav-link d-lg-none">Giriş Yap</a>
                @else
                    <div class="nav-item dropdown d-lg-none">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-label="Kullanıcı menüsü" aria-expanded="false">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width: 32px; height: 32px;">{{ $initials ?: 'Ü' }}</span>
                        </a>
                        <div class="dropdown-menu bg-light rounded-0 m-0">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">Çıkış</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
            @guest
                <a href="{{ route('frontend.login', ['return' => request()->route()?->getName()]) }}" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Giriş Yap<i class="fa fa-arrow-right ms-3"></i></a>
            @else
                <div class="nav-item dropdown d-none d-lg-flex align-items-stretch">
                    <a href="#" class="btn btn-primary rounded-0 py-4 px-lg-4 d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown" aria-label="Kullanıcı menüsü" aria-expanded="false">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-primary fw-bold" style="width: 36px; height: 36px;">{{ $initials ?: 'Ü' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light rounded-0 m-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <input type="hidden" name="return" value="{{ request()->route()?->getName() }}">
                            <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">Çıkış</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </nav>
    <!-- Navbar End -->
