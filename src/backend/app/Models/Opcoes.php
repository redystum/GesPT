<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opcoes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opcoes';

    protected $fillable = [
        'id_uc_opcional',
        'id_uc',
    ];

    public function ucOpcional(): BelongsTo
    {
        return $this->belongsTo(Uc::class, 'id_uc_opcional');
    }

    public function ucBase(): BelongsTo
    {
        return $this->belongsTo(Uc::class, 'id_uc');
    }

    public function escolhasEstudantes(): HasMany
    {
        return $this->hasMany(OpcoesEstudante::class, 'id_opcao');
    }
}
