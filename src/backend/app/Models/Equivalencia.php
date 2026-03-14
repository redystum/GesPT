<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equivalencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equivalencias';

    protected $fillable = [
        'id_rule',
        'id_uc',
        'lado',
        'custom',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(EquivalenciaRule::class, 'id_rule');
    }

    public function uc(): BelongsTo
    {
        return $this->belongsTo(Uc::class, 'id_uc');
    }
}
