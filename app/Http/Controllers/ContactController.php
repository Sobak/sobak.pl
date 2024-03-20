<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\ContactFormMessage;
use App\Rules\ReCaptcha;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'title' => page_title(__('contact.title')),
        ]);
    }

    public function send(Request $request)
    {
        $validationRules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];

        if (config('services.google.recaptcha.key')) {
            $validationRules['g-recaptcha-response'] = ['required', new ReCaptcha()];
        }

        $this->validate($request, $validationRules);

        $error = null;

        try {
            Mail::send(new ContactMail(
                $request->get('name'),
                $request->get('email'),
                $request->get('subject'),
                $request->get('message')
            ));
        } catch (Exception $exception) {
            report($exception);

            $error = (string) $exception;

            return redirect()
                ->route('contact')
                ->withInput()
                ->with('message_type', 'warning')
                ->with('message', __('contact.error'));
        } finally {
            $log = new ContactFormMessage();
            $log->name = $request->get('name');
            $log->email = $request->get('email');
            $log->subject = $request->get('subject');
            $log->content = $request->get('message');
            $log->error = $error;
            $log->ip = $request->getClientIp();
            $log->user_agent = Str::limit($request->userAgent(), 255);

            try {
                $log->save();
            } catch (Exception $exception) {
                // Never fail if we can't save the log, just report
                report($exception);
            }
        }

        return redirect()
            ->route('contact')
            ->with('message_type', 'info')
            ->with('message', __('contact.success'));
    }
}
