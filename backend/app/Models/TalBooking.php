<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalBooking extends Model
{
    use HasFactory;

    protected $table = 'tal_bookings';

    protected $fillable = [
        'tal_master_id',
        'user_id',
        'message',
        'desired_time',
        'status',
    ];

    public function talMaster(): BelongsTo
    {
        return $this->belongsTo(TalMaster::class, 'tal_master_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
