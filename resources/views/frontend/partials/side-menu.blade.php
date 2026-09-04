{{-- Frontend sol menü: üst navbar'dan bağımsız, sabit konumlu panel --}}
<link rel="stylesheet" href="{{ asset('frontend/css/side-menu.css') }}?v=26">
<div id="fsmRoot" class="fsm-root is-closed">
    <nav id="fsmPanel" class="fsm-panel">
        <div id="fsmAccordion" class="fsm-body">
            @if ($fsmHasDocumentThemes)
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
                                    href="{{ route('frontend.materials') }}"><i class="fa fa-circle fsm-subbullet" aria-hidden="true"></i>{{ mb_convert_case($fsmSubLevel->name, MB_CASE_TITLE, 'UTF-8') }}</a>
                            @endauth
                        @endforeach
                    </div>
                    <hr class="fsm-divider">
                @endforeach
            @else
                <div class="p-3 text-center text-muted">{{ __('dictt.documents_menu_coming_soon') }}</div>
            @endif
        </div>
        <button type="button" id="fsmStrip" class="fsm-strip {{ $fsmHasActiveDocument ? 'active' : '' }}"
            aria-expanded="false"
            aria-label="{{ __('dictt.materials_menu') }}">
            <i class="fa fa-chevron-down fsm-strip-chevron" aria-hidden="true"></i>
            <span class="fsm-strip-text">{{ strtoupper(__('dictt.materials_menu')) }}</span>
        </button>
    </nav>
</div>

<script src="{{ asset('frontend/js/side-menu.js') }}?v=3" defer></script>
