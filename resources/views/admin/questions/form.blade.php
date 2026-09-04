@php
    $currentQuestion = $question ?? null;
    $submittedOptions = old('options');
    $optionRows = [];

    if (is_array($submittedOptions)) {
        foreach ($submittedOptions as $option) {
            $optionRows[] = [
                'key' => 'option-' . count($optionRows),
                'id' => data_get($option, 'id'),
                'text' => (string) data_get($option, 'text', ''),
            ];
        }
    } elseif ($currentQuestion !== null) {
        foreach ($currentQuestion->options as $option) {
            $optionRows[] = [
                'key' => 'option-' . count($optionRows),
                'id' => $option->id,
                'text' => $option->option_text,
            ];
        }
    }

    $minimumOptionRows = $currentQuestion === null && ! is_array($submittedOptions) ? 4 : 2;

    while (count($optionRows) < $minimumOptionRows) {
        $optionRows[] = [
            'key' => 'option-' . count($optionRows),
            'id' => null,
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

    $initialCorrectOptionIndex = old('correct_option_index', $savedCorrectOptionIndex);
    $exerciseId = $currentQuestion?->exercise_id ?? $exercise->id;
@endphp

<div class="card"
    x-data="legacyQuestionForm({
        options: @js($optionRows),
        correctOptionIndex: @js($initialCorrectOptionIndex),
    })">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('questions_list', ['exercise_id' => $exerciseId]) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="legacy-question-form" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="legacy-question-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="form-group mb-3">
                <label for="question">{{ __('dictt.question') }}</label>
                <textarea id="question" name="question" class="form-control @error('question') is-invalid @enderror" rows="4" required>{{ old('question', $currentQuestion?->question) }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="image">{{ __('dictt.image') }}</label>
                @php($imageUrl = $currentQuestion?->privateImageUrl())
                @if ($imageUrl)
                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                        <img class="img-fluid rounded d-block mb-2" src="{{ $imageUrl }}"
                            style="width:120px" alt="">
                    </a>
                @endif
                <input id="image" type="file" name="image" class="form-control">
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

                <div class="d-flex flex-column gap-2">
                    <template x-for="(option, index) in options" :key="option.key">
                        <div class="input-group">
                            <template x-if="option.id">
                                <input type="hidden" x-bind:name="'options[' + index + '][id]'" x-bind:value="option.id">
                            </template>
                            <div class="input-group-text">
                                <input type="radio" name="correct_option_index" x-bind:value="index"
                                    x-model.number="correctOptionIndex"
                                    x-bind:aria-label="(index + 1) + '. ' + '{{ __('dictt.pt_option_correct_suffix') }}'">
                            </div>
                            <textarea rows="2" x-bind:id="'option-text-' + option.key"
                                x-bind:name="'options[' + index + '][text]'" x-model="option.text"
                                class="form-control" x-bind:placeholder="(index + 1) + '. ' + '{{ __('dictt.pt_option_placeholder_suffix') }}'"
                                required></textarea>
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

        </form>
    </div>
</div>

<script>
    function legacyQuestionForm(config) {
        return {
            options: config.options,
            correctOptionIndex: config.correctOptionIndex ?? 0,
            nextOptionKey: config.options.length,

            addOption() {
                this.options.push({
                    key: `option-${this.nextOptionKey++}`,
                    id: null,
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
