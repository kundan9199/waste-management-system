<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_request_id',
        'subject',
        'description',
        'status',
        'admin_response',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickupRequest()
    {
        return $this->belongsTo(PickupRequest::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open'      => 'bg-red-100 text-red-800',
            'in_review' => 'bg-yellow-100 text-yellow-800',
            'resolved'  => 'bg-green-100 text-green-800',
            'closed'    => 'bg-gray-100 text-gray-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }
}
