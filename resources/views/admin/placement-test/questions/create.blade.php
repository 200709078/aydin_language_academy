<x-app-layout>
    <x-slot name="header">Yeni Soru Ekle</x-slot>

    @include('admin.placement-test.questions.form', [
        'placementTestQuestion' => null,
        'action' => route('placement_test_questions_store'),
        'method' => 'POST',
        'submitLabel' => 'Soruyu Ekle',
    ])
</x-app-layout>
