<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemReserva extends Pivot
{
    protected $table = 'item_reservas';

    public $incrementing = true;

    protected $fillable = [
        'cantidadPersonas',
        'precioUnitario',
        'reserva_id',
        'salida_id',
    ];

    protected $casts = [
        'cantidadPersonas' => 'integer',
        'precioUnitario' => 'float',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }
}
