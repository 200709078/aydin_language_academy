<x-app-layout>
    <x-slot name="header">{{ __('dictt.levels') }}</x-slot>

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

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.pt_levels_title') }}</h5>
                    <p class="text-muted small mb-0">
                        {{ __('dictt.pt_levels_note') }}
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.level') }}</th>
                            <th scope="col">{{ __('dictt.active_questions') }}</th>
                            <th scope="col">{{ __('dictt.points') }}</th>
                            <th scope="col">{{ __('dictt.pass_percentage') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($levels as $level)
                            <tr>
                                <th scope="row">{{ $level->code }}</th>
                                <td>{{ $level->has_exam ? $level->active_questions_count : '—' }}</td>
                                <td>
                                    @if (! $level->has_exam)
                                        <span class="text-muted">—</span>
                                    @else
                                        {{ number_format((float) ($level->active_questions_points_sum ?? 0), 2, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($level->has_exam)
                                        %{{ (int) $level->pass_percentage }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('placement_test_levels_edit', $level) }}" class="btn btn-sm btn-outline-primary"
                                        title="{{ __('dictt.edit') }}">
                                        <i class="fa fa-pen w-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.pt_no_levels') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
