@php
    $currentEntry = $achievementEntry ?? null;
    $isEditing = $currentEntry !== null;
    $initialPermissionGranted = (string) old(
        'name_permission_granted',
        $currentEntry?->name_permission_status === \App\Models\AchievementEntry::NAME_PERMISSION_GRANTED ? '1' : '0',
    ) === '1';
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                @if ($isEditing)
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.achievements.entries.index', $achievementYear) }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.achievement_back') }}
                        </a>
                        <button type="submit" form="achievement-entry-form" class="btn btn-success btn-sm">
                            {{ $submitLabel }}
                        </button>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.achievements.entries.index', $achievementYear) }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.achievement_back') }}
                        </a>
                        <button type="submit" form="achievement-entry-form" class="btn btn-success btn-sm">
                            {{ $submitLabel }}
                        </button>
                    </div>
                @endif
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="achievement-entry-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="border rounded p-3 mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">{{ __('dictt.achievement_entry_student_full_name') }}</label>
                        <input id="full_name" type="text" name="full_name"
                            value="{{ old('full_name', $currentEntry?->full_name) }}"
                            class="form-control @error('full_name') is-invalid @enderror" maxlength="255" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="branch" class="form-label">{{ __('dictt.branch') }}</label>
                        <select id="branch" name="branch" class="form-select @error('branch') is-invalid @enderror">
                            <option value="">{{ __('dictt.none') }}</option>
                            @foreach (\App\Models\AchievementEntry::BRANCHES as $branch)
                                <option value="{{ $branch }}" @selected(old('branch', $currentEntry?->branch) === $branch)>
                                    {{ __('dictt.branch_' . $branch) }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="university_name" class="form-label">{{ __('dictt.achievement_university_name') }}</label>
                        <input id="university_name" type="text" name="university_name"
                            value="{{ old('university_name', $currentEntry?->university_name) }}"
                            class="form-control @error('university_name') is-invalid @enderror" maxlength="255">
                        @error('university_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="department_name" class="form-label">{{ __('dictt.achievement_department_name') }}</label>
                        <input id="department_name" type="text" name="department_name"
                            value="{{ old('department_name', $currentEntry?->department_name) }}"
                            class="form-control @error('department_name') is-invalid @enderror" maxlength="255">
                        @error('department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">{{ __('dictt.description') }}</label>
                        <textarea id="description" name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $currentEntry?->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="card_sub_title" class="form-label">{{ __('dictt.achievement_program_label') }}</label>
                        <input id="card_sub_title" type="text" name="card_sub_title"
                            value="{{ old('card_sub_title', $currentEntry?->card_sub_title) }}"
                            class="form-control @error('card_sub_title') is-invalid @enderror" maxlength="100">
                        @error('card_sub_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="name_permission_granted" class="form-label">
                            {{ __('dictt.achievement_name_permission') }}
                        </label>
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="name_permission_granted" value="0">
                            <input id="name_permission_granted" type="checkbox" class="form-check-input"
                                name="name_permission_granted" value="1" role="switch"
                                aria-describedby="name_permission_auto_date_help" @checked($initialPermissionGranted)>
                        </div>
                        <div id="name_permission_auto_date_help" class="form-text">
                            {{ __('dictt.achievement_name_permission_auto_date_help') }}
                        </div>
                        @error('name_permission_granted')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
