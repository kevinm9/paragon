<?php

namespace App\Http\Controllers;

use App\Models\Deudor; // tu modelo
use Inertia\Inertia;
use Illuminate\Http\Request;
class DeudorController extends Controller
{
    public function index()
    {
        $deudores = [
            [
                "id" => 1,
                "nombre" => "Juan Pérez",
                "deuda" => 1200.50,
                "dias" => 15,
                "ultimo_contacto" => "2024-09-15",
                "estado" => "En gestión",
                "contacto" => [
                    "telefono" => "+593 999 888 777",
                    "email" => "juan.perez@email.com",
                    "direccion" => "Av. Quito 123, Guayaquil"
                ],
                "compromisoPago" => [
                    "fecha" => "2024-09-30",
                    "monto" => 300.00
                ],
                "historialGestion" => [
                    ["fecha" => "2024-09-01", "accion" => "Llamada telefónica", "resultado" => "Prometió pagar"],
                    ["fecha" => "2024-09-10", "accion" => "Visita domiciliaria", "resultado" => "No encontrado"]
                ],
                "detalleOperaciones" => [
                    ["fecha" => "2024-08-01", "monto" => 500.00, "descripcion" => "Compra en tienda A"],
                    ["fecha" => "2024-08-15", "monto" => 700.50, "descripcion" => "Compra en tienda B"]
                ]
            ],
            [
                "id" => 2,
                "nombre" => "María López",
                "deuda" => 850.00,
                "dias" => 30,
                "ultimo_contacto" => "2024-09-10",
                "estado" => "Pendiente",
                "contacto" => [
                    "telefono" => "+593 988 777 666",
                    "email" => "maria.lopez@email.com",
                    "direccion" => "Calle 10 de Agosto, Quito"
                ],
                "compromisoPago" => null,
                "historialGestion" => [
                    ["fecha" => "2024-09-05", "accion" => "Correo enviado", "resultado" => "Sin respuesta"],
                ],
                "detalleOperaciones" => [
                    ["fecha" => "2024-07-20", "monto" => 300.00, "descripcion" => "Compra supermercado"],
                ]
            ]
        ];

        return Inertia::render('Deudores/Index', [
            'deudores' => $deudores,
        ]);
    }
}
