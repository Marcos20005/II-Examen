<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'cuposDisponibles',
        'tour_id',
        'guia_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cuposDisponibles' => 'integer',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function guia(): BelongsTo
    {
        return $this->belongsTo(Guia::class, 'guia_id');
    }
}
