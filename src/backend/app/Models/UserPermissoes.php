<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermissoes extends Model
{
    use HasFactory;

    protected $table = 'user_permissoes';

    protected $fillable = [
        'id_user',
        'id_permiscao',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(Permissoes::class, 'id_permiscao');
    }
}
