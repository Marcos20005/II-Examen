<?php

namespace App\Services;

use App\Models\ItemReserva;
use App\Models\Reserva;
use App\Models\Salida;
use DomainException;
use Illuminate\Support\Facades\DB;

class ServicioReservas
{
    
    public function reservar(int $clienteId, array $items): Reserva
    {
        return DB::transaction(function () use ($clienteId, $items) {
            $itemsOrdenados = collect($items)
                ->sortBy('salida_id')
                ->values();

            $total = 0.0;
            $itemsParaCrear = [];

            foreach ($itemsOrdenados as $item) {
                $cantidadPersonas = (int) $item['cantidad_personas'];

                $salida = Salida::with('tour')
                    ->lockForUpdate()
                    ->findOrFail($item['salida_id']);

                if ($salida->cuposDisponibles < $cantidadPersonas) {
                    throw new DomainException(
                        "La salida {$salida->id} no tiene cupos suficientes."
                    );
                }

                $salida->decrement('cuposDisponibles', $cantidadPersonas);

                $precioUnitario = (float) $salida->tour->precioPorPersona;
                $total += $precioUnitario * $cantidadPersonas;

                $itemsParaCrear[] = [
                    'salida_id' => $salida->id,
                    'cantidadPersonas' => $cantidadPersonas,
                    'precioUnitario' => $precioUnitario,
                ];
            }

            $reserva = Reserva::create([
                'cliente_id' => $clienteId,
                'fecha' => now()->toDateString(),
                'total' => $total,
            ]);

            foreach ($itemsParaCrear as $itemData) {
                ItemReserva::create([
                    'reserva_id' => $reserva->id,
                    'salida_id' => $itemData['salida_id'],
                    'cantidadPersonas' => $itemData['cantidadPersonas'],
                    'precioUnitario' => $itemData['precioUnitario'],
                ]);
            }

            return $reserva;
        });
    }
}
