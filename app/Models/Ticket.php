<?php

namespace App\Models;

use App\Enums\TicketState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
        use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachment_path',
        'state',
        'user_id',
    ];

    protected $casts = [
        'state' => TicketState::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stateLogs()
    {
        return $this->hasMany(TicketStateLog::class);
    }

    public function apiLogs()
    {
        return $this->hasMany(ExternalApiLog::class);
    }

    public function scopePendingAdmin1($query)
    {
        return $query->whereIn('state', [
            TicketState::Submitted,
            TicketState::ApprovedByAdmin1,
            TicketState::RejectedByAdmin1
        ]);
    }

    public function scopePendingAdmin2($query)
    {
        return $query->whereIn('state', [
            TicketState::ApprovedByAdmin1,
            TicketState::RejectedByAdmin2
        ]);
    }

    public function scopeExternalProcessing($query)
    {
        return $query->where('state', TicketState::ExternalProcessing);
    }
}
