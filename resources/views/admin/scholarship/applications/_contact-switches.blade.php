@foreach (['application', 'result'] as $contactPhase)
    <form method="POST" action="{{ route('admin.scholarship.applications.contact.update', ['application' => $application, 'phase' => $contactPhase]) }}" class="mb-2">
        @csrf @method('PATCH')
        <input type="hidden" name="reached" value="0">
        <div class="form-check form-switch">
            <input type="checkbox" id="contact-{{ $application->id }}-{{ $contactPhase }}" name="reached" value="1" class="form-check-input" role="switch"
                @checked($application->getAttribute($contactPhase.'_contact_status') === 'reached') onchange="this.form.submit()">
            <label for="contact-{{ $application->id }}-{{ $contactPhase }}" class="form-check-label small">{{ __('scholarship.'.$contactPhase.'_contact') }}: {{ __('scholarship.contact_'.$application->getAttribute($contactPhase.'_contact_status')) }}</label>
        </div>
        <noscript><button class="btn btn-sm btn-primary" type="submit">{{ __('dictt.save') }}</button></noscript>
    </form>
@endforeach
