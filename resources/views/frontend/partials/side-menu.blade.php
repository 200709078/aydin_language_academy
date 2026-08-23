{{-- Frontend sol menü: üst navbar'dan bağımsız, sabit konumlu panel --}}
<link rel="stylesheet" href="{{ asset('frontend/css/side-menu.css') }}?v=14">

@php
    $fsmLevels = \App\Models\model_levels::query()->orderBy('id')->get(['id', 'name', 'slug']);
    $fsmSubLevels = \App\Models\model_sub_levels::query()->orderBy('id')->get(['id', 'name', 'slug']);
@endphp

<div id="fsmRoot" class="fsm-root">
    @auth
    <nav id="fsmPanel" class="fsm-panel">
        <div class="fsm-body">
            @foreach ($fsmLevels as $fsmLevel)
                <button type="button" class="fsm-item"
                    data-bs-toggle="collapse"
                    data-bs-target="#fsm-level-collapse-{{ $fsmLevel->id }}"
                    aria-expanded="false"
                    aria-controls="fsm-level-collapse-{{ $fsmLevel->id }}">
                    <i class="fa fa-circle fsm-bullet" aria-hidden="true"></i>
                    <span>{{ ucfirst(mb_strtolower($fsmLevel->name)) }}</span>
                    <i class="fa fa-chevron-down fsm-chevron" aria-hidden="true"></i>
                </button>
                <div id="fsm-level-collapse-{{ $fsmLevel->id }}" class="fsm-collapse collapse">
                    @foreach ($fsmSubLevels as $fsmSubLevel)
                        <a class="fsm-subitem"
                            href="{{ route('frontend.themes.list', [$fsmLevel->slug, $fsmSubLevel->slug]) }}"><i class="fa fa-circle fsm-subbullet" aria-hidden="true"></i>{{ ucfirst(mb_strtolower($fsmSubLevel->name)) }}</a>
                    @endforeach
                </div>
                <hr class="fsm-divider">
            @endforeach
        </div>
        <button type="button" id="fsmStrip" class="fsm-strip"
            aria-expanded="true"
            aria-label="{{ __('dictt.documents') }}">
            <span class="fsm-strip-text">{{ strtoupper(__('dictt.documents')) }}</span>
        </button>
    </nav>
    @endauth
    @guest
    <a id="fsmStripGuest" class="fsm-strip fsm-strip--solo"
        href="{{ route('frontend.documents') }}"
        aria-label="{{ __('dictt.documents') }}">
        <span class="fsm-strip-text">{{ strtoupper(__('dictt.documents')) }}</span>
    </a>
    @endguest
</div>

<script src="{{ asset('frontend/js/side-menu.js') }}?v=2" defer></script>
