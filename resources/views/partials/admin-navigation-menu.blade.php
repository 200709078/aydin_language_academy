<nav x-data="{ themesOpen: {{ request()->routeIs('levels_list', 'sub_levels_list', 'themes_list', 'courses_list') ? 'true' : 'false' }}, placementOpen: {{ request()->routeIs('placement_test_levels_*', 'placement_test_question_contents_*', 'placement_test_questions_*') ? 'true' : 'false' }}, userOpen: {{ request()->routeIs('settings_list', 'profile.show') ? 'true' : 'false' }} }"
    class="admin-navigation border-b border-gray-200 bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('admin') }}" class="admin-navigation-brand">
            <img src="{{ asset('front/img/favicon.png') }}" alt="AYDIN LANGUAGE ACADEMY" class="admin-navigation-brand-logo">
            <span>ALA</span>
        </a>
        <div class="flex min-h-16 items-center justify-between gap-6">
            <div class="hidden flex-1 items-center justify-start gap-2 lg:flex">
                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('settings_list', 'profile.show') ? 'is-active' : '' }}"
                        @click="userOpen = !userOpen" :aria-expanded="userOpen.toString()">
                        {{ Auth::user()->name }}
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': userOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 1 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.512a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="userOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('settings_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('settings_list') ? 'is-active' : '' }}">Settings</a>
                        <a href="{{ route('profile.show') }}" class="admin-navigation-collapse-link {{ request()->routeIs('profile.show') ? 'is-active' : '' }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-navigation-collapse-link w-full text-left">Log Out</button>
                        </form>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('levels_list', 'sub_levels_list', 'themes_list', 'courses_list') ? 'is-active' : '' }}"
                        @click="themesOpen = !themesOpen" :aria-expanded="themesOpen.toString()">
                        Themes
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': themesOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.512a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="themesOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('levels_list') ? 'is-active' : '' }}">Levels</a>
                        <a href="{{ route('sub_levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('sub_levels_list') ? 'is-active' : '' }}">Sub Levels</a>
                        <a href="{{ route('themes_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('themes_list') ? 'is-active' : '' }}">Themes</a>
                        <a href="{{ route('courses_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('courses_list') ? 'is-active' : '' }}">Courses</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

                <div class="admin-navigation-group">
                    <button type="button"
                        class="admin-navigation-trigger {{ request()->routeIs('placement_test_levels_*', 'placement_test_question_contents_*', 'placement_test_questions_*') ? 'is-active' : '' }}"
                        @click="placementOpen = !placementOpen" :aria-expanded="placementOpen.toString()">
                        Placement Test
                        <svg class="admin-navigation-arrow h-4 w-4" :class="{ 'is-open': placementOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 1 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.512a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="placementOpen" x-transition class="admin-navigation-collapse" style="display: none;">
                        <a href="{{ route('placement_test_levels_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_levels_list') ? 'is-active' : '' }}">Levels</a>
                        <a href="{{ route('placement_test_question_contents_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_question_contents_list') ? 'is-active' : '' }}">Ortak İçerikler</a>
                        <a href="{{ route('placement_test_questions_list') }}" class="admin-navigation-collapse-link {{ request()->routeIs('placement_test_questions_list') ? 'is-active' : '' }}">Sorular</a>
                    </div>
                </div>

                <hr class="admin-navigation-divider">

            </div>

        </div>

    </div>
</nav>
