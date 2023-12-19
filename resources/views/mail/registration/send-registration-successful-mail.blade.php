<x-mail::message>
# Dear {{ $user->getFullName() }}

We are honored to welcome you to ADIEU, a platform dedicated to honoring the wishes and legacies of those who have departed. Your email address has been successfully verified, and your ADIEU account is now activated.

With ADIEU, you can:

- Record and preserve your wishes for your loved ones.
- Share meaningful messages, memories, and expressions.
- Plan and organize your final arrangements according to your preferences.

To access your ADIEU account and start creating your legacy, please log in using the following link:

<x-mail::button :url="$loginUrl">
    Login
</x-mail::button>

If you have any questions or need assistance, please don't hesitate to contact our support team at [{{ $supportEmail }}].

Thank you for choosing ADIEU. We are here to support you in preserving and honoring the wishes of your loved ones.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
