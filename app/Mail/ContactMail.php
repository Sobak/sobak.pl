<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageSubject;

    public $messageText;

    public $senderEmail;

    public $senderName;

    /**
     * Create a new message instance.
     *
     * @param $senderName string
     * @param $senderEmail string
     * @param $subject string
     * @param $message string
     * @return void
     */
    public function __construct($senderName, $senderEmail, $subject, $message)
    {
        $this->messageSubject = $subject ?? 'Brak tematu';
        $this->messageText = $message;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.contact')
            ->from(config('mail.from.address'), $this->senderName)
            ->replyTo($this->senderEmail, $this->senderName)
            ->subject('[sobak.pl] Wiadomość z formularza kontaktowego');
    }
}
