<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class AgalRequest extends Model
{
    use HasFactory;

    protected $table = 'agal_requests';

    protected $fillable = [
        'sender_id',
        'from_address',
        'to_address',
        'date',
        'time',
        'size_label',
        'weight_kg',
        'contents_summary',
        'fragility',
        'documents_required',
        'budget',
        'notes',
        'status',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AgalResponse::class, 'request_id');
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
