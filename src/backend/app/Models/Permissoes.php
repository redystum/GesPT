<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permissoes extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    protected $fillable = [
        'permissao',
        'nome',
        'descricao',
    ];

    public function rolePermissoes(): HasMany
    {
        return $this->hasMany(RolePermissoes::class, 'id_permissao');
    }

    public function userPermissoes(): HasMany
    {
        return $this->hasMany(UserPermissoes::class, 'id_permiscao');
    }
}
