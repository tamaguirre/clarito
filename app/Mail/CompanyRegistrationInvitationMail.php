<?php

namespace App\Mail;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyRegistrationInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompanyInvitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Completa el registro de tu empresa en Clarito',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-registration-invitation',
            with: [
                'companyName' => $this->invitation->company->name,
                'completionUrl' => route('company.complete-registration', ['token' => $this->invitation->token]),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
