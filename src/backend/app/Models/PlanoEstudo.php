<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlanoEstudo extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'plano_estudos';

    protected $fillable = [
        'ano',
        'id_curso',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function planoUcs(): HasMany
    {
        return $this->hasMany(PlanoUc::class, 'id_plano');
    }

    public function estudantes(): HasMany
    {
        return $this->hasMany(Estudante::class, 'plano_de_estudo');
    }

    public function equivalenciaRules(): HasMany
    {
        return $this->hasMany(EquivalenciaRule::class, 'id_plano');
    }
}
