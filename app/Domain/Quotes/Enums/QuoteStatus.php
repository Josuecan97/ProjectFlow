<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Enums;

enum QuoteStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Archived = 'archived';

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Draft => in_array($status, [self::Sent, self::Expired, self::Archived], true),
            self::Sent => in_array(
                $status,
                [self::Approved, self::Rejected, self::Expired, self::Archived],
                true,
            ),
            self::Approved, self::Rejected, self::Expired => $status === self::Archived,
            self::Archived => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Sent => 'Enviada',
            self::Approved => 'Aprobada',
            self::Rejected => 'Rechazada',
            self::Expired => 'Expirada',
            self::Archived => 'Archivada',
        };
    }
}
