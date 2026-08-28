<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactMessageReply;
use App\Models\MessageReply;
use App\Models\model_messages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class AdminContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'all');
        $allowedFilters = [
            'all',
            model_messages::STATUS_UNREAD,
            model_messages::STATUS_READ,
            'replied',
            model_messages::STATUS_ARCHIVED,
        ];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $messages = model_messages::query()
            ->with(['readBy', 'lastRepliedBy', 'latestReply']);

        if ($filter === model_messages::STATUS_UNREAD) {
            $messages->where('status', model_messages::STATUS_UNREAD)
                ->orderBy('created_at');
        } elseif ($filter === model_messages::STATUS_READ) {
            $messages->where('status', model_messages::STATUS_READ)
                ->orderByDesc('created_at');
        } elseif ($filter === 'replied') {
            $messages->whereNotNull('last_replied_at')
                ->orderByDesc('last_replied_at');
        } elseif ($filter === model_messages::STATUS_ARCHIVED) {
            $messages->where('status', model_messages::STATUS_ARCHIVED)
                ->orderByDesc('created_at');
        } else {
            $messages->orderByRaw(
                "CASE status WHEN 'unread' THEN 0 WHEN 'read' THEN 1 WHEN 'archived' THEN 2 ELSE 3 END",
            )
                ->orderByRaw("CASE WHEN status = 'unread' THEN created_at END ASC")
                ->orderByDesc('created_at');
        }

        $messages = $messages->paginate(20)->withQueryString();

        return view('admin.messages.index', compact('filter', 'messages'));
    }

    public function show(Request $request, model_messages $message): View
    {
        if ($message->status === model_messages::STATUS_UNREAD) {
            $message->forceFill([
                'status' => model_messages::STATUS_READ,
                'read_at' => now(),
                'read_by' => $request->user()->id,
            ])->save();
        }

        $message->load(['readBy', 'lastRepliedBy', 'replies.sender']);

        return view('admin.messages.show', compact('message'));
    }

    public function updateStatus(Request $request, model_messages $message): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                model_messages::STATUS_UNREAD,
                model_messages::STATUS_READ,
                model_messages::STATUS_ARCHIVED,
            ])],
        ], [
            'status.required' => __('dictt.required_item', ['name' => __('dictt.status')]),
            'status.in' => __('dictt.invalidvalue_item', ['name' => __('dictt.status')]),
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === model_messages::STATUS_UNREAD) {
            $updates['read_at'] = null;
            $updates['read_by'] = null;
        } elseif ($validated['status'] === model_messages::STATUS_READ
            && ($message->status === model_messages::STATUS_UNREAD || $message->read_at === null)) {
            $updates['read_at'] = now();
            $updates['read_by'] = $request->user()->id;
        }

        $message->forceFill($updates)->save();

        if ($validated['status'] === model_messages::STATUS_UNREAD) {
            return redirect()
                ->route('admin.messages.index', ['filter' => model_messages::STATUS_UNREAD])
                ->with('success', __('dictt.contact_message_status_updated'));
        }

        return redirect()
            ->route('admin.messages.show', $message)
            ->with('success', __('dictt.contact_message_status_updated'));
    }

    public function reply(Request $request, model_messages $message): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'subject.required' => __('dictt.required_item', ['name' => __('dictt.subject')]),
            'subject.min' => __('dictt.mincharacter_item', ['name' => __('dictt.subject'), 'number' => 3]),
            'subject.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.subject'), 'number' => 150]),
            'body.required' => __('dictt.required_item', ['name' => __('dictt.message')]),
            'body.min' => __('dictt.mincharacter_item', ['name' => __('dictt.message'), 'number' => 3]),
            'body.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.message'), 'number' => 2000]),
        ]);

        $reply = DB::transaction(function () use ($request, $message, $validated): MessageReply {
            if ($message->status === model_messages::STATUS_UNREAD) {
                $message->forceFill([
                    'status' => model_messages::STATUS_READ,
                    'read_at' => now(),
                    'read_by' => $request->user()->id,
                ])->save();
            }

            return MessageReply::create([
                'message_id' => $message->id,
                'sent_by' => $request->user()->id,
                'recipient_email' => $message->email,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'delivery_status' => MessageReply::STATUS_QUEUED,
                'queued_at' => now(),
            ]);
        });

        try {
            Bus::dispatch(new SendContactMessageReply($reply->id));
        } catch (Throwable $exception) {
            $reply->forceFill([
                'delivery_status' => MessageReply::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => 'Yanıt e-posta kuyruğa alınamadı.',
            ])->save();

            Log::error('İletişim mesajı yanıtı kuyruklanamadı.', [
                'message_reply_id' => $reply->id,
                'message_id' => $message->id,
            ]);

            return redirect()
                ->route('admin.messages.show', $message)
                ->with('error', __('dictt.contact_message_reply_queue_failed'));
        }

        return redirect()
            ->route('admin.messages.show', $message)
            ->with('success', __('dictt.contact_message_reply_queued'));
    }
}
