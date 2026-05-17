<?php

namespace App\Enums;

use DomainException;

//Tickets states
enum TicketState: string
{
    case Submitted = 'submitted';
    case ApprovedByAdmin1 = 'approved_by_admin1';
    case RejectedByAdmin1 = 'rejected_by_admin1';
    case RejectedByAdmin2 = 'rejected_by_admin2';
    case ExternalProcessing = 'external_processing';
    case ExternalFailed = 'external_failed';
    case Success = 'success';

    //Controls state transmission logic
    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    //This function is called in the client code, on the current enum, to decide if it can be transitioned to the other state.
    public function assertCanTransition(self $next): void
    {
        if (!$this->canTransitionTo($next)) {
            throw new DomainException(
                "Invalid transition from {$this->value} to {$next->value}"
            );
        }
    }

    //This function decides whether a state can be transitioned to another or not.
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
