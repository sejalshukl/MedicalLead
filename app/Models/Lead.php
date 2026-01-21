<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'email',
        'phone',
        'country',
        'medical_issue',
        'preferred_date',
        'status',
        'assigned_to',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeForCoordinator($query, $user)
    {
        return $query->where('assigned_to', $user->id);
    }
}
