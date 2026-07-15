<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OpcoesEstudante extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
