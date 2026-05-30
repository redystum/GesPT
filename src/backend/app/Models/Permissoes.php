<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Permissoes extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    protected $fillable = [
        'permissao',
        'nome',
        'descricao',
        'id_categoria',
        'required_permissoes'
    ];

    protected function casts(): array
    {
        return [
            'required_permissoes' => 'array',
        ];
    }

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(Role::class, RolePermissoes::class, 'id_permissao', 'id', 'id', 'id_role');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UserPermissoes::class, 'id_permissao', 'id', 'id', 'id_user');
    }

    public function categorias(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categoria');
    }
}
