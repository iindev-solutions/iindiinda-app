<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgalResponse extends Model
{
    use HasFactory;

    protected $table = 'agal_responses';

    protected $fillable = [
        'user_id',
        'route_id',
        'request_id',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(AgalRoute::class, 'route_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AgalRequest::class, 'request_id');
    }
}
