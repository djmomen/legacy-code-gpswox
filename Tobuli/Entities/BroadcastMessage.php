<?php

namespace Tobuli\Entities;

use App\Events\BroadcastMessageProgress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tobuli\Traits\Searchable;

class BroadcastMessage extends AbstractEntity
{
    public const STATUS_COMPLETED = 10;
    public const STATUS_FAILED = 9;
    public const STATUS_IN_PROGRESS = 5;
    public const STATUS_NEW = 0;

    use Searchable;

    protected $fillable = [
        'user_id',
        'channel',
        'status',
        'title',
        'content',
        'total',
        'success',
        'fail',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    protected array $searchable = [
        'title',
        'content',
    ];

    public static function boot()
    {
        parent::boot();

        static::updated(function (BroadcastMessage $model) {
            if ($model->isDirty('success')) {
                event(new BroadcastMessageProgress($model));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTitleAttribute()
    {
        $list = [
            self::STATUS_NEW => trans('front.waiting'),
            self::STATUS_IN_PROGRESS => trans('front.in_progress'),
            self::STATUS_FAILED => trans('global.failed'),
            self::STATUS_COMPLETED => trans('front.completed'),
        ];

        return $list[$this->status] ?? '';
    }

    public function getChannelTitleAttribute()
    {
        $list = [
            self::STATUS_NEW => trans('front.waiting'),
            self::STATUS_IN_PROGRESS => trans('front.in_progress'),
            self::STATUS_FAILED => trans('global.failed'),
            self::STATUS_COMPLETED => trans('front.completed'),
        ];

        return $list[$this->status] ?? '';
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }
}
