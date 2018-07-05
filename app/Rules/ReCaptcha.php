<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;

class ReCaptcha implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => config('services.google.recaptcha.secret'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            ]
        );

        $body = json_decode($response->getBody()->getContents());

        return $body->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.recaptcha');
    }
}
