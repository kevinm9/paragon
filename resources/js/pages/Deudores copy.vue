<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';
import axios from 'axios';

const page = usePage();
const props = page.props as { deudores: any[] };

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bandeja de Deudores',
        href: "deudores",
    },
];


// Datos de ejemplo para la tabla (reemplazar con datos reales de tu backend)
const deudores = ref([
    { id: 1, nombre: 'López S.A.', deuda: '$60,000', dias: '10 días', ultimoContacto: '12/07/2024', estado: 'Al día' },
    { id: 2, nombre: 'Maria Fernández', deuda: '$80,000', dias: '12 días', ultimoContacto: '10/07/2024', estado: 'Mora Leve' },
    { id: 3, nombre: 'Grupo Torres', deuda: '$50,000', dias: '12 días', ultimoContacto: '08/07/2024', estado: 'En Gestión' },
    { id: 4, nombre: 'Juan Perez', deuda: '$50,000', dias: '13/07/2024', estado: 'En Gestión' },
    { id: 5, nombre: 'Carla Gómez', deuda: '$40,000', dias: '27 días', ultimoContacto: '15/06/2024', estado: 'Mora Alta' },
    { id: 6, nombre: 'Industrias Ruiz', deuda: '$60,000', dias: '20 días', ultimoContacto: '-', estado: 'Sin Contacto' },
]);

// Deudor seleccionado para la ficha de detalle (reemplazar con lógica de selección)
const selectedDeudor = ref({
    nombre: 'Maria Fernández',
    contacto: {
        telefono: '25456789',
        celular: '1 85456788',
        email: 'm.fernandez@mail.com',
        direccion: 'distante@formal.com'
    },
    compromisoPago: {
        monto: '$2,000',
        vencimiento: '20/07/2024',
        estado: 'Incompleto',
        observacion: 'No pudo pagar'
    },
    historialGestion: [
        { tipo: 'Llamada', fecha: '16 Jul' },
        { tipo: 'Email por mora', fecha: '16 Jul' },
        { tipo: 'Promesa de pago', fecha: '10 Jul' },
        { tipo: 'Visita', fecha: '05 Jul' },
    ],
    detalleOperaciones: [
        { documento: 'Factura 001', tipo: 'Factura', monto: '$7,000', vencimiento: '20 días' },
        { documento: 'Factura 002', tipo: 'Factura', monto: '$5,000', vencimiento: '20 días' },
        { documento: 'Cheque 005', tipo: 'Factura', monto: '$8,000', vencimiento: '100 días' },
    ],
    gestion: {
        tipoAccion: 'Llamada',
        tipoContacto: 'Con el titular',
        resultado: 'Efectivo',
        notas: ''
    }
});

const searchDeudores = ref('');

const filteredDeudores = computed(() => {
    if (!searchDeudores.value) {
        return deudores.value;
    }
    return deudores.value.filter(deudor =>
        deudor.nombre.toLowerCase().includes(searchDeudores.value.toLowerCase())
    );
});

// Función para simular la selección de un deudor
function selectDeudor(deudorId: number) {
    // Aquí podrías hacer una llamada a tu API para obtener los detalles completos del deudor
    // Por ahora, usamos el deudor de ejemplo
    const deudorEncontrado = deudores.value.find(d => d.id === deudorId);
    if (deudorEncontrado) {
        // Simular que el deudor seleccionado es Maria Fernández para este ejemplo
        selectedDeudor.value = {
            nombre: deudorEncontrado.nombre, // Mostrar el nombre del deudor seleccionado
            contacto: { /* ... datos de contacto ... */ },
            compromisoPago: { /* ... datos de compromiso ... */ },
            historialGestion: [ /* ... historial ... */ ],
            detalleOperaciones: [ /* ... operaciones ... */ ],
            gestion: { /* ... gestión ... */ }
        };
    }
}
</script>

