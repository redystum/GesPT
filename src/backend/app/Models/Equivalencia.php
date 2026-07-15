<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Equivalencia extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    const LADO_ORIGINAL = 'Original';

    const LADO_FINAL = 'Final';

    protected $table = 'equivalencias';

    protected $fillable = [
        'id_rule',
        'id_plano_uc',
        'lado',
        'custom',
        'observacao',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(EquivalenciaRule::class, 'id_rule');
    }

    public function plano_uc(): BelongsTo
    {
        return $this->belongsTo(PlanoUc::class, 'id_plano_uc');
    }
}
