<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['name', 'code', 'region'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sales_area_id');
    }
}
