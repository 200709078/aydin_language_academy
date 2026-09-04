<x-app-layout>
    <x-slot name="header">{{ __('dictt.contact_message_details') }} #{{ $message->id }}</x-slot>

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

    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-secondary align-self-start">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
        </a>

        <form method="POST" action="{{ route('admin.messages.status.update', $message) }}" class="d-flex flex-wrap align-items-center gap-2">
            @csrf
            @method('PUT')
            <label for="message-status" class="visually-hidden">{{ __('dictt.status') }}</label>
            <select id="message-status" name="status" class="form-select form-select-sm" style="width: auto;">
                <option value="{{ \App\Models\model_messages::STATUS_UNREAD }}" @selected($message->status === \App\Models\model_messages::STATUS_UNREAD)>{{ __('dictt.message_status_unread') }}</option>
                <option value="{{ \App\Models\model_messages::STATUS_READ }}" @selected($message->status === \App\Models\model_messages::STATUS_READ)>{{ __('dictt.message_status_read') }}</option>
                <option value="{{ \App\Models\model_messages::STATUS_ARCHIVED }}" @selected($message->status === \App\Models\model_messages::STATUS_ARCHIVED)>{{ __('dictt.message_status_archived') }}</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('dictt.message_update_status') }}</button>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5 card-title mb-3">{{ __('dictt.contact_message_details') }}</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('dictt.fullname') }}</dt>
                        <dd class="col-sm-7 text-break">{{ $message->fullname }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.email') }}</dt>
                        <dd class="col-sm-7 text-break"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                        <dt class="col-sm-5">{{ __('dictt.phone') }}</dt>
                        <dd class="col-sm-7 text-break"><a href="tel:{{ $message->telephone }}">{{ $message->telephone }}</a></dd>
                        <dt class="col-sm-5">{{ __('dictt.branch') }}</dt>
                        <dd class="col-sm-7">{{ $message->branchLabel() }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.status') }}</dt>
                        <dd class="col-sm-7">{{ $message->statusLabel() }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.message_sent_at') }}</dt>
                        <dd class="col-sm-7">{{ $message->created_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.message_read_at') }}</dt>
                        <dd class="col-sm-7">{{ $message->read_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.message_read_by') }}</dt>
                        <dd class="col-sm-7">{{ $message->readBy?->name ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('dictt.message_last_replied_at') }}</dt>
                        <dd class="col-sm-7">{{ $message->last_replied_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                    </dl>
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5 card-title mb-3">{{ __('dictt.contact_message_original') }}</h2>
                    <h3 class="h6 text-muted">{{ $message->subject }}</h3>
                    <div class="text-dark lh-lg">{!! nl2br(e($message->message)) !!}</div>
                </div>
            </section>
        </div>
    </div>

    <section class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title mb-3">{{ __('dictt.message_replies') }}</h2>

            @forelse ($message->replies as $reply)
                @php
                    $deliveryStatusClass = match ($reply->delivery_status) {
                        \App\Models\MessageReply::STATUS_SENT => 'text-bg-success',
                        \App\Models\MessageReply::STATUS_FAILED => 'text-bg-danger',
                        default => 'text-bg-secondary',
                    };
                @endphp
                <article class="border rounded p-3 mb-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-2 mb-3">
                        <div>
                            <h3 class="h6 mb-1">{{ $reply->subject }}</h3>
                            <div class="small text-muted">
                                {{ __('dictt.message_sent_by') }}: {{ $reply->sender?->name ?? '—' }}
                                <span aria-hidden="true">·</span>
                                {{ $reply->created_at?->format('d.m.Y H:i') ?? '—' }}
                            </div>
                        </div>
                        <span class="badge {{ $deliveryStatusClass }}">{{ $reply->deliveryStatusLabel() }}</span>
                    </div>
                    <p class="small text-muted mb-2">{{ __('dictt.message_recipient') }}: {{ $reply->recipient_email }}</p>
                    <div class="text-dark lh-lg">{!! nl2br(e($reply->body)) !!}</div>
                </article>
            @empty
                <p class="text-muted mb-0">{{ __('dictt.message_replies_empty') }}</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <h2 class="h5 card-title mb-3">{{ __('dictt.message_reply') }}</h2>

            <form method="POST" action="{{ route('admin.messages.replies.store', $message) }}">
                @csrf
                <div class="mb-3">
                    <label for="reply-subject" class="form-label">{{ __('dictt.subject') }}</label>
                    <input id="reply-subject" name="subject" type="text" class="form-control @error('subject') is-invalid @enderror"
                        value="{{ old('subject', 'Re: ' . $message->subject) }}" maxlength="150" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="reply-body" class="form-label">{{ __('dictt.message') }}</label>
                    <textarea id="reply-body" name="body" class="form-control @error('body') is-invalid @enderror" rows="7" maxlength="2000" required>{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i> {{ __('dictt.send_reply') }}
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
