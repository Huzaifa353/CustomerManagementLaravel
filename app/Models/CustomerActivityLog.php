<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CustomerActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customer_activity_logs';

    protected $fillable = [
        'customer_id',
        'action',
        'ip_address',
        'device',
        'timestamp',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',      // auto-cast JSON
        'timestamp' => 'datetime',  // Carbon instances
    ];

    // Optional: define relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', '_id');
    }
}
