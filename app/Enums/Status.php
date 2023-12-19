<?php
namespace App\Enums;

enum Status: int
{
    case Pending = 0;
    case Active = 1;
    case Inactive = 2;
}
