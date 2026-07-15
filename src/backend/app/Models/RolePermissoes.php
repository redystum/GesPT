<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermissoes extends Model
{
    use HasFactory;

    protected $table = 'role_permissoes';

    protected $fillable = [
        'id_role',
        'id_permissao',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(Permissoes::class, 'id_permissao');
    }
}
