@foreach (['application', 'result'] as $publicationPhase)
    @php
        $publicationField = $publicationPhase.'_published';
        $publicationBlocked = $publicationPhase === 'result' && $application->scholarship_percentage === null;
    @endphp
    <form method="POST" action="{{ route('admin.scholarship.applications.publication.update', ['application' => $application->id, 'phase' => $publicationPhase]) }}" class="mb-2">
        @csrf @method('PATCH')
        <input type="hidden" name="published" value="0">
        <div class="form-check form-switch admin-list-switch mb-1">
            <input id="publication-{{ $application->id }}-{{ $publicationPhase }}" type="checkbox" name="published" value="1"
                class="form-check-input" role="switch" @checked($application->{$publicationField})
                @disabled($publicationBlocked && !$application->{$publicationField}) onchange="this.form.submit()"
                aria-label="{{ __('scholarship.publication_for', ['number' => $application->application_number, 'phase' => __('scholarship.'.$publicationPhase.'_publication')]) }}">
            <label for="publication-{{ $application->id }}-{{ $publicationPhase }}" class="form-check-label small">{{ __('scholarship.'.$publicationPhase.'_publication') }}</label>
        </div>
        <noscript><button type="submit" class="btn btn-sm btn-outline-secondary" @disabled($publicationBlocked && !$application->{$publicationField})>{{ __('dictt.update') }}</button></noscript>
        @if ($publicationBlocked)<p class="small text-muted mb-0">{{ __('scholarship.publication_incomplete') }}</p>@endif
    </form>
@endforeach
