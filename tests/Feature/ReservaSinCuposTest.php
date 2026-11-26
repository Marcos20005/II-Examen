<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Salida;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaSinCuposTest extends TestCase
{
    use RefreshDatabase;

    public function test_si_la_segunda_salida_no_tiene_cupos_no_queda_ningun_registro_y_los_cupos_de_la_primera_no_cambian(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Juan Pérez',
            'correo' => 'juan@example.com',
        ]);

        $tour = Tour::create([
            'nombre' => 'Catarata La Cangreja',
            'precioPorPersona' => 40.00,
            'duracionHoras' => 5,
        ]);

        $salida1 = Salida::create([
            'fecha' => '2026-12-05',
            'cuposDisponibles' => 10,
            'tour_id' => $tour->id,
            'guia_id' => null,
        ]);

        $salida2 = Salida::create([
            'fecha' => '2026-12-06',
            'cuposDisponibles' => 2,
            'tour_id' => $tour->id,
            'guia_id' => null,
        ]);

        $response = $this->postJson('/reservas', [
            'cliente_id' => $cliente->id,
            'items' => [
                ['salida_id' => $salida1->id, 'cantidad_personas' => 4],
                ['salida_id' => $salida2->id, 'cantidad_personas' => 5],
            ],
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseCount('reservas', 0);
        $this->assertDatabaseCount('item_reservas', 0);

        $this->assertSame(10, $salida1->fresh()->cuposDisponibles);
        $this->assertSame(2, $salida2->fresh()->cuposDisponibles);
    }
}
