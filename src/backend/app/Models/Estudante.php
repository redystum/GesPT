<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudantes';

    protected $fillable = [
        'email',
        'plano_de_estudo',
    ];

    public function planoEstudo(): BelongsTo
    {
        return $this->belongsTo(PlanoEstudo::class, 'plano_de_estudo');
    }

    public function opcoesEscolhidas(): HasMany
    {
        return $this->hasMany(OpcoesEstudante::class, 'id_estudante');
    }
}
