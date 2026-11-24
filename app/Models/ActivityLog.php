<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_name',
        'actor_role',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public static function record(string $action, ?string $description = null): self
    {
        $actorId = (int) session('user.id');
        $actor = $actorId > 0 ? User::query()->find($actorId) : null;

        return static::create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? session('user.name', 'System'),
            'actor_role' => $actor?->role ?? session('user.role'),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 2000),
        ]);
    }
}
