<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AyanResponse extends Model
{
    use HasFactory;

    protected $table = 'responses';

    protected $fillable = [
        'user_id',
        'trip_id',
        'request_id',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AyanRequest::class, 'request_id');
    }
}
