<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\model_messages;
use App\Services\AdminApprovalNotificationService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(private readonly AdminApprovalNotificationService $adminNotifications)
    {
    }

    public function show(?string $branch = null)
    {
        $branchNames = ['ortaca' => 'Ortaca', 'dalaman' => 'Dalaman', 'koycegiz' => 'Köyceğiz'];
        $branch = $branch ?? 'ortaca';
        $branchName = $branchNames[$branch] ?? $branchNames['ortaca'];

        return view('frontend.contact', compact('branch', 'branchName'));
    }

    public function submit(Request $request, ?string $branch = null)
    {
        $branch = $branch ?? 'ortaca';

        $request->merge([
            'fullname' => $this->capitalizeNameWords((string) $request->input('fullname')),
        ]);

        $request->validate([
            'fullname' => 'required|min:3|max:100|',
            'email' => 'required|email|',
            'telephone' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'subject' => 'required|min:3|max:150|',
            'message' => 'required|min:10|max:2000|',
            'website' => ['prohibited'],
        ], [
            'fullname.required' => __('dictt.required_item', ['name' => __('dictt.fullname')]),
            'fullname.min' => __('dictt.mincharacter_item', ['name' => __('dictt.fullname'), 'number' => 3]),
            'fullname.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.fullname'), 'number' => 100]),
            'email.required' => __('dictt.required_item', ['name' => __('dictt.email')]),
            'email.email' => __('dictt.emailvalidation_item', ['name' => __('dictt.email')]),
            'telephone.required' => __('dictt.required_item', ['name' => __('dictt.phone')]),
            'telephone.regex' => __('dictt.phone_international_format'),
            'subject.required' => __('dictt.required_item', ['name' => __('dictt.subject')]),
            'subject.min' => __('dictt.mincharacter_item', ['name' => __('dictt.subject'), 'number' => 3]),
            'subject.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.subject'), 'number' => 150]),
            'message.required' => __('dictt.required_item', ['name' => __('dictt.message')]),
            'message.min' => __('dictt.mincharacter_item', ['name' => __('dictt.message'), 'number' => 10]),
            'message.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.message'), 'number' => 2000]),
            'website.prohibited' => __('dictt.contact_message_spam'),
        ]);

        $newMessage = new model_messages([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'branch' => $branch,
            'locale' => in_array(app()->getLocale(), ['tr', 'en'], true) ? app()->getLocale() : 'tr',
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        $newMessage->save();

        $this->adminNotifications->contactMessageCreated($newMessage);

        return redirect()
            ->route('frontend.contact', ['branch' => $branch])
            ->with('modalSuccessTitle', __('dictt.sendmessagesuccesstitle'))
            ->with('modalSuccessContent', __('dictt.sendmessagesuccesscontent'));
    }

    private function capitalizeNameWords(string $name): string
    {
        return preg_replace_callback(
            '/(^|\s)(\p{L})/u',
            static fn (array $matches): string => $matches[1]
                . ($matches[2] === 'i' ? 'İ' : mb_strtoupper($matches[2], 'UTF-8')),
            $name,
        ) ?? $name;
    }
}
