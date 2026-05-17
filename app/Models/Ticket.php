<?php

namespace App\Models;

use App\Enums\TicketState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
        use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'state',
        'attachment_path',
        'admin1_comment',
        'admin2_comment',
        'admin1_action_at',
        'admin2_action_at',
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

    //The scope used to retrieve tickets for first admin.
    //Only tickets in these scopes are shown to admin 1.
    public function scopePendingAdmin1($query)
    {
        return $query->whereIn('state', [
            TicketState::Submitted,
            TicketState::ApprovedByAdmin1,
            TicketState::RejectedByAdmin1
        ]);
    }

    //The scope used to retrieve tickets for second admin.
    //Only tickets in these scopes are shown to admin 2.
    public function scopePendingAdmin2($query)
    {
        return $query->whereIn('state', [
            TicketState::ApprovedByAdmin1,
            TicketState::RejectedByAdmin2
        ]);
    }

    //Tickets that are currently being processed by external API.
    //Hasn't been used by any UI.
    public function scopeExternalProcessing($query)
    {
        return $query->where('state', TicketState::ExternalProcessing);
    }
}
