<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewquestion') }} - {{ $exercise->title }}</x-slot>
    @include('admin.questions.form', [
        'action' => route('question_store', $exercise->id),
        'method' => 'POST',
        'pageTitle' => __('dictt.addnewquestion'),
        'submitLabel' => __('dictt.add'),
    ])
</x-app-layout>
