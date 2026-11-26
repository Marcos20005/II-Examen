<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precioPorPersona',
        'duracionHoras',
    ];

    protected $casts = [
        'precioPorPersona' => 'float',
        'duracionHoras' => 'integer',
    ];

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class, 'tour_id');
    }
}
