<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'roles';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function permissoes(): HasManyThrough
    {
        return $this->hasManyThrough(Permissoes::class, RolePermissoes::class, 'id_role', 'id', 'id', 'id_permissao');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UserRole::class, 'id_role', 'id', 'id', 'id_user');
    }
}
