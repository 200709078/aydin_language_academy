<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\model_messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(?string $branch = null)
    {
        $branchNames = ['ortaca' => 'Ortaca', 'dalaman' => 'Dalaman', 'koycegiz' => 'Köyceğiz'];
        $branchName = $branchNames[$branch ?? 'ortaca'];

        return view('frontend.contact', ['branchName' => $branchName]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'fullname' => 'required|min:3|max:100|',
            'email' => 'required|email|',
            'telephone' => 'digits:10|',
            'subject' => 'required|min:3|max:150|',
            'message' => 'required|min:10|max:2000|',
        ], [
            'fullname.required' => __('dictt.required_item', ['name' => __('dictt.fullname')]),
            'fullname.min' => __('dictt.mincharacter_item', ['name' => __('dictt.fullname'), 'number' => 3]),
            'fullname.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.fullname'), 'number' => 100]),
            'email.required' => __('dictt.required_item', ['name' => __('dictt.email')]),
            'email.email' => __('dictt.emailvalidation_item', ['name' => __('dictt.email')]),
            'telephone.digits' => __('dictt.digit_character_item', ['name' => __('dictt.phone'), 'number' => 10]),
            'subject.required' => __('dictt.required_item', ['name' => __('dictt.subject')]),
            'subject.min' => __('dictt.mincharacter_item', ['name' => __('dictt.subject'), 'number' => 3]),
            'subject.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.subject'), 'number' => 150]),
            'message.required' => __('dictt.required_item', ['name' => __('dictt.message')]),
            'message.min' => __('dictt.mincharacter_item', ['name' => __('dictt.message'), 'number' => 10]),
            'message.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.message'), 'number' => 2000]),
        ]);

        $newMessage = new model_messages();
        $newMessage->fullname = $request->fullname;
        $newMessage->email = $request->email;
        $newMessage->telephone = $request->telephone;
        $newMessage->subject = $request->subject;
        $newMessage->message = $request->message;
        $newMessage->save();

        Mail::send([], [], function ($message) use ($request) {
            $message->to('learnenglishwithala@gmail.com', 'Adem VAROL')
                ->subject($request->subject)
                ->html(
                    "<b>Ad Soyad:</b> {$request->fullname}<br>
             <b>Email:</b> {$request->email}<br>
             <b>Telefon:</b> {$request->telephone}<br><br>
             <b>Mesaj:</b><br>" . nl2br(e($request->message))
                );
        });

        return redirect()
            ->route('frontend.contact')
            ->with('modalSuccessTitle', __('dictt.sendmessagesuccesstitle'))
            ->with('modalSuccessContent', __('dictt.sendmessagesuccesscontent'));
    }
}
