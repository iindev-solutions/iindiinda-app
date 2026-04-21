<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'from_address',
        'to_address',
        'date',
        'time',
        'seats',
        'price',
        'comment',
        'status',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AyanResponse::class);
    }
}
