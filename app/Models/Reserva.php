<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'total',
        'cliente_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'float',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemReserva::class, 'reserva_id');
    }

    public function salidas(): BelongsToMany
    {
        return $this->belongsToMany(Salida::class, 'item_reservas', 'reserva_id', 'salida_id')
            ->using(ItemReserva::class)
            ->withPivot(['cantidadPersonas', 'precioUnitario'])
            ->withTimestamps();
    }
}
