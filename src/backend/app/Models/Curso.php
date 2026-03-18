<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cursos';

    protected $fillable = [
        'nome',
        'sigla',
        'codigo',
        'id_curso_parente',
    ];

    public function parentCurso(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_curso_parente');
    }

    public function childCursos(): HasMany
    {
        return $this->hasMany(self::class, 'id_curso_parente');
    }

    public function ucs(): HasMany
    {
        return $this->hasMany(Uc::class, 'id_curso');
    }

    public function planoEstudos(): HasMany
    {
        return $this->hasMany(PlanoEstudo::class, 'id_curso');
    }
}
