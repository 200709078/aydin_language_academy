    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">{{ __('dictt.loading') }}</span>
        </div>
    </div>
    <!-- Spinner End -->

    @php
        $headerLocale = session('locale') ?? config('app.locale');
        $headerReturnRoute = request()->route()?->getName();
        $headerDocumentLevels = \App\Models\model_levels::query()->orderBy('id')->get(['id', 'name', 'slug']);
        $headerDocumentSubLevels = \App\Models\model_sub_levels::query()->orderBy('id')->get(['id', 'name', 'slug']);

        $headerNavigation = [
            'home' => [
                'route' => 'home',
                'label' => __('dictt.home'),
                'is_active' => request()->routeIs('home', 'frontend.preview.home'),
            ],
            'courses_is_active' => request()->routeIs('frontend.courses.*'),
            'course_groups' => [
                [
                    'label' => __('dictt.featured_programs'),
                    'is_active' => request()->routeIs('frontend.courses.ielts', 'frontend.courses.yks-dil'),
                    'items' => [
                        ['route' => 'frontend.courses.ielts', 'label' => __('dictt.ielts_prep'), 'is_active' => request()->routeIs('frontend.courses.ielts')],
                        ['route' => 'frontend.courses.yks-dil', 'label' => __('dictt.yks_dil_prep'), 'is_active' => request()->routeIs('frontend.courses.yks-dil')],
                    ],
                ],
                [
                    'label' => __('dictt.academic_exam_prep'),
                    'is_active' => request()->routeIs('frontend.courses.yds-yokdil', 'frontend.courses.toefl', 'frontend.courses.pte-academic', 'frontend.courses.test-of-english', 'frontend.courses.sat'),
                    'items' => [
                        ['route' => 'frontend.courses.yds-yokdil', 'label' => __('dictt.yds_yokdil'), 'is_active' => request()->routeIs('frontend.courses.yds-yokdil')],
                        ['route' => 'frontend.courses.toefl', 'label' => __('dictt.toefl'), 'is_active' => request()->routeIs('frontend.courses.toefl')],
                        ['route' => 'frontend.courses.pte-academic', 'label' => __('dictt.pte_academic'), 'is_active' => request()->routeIs('frontend.courses.pte-academic')],
                        ['route' => 'frontend.courses.test-of-english', 'label' => __('dictt.test_of_english'), 'is_active' => request()->routeIs('frontend.courses.test-of-english')],
                        ['route' => 'frontend.courses.sat', 'label' => __('dictt.sat'), 'is_active' => request()->routeIs('frontend.courses.sat')],
                    ],
                ],
                [
                    'label' => __('dictt.kids_teens_english'),
                    'is_active' => request()->routeIs('frontend.courses.preschool', 'frontend.courses.primary-school', 'frontend.courses.middle-school', 'frontend.courses.high-school'),
                    'items' => [
                        ['route' => 'frontend.courses.preschool', 'label' => __('dictt.preschool'), 'is_active' => request()->routeIs('frontend.courses.preschool')],
                        ['route' => 'frontend.courses.primary-school', 'label' => __('dictt.primary_school'), 'is_active' => request()->routeIs('frontend.courses.primary-school')],
                        ['route' => 'frontend.courses.middle-school', 'label' => __('dictt.middle_school'), 'is_active' => request()->routeIs('frontend.courses.middle-school')],
                        ['route' => 'frontend.courses.high-school', 'label' => __('dictt.high_school'), 'is_active' => request()->routeIs('frontend.courses.high-school')],
                    ],
                ],
                [
                    'label' => __('dictt.adult_english'),
                    'is_active' => request()->routeIs('frontend.courses.general-english', 'frontend.courses.speaking-clubs'),
                    'items' => [
                        ['route' => 'frontend.courses.general-english', 'label' => __('dictt.general_english'), 'is_active' => request()->routeIs('frontend.courses.general-english')],
                        ['route' => 'frontend.courses.speaking-clubs', 'label' => __('dictt.speaking_clubs'), 'is_active' => request()->routeIs('frontend.courses.speaking-clubs')],
                    ],
                ],
            ],
            'secondary_links' => [
                ['route' => 'frontend.achievements', 'label' => __('dictt.achievements'), 'is_active' => request()->routeIs('frontend.achievements')],
                ['route' => 'frontend.campaigns', 'label' => __('dictt.campaigns'), 'is_active' => request()->routeIs('frontend.campaigns')],
            ],
            'branches' => [
                'is_active' => request()->routeIs('frontend.branches.*'),
                'items' => [
                    ['route' => 'frontend.branches.ortaca', 'label' => 'Ortaca', 'is_active' => request()->routeIs('frontend.branches.ortaca')],
                    ['route' => 'frontend.branches.dalaman', 'label' => 'Dalaman', 'is_active' => request()->routeIs('frontend.branches.dalaman')],
                    ['route' => 'frontend.branches.koycegiz', 'label' => 'Köyceğiz', 'is_active' => request()->routeIs('frontend.branches.koycegiz')],
                ],
            ],
            'placement_test' => [
                'route' => 'frontend.placement-test',
                'label' => __('dictt.placement_test'),
                'is_active' => request()->routeIs('frontend.placement-test'),
            ],
        ];
    @endphp

    <header class="frontend-header">
        <div class="frontend-header__desktop">
            @include('frontend.partials.header-desktop', [
                'headerNavigation' => $headerNavigation,
                'headerLocale' => $headerLocale,
                'headerReturnRoute' => $headerReturnRoute,
            ])
        </div>

        <div class="frontend-header__mobile">
            @include('frontend.partials.header-mobile', [
                'headerNavigation' => $headerNavigation,
                'headerLocale' => $headerLocale,
                'headerReturnRoute' => $headerReturnRoute,
                'headerDocumentLevels' => $headerDocumentLevels,
                'headerDocumentSubLevels' => $headerDocumentSubLevels,
            ])
        </div>
    </header>

    @include('frontend.partials.side-menu', [
        'fsmLevels' => $headerDocumentLevels,
        'fsmSubLevels' => $headerDocumentSubLevels,
    ])
