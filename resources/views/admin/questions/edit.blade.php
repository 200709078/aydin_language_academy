<x-app-layout>
    <x-slot name="header">{{ __('dictt.update') }} - {{ Str::limit($question->question,20) }}</x-slot>
    @include('admin.questions.form', [
        'action' => route('question_update', $question->id),
        'method' => 'PUT',
        'pageTitle' => __('dictt.edit_question'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
