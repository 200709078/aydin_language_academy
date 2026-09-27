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
            @if ($type === 'branch')
                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('scholarship.definition_address') }}</label>
                    <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $currentDefinition?->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif
        </form>
    </div>
</div>
