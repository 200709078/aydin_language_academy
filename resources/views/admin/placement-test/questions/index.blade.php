<x-app-layout>
    <x-slot name="header">{{ __('dictt.questions') }}</x-slot>

    @if (session('modalSuccessTitle') && session('modalSuccessContent'))
        <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full">
            <div
                class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-green-100">
            </div>
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {!! session('modalSuccessTitle') !!}
                </h2>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-red-600 ml-4" title="{{ __('dictt.close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 text-sm">
                {!! session('modalSuccessContent') !!}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
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
            <a href="{{ route('placement_test_questions_create') }}" class="btn btn-sm btn-outline-primary float-right">
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
                                <td class="admin-placement-question-cell">
                                    <span class="admin-table-cell-ellipsis" title="{{ $question->question_text }}">{{ $question->question_text }}</span>
                                </td>
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
                                        <a href="{{ route('placement_test_questions_edit', $question) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen w-4"></i>
                                        </a>
                                        <form id="placement-question-delete-{{ $question->id }}" method="POST"
                                            action="{{ route('placement_test_questions_destroy', $question) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-primary admin-danger-action" title="{{ __('dictt.delete') }}"
                                                data-action-confirmation
                                                data-confirm-form="placement-question-delete-{{ $question->id }}"
                                                data-confirm-title="{{ __('dictt.delete') }}"
                                                data-confirm-content="{{ $deletePrompt }}"
                                                data-confirm-action="{{ __('dictt.delete') }}"
                                                data-confirm-icon="fa-trash-alt"
                                                data-confirm-tone="danger">
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

    <x-action-confirmation-modal />
</x-app-layout>
