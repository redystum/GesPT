<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EquivalenciaRule extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
