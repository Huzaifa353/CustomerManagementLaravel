<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'gender',
        'country',
        'department',
        'designation',
        'signup_date',
    ];

    protected $casts = [
        'signup_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Optional: relationship to activity logs (if you create a table for it)
    public function activityLogs()
    {
        return $this->hasMany(CustomerActivityLog::class, 'customer_id', 'id');
    }
}
