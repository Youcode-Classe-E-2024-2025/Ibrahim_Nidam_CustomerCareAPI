<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'subject', 'description', 'customer_id', 'assigned_agent_id', 'status', 'claimed_at', 'resolved_at'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id')
            ->select(['id', 'name', 'email']);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function claimingAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }
}
