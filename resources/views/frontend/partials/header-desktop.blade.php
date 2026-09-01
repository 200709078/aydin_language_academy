    <!-- Desktop Navbar Start -->
    @php
        $desktopSecondaryLinkIcons = [
            'frontend.achievements' => 'fa-trophy',
            'frontend.campaigns' => 'fa-tags',
        ];
    @endphp

    <nav class="navbar navbar-expand-xl bg-light navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="{{ asset('frontend/images/logo/logo-2.png') }}" alt="Aydın Language Academy" style="height: 56px; width: auto;">
            <span class="frontend-brand-wordmark">
                <span class="frontend-brand-wordmark__main">{{ __('dictt.brand_title') }}</span>
                <span class="frontend-brand-wordmark__sub">{{ __('dictt.brand_subtitle') }}</span>
            </span>
        </a>
        <div class="navbar-collapse">
            <div class="navbar-nav mx-auto p-4 p-lg-0">
                <a href="{{ route($headerNavigation['home']['route']) }}" class="nav-item nav-link frontend-header-home-link {{ $headerNavigation['home']['is_active'] ? 'active' : '' }}"><i class="fa fa-home fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $headerNavigation['home']['label'] }}</a>
                <div class="nav-item dropdown frontend-courses-dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ $headerNavigation['courses_is_active'] ? 'active' : '' }}" data-bs-toggle="dropdown"><i class="fa fa-graduation-cap fa-sm fa-fw me-1" aria-hidden="true"></i>{{ __('dictt.ourcourses') }}</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        @foreach ($headerNavigation['course_groups'] as $courseGroup)
                            <div class="dropdown-submenu">
                                <span class="dropdown-item dropdown-submenu-toggle {{ $courseGroup['label'] === __('dictt.academic_exam_prep') ? 'frontend-header-academic-exam-link' : '' }} {{ $courseGroup['is_active'] ? 'active' : '' }}"><i class="fa fa-graduation-cap fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $courseGroup['label'] }}</span>
                                <div class="dropdown-menu bg-light rounded-0 m-0">
                                    @foreach ($courseGroup['items'] as $course)
                                        <a href="{{ route($course['route']) }}" class="dropdown-item {{ $course['is_active'] ? 'active' : '' }}"><i class="fa fa-graduation-cap fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $course['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @foreach ($headerNavigation['secondary_links'] as $secondaryLink)
                    <a href="{{ route($secondaryLink['route']) }}" class="nav-item nav-link {{ $secondaryLink['is_active'] ? 'active' : '' }}"><i class="fa {{ $desktopSecondaryLinkIcons[$secondaryLink['route']] ?? 'fa-link' }} fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $secondaryLink['label'] }}</a>
                @endforeach
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ $headerNavigation['branches']['is_active'] ? 'active' : '' }}" data-bs-toggle="dropdown"><i class="fa fa-map-marker-alt fa-sm fa-fw me-1" aria-hidden="true"></i>{{ __('dictt.branches') }}</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        @foreach ($headerNavigation['branches']['items'] as $branch)
                            <a href="{{ route($branch['route']) }}" class="dropdown-item {{ $branch['is_active'] ? 'active' : '' }}"><i class="fa fa-map-marker-alt fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $branch['label'] }}</a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route($headerNavigation['placement_test']['route']) }}" class="nav-item nav-link {{ $headerNavigation['placement_test']['is_active'] ? 'active' : '' }}"><i class="fa fa-clipboard-check fa-sm fa-fw me-1" aria-hidden="true"></i>{{ $headerNavigation['placement_test']['label'] }}</a>
                <a href="{{ route('changeLanguage', $headerLocale === 'tr' ? 'en' : 'tr') }}" class="nav-item nav-link">
                    <i class="fa fa-globe fa-sm fa-fw me-1" aria-hidden="true"></i>
                    <span class="av-lang-badge" aria-label="{{ $headerLocale === 'tr' ? __('dictt.lang_en') : __('dictt.lang_tr') }}">{{ $headerLocale === 'tr' ? 'EN' : 'TR' }}</span>
                </a>
                @auth
                    <a href="{{ route('profile.show') }}" class="nav-item nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="fa fa-user-circle fa-sm fa-fw me-1" aria-hidden="true"></i>{{ __('dictt.profile') }}
                    </a>
                @endauth
            </div>
            @guest
                <a href="{{ route('frontend.login', ['return' => $headerReturnRoute]) }}" class="btn btn-primary frontend-header-action rounded-0 py-4 px-lg-5">{{ __('dictt.login') }}<i class="fa fa-arrow-left ms-3" aria-hidden="true"></i></a>
            @else
                <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-stretch">
                    @csrf
                    <input type="hidden" name="return" value="{{ $headerReturnRoute }}">
                    <button type="submit" class="btn btn-primary frontend-header-action rounded-0 py-4 px-lg-5 border-0" title="{{ __('dictt.logout') }}">{{ __('dictt.logout') }}<i class="fa fa-sign-out-alt ms-3" aria-hidden="true"></i></button>
                </form>
            @endguest
        </div>
    </nav>
    <!-- Desktop Navbar End -->
