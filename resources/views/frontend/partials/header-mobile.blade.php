    <!-- Mobile Navbar Start -->
    @php
        $mobileSecondaryLinkIcons = [
            'frontend.achievements' => 'fa-trophy',
            'frontend.campaigns' => 'fa-tags',
        ];
    @endphp

    <nav class="navbar navbar-expand-xl bg-light navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <button type="button"
            class="navbar-brand d-flex align-items-center px-4 px-xl-5"
            data-bs-toggle="collapse"
            data-bs-target="#frontendMobileNavbarCollapse"
            aria-controls="frontendMobileNavbarCollapse"
            aria-expanded="false"
            aria-label="{{ $headerLocale === 'tr' ? 'Menüyü aç/kapat' : 'Toggle menu' }}">
            <img src="{{ asset('frontend/images/logo/logo-2.png') }}" alt="Aydın Language Academy" style="height: 56px; width: auto;">
            <span class="frontend-brand-wordmark">ALA</span>
        </button>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#frontendMobileNavbarCollapse" aria-controls="frontendMobileNavbarCollapse" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="frontendMobileNavbarCollapse">
            <div class="navbar-nav mx-auto p-4 p-xl-0">
                <a href="{{ route($headerNavigation['home']['route']) }}" class="nav-item nav-link {{ $headerNavigation['home']['is_active'] ? 'active' : '' }}"><i class="fa fa-home fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $headerNavigation['home']['label'] }}</a>
                <div class="nav-item dropdown frontend-courses-dropdown">
                    <a href="#" class="nav-link dropdown-toggle mobile-dropdown-toggle {{ $headerNavigation['courses_is_active'] ? 'active' : '' }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="fa fa-graduation-cap fa-sm fa-fw me-2" aria-hidden="true"></i>{{ __('dictt.ourcourses') }}</a>
                    <div id="frontendMobileCourseGroups" class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        @foreach ($headerNavigation['course_groups'] as $courseGroup)
                            @php
                                $mobileCourseGroupCollapseId = 'frontendMobileCourseGroup' . $loop->index;
                            @endphp
                            <div class="dropdown-submenu">
                                <button type="button" class="dropdown-item dropdown-submenu-toggle mobile-course-submenu-toggle text-start {{ $courseGroup['is_active'] ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#{{ $mobileCourseGroupCollapseId }}" aria-controls="{{ $mobileCourseGroupCollapseId }}" aria-expanded="false"><i class="fa fa-graduation-cap fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $courseGroup['label'] }}</button>
                                <div id="{{ $mobileCourseGroupCollapseId }}" class="collapse mobile-course-submenu-content" data-bs-parent="#frontendMobileCourseGroups">
                                    @foreach ($courseGroup['items'] as $course)
                                        <a href="{{ route($course['route']) }}" class="dropdown-item {{ $course['is_active'] ? 'active' : '' }}"><i class="fa fa-graduation-cap fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $course['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @foreach ($headerNavigation['secondary_links'] as $secondaryLink)
                    <a href="{{ route($secondaryLink['route']) }}" class="nav-item nav-link {{ $secondaryLink['is_active'] ? 'active' : '' }}"><i class="fa {{ $mobileSecondaryLinkIcons[$secondaryLink['route']] ?? 'fa-link' }} fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $secondaryLink['label'] }}</a>
                @endforeach
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle mobile-dropdown-toggle {{ $headerNavigation['branches']['is_active'] ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-map-marker-alt fa-sm fa-fw me-2" aria-hidden="true"></i>{{ __('dictt.branches') }}</a>
                    <div class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        @foreach ($headerNavigation['branches']['items'] as $branch)
                            <a href="{{ route($branch['route']) }}" class="dropdown-item {{ $branch['is_active'] ? 'active' : '' }}"><i class="fa fa-map-marker-alt fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $branch['label'] }}</a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route($headerNavigation['placement_test']['route']) }}" class="nav-item nav-link {{ $headerNavigation['placement_test']['is_active'] ? 'active' : '' }}"><i class="fa fa-clipboard-check fa-sm fa-fw me-2" aria-hidden="true"></i>{{ $headerNavigation['placement_test']['label'] }}</a>
                <hr class="mobile-documents-divider">
                <div class="nav-item dropdown frontend-documents-dropdown">
                    <a href="#" class="nav-link dropdown-toggle mobile-dropdown-toggle mobile-documents-heading {{ $headerHasActiveDocument ? 'active' : '' }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="fa fa-folder-open fa-sm fa-fw me-2" aria-hidden="true"></i>
                        <span>{{ __('dictt.documents') }}</span>
                    </a>
                    <div id="frontendMobileDocumentGroups" class="dropdown-menu bg-light rounded-0 rounded-bottom m-0">
                        @foreach ($headerDocumentLevels as $headerDocumentLevel)
                            @php
                                $mobileDocumentLevelCollapseId = 'frontendMobileDocumentLevel' . $headerDocumentLevel->id;
                                $mobileDocumentSubLevels = $headerDocumentSubLevelsByLevel->get($headerDocumentLevel->id, collect());
                                $mobileDocumentLevelIsActive = $headerActiveDocumentLevelId == $headerDocumentLevel->id;
                            @endphp
                            <button type="button" class="nav-item nav-link mobile-document-level-toggle border-0 bg-transparent w-100 text-start {{ $mobileDocumentLevelIsActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#{{ $mobileDocumentLevelCollapseId }}" aria-controls="{{ $mobileDocumentLevelCollapseId }}" aria-expanded="false"><i class="fa fa-book-open fa-sm fa-fw me-2" aria-hidden="true"></i>{{ mb_strtoupper($headerDocumentLevel->name, 'UTF-8') }}</button>
                            <div id="{{ $mobileDocumentLevelCollapseId }}" class="collapse mobile-document-level-content" data-bs-parent="#frontendMobileDocumentGroups">
                                @foreach ($mobileDocumentSubLevels as $headerDocumentSubLevel)
                                    @php
                                        $mobileDocumentSubLevelIsActive = $mobileDocumentLevelIsActive
                                            && $headerActiveDocumentSubLevelId == $headerDocumentSubLevel->id;
                                    @endphp
                                    @auth
                                        <a href="{{ route('frontend.themes.list', [$headerDocumentLevel->slug, $headerDocumentSubLevel->slug]) }}" class="nav-item nav-link mobile-document-subitem {{ $mobileDocumentSubLevelIsActive ? 'active' : '' }}"><i class="fa fa-circle fa-xs fa-fw me-2" aria-hidden="true"></i>{{ mb_convert_case($headerDocumentSubLevel->name, MB_CASE_TITLE, 'UTF-8') }}</a>
                                    @else
                                        <a href="{{ route('frontend.documents') }}" class="nav-item nav-link mobile-document-subitem {{ $mobileDocumentSubLevelIsActive ? 'active' : '' }}"><i class="fa fa-circle fa-xs fa-fw me-2" aria-hidden="true"></i>{{ mb_convert_case($headerDocumentSubLevel->name, MB_CASE_TITLE, 'UTF-8') }}</a>
                                    @endauth
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <hr class="mobile-documents-divider">
                <a href="{{ route('changeLanguage', $headerLocale === 'tr' ? 'en' : 'tr') }}" class="nav-item nav-link">
                    <i class="fa fa-globe fa-sm fa-fw me-2" aria-hidden="true"></i>
                    <span class="av-lang-badge" aria-label="{{ $headerLocale === 'tr' ? __('dictt.lang_en') : __('dictt.lang_tr') }}">{{ $headerLocale === 'tr' ? 'EN' : 'TR' }}</span>
                </a>
                @guest
                    <a href="{{ route('frontend.login', ['return' => $headerReturnRoute]) }}" class="nav-item nav-link"><i class="fa fa-sign-in-alt fa-sm fa-fw me-2" aria-hidden="true"></i>{{ __('dictt.login') }}</a>
                @else
                    <a href="{{ route('profile.show') }}" class="nav-item nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="fa fa-user-circle fa-sm fa-fw me-2" aria-hidden="true"></i>{{ __('dictt.profile') }}</a>
                    <div class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <input type="hidden" name="return" value="{{ $headerReturnRoute }}">
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start"><i class="fa fa-sign-out-alt me-2" aria-hidden="true"></i>{{ __('dictt.logout') }}</button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </nav>
    <!-- Mobile Navbar End -->

    <script src="{{ asset('frontend/js/mobile-menu.js') }}?v=1" defer></script>
