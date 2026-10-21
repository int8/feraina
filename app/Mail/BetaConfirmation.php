<?php

namespace App\Mail;

use App\Models\BetaLead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class BetaConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BetaLead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Potwierdź zapis do bety');
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.route('unsubscribe', $this->lead->unsubscribe_token).'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.beta-confirmation',
            text: 'emails.beta-confirmation-text',
            with: [
                'confirmUrl' => route('beta.confirm', $this->lead->confirm_token),
                'unsubscribeUrl' => route('unsubscribe', $this->lead->unsubscribe_token),
            ],
        );
    }
}
