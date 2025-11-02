<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgendaPagosController extends Controller
{
    /**
     * Muestra la vista principal de Agenda de Pagos
     */
    public function index(): Response
    {
        // Aquí puedes cargar los datos desde la base de datos
        // Por ahora retornamos la vista con datos de ejemplo
        
        $compromisos = $this->getCompromisos();
        
        return Inertia::render('Agendas/Index', [
            'compromisos' => $compromisos,
            'estadosDisponibles' => $this->getEstados(),
            'responsables' => $this->getResponsables(),
            'cedentes' => $this->getCedentes(),
        ]);
    }

    /**
     * Cambia el estado de un compromiso
     */
    public function cambiarEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'nuevoEstado' => 'required|string',
            'observacion' => 'nullable|string|max:500',
        ]);

        // Aquí implementarías la lógica para actualizar el estado en la BD
        // Ejemplo:
        /*
        $compromiso = CompromisoPago::findOrFail($id);
        $estadoAnterior = $compromiso->estado;
        $compromiso->estado = $validated['nuevoEstado'];
        $compromiso->save();
        
        // Registrar en historial
        HistorialCambioEstado::create([
            'compromiso_id' => $id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $validated['nuevoEstado'],
            'observacion' => $validated['observacion'],
            'usuario_id' => auth()->id(),
            'fecha_cambio' => now(),
        ]);
        */

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data' => [
                'id' => $id,
                'nuevoEstado' => $validated['nuevoEstado'],
                'observacion' => $validated['observacion'],
            ]
        ]);
    }

    /**
     * Exporta la agenda de pagos
     */
    public function exportar(Request $request)
    {
        // Implementar lógica de exportación (CSV, Excel, etc.)
        // Ejemplo:
        /*
        $compromisos = CompromisoPago::with(['deudor', 'cedente', 'responsable'])
            ->whereIn('estado', ['Pendiente', 'Confirmada', 'Promesa de Pago'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
        
        return Excel::download(new CompromisosExport($compromisos), 'agenda-pagos.xlsx');
        */
        
        return response()->json([
            'success' => true,
            'message' => 'Funcionalidad de exportación en desarrollo',
        ]);
    }

    /**
     * Obtiene los compromisos de la base de datos
     * (Método temporal con datos de ejemplo)
     */
    private function getCompromisos(): array
    {
        // Aquí harías la consulta real a tu base de datos
        // Ejemplo con Eloquent:
        /*
        return CompromisoPago::with(['deudor', 'cedente', 'responsable'])
            ->whereIn('estado', ['Pendiente', 'Confirmada', 'Promesa de Pago', 'Vencida (1 día)', 'Vencida (3 días)'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get()
            ->map(function($compromiso) {
                return [
                    'id' => $compromiso->id,
                    'fechaVencimiento' => $compromiso->fecha_vencimiento->format('Y-m-d'),
                    'clienteDeudor' => $compromiso->deudor->nombre,
                    'numCompromiso' => $compromiso->numero_compromiso,
                    'totalCompromisos' => $compromiso->deudor->compromisos()->count(),
                    'montoPendiente' => (float) $compromiso->monto,
                    'estado' => $compromiso->estado,
                    'responsable' => $compromiso->responsable->nombre ?? 'Sin asignar',
                    'cedente' => $compromiso->cedente->nombre,
                ];
            })
            ->toArray();
        */

        // Datos de ejemplo para pruebas
        return [
            [
                'id' => 1,
                'fechaVencimiento' => '2025-10-08',
                'clienteDeudor' => 'Juan Pérez',
                'numCompromiso' => 1,
                'totalCompromisos' => 12,
                'montoPendiente' => 350.00,
                'estado' => 'Pendiente',
                'responsable' => 'Asesor A',
                'cedente' => 'Banco Guayaquil'
            ],
            [
                'id' => 2,
                'fechaVencimiento' => '2025-10-09',
                'clienteDeudor' => 'EcoKakao S.A.',
                'numCompromiso' => 3,
                'totalCompromisos' => 6,
                'montoPendiente' => 2100.00,
                'estado' => 'Promesa de Pago',
                'responsable' => 'Asesor B',
                'cedente' => 'Banco Pichincha'
            ],
            [
                'id' => 3,
                'fechaVencimiento' => '2025-10-10',
                'clienteDeudor' => 'Barry Callebaut',
                'numCompromiso' => 2,
                'totalCompromisos' => 8,
                'montoPendiente' => 1750.00,
                'estado' => 'Vencida (3 días)',
                'responsable' => 'Asesor C',
                'cedente' => 'Supermercado Tuti'
            ],
            [
                'id' => 4,
                'fechaVencimiento' => '2025-10-11',
                'clienteDeudor' => 'Cargill Ecuador',
                'numCompromiso' => 1,
                'totalCompromisos' => 1,
                'montoPendiente' => 500.00,
                'estado' => 'Pendiente',
                'responsable' => 'Asesor A',
                'cedente' => 'Banco Guayaquil'
            ],
            [
                'id' => 5,
                'fechaVencimiento' => '2025-10-12',
                'clienteDeudor' => 'María Fernández',
                'numCompromiso' => 4,
                'totalCompromisos' => 12,
                'montoPendiente' => 800.00,
                'estado' => 'Confirmada',
                'responsable' => 'Asesor D',
                'cedente' => 'Banco Pichincha'
            ],
            [
                'id' => 6,
                'fechaVencimiento' => '2025-10-13',
                'clienteDeudor' => 'Grupo Torres',
                'numCompromiso' => 2,
                'totalCompromisos' => 10,
                'montoPendiente' => 1200.00,
                'estado' => 'Pendiente',
                'responsable' => 'Asesor B',
                'cedente' => 'Banco Guayaquil'
            ],
            [
                'id' => 7,
                'fechaVencimiento' => '2025-10-14',
                'clienteDeudor' => 'Ana Gómez',
                'numCompromiso' => 1,
                'totalCompromisos' => 3,
                'montoPendiente' => 450.00,
                'estado' => 'Promesa de Pago',
                'responsable' => 'Asesor C',
                'cedente' => 'Supermercado Tuti'
            ],
            [
                'id' => 8,
                'fechaVencimiento' => '2025-10-15',
                'clienteDeudor' => 'Carlos López',
                'numCompromiso' => 5,
                'totalCompromisos' => 12,
                'montoPendiente' => 950.00,
                'estado' => 'Vencida (1 día)',
                'responsable' => 'Asesor A',
                'cedente' => 'Banco Pichincha'
            ],
            [
                'id' => 9,
                'fechaVencimiento' => '2025-10-16',
                'clienteDeudor' => 'Industrias Ruiz',
                'numCompromiso' => 1,
                'totalCompromisos' => 6,
                'montoPendiente' => 3200.00,
                'estado' => 'Pendiente',
                'responsable' => 'Asesor E',
                'cedente' => 'Banco Guayaquil'
            ],
            [
                'id' => 10,
                'fechaVencimiento' => '2025-10-17',
                'clienteDeudor' => 'Laura Martínez',
                'numCompromiso' => 2,
                'totalCompromisos' => 4,
                'montoPendiente' => 600.00,
                'estado' => 'Confirmada',
                'responsable' => 'Asesor D',
                'cedente' => 'Supermercado Tuti'
            ],
            [
                'id' => 11,
                'fechaVencimiento' => '2025-10-18',
                'clienteDeudor' => 'Pedro Sánchez',
                'numCompromiso' => 3,
                'totalCompromisos' => 5,
                'montoPendiente' => 1500.00,
                'estado' => 'Vencida (7+ días)',
                'responsable' => 'Asesor B',
                'cedente' => 'Banco Guayaquil'
            ],
            [
                'id' => 12,
                'fechaVencimiento' => '2025-10-20',
                'clienteDeudor' => 'Comercial del Norte',
                'numCompromiso' => 1,
                'totalCompromisos' => 8,
                'montoPendiente' => 2800.00,
                'estado' => 'Pendiente',
                'responsable' => 'Asesor C',
                'cedente' => 'Banco Pichincha'
            ],
        ];
    }

    /**
     * Obtiene los estados disponibles para compromisos
     */
    private function getEstados(): array
    {
        // En producción, podrías cargar desde una tabla de catálogos
        /*
        return Catalogo::where('tipo', 'estado_compromiso')
            ->where('activo', true)
            ->pluck('valor')
            ->toArray();
        */
        
        return [
            'Pendiente',
            'Confirmada',
            'Promesa de Pago',
            'Vencida (1 día)',
            'Vencida (3 días)',
            'Vencida (7+ días)',
            'Pagada',
            'Incumplida'
        ];
    }

    /**
     * Obtiene la lista de responsables (gestores/asesores)
     */
    private function getResponsables(): array
    {
        // En producción, obtener desde la BD
        /*
        return User::where('rol', 'gestor')
            ->where('activo', true)
            ->orderBy('nombre')
            ->pluck('nombre')
            ->toArray();
        */
        
        return [
            'Asesor A',
            'Asesor B',
            'Asesor C',
            'Asesor D',
            'Asesor E'
        ];
    }

    /**
     * Obtiene la lista de cedentes
     */
    private function getCedentes(): array
    {
        // En producción, obtener desde la BD
        /*
        return Cedente::where('activo', true)
            ->orderBy('nombre')
            ->pluck('nombre')
            ->toArray();
        */
        
        return [
            'Banco Guayaquil',
            'Banco Pichincha',
            'Supermercado Tuti'
        ];
    }
}
