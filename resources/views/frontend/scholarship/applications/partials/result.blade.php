<dl class="row mt-3 mb-0" id="application-result-{{ $application['id'] }}" aria-label="{{ __('scholarship.member_result_status') }}">
    @foreach ([
        'scholarship_percentage' => $result['scholarship_percentage'] === 0
            ? __('scholarship.award_none') : __('scholarship.award_percentage', ['percentage' => $result['scholarship_percentage']]),
        'score' => $result['score'] ?? __('scholarship.not_entered'),
        'attendance' => __('scholarship.attendance_'.$result['attendance_status']),
        'correct_count' => $result['attendance_status'] === 'absent' ? __('scholarship.not_applicable') : ($result['correct_count'] ?? __('scholarship.not_entered')),
        'wrong_count' => $result['attendance_status'] === 'absent' ? __('scholarship.not_applicable') : ($result['wrong_count'] ?? __('scholarship.not_entered')),
        'blank_count' => $result['attendance_status'] === 'absent' ? __('scholarship.not_applicable') : ($result['blank_count'] ?? __('scholarship.not_entered')),
    ] as $label => $value)
        <div class="col-12 col-sm-6 col-lg-4 mb-2">
            <dt>{{ __('scholarship.'.$label) }}</dt>
            <dd class="text-break mb-0">{{ $value }}</dd>
        </div>
    @endforeach
</dl>
