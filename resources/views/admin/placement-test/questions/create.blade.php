<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewquestion') }}</x-slot>

    @include('admin.placement-test.questions.form', [
        'placementTestQuestion' => null,
        'action' => route('placement_test_questions_store'),
        'method' => 'POST',
        'submitLabel' => __('dictt.add_question'),
        'pageTitle' => __('dictt.placement_test') . ' — ' . __('dictt.addnewquestion'),
    ])
</x-app-layout>
