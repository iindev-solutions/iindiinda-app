<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UusTask extends Model
{
    use HasFactory;

    protected $table = 'uus_tasks';

    protected $fillable = [
        'customer_id',
        'category',
        'description',
        'location',
        'desired_when',
        'date',
        'budget',
        'budget_type',
        'urgency',
        'response_limit',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(UusResponse::class, 'task_id');
    }
}