<template>
    <Head title="Bandeja de Deudores" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 bg-gray-50 min-h-screen">
            <!-- Sección de Listado de Deudores -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold ">Listado de Deudores</h2>
                <h4 class="mb-4">Listado de deudores y estados de sus cuentas</h4>
                <div class="flex justify-between items-center mb-4">
                    <div class="relative">
                        <input
                            type="text"
                            v-model="searchDeudores"
                            placeholder="Buscar..."
                            class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Deudor
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre / Razón Social</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Deuda Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días Mora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Contacto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="deudor in filteredDeudores" :key="deudor.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="form-checkbox" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ deudor.nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ deudor.deuda }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ deudor.dias }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ deudor.ultimoContacto }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span :class="{
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800': deudor.estado === 'Al día',
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800': deudor.estado === 'Mora Leve' || deudor.estado === 'En Gestión',
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800': deudor.estado === 'Mora Alta',
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800': deudor.estado === 'Sin Contacto',
                                    }">
                                        {{ deudor.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button @click="selectDeudor(deudor.id)" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección de Ficha del Deudor -->
            <div v-if="selectedDeudor" class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Ficha del Deudor: {{ selectedDeudor.nombre }}</h2>
                    <span class="text-sm text-gray-500">ID-SOGESTTN</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Datos de Contacto -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Datos de Contacto</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ selectedDeudor.contacto.telefono }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>{{ selectedDeudor.contacto.celular }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>{{ selectedDeudor.contacto.email }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ selectedDeudor.contacto.direccion }}</p>
                        </div>
                    </div>

                    <!-- Compromisos de Pago -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Compromisos de Pago</h3>
                        <p class="text-2xl font-bold text-blue-600 mb-1">{{ selectedDeudor.compromisoPago.monto }}</p>
                        <p class="text-sm text-gray-600">Vencimiento: {{ selectedDeudor.compromisoPago.vencimiento }}</p>
                        <p :class="{
                            'text-red-500 font-semibold text-sm': selectedDeudor.compromisoPago.estado === 'Incompleto',
                            'text-green-600 font-semibold text-sm': selectedDeudor.compromisoPago.estado === 'Completo',
                        }">{{ selectedDeudor.compromisoPago.estado }}</p>
                        <p v-if="selectedDeudor.compromisoPago.observacion" class="text-xs text-gray-500">{{ selectedDeudor.compromisoPago.observacion }}</p>
                    </div>

                    <!-- Historial de Gestión -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Historial de Gestión</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li v-for="(gestion, index) in selectedDeudor.historialGestion" :key="index" class="flex justify-between">
                                <span>{{ gestion.tipo }}</span>
                                <span class="text-gray-500">{{ gestion.fecha }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Detalle de Operaciones -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Detalle de Operaciones</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(op, index) in selectedDeudor.detalleOperaciones" :key="index">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ op.documento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ op.tipo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ op.monto }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ op.vencimiento }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Registrar Gestión -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Registrar Gestión</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="tipoAccion" class="block text-sm font-medium text-gray-700">Tipo de acción</label>
                            <select id="tipoAccion" v-model="selectedDeudor.gestion.tipoAccion" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option>Llamada</option>
                                <option>Email</option>
                                <option>Visita</option>
                                <option>Mensaje</option>
                            </select>
                        </div>
                        <div>
                            <label for="tipoContacto" class="block text-sm font-medium text-gray-700">Tipo de contacto</label>
                            <select id="tipoContacto" v-model="selectedDeudor.gestion.tipoContacto" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option>Con el titular</option>
                                <option>Con tercero</option>
                            </select>
                        </div>
                        <div>
                            <label for="resultado" class="block text-sm font-medium text-gray-700">Resultado</label>
                            <select id="resultado" v-model="selectedDeudor.gestion.resultado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option>Efectivo</option>
                                <option>No Efectivo</option>
                                <option>Pendiente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="notas" class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea id="notas" v-model="selectedDeudor.gestion.notas" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                    <div class="text-right">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">Registrar</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Puedes añadir estilos específicos aquí si no usas un framework CSS como Tailwind CSS */
/* Aunque con Tailwind, la mayoría de los estilos se aplican directamente en las clases */
</style>