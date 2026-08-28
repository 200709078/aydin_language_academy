<nav x-data="{
        sidebarOpen: (function () { var v = localStorage.getItem('adminSidebarOpenV2'); return v === null ? true : v === 'true'; })(),
        themesOpen: {{ request()->routeIs('levels_list', 'sub_levels_list', 'themes_list') ? 'true' : 'false' }},
        siteSettingsOpen: {{ request()->routeIs('reviews_list', 'review_edit', 'admin.messages.*') ? 'true' : 'false' }},
        placementOpen: {{ request()->routeIs('placement_test_levels_*', 'placement_test_question_contents_*', 'placement_test_questions_*', 'placement_test_attempts_*') ? 'true' : 'false' }},
        userOpen: {{ request()->routeIs('admin.profile.show') ? 'true' : 'false' }},
        languageOpen: false,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('adminSidebarOpenV2', this.sidebarOpen ? 'true' : 'false');
        },
        toggleGroup(group) {
            if (!this.sidebarOpen) {
                this.sidebarOpen = true;
                localStorage.setItem('adminSidebarOpenV2', 'true');
                this[group] = true;
                return;
            }
            this[group] = !this[group];
        }
    }"
    :class="{ 'is-collapsed': ! sidebarOpen }"
    class="admin-navigation border-b border-gray-200 bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('admin') }}"
            class="admin-navigation-brand"
            role="button"
            @click.prevent.stop="toggleSidebar()"
            :aria-expanded="sidebarOpen.toString()"
            aria-label="{{ app()->getLocale() === 'tr' ? 'Menüyü aç/kapat' : 'Toggle menu' }}">
            <img src="{{ asset('frontend/images/logo/favicon.png') }}" alt="AYDIN LANGUAGE ACADEMY" class="admin-navigation-brand-logo">
            <span class="admin-navigation-label">ALA</span>
        </a>
        <div class="flex min-h-16 items-center justify-between gap-6">
            <div class="hidden flex-1 items-center justify-start gap-2 lg:flex">
                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('admin.profile.show') ? 'is-active' : '' }}"
                        @click="toggleGroup('userOpen')" :aria-expanded="userOpen.toString()">
                        <i class="fas fa-user admin-navigation-icon" aria-hidden="true"></i>
                        <span class="admin-navigation-label">{{ Auth::user()->name }}</span>
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': userOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="userOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('admin.profile.show') }}" class="admin-navigation-collapse-link {{ request()->routeIs('admin.profile.show') ? 'is-active' : '' }}"><i class="fas fa-user-pen admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-navigation-collapse-link w-full text-left"><i class="fas fa-right-from-bracket admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.logout') }}</button>
                        </form>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('reviews_list', 'review_edit', 'admin.messages.*') ? 'is-active' : '' }}"
                        @click="toggleGroup('siteSettingsOpen')" :aria-expanded="siteSettingsOpen.toString()">
                        <i class="fas fa-gear admin-navigation-icon" aria-hidden="true"></i>
                        <span class="admin-navigation-label">{{ __('dictt.site_settings') }}</span>
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': siteSettingsOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="siteSettingsOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('reviews_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('reviews_list', 'review_edit') ? 'is-active' : '' }}"><i class="fas fa-star admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.reviews') }}</a>
                        <a href="{{ route('admin.messages.index') }}" class="admin-navigation-collapse-link {{ request()->routeIs('admin.messages.*') ? 'is-active' : '' }}"><i class="fas fa-envelope admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.contact_messages') }}</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('levels_list', 'sub_levels_list', 'themes_list') ? 'is-active' : '' }}"
                        @click="toggleGroup('themesOpen')" :aria-expanded="themesOpen.toString()">
                        <i class="fas fa-layer-group admin-navigation-icon" aria-hidden="true"></i>
                        <span class="admin-navigation-label">{{ __('dictt.themes') }}</span>
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': themesOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="themesOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('levels_list') ? 'is-active' : '' }}"><i class="fas fa-list-ol admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.levels') }}</a>
                        <a href="{{ route('sub_levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('sub_levels_list') ? 'is-active' : '' }}"><i class="fas fa-sitemap admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.sublevels') }}</a>
                        <a href="{{ route('themes_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('themes_list') ? 'is-active' : '' }}"><i class="fas fa-palette admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.themes') }}</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('placement_test_levels_*', 'placement_test_question_contents_*', 'placement_test_questions_*', 'placement_test_attempts_*') ? 'is-active' : '' }}"
                        @click="toggleGroup('placementOpen')" :aria-expanded="placementOpen.toString()">
                        <i class="fas fa-clipboard-check admin-navigation-icon" aria-hidden="true"></i>
                        <span class="admin-navigation-label">{{ __('dictt.placement_test') }}</span>
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': placementOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="placementOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('placement_test_levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_levels_list') ? 'is-active' : '' }}"><i class="fas fa-list-ol admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.levels') }}</a>
                        <a href="{{ route('placement_test_question_contents_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_question_contents_list') ? 'is-active' : '' }}"><i class="fas fa-photo-film admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.question_contents') }}</a>
                        <a href="{{ route('placement_test_questions_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_questions_list') ? 'is-active' : '' }}"><i class="fas fa-circle-question admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.questions') }}</a>
                        <a href="{{ route('placement_test_attempts_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_attempts_*') ? 'is-active' : '' }}"><i class="fas fa-clipboard-list admin-navigation-subicon" aria-hidden="true"></i>{{ __('dictt.placement_test_results') }}</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger"
                        @click="toggleGroup('languageOpen')" :aria-expanded="languageOpen.toString()">
                        <i class="fas fa-globe admin-navigation-icon" aria-hidden="true"></i>
                        <span class="admin-navigation-label">{{ __('dictt.languages') }}</span>
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': languageOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="languageOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('changeLanguage', 'tr') }}" class="admin-navigation-collapse-link {{ session('locale', config('app.locale')) === 'tr' ? 'is-active' : '' }}"><span class="admin-nav-lang-badge">TR</span>{{ __('dictt.lang_tr') }}</a>
                        <a href="{{ route('changeLanguage', 'en') }}" class="admin-navigation-collapse-link {{ session('locale', config('app.locale')) === 'en' ? 'is-active' : '' }}"><span class="admin-nav-lang-badge">EN</span>{{ __('dictt.lang_en') }}</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

            </div>

        </div>

    </div>

    <button type="button"
        class="admin-sidebar-toggle"
        @click="toggleSidebar()"
        :aria-expanded="sidebarOpen.toString()"
        aria-label="{{ app()->getLocale() === 'tr' ? 'Menüyü aç/kapat' : 'Toggle menu' }}">
        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M8.72 15.47a.75.75 0 0 1 0-1.06L12.19 10 8.72 6.53a.75.75 0 1 1 1.06-1.06l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
        </svg>
    </button>
</nav>
