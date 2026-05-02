<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UusResponse extends Model
{
    use HasFactory;

    protected $table = 'uus_responses';

    protected $fillable = [
        'task_id',
        'user_id',
        'message',
        'offered_price',
        'status',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(UusTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
