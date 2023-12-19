<?php

namespace App\Services;

abstract class CoreService
{
    public function loginUrl(): string
    {
        // @todo need to change with correct url
        return config('app.url') . '/login';
    }

    public function supportEmail(): string
    {
        // @todo need to change with correct email
        return 'support@adieu.day';
    }

}
