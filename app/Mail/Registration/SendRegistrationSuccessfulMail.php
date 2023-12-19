<?php

namespace App\Mail\Registration;

use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendRegistrationSuccessfulMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ADIEU - Your Account is Activated!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $registrationService = new RegistrationService();
        return new Content(
            markdown: 'mail.registration.send-registration-successful-mail',
            with: [
                'loginUrl' => $registrationService->loginUrl(),
                'supportEmail' => $registrationService->supportEmail(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
