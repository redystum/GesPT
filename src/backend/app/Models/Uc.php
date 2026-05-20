<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ucs';

    protected $fillable = [
        'nome',
        'sigla',
        'ects',
        'horas',
        'id_curso',
        'semestre',
        'name',
        'ano',
        'horas_t',
        'horas_tp',
        'horas_pl',
        'horas_tc',
        'horas_s',
        'horas_e',
        'horas_ot',
        'horas_o',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function planoUcs(): HasMany
    {
        return $this->hasMany(PlanoUc::class, 'id_uc');
    }

    public function opcoesBase(): HasMany
    {
        return $this->hasMany(Opcoes::class, 'id_uc');
    }

    public function opcoesOpcionais(): HasMany
    {
        return $this->hasMany(Opcoes::class, 'id_uc_opcional');
    }

    public function equivalencias(): HasMany
    {
        return $this->hasMany(Equivalencia::class, 'id_uc');
    }
}
