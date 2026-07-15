<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquivalenciaRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equivalencia_rules';

    protected $fillable = [
        'id_plano',
    ];

    public function planoEstudo(): BelongsTo
    {
        return $this->belongsTo(PlanoEstudo::class, 'id_plano');
    }

    public function equivalencias(): HasMany
    {
        return $this->hasMany(Equivalencia::class, 'id_rule');
    }
}
