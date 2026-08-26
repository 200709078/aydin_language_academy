<x-app-layout>
    <x-slot name="header">{{ __('dictt.questions') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Kapat"></button>
        </div>
    @endif

    @php
        $typeLabels = [
            'text' => __('dictt.content_type_text'),
            'audio' => __('dictt.content_type_audio'),
            'image' => __('dictt.content_type_image'),
            'video' => __('dictt.content_type_video'),
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <a href="{{ route('placement_test_questions_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('dictt.addnewquestion') }}
            </a>

            <h5 class="card-title mb-1">{{ __('dictt.pt_questions_title') }}</h5>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.level') }}</th>
                            <th scope="col">{{ __('dictt.question') }}</th>
                            <th scope="col">{{ __('dictt.question_contents') }}</th>
                            <th scope="col">{{ __('dictt.points') }}</th>
                            <th scope="col">{{ __('dictt.options_col') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $question)
                            <tr>
                                <th scope="row">{{ $question->level?->code ?? '—' }}</th>
                                <td class="text-break">{{ \Illuminate\Support\Str::limit($question->question_text, 110) }}</td>
                                <td>
                                    @if ($question->questionContent)
                                        <a href="{{ route('placement_test_question_contents_edit', $question->questionContent) }}"
                                            class="text-decoration-none">
                                            {{ $typeLabels[$question->questionContent->type] }} #{{ $question->questionContent->id }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('dictt.independent') }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $question->points, 2, ',', '.') }}</td>
                                <td>{{ $question->options_count }}</td>
                                <td>
                                    @php
                                        $deletePrompt = $question->level_question_snapshots_count > 0
                                            ? __('dictt.pt_question_delete_confirm_with_snapshots')
                                            : __('dictt.pt_question_delete_confirm');
                                    @endphp

                                    <div class="flex gap-1">
                                        <a href="{{ route('placement_test_questions_edit', $question) }}" class="btn btn-sm btn-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen w-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('placement_test_questions_destroy', $question) }}"
                                            onsubmit="return confirm(@js($deletePrompt));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('dictt.delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dictt.pt_no_questions') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($questions->hasPages())
                <div class="mt-3">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
