@php
    $currentQuestion = $placementTestQuestion ?? null;
    $submittedOptions = old('options');
    $optionRows = [];

    if (is_array($submittedOptions)) {
        foreach ($submittedOptions as $option) {
            $optionRows[] = [
                'key' => 'option-' . count($optionRows),
                'text' => (string) data_get($option, 'text', ''),
            ];
        }
    } elseif ($currentQuestion !== null) {
        foreach ($currentQuestion->options as $option) {
            $optionRows[] = [
                'key' => 'option-' . count($optionRows),
                'text' => $option->option_text,
            ];
        }
    }

    $minimumOptionRows = $currentQuestion === null && ! is_array($submittedOptions) ? 4 : 2;

    while (count($optionRows) < $minimumOptionRows) {
        $optionRows[] = [
            'key' => 'option-' . count($optionRows),
            'text' => '',
        ];
    }

    $savedCorrectOptionIndex = 0;

    if ($currentQuestion !== null) {
        foreach ($currentQuestion->options as $index => $option) {
            if ($option->is_correct) {
                $savedCorrectOptionIndex = $index;
                break;
            }
        }
    }

    $initialLevelId = old(
        'placement_test_level_id',
        $currentQuestion?->placement_test_level_id ?? ($defaultLevelId ?? null),
    );
    $initialContentId = old(
        'placement_test_question_content_id',
        $currentQuestion?->placement_test_question_content_id ?? ($defaultContentId ?? null),
    );
    $initialContentPosition = old(
        'content_position',
        $currentQuestion?->content_position ?? ($defaultContentPosition ?? null),
    );
    $initialCorrectOptionIndex = old('correct_option_index', $savedCorrectOptionIndex);
    $initialIsActive = old('is_active', $currentQuestion?->is_active ?? true);
    $initialPoints = (int) old('points', $currentQuestion?->points ?? 4);
    $initialPoints = min(20, max(1, $initialPoints));
@endphp

