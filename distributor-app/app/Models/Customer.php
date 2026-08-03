<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'contact_person', 'phone',
        'email', 'address', 'area_id', 'customer_type', 'referal_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function referals(): HasMany
    {
        return $this->hasMany(Referal::class);
    }

    public function referal(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referal_id');
    }

    public function referredCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'referal_id');
    }
}
