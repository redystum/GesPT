<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginDominios extends Model
{
    protected $table = 'login_dominios';

    protected $fillable = [
        'dominio',
        'id_role'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role');
    }
}
