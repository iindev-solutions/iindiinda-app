<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function scopeUpcomingForFeed(Builder $query): Builder
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i');

        return $query->where(function (Builder $query) use ($today, $currentTime) {
            $query->whereDate('date', '>', $today)
                ->orWhere(function (Builder $query) use ($today, $currentTime) {
                    $query->whereDate('date', $today)
                        ->where('time', '>=', $currentTime);
                });
        });
    }

    public function isPast(): bool
    {
        return Carbon::parse(sprintf('%s %s', $this->date, $this->time))->isPast();
    }
}
