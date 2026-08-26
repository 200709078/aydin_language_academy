<x-app-layout>
    <x-slot name="header">{{ __('dictt.levels') }}</x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Kapat"></button>
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
                                    <a href="{{ route('placement_test_levels_edit', $level) }}" class="btn btn-sm btn-primary"
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
