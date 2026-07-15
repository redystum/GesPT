<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Opcoes extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'opcoes';

    protected $fillable = [
        'id_uc_opcional',
        'id_uc',
    ];

    public function ucEscolhida(): BelongsTo
    {
        return $this->belongsTo(Uc::class, 'id_uc');
    }

    public function escolhasEstudantes(): HasMany
    {
        return $this->hasMany(OpcoesEstudante::class, 'id_opcao');
    }

    public function planoUc(): BelongsTo
    {
        return $this->belongsTo(PlanoUc::class, 'id_uc_opcional');
    }
}
