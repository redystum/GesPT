<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

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
