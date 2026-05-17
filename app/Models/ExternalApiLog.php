<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalApiLog extends Model
{
    protected $table = 'external_apis_logs';
    protected $fillable = [
        'ticket_id',
        'status_code',
        'response_body',
        'success',
        'attempted_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