<div class="card"
    x-data="placementQuestionForm({
        levelId: @js($initialLevelId),
        contentId: @js($initialContentId),
        contentPosition: @js($initialContentPosition),
        options: @js($optionRows),
        correctOptionIndex: @js($initialCorrectOptionIndex),
        contents: @js($contents),
    })">
    <div class="card-body">
        @isset($pageTitle)
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <a href="{{ route('placement_test_questions_list') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.back') }}
                    </a>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
        @else
            <h5 class="card-title">
                <a href="{{ route('placement_test_questions_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.back') }}
                </a>
            </h5>
        @endisset

        @if ($currentQuestion !== null && $currentQuestion->levelQuestionSnapshots()->exists())
            <div class="alert alert-info" role="alert">
                {{ __('dictt.pt_question_used_alert') }}
            </div>
        @endif

        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <input type="hidden" name="is_active" value="{{ $initialIsActive ? '1' : '0' }}">

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="placement_test_level_id" class="form-label">{{ __('dictt.level') }}</label>
                    <select id="placement_test_level_id" name="placement_test_level_id" x-model="levelId" x-on:change="clearInvalidContent()"
                        class="form-control @error('placement_test_level_id') is-invalid @enderror" required>
                        <option value="">{{ __('dictt.select_level') }}</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->code }}</option>
                        @endforeach
                    </select>
                    @error('placement_test_level_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="placement_test_question_content_id" class="form-label">{{ __('dictt.question_contents') }}</label>
                    <input type="hidden" name="placement_test_question_content_id" x-bind:value="contentId">
                    <input type="hidden" name="content_position" x-bind:value="contentPosition">
                    <select id="placement_test_question_content_id" x-model="contentId"
                        x-on:change="selectQuestionContent($event.target.value)"
                        class="form-control @error('placement_test_question_content_id') is-invalid @enderror"
                        x-bind:disabled="!levelId">
                        <option value="">{{ __('dictt.independent_question') }}</option>
                        <template x-for="content in contentsForSelectedLevel" :key="content.id">
                            <option x-bind:value="String(content.id)"
                                x-bind:selected="String(content.id) === String(contentId)"
                                x-text="content.label"></option>
                        </template>
                    </select>
                    @error('placement_test_question_content_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('content_position')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="question_text" class="form-label">{{ __('dictt.question_text_label') }}</label>
                <textarea id="question_text" name="question_text" rows="5"
                    class="form-control @error('question_text') is-invalid @enderror" required>{{ old('question_text', $currentQuestion?->question_text) }}</textarea>
                @error('question_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="points" class="form-label">{{ __('dictt.question_points') }}</label>
                <div x-data="{ points: {{ $initialPoints }} }">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">1 – 20</span>
                        <span class="badge text-bg-primary" x-text="points">{{ $initialPoints }}</span>
                    </div>
                    <input id="points" name="points" type="range" min="1" max="20" step="1"
                        class="form-range @error('points') is-invalid @enderror"
                        x-model.number="points" value="{{ $initialPoints }}" required>
                    @error('points')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h6 class="mb-1">{{ __('dictt.options_title') }}</h6>
                        <p class="text-muted small mb-0">{{ __('dictt.pt_options_hint') }}</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" x-on:click="addOption()">
                        <i class="fa fa-plus"></i> {{ __('dictt.add_option') }}
                    </button>
                </div>

                <div class="vstack gap-2">
                    <template x-for="(option, index) in options" :key="option.key">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="radio" name="correct_option_index" x-bind:value="index" x-model.number="correctOptionIndex"
                                    x-bind:aria-label="(index + 1) + '. ' + '{{ __('dictt.pt_option_correct_suffix') }}'">
                            </div>
                            <textarea rows="2" x-bind:id="'option-text-' + option.key" x-bind:name="'options[' + index + '][text]'"
                                x-model="option.text" class="form-control" x-bind:placeholder="(index + 1) + '. ' + '{{ __('dictt.pt_option_placeholder_suffix') }}'" required></textarea>
                            <button type="button" class="btn btn-outline-danger" x-on:click="removeOption(index)"
                                x-bind:disabled="options.length <= 2" title="{{ __('dictt.remove_option') }}">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </template>
                </div>

                @error('options')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                @error('correct_option_index')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                @if ($errors->get('options.*.text'))
                    <div class="text-danger small mt-2">{{ __('dictt.pt_options_hint') }}</div>
                @endif
            </div>

            <div class="form-group mt-2">
                <button type="submit" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    function placementQuestionForm(config) {
        return {
            levelId: config.levelId ?? '',
            contentId: config.contentId ?? '',
            contentPosition: config.contentPosition ?? '',
            options: config.options,
            correctOptionIndex: config.correctOptionIndex ?? 0,
            contents: config.contents,
            nextOptionKey: config.options.length,

            get contentsForSelectedLevel() {
                return this.contents.filter((content) => String(content.level_id) === String(this.levelId));
            },

            clearInvalidContent() {
                const selectedContent = this.contents.find((content) => String(content.id) === String(this.contentId));

                if (selectedContent && String(selectedContent.level_id) !== String(this.levelId)) {
                    this.contentId = '';
                    this.contentPosition = '';
                }
            },

            selectQuestionContent(contentId) {
                this.contentId = contentId;

                if (!contentId) {
                    this.contentPosition = '';

                    return;
                }

                const selectedContent = this.contents.find((content) => String(content.id) === String(contentId));
                this.contentPosition = selectedContent?.next_position ?? '';
            },

            addOption() {
                this.options.push({
                    key: `option-${this.nextOptionKey++}`,
                    text: '',
                });
            },

            removeOption(index) {
                if (this.options.length <= 2) {
                    return;
                }

                this.options.splice(index, 1);

                if (Number(this.correctOptionIndex) === index) {
                    this.correctOptionIndex = 0;
                } else if (Number(this.correctOptionIndex) > index) {
                    this.correctOptionIndex--;
                }
            },
        };
    }
</script>
