<x-app-layout>
    <x-slot name="header">{{ __('scholarship.application_edit') }}</x-slot>
    <div class="card"><div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <a href="{{ route('admin.scholarship.applications.show', $application) }}" class="btn btn-sm btn-secondary">{{ __('dictt.back_short') }}</a>
            <button type="submit" form="application-edit" class="btn btn-sm btn-success">{{ __('dictt.save') }}</button>
            <h5 class="card-title mb-0">{{ __('scholarship.application_edit') }}</h5>
        </div>
        <p class="text-break">{{ $application->application_number }} · {{ $application->period->title }}</p>
        <p class="small text-muted">{{ __('scholarship.application_edit_help') }}</p>
        <form id="application-edit" method="POST" action="{{ route('admin.scholarship.applications.update', $application) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="student_name" class="form-label">{{ __('scholarship.student_name') }}</label>
                <input id="student_name" name="student_name" value="{{ old('student_name', $application->student_name_snapshot) }}" maxlength="255" required class="form-control @error('student_name') is-invalid @enderror">
                @error('student_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                @foreach (['school_id' => [$schools, 'current_school', 'school_name_snapshot'], 'student_level_id' => [$levels, 'current_level', 'student_level_name_snapshot']] as $field => [$options, $label, $snapshot])
                    <div class="col-md-6 mb-3">
                        <label for="{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" required class="form-select @error($field) is-invalid @enderror">
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}" @selected(old($field, $application->{$field}) == $option->id)>{{ $application->{$field} === $option->id ? $application->{$snapshot} : $option->name }}{{ $option->is_active ? '' : ' ('.__('dictt.passive').')' }}</option>
                            @endforeach
                        </select>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
            <div class="mb-3">
                <label for="session_id" class="form-label">{{ __('scholarship.sessions') }}</label>
                <select id="session_id" name="session_id" required class="form-select @error('session_id') is-invalid @enderror" aria-describedby="transfer-help">
                    @foreach ($sessions as $session)
                        @php($full = $session->applications_count >= $session->capacity && $session->id !== $application->session_id)
                        <option value="{{ $session->id }}" @selected(old('session_id', $application->session_id) == $session->id) @disabled($full)>
                            @include('admin.scholarship.applications._session-label') · {{ $session->applications_count }} / {{ $session->capacity }}
                            @if ($full) · {{ __('scholarship.session_full') }} @endif
                            @if ($session->archived_at) · {{ __('scholarship.session_archived') }} @elseif (!$session->is_active) · {{ __('scholarship.session_suspended') }} @endif
                        </option>
                    @endforeach
                </select>
                @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div id="transfer-help" class="form-text">{{ __('scholarship.transfer_help') }}</div>
            </div>
        </form>
    </div></div>
</x-app-layout>
