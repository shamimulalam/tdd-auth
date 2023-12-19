<x-mail::message>
# Dear {{ $user->getFullName() }}

Thank you for registering with ADIEU! We're excited to have you on board. Before you can start using our platform, we need to verify your email address.

To complete your registration, please click on the button below to confirm your email address.

<x-mail::button :url="$verificationUrl">
    Verify
</x-mail::button>

If you are unable to click the link, you can copy and paste it into your browser's address bar.

<code>{{ $verificationUrl }}</code>

This link will expire in 24 hours for security reasons, so please verify your email address as soon as possible.

If you did not sign up for ADIEU, please disregard this email.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
