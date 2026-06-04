<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const USER_TYPE_LOCAL = "local";
    const USER_TYPE_REMOTE = "remote";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'blocked',
        'blocked_reason',
        'blocked_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissoes(): HasManyThrough
    {
        return $this->hasManyThrough(Permissoes::class, UserPermissoes::class, 'id_user', 'id', 'id', 'id_permissao');
    }

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(Role::class, UserRole::class, 'id_user', 'id', 'id', 'id_role');
    }

    public function allPermissoes()
    {
        return $this->permissoes()->get()->merge($this->roles()->with('permissoes')->get()->pluck('permissoes')->flatten())->unique('id');
    }

    public function hasPermission($permission)
    {
        if (is_array($permission)) {
            return $this->allPermissoes()->whereIn('permissao', $permission)->isNotEmpty();
        }
        return $this->allPermissoes()->contains('permissao', $permission);
    }
}
