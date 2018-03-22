<?php

namespace App\Http\Controllers;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'title' => page_title('Kontakt'),
        ]);
    }
}
