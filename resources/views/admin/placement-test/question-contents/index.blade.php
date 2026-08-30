<x-app-layout>
    <x-slot name="header">{{ __('dictt.question_contents') }}</x-slot>

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
            <a href="{{ route('placement_test_question_contents_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('dictt.add_shared_content') }}
            </a>

            <h5 class="card-title mb-3">{{ __('dictt.question_contents') }}</h5>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.level') }}</th>
                            <th scope="col">{{ __('dictt.type') }}</th>
                            <th scope="col">{{ __('dictt.content_header') }}</th>
                            <th scope="col">{{ __('dictt.linked_questions') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contents as $content)
                            <tr>
                                <th scope="row">{{ $content->level->code }}</th>
                                <td>{{ $typeLabels[$content->type] }}</td>
                                <td class="admin-question-content-cell">
                                    @if ($content->type === 'text')
                                        <span class="admin-table-cell-ellipsis" title="{{ $content->text_content }}">{{ $content->text_content }}</span>
                                    @else
                                        <a href="{{ route('placement_test_question_contents_media', $content) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-up-right-from-square"></i> {{ __('dictt.open_media') }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $content->questions_count }}</td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('placement_test_question_contents_edit', $content) }}"
                                            class="btn btn-sm btn-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen w-4"></i>
                                        </a>

                                        @if ($content->questions_count === 0)
                                            <form id="placement-content-delete-{{ $content->id }}" method="POST"
                                                action="{{ route('placement_test_question_contents_destroy', $content) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="{{ __('dictt.delete') }}"
                                                    data-action-confirmation
                                                    data-confirm-form="placement-content-delete-{{ $content->id }}"
                                                    data-confirm-title="{{ __('dictt.delete') }}"
                                                    data-confirm-content="{{ __('dictt.pt_content_delete_confirm') }}"
                                                    data-confirm-action="{{ __('dictt.delete') }}"
                                                    data-confirm-icon="fa-trash-alt"
                                                    data-confirm-tone="danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled
                                                title="{{ __('dictt.pt_content_delete_blocked') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.pt_no_contents') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contents->hasPages())
                <div class="mt-3">
                    {{ $contents->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-action-confirmation-modal />
</x-app-layout>
