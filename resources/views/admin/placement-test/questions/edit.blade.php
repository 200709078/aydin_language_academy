<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit_question') }}</x-slot>

    @include('admin.placement-test.questions.form', [
        'action' => route('placement_test_questions_update', $placementTestQuestion),
        'method' => 'PUT',
        'submitLabel' => __('dictt.save_changes'),
    ])
</x-app-layout>
