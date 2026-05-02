<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TalMaster extends Model
{
    use HasFactory;

    protected $table = 'tal_masters';

    protected $fillable = [
        'master_id',
        'category',
        'service_label',
        'description',
        'location',
        'availability_status',
        'available_note',
        'price_from',
        'status',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TalBooking::class, 'tal_master_id');
    }
}
