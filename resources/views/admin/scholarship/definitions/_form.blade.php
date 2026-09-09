@php($currentDefinition = $definition ?? null)

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.scholarship.definitions.index', ['type' => $type]) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="scholarship-definition-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <p class="text-muted small">{{ __('scholarship.'.$meta['help']) }}</p>
        <form id="scholarship-definition-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('scholarship.'.$meta['name']) }}</label>
                <input id="name" name="name" type="text" maxlength="{{ $meta['max_name'] }}" required autofocus
                    value="{{ old('name', $currentDefinition?->name) }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if ($type !== 'school')
                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('scholarship.definition_code') }}</label>
                    <input id="code" name="code" type="text" maxlength="64" required aria-describedby="definition-code-help"
                        value="{{ old('code', $currentDefinition?->code) }}" class="form-control @error('code') is-invalid @enderror">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div id="definition-code-help" class="form-text">{{ __('scholarship.definition_code_help') }}</div>
                </div>
            @endif
            @if ($type === 'branch')
                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('scholarship.definition_address') }}</label>
                    <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $currentDefinition?->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif
            <div class="mb-3">
                <label for="sort_order" class="form-label">{{ __('scholarship.definition_sort_order') }}</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="4294967295" step="1" required
                    value="{{ old('sort_order', $currentDefinition?->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check form-switch">
                    <input id="is_active" name="is_active" type="checkbox" value="1" role="switch" aria-describedby="definition-active-help"
                        class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $currentDefinition?->is_active ?? true))>
                    <label for="is_active" class="form-check-label">{{ __('scholarship.definition_active') }}</label>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <p id="definition-active-help" class="text-muted small mb-0">{{ __('scholarship.definition_active_help') }}</p>
        </form>
    </div>
</div>
