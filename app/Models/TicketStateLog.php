<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStateLog extends Model
{
    protected $table = 'tickets_state_logs';
    protected $fillable = [
        'ticket_id',
        'from_state',
        'to_state',
        'performed_by',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
