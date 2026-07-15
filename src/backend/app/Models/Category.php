<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categorias';

    protected $fillable = [
        'nome',
        'name',
    ];

    public function permissoes(){
        return $this->hasMany(Permissoes::class, 'id_categoria');
    }
}
