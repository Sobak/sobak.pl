<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'title' => page_title('Kontakt'),
        ]);
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'g-recaptcha-response' => ['required', new ReCaptcha()],
        ]);

        try {
            Mail::send(new ContactMail(
                $request->get('name'),
                $request->get('email'),
                $request->get('subject'),
                $request->get('message')
            ));
        } catch (\Exception $exception) {
            report($exception);

            return redirect()
                ->route('contact')
                ->withInput()
                ->with('message_type', 'warning')
                ->with('message', 'Wystąpił błąd przy wysyłaniu wiadomości. Spróbuj ponownie później
                        lub wyśli ją bezpośrednio pod podany niżej adres email. Przepraszam
                        za utrudnienia.');
        }

        return redirect()
            ->route('contact')
            ->with('message_type', 'info')
            ->with('message', 'Wiadomość została wysłana pomyślnie');
    }
}
