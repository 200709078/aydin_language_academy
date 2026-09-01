<x-app-layout>
    <x-slot name="header">{{ __('dictt.users') }}</x-slot>

    @php
        $typeLabels = [
            'admin' => __('dictt.user_type_admin'),
            'user' => __('dictt.user_type_user'),
        ];
        $isNameSorted = $sort === 'name';
        $nextNameDirection = $isNameSorted && $direction === 'asc' ? 'desc' : 'asc';
        $nameSortTitle = $nextNameDirection === 'asc'
            ? __('dictt.sort_name_ascending')
            : __('dictt.sort_name_descending');
        $isRegistrationDateSorted = $sort === 'created_at';
        $nextRegistrationDateDirection = $isRegistrationDateSorted && $direction === 'asc' ? 'desc' : 'asc';
        $registrationDateSortTitle = $nextRegistrationDateDirection === 'asc'
            ? __('dictt.sort_registration_date_ascending')
            : __('dictt.sort_registration_date_descending');
    @endphp

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('dictt.users') }}</h5>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.photo') }}</th>
                            <th scope="col" aria-sort="{{ $isNameSorted ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a href="{{ route('admin.users.index', ['sort' => 'name', 'direction' => $nextNameDirection]) }}"
                                    class="text-decoration-none text-reset" title="{{ $nameSortTitle }}">
                                    {{ __('dictt.fullname') }}
                                    <span class="ms-1 text-muted" aria-hidden="true">{{ $isNameSorted ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                </a>
                            </th>
                            <th scope="col">{{ __('dictt.email') }}</th>
                            <th scope="col">{{ __('dictt.phone') }}</th>
                            <th scope="col">{{ __('dictt.type') }}</th>
                            <th scope="col" aria-sort="{{ $isRegistrationDateSorted ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a href="{{ route('admin.users.index', ['sort' => 'created_at', 'direction' => $nextRegistrationDateDirection]) }}"
                                    class="text-decoration-none text-reset" title="{{ $registrationDateSortTitle }}">
                                    {{ __('dictt.created_at') }}
                                    <span class="ms-1 text-muted" aria-hidden="true">{{ $isRegistrationDateSorted ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                </a>
                            </th>
                            <th scope="col">{{ __('dictt.updated_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    @if ($user->profile_photo_path)
                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                            class="rounded-circle border object-fit-cover" width="60" height="60">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-muted border"
                                            style="width: 60px; height: 60px;" title="{{ __('dictt.no_photo') }}">
                                            <i class="fa-solid fa-image" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.no_photo') }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-break">{{ $user->name }}</td>
                                <td class="text-break">{{ $user->email }}</td>
                                <td class="text-nowrap">{{ $user->phone ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $user->type === 'admin' ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                        {{ $typeLabels[$user->type] ?? $user->type }}
                                    </span>
                                </td>
                                <td class="text-nowrap">{{ $user->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="text-nowrap">{{ $user->updated_at?->format('d.m.Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dictt.users_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-3">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
