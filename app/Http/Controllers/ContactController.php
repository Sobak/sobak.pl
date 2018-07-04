<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
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
        ]);

        Mail::send(new ContactMail(
            $request->get('name'),
            $request->get('email'),
            $request->get('subject'),
            $request->get('message')
        ));

        return redirect()->route('contact')->with('contact_success', true);
    }
}
