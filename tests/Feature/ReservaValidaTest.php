<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Guia;
use App\Models\Salida;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaValidaTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_reserva_valida_con_dos_salidas_se_registra_y_descuenta_los_cupos(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'María Solís',
            'correo' => 'maria@example.com',
        ]);

        $guia = Guia::create([
            'nombre' => 'Carlos Méndez',
            'telefono' => '8888-9999',
        ]);

        $tourVolcan = Tour::create([
            'nombre' => 'Cráter Rincón de la Vieja',
            'precioPorPersona' => 50.00,
            'duracionHoras' => 4,
        ]);

        $tourTermales = Tour::create([
            'nombre' => 'Aguas Termales y Barro',
            'precioPorPersona' => 35.00,
            'duracionHoras' => 3,
        ]);

        $salida1 = Salida::create([
            'fecha' => '2026-12-01',
            'cuposDisponibles' => 10,
            'tour_id' => $tourVolcan->id,
            'guia_id' => $guia->id,
        ]);

        $salida2 = Salida::create([
            'fecha' => '2026-12-02',
            'cuposDisponibles' => 8,
            'tour_id' => $tourTermales->id,
            'guia_id' => null,
        ]);

        $response = $this->postJson('/reservas', [
            'cliente_id' => $cliente->id,
            'items' => [
                ['salida_id' => $salida1->id, 'cantidad_personas' => 3],
                ['salida_id' => $salida2->id, 'cantidad_personas' => 2],
            ],
        ]);

        
        $response->assertStatus(201)
            ->assertJsonPath('reserva.cliente_id', $cliente->id)
            ->assertJsonPath('reserva.total', 220);

        $this->assertDatabaseCount('reservas', 1);
        $this->assertDatabaseHas('reservas', [
            'cliente_id' => $cliente->id,
            'total' => 220.00,
        ]);

        $this->assertDatabaseCount('item_reservas', 2);
        $this->assertDatabaseHas('item_reservas', [
            'salida_id' => $salida1->id,
            'cantidadPersonas' => 3,
            'precioUnitario' => 50.00,
        ]);
        $this->assertDatabaseHas('item_reservas', [
            'salida_id' => $salida2->id,
            'cantidadPersonas' => 2,
            'precioUnitario' => 35.00,
        ]);

        $this->assertSame(7, $salida1->fresh()->cuposDisponibles);
        $this->assertSame(6, $salida2->fresh()->cuposDisponibles);
    }
}
