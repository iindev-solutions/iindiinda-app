<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function scopeUpcomingForFeed(Builder $query): Builder
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i');

        return $query->where(function (Builder $query) use ($today, $currentTime) {
            $query->whereDate('date', '>', $today)
                ->orWhere(function (Builder $query) use ($today, $currentTime) {
                    $query->whereDate('date', $today)
                        ->where(function (Builder $query) use ($currentTime) {
                            $query->whereNull('time')
                                ->orWhere('time', '>=', $currentTime);
                        });
                });
        });
    }

    public function isPast(): bool
    {
        return Carbon::parse(sprintf('%s %s', $this->date, $this->time ?: '23:59'))->isPast();
    }
}
