<x-app-layout>
    <x-slot name="header">Soru Düzenle</x-slot>

    @include('admin.placement-test.questions.form', [
        'action' => route('placement_test_questions_update', $placementTestQuestion),
        'method' => 'PUT',
        'submitLabel' => 'Değişiklikleri Kaydet',
    ])
</x-app-layout>
