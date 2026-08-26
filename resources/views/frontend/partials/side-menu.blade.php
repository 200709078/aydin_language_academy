{{-- Frontend sol menü: üst navbar'dan bağımsız, sabit konumlu panel --}}
<link rel="stylesheet" href="{{ asset('frontend/css/side-menu.css') }}?v=24">

@php
    if (! isset($fsmSubLevelsByLevel)) {
        $fsmThemePairs = \App\Models\model_themes::query()
            ->select(['level_id', 'sub_level_id'])
            ->whereNotNull('level_id')
            ->whereNotNull('sub_level_id')
            ->distinct()
            ->with('sub_levels:id,name,slug')
            ->orderBy('level_id')
            ->orderBy('sub_level_id')
            ->get();
        $fsmSubLevelsByLevel = $fsmThemePairs
            ->groupBy('level_id')
            ->map(fn ($themePairs) => $themePairs
                ->map(fn ($themePair) => $themePair->sub_levels)
                ->filter()
                ->values())
            ->filter(fn ($subLevels) => $subLevels->isNotEmpty());
    }

    $fsmLevels ??= \App\Models\model_levels::query()
        ->whereIn('id', $fsmSubLevelsByLevel->keys()->all())
        ->orderBy('id')
        ->get(['id', 'name', 'slug']);
    $fsmActiveLevelId ??= null;
    $fsmActiveSubLevelId ??= null;
    $fsmHasActiveDocument ??= false;
@endphp

<div id="fsmRoot" class="fsm-root">
    <nav id="fsmPanel" class="fsm-panel">
        <div id="fsmAccordion" class="fsm-body">
            @foreach ($fsmLevels as $fsmLevel)
                <button type="button" class="fsm-item {{ $fsmActiveLevelId == $fsmLevel->id ? 'active' : '' }}"
                    data-bs-toggle="collapse"
                    data-bs-target="#fsm-level-collapse-{{ $fsmLevel->id }}"
                    aria-expanded="false"
                    aria-controls="fsm-level-collapse-{{ $fsmLevel->id }}">
                    <i class="fa fa-circle fsm-bullet" aria-hidden="true"></i>
                    <span>{{ mb_strtoupper($fsmLevel->name, 'UTF-8') }}</span>
                    <i class="fa fa-chevron-down fsm-chevron" aria-hidden="true"></i>
                </button>
                <div id="fsm-level-collapse-{{ $fsmLevel->id }}" class="fsm-collapse collapse" data-bs-parent="#fsmAccordion">
                    @foreach ($fsmSubLevelsByLevel->get($fsmLevel->id, collect()) as $fsmSubLevel)
                        @auth
                            <a class="fsm-subitem {{ $fsmActiveLevelId == $fsmLevel->id && $fsmActiveSubLevelId == $fsmSubLevel->id ? 'active' : '' }}"
                                href="{{ route('frontend.themes.list', [$fsmLevel->slug, $fsmSubLevel->slug]) }}"><i class="fa fa-circle fsm-subbullet" aria-hidden="true"></i>{{ mb_convert_case($fsmSubLevel->name, MB_CASE_TITLE, 'UTF-8') }}</a>
                        @else
                            <a class="fsm-subitem {{ $fsmActiveLevelId == $fsmLevel->id && $fsmActiveSubLevelId == $fsmSubLevel->id ? 'active' : '' }}"
                                href="{{ route('frontend.documents') }}"><i class="fa fa-circle fsm-subbullet" aria-hidden="true"></i>{{ mb_convert_case($fsmSubLevel->name, MB_CASE_TITLE, 'UTF-8') }}</a>
                        @endauth
                    @endforeach
                </div>
                <hr class="fsm-divider">
            @endforeach
        </div>
        <button type="button" id="fsmStrip" class="fsm-strip {{ $fsmHasActiveDocument ? 'active' : '' }}"
            aria-expanded="true"
            aria-label="{{ __('dictt.documents') }}">
            <i class="fa fa-chevron-down fsm-strip-chevron" aria-hidden="true"></i>
            <span class="fsm-strip-text">{{ strtoupper(__('dictt.documents')) }}</span>
        </button>
    </nav>
</div>

<script src="{{ asset('frontend/js/side-menu.js') }}?v=2" defer></script>
