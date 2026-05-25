<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'address',
        'pickup_location',
        'block',
        'pickup_date',
        'pickup_time',
        'status',
        'waste_image',
        'admin_notes',
        'completed_at',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteCategories()
    {
        return $this->belongsToMany(WasteCategory::class, 'pickup_request_waste_category');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed'   => 'bg-green-100 text-green-800',
            'cancelled'   => 'bg-red-100 text-red-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($this->status),
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->request_number = 'WMP-' . strtoupper(uniqid());
        });
    }
}
