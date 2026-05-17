<?php

namespace App\Enums;

use DomainException;

enum TicketState: string
{
    case Submitted = 'submitted';
    case ApprovedByAdmin1 = 'approved_by_admin1';
    case RejectedByAdmin1 = 'rejected_by_admin1';
    case RejectedByAdmin2 = 'rejected_by_admin2';
    case ExternalProcessing = 'external_processing';
    case ExternalFailed = 'external_failed';
    case Success = 'success';

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    public function assertCanTransition(self $next): void
    {
        if (!$this->canTransitionTo($next)) {
            throw new DomainException(
                "Invalid transition from {$this->value} to {$next->value}"
            );
        }
    }

    private function allowedTransitions(): array
    {
        return match ($this) {
            self::Submitted => [
                self::ApprovedByAdmin1,
                self::RejectedByAdmin1
            ],

            self::ApprovedByAdmin1 => [
                self::ExternalProcessing,
                self::RejectedByAdmin2,
                self::RejectedByAdmin1
            ],

            self::RejectedByAdmin1 => [
                self::ApprovedByAdmin1,
            ],

            self::ExternalProcessing => [
                self::Success,
                self::RejectedByAdmin2,
                self::ExternalFailed
            ],

            default => []
        };
    }
}
