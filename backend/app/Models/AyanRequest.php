<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AyanRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'passenger_id',
        'from_address',
        'to_address',
        'date',
        'time',
        'description',
        'status',
    ];

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AyanResponse::class, 'request_id');
    }
}
