<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpcoesEstudante extends Model
{
    use HasFactory;

    protected $table = 'opcoes_estudante';

    protected $fillable = [
        'id_estudante',
        'id_opcao',
    ];

    public function estudante(): BelongsTo
    {
        return $this->belongsTo(Estudante::class, 'id_estudante');
    }

    public function opcao(): BelongsTo
    {
        return $this->belongsTo(Opcoes::class, 'id_opcao');
    }
}
