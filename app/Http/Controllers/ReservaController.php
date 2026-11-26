<?php

namespace App\Http\Controllers;

use App\Services\ServicioReservas;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Validar los datos de entrada y delegar la creación de la reserva al servicio.
     */
    public function store(Request $request, ServicioReservas $servicioReservas): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.salida_id' => ['required', 'integer', 'exists:salidas,id', 'distinct'],
            'items.*.cantidad_personas' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $reserva = $servicioReservas->reservar(
                (int) $validated['cliente_id'],
                $validated['items']
            );
        } catch (DomainException $e) {
            return response()->json([
                'mensaje' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'mensaje' => 'Reserva creada exitosamente',
            'reserva' => $reserva->load('items'),
        ], 201);
    }
}
