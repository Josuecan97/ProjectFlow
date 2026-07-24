<?php

namespace App\Domain\Organizations\Enums;

enum SubscriptionStatus: string
{
    case Trial = 'trial';
    case Active = 'active';
    case PastDue = 'past_due';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Trial => 'Prueba',
            self::Active => 'Activa',
            self::PastDue => 'Pago pendiente',
            self::Suspended => 'Suspendida',
            self::Cancelled => 'Cancelada',
            self::Expired => 'Expirada',
        };
    }
}
