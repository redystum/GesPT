<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoUc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plano_ucs';

    protected $fillable = [
        'id_plano',
        'id_uc',
        'opcional',
    ];

    protected $casts = [
        'opcional' => 'boolean',
    ];

    public function planoEstudo(): BelongsTo
    {
        return $this->belongsTo(PlanoEstudo::class, 'id_plano');
    }

    public function uc(): BelongsTo
    {
        return $this->belongsTo(Uc::class, 'id_uc');
    }
}

