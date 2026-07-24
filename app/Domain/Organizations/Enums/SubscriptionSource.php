<?php

namespace App\Domain\Organizations\Enums;

enum SubscriptionSource: string
{
    case System = 'system';
    case Manual = 'manual';
    case Payment = 'payment';
}
