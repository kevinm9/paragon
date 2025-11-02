
<script setup lang="ts">
// Vue & Inertia
import { ref, computed, nextTick } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

// Layouts & Types
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// PrimeVue Components
import { Tabs, TabList, Tab, TabPanels, TabPanel, Calendar, InputText, Textarea, Select, Dialog, DataTable, Column, Toolbar } from 'primevue';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

// Custom Components
import NuevoDeudorModal from '@/pages/Deudores/components/NuevoDeudorModal.vue';
import ContactosDeudorModal from '@/pages/Deudores/components/ContactosDeudorModal.vue';
import CompromisosPagoModal from '@/pages/Deudores/components/CompromisosPagoModal.vue';
import ProcesoJudicialModal from '@/pages/Deudores/components/ProcesoJudicialModal.vue';
import RegistrarGestionModal from '@/pages/Deudores/components/RegistrarGestionModal.vue';


const showDeleteDialog = ref(false);
const bandejaDeudores = [
    { gestor: 'María González', cliente: 'Banco Guayaquil', deudor: 'Juan Pérez', garante: 'Pedro Sánchez', cedula_deudor: '1234567890', cuantia_total: 15000, estado_judicial: 'Presentación Demanda', gestion_extrajudicial: 'Contactado', demandado: true, ultima_gestion: '2025-09-01 Llamada' },
    { gestor: 'Carlos Ramírez', cliente: 'Supermercado Tuti', deudor: 'Empresa XYZ S.A.', garante: 'Ana Torres', cedula_deudor: '0987654321', cuantia_total: 25000, estado_judicial: 'En Citación', gestion_extrajudicial: 'Inubicable', demandado: false, ultima_gestion: '2025-09-03 Email' },
    { gestor: 'Laura Mendoza', cliente: 'Banco Pichincha', deudor: 'Ana Gómez', garante: 'Luis Vargas', cedula_deudor: '1122334455', cuantia_total: 18000, estado_judicial: 'Audiencia', gestion_extrajudicial: 'Compromiso de Pago', demandado: true, ultima_gestion: '2025-09-02 Llamada' },
    { gestor: 'Roberto Silva', cliente: 'Banco Guayaquil', deudor: 'Carlos López', garante: 'María Ruiz', cedula_deudor: '5566778899', cuantia_total: 32000, estado_judicial: 'Sentencia', gestion_extrajudicial: 'Negativa de Pago', demandado: true, ultima_gestion: '2025-09-04 Visita' },
    { gestor: 'Patricia Ortiz', cliente: 'Banco Pichincha', deudor: 'Roberto Díaz', garante: 'Carmen Flores', cedula_deudor: '9988776655', cuantia_total: 12000, estado_judicial: 'Embargo', gestion_extrajudicial: 'Contacto con Terceros', demandado: false, ultima_gestion: '2025-09-05 Llamada' },
    { gestor: 'Diego Castro', cliente: 'Supermercado Tuti', deudor: 'Sofía Martínez', garante: 'Jorge Herrera', cedula_deudor: '4455667788', cuantia_total: 8500, estado_judicial: 'Calificación', gestion_extrajudicial: 'Fuera del País', demandado: true, ultima_gestion: '2025-09-06 Email' },
    { gestor: 'Andrea Vega', cliente: 'Banco Guayaquil', deudor: 'Fernando Rojas', garante: 'Isabel Moreno', cedula_deudor: '7788990011', cuantia_total: 45000, estado_judicial: 'Remate', gestion_extrajudicial: 'Acepta Refinanciamiento', demandado: true, ultima_gestion: '2025-09-07 Llamada' },
    { gestor: 'Miguel Torres', cliente: 'Banco Pichincha', deudor: 'Valeria Campos', garante: 'Ricardo Peña', cedula_deudor: '3344556677', cuantia_total: 6500, estado_judicial: 'Juicio Concluido', gestion_extrajudicial: 'Deuda Cancelada', demandado: false, ultima_gestion: '2025-09-08 Llamada' }
];

const cedentes = ['Banco Guayaquil', 'Supermercado Tuti', 'Banco Pichincha'];

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    deudor: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    cliente: { value: null, matchMode: FilterMatchMode.EQUALS },
    cedula_deudor: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const loading = ref(false);
const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.cliente.value = null;
    filters.value.deudor.value = null;
};




const visible = ref(false);
const showContactDialog = ref(false);
const showPagos = ref(false);

const visibleProcesoJudicial = ref(false);
// 🔹 Solo variable necesaria: etapas
const etapas = ref([
    { nombre: "1.- Demanda", fecha: "2025-09-02", responsable: "Abg. López", estado: "completado", observations: "Demanda ingresada" },
    { nombre: "2.- Notificación", fecha: "2025-09-05", responsable: "Abg. López", estado: "en curso", observations: "En trámite" }
]);
const fichaDeudorRef = ref<HTMLElement | null>(null);

const page = usePage();
const props = page.props as { deudores: any[] };

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bandeja de Deudores',
        href: "deudores",
    },
];


const showGestionDialog = ref(false);

// Modelo de nueva gestión
const nuevaGestion = ref({
    fecha: "",
    accion: "",
    detalle: ""
});

const form = ref({
    judicial: null as boolean | null,
    fechaHora: new Date(),
    tipo: null as string | null,
    detalle: '',
    diagnostico: null as string | null,
    proximaGestion: new Date(),
    fechaAgenda: new Date()
});


interface Gestion {
    fechaHora: string; // o Date
    judicial: string;
    tipo: string;
    detalle: string;
    diagnostico: string;
    proximaGestion: string;
    fechaAgenda: string;
    usuario: string;
}

// Datos de ejemplo
const gestiones = ref<Gestion[]>([
    { fechaHora: new Date().toISOString(), judicial: 'si', tipo: 'Cobranza', detalle: 'Detalle 1', diagnostico: 'Contactado', proximaGestion: 'Llamada', fechaAgenda: '2025-09-28', usuario: 'Kevin Mosquera' },
    { fechaHora: new Date().toISOString(), judicial: 'no', tipo: 'Seguimiento', detalle: 'Detalle 2', diagnostico: 'No Contactado', proximaGestion: 'Correo', fechaAgenda: '2025-09-29', usuario: 'Admin' },
    { fechaHora: new Date().toISOString(), judicial: 'si', tipo: 'Visita', detalle: 'Detalle 3', diagnostico: 'Promesa de Pago', proximaGestion: 'Llamada', fechaAgenda: '2025-09-30', usuario: 'Usuario 1' },
    { fechaHora: new Date().toISOString(), judicial: 'no', tipo: 'Email', detalle: 'Detalle 4', diagnostico: 'Rechazo', proximaGestion: 'Email', fechaAgenda: '2025-10-01', usuario: 'Usuario 2' },
    { fechaHora: new Date().toISOString(), judicial: 'si', tipo: 'Cobranza', detalle: 'Detalle 5', diagnostico: 'Contactado', proximaGestion: 'Visita', fechaAgenda: '2025-10-02', usuario: 'Usuario 3' },
    { fechaHora: new Date().toISOString(), judicial: 'no', tipo: 'Seguimiento', detalle: 'Detalle 6', diagnostico: 'No Contactado', proximaGestion: 'Llamada', fechaAgenda: '2025-10-03', usuario: 'Usuario 4' },
    { fechaHora: new Date().toISOString(), judicial: 'si', tipo: 'Otro', detalle: 'Detalle 7', diagnostico: 'Promesa de Pago', proximaGestion: 'Correo', fechaAgenda: '2025-10-04', usuario: 'Usuario 5' }
]);


// Guardar nueva gestión
function guardarGestion() {
    if (!nuevaGestion.value.fecha || !nuevaGestion.value.accion) return;

    gestiones.value.push({ ...nuevaGestion.value });
    nuevaGestion.value = { fecha: "", accion: "", detalle: "" }; // limpiar formulario
    showGestionDialog.value = false;
}




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
    cedente: 'Banco Guayaquil',
    cedula: '1234567890',
    contacto: {
        telefono: '25456789',
        celular: '1 85456788',
        email: 'm.fernandez@mail.com',
        direccion: 'Av. Amazonas N34-567 y Av. Colón',
    },
    compromisoPago: {
        valorPago: '$2,000',
        tipoAcuerdo: 'Abono',
        fechaPago: '20/07/2024',
        estado: 'Incompleto',
        situacion: 'En Mora'
    },
    gestionJudicial: {
        numeroJuicio: '09332-2024-00123',
        unidadJudicial: 'Unidad Judicial Civil Norte de Quito',
        juez: 'Dr. Carlos Mendoza',
        etapaProcesal: 'Notificación',
        tipoProcedimiento: 'Juicio Coactivo'
    },
    historialGestion: [
        { tipo: 'Llamada', fecha: '16 Jul' },
        { tipo: 'Email por mora', fecha: '16 Jul' },
        { tipo: 'Promesa de pago', fecha: '10 Jul' },
        { tipo: 'Visita', fecha: '05 Jul' },
    ],
    // ... resto de propiedades
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
            historialGestion: [ /* ... historial ... */],
            detalleOperaciones: [ /* ... operaciones ... */],
            gestion: { /* ... gestión ... */ }
        };
        // Hacer scroll al formulario
        nextTick(() => {
            fichaDeudorRef.value?.scrollIntoView({ behavior: 'smooth' });
        });
    }

}
</script>

<template>

    <Head title="Bandeja de Deudores" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 bg-gray-50 min-h-screen">
            <!-- Sección de Listado de Deudores -->

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold ">Listado de deudores de instituciones públicas</h2>
                <h4 class="mb-4">Listado de deudores y estados de sus cuentas</h4>
                <div class="flex justify-between items-center mb-4">
                    <button @click="visible = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Deudor
                    </button>


                </div>

                <div class="card">
                    <DataTable v-model:filters="filters" :value="bandejaDeudores" paginator :rows="10"
                        :rowsPerPageOptions="[10, 20, 50]" dataKey="cedula_deudor" removableSort filterDisplay="row"
                        :loading="loading" :globalFilterFields="['deudor', 'cliente', 'cedula_deudor', 'gestor', 'garante']" size="small">

                        <template #header>
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <h3 class="text-lg font-semibold">Listado de deudores</h3>

                                <div class="flex flex-wrap items-center gap-2">
                                    <Button icon="pi pi-filter-slash" label="Limpiar" severity="secondary" size="small"
                                        @click="clearFilter()" />
                                    <Button icon="pi pi-download" label="Plantilla" severity="info" size="small"
                                        @click="descargarPlantilla" />
                                    <Button icon="pi pi-upload" label="Importar" severity="help" size="small"
                                        @click="importarCSV" />
                                    <Button icon="pi pi-file-export" label="Exportar" severity="success" size="small"
                                        @click="exportarCSV" />
                                    <div class="flex items-center gap-2">
                                        <i class="pi pi-search"></i>
                                        <InputText v-model="filters['global'].value" placeholder="Buscar..."
                                            class="w-full md:w-auto" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <Column sortable field="gestor" header="Gestor" style="min-width: 10rem"></Column>
                        
                        <Column field="cliente" header="Cliente" style="min-width: 14rem">
                            <template #filter="{ filterModel, filterCallback }">
                                <Select v-model="filterModel.value" @change="filterCallback()" :options="cedentes"
                                    placeholder="Seleccione Cliente" />
                            </template>
                        </Column>

                        <Column field="deudor" header="Deudor" style="min-width: 14rem">
                            <template #filter="{ filterModel, filterCallback }">
                                <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                    placeholder="Buscar por deudor..." />
                            </template>
                        </Column>

                        <Column sortable field="garante" header="Garante" style="min-width: 12rem"></Column>
                        <Column sortable field="cedula_deudor" header="Cédula Deudor" style="min-width: 10rem"></Column>

                        <Column field="cuantia_total" header="Cuantía Total" style="min-width: 12rem">
                            <template #body="{ data }">${{ data.cuantia_total.toLocaleString() }}</template>
                        </Column>

                        <!-- Estado Judicial -->
                        <Column header="Estado Judicial" style="min-width: 14rem">
                            <template #body="slotProps">
                                <span v-if="slotProps.data.estado_judicial === 'Presentación Demanda'"
                                    class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-sm">Presentación Demanda</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Completar Demanda'"
                                    class="bg-cyan-200 text-cyan-800 px-2 py-1 rounded-full text-sm">Completar Demanda</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Calificación'"
                                    class="bg-teal-200 text-teal-800 px-2 py-1 rounded-full text-sm">Calificación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'En Citación'"
                                    class="bg-indigo-200 text-indigo-800 px-2 py-1 rounded-full text-sm">En Citación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Citación Realizada'"
                                    class="bg-violet-200 text-violet-800 px-2 py-1 rounded-full text-sm">Citación Realizada</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Audiencia'"
                                    class="bg-purple-200 text-purple-800 px-2 py-1 rounded-full text-sm">Audiencia</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Sentencia'"
                                    class="bg-fuchsia-200 text-fuchsia-800 px-2 py-1 rounded-full text-sm">Sentencia</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Apelación'"
                                    class="bg-pink-200 text-pink-800 px-2 py-1 rounded-full text-sm">Apelación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Casación'"
                                    class="bg-rose-200 text-rose-800 px-2 py-1 rounded-full text-sm">Casación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Sentencia Ejecutoriada'"
                                    class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Sentencia Ejecutoriada</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Liquidación'"
                                    class="bg-orange-200 text-orange-800 px-2 py-1 rounded-full text-sm">Liquidación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Mandamiento de Ejecución'"
                                    class="bg-amber-200 text-amber-800 px-2 py-1 rounded-full text-sm">Mandamiento de Ejecución</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Archivo de la Causa'"
                                    class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm">Archivo de la Causa</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Embargo'"
                                    class="bg-lime-200 text-lime-800 px-2 py-1 rounded-full text-sm">Embargo</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Avaluo'"
                                    class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Avaluo</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Remate'"
                                    class="bg-emerald-200 text-emerald-800 px-2 py-1 rounded-full text-sm">Remate</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Calificación de Postura'"
                                    class="bg-teal-300 text-teal-900 px-2 py-1 rounded-full text-sm">Calificación de Postura</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Adjudicación'"
                                    class="bg-sky-200 text-sky-800 px-2 py-1 rounded-full text-sm">Adjudicación</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Consiganación de valores'"
                                    class="bg-blue-300 text-blue-900 px-2 py-1 rounded-full text-sm">Consignación de valores</span>
                                
                                <span v-else-if="slotProps.data.estado_judicial === 'Juicio Concluido'"
                                    class="bg-slate-200 text-slate-800 px-2 py-1 rounded-full text-sm">Juicio Concluido</span>
                            </template>
                        </Column>

                        <!-- Gestión Extrajudicial -->
                        <Column header="Gestión Extrajudicial" style="min-width: 14rem">
                            <template #body="slotProps">
                                <span v-if="slotProps.data.gestion_extrajudicial === 'Contactado'"
                                    class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Contactado</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Inubicable'"
                                    class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Inubicable</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Compromiso de Pago'"
                                    class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-sm">Compromiso de Pago</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Negativa de Pago'"
                                    class="bg-purple-200 text-purple-800 px-2 py-1 rounded-full text-sm">Negativa de Pago</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Contacto con Terceros'"
                                    class="bg-orange-200 text-orange-800 px-2 py-1 rounded-full text-sm">Contacto con Terceros</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Fuera del País'"
                                    class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Fuera del País</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Acepta Refinanciamiento'"
                                    class="bg-cyan-200 text-cyan-800 px-2 py-1 rounded-full text-sm">Acepta Refinanciamiento</span>

                                <span v-else-if="slotProps.data.gestion_extrajudicial === 'Deuda Cancelada'"
                                    class="bg-emerald-200 text-emerald-800 px-2 py-1 rounded-full text-sm">Deuda Cancelada</span>
                            </template>
                        </Column>

                        <Column field="demandado" header="Demandado" style="min-width: 8rem">
                            <template #body="{ data }">
                                <span v-if="data.demandado" class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-sm font-semibold">Sí</span>
                                <span v-else class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm font-semibold">No</span>
                            </template>
                        </Column>

                        <Column field="ultima_gestion" header="Última Gestión" style="min-width: 12rem"></Column>
                        <Column header="Acciones"
                            headerClass="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                            style="width: 6rem">
                            <template #body="slotProps">
                                <button @click="selectDeudor(slotProps.data.cedula_deudor)"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    <i class="pi pi-eye"></i>
                                </button>
                                <button @click="visible = true" class="ml-2 text-green-600 hover:text-green-900">
                                    <i class="pi pi-pencil"></i>
                                </button>
                                <button @click="showDeleteDialog = true" class="ml-2 text-red-600 hover:text-red-900">
                                    <i class="pi pi-trash"></i>
                                </button>
                            </template>
                        </Column>

                    </DataTable>
                </div>





            </div>


            <!-- Sección de Ficha del Deudor -->
            <div ref="fichaDeudorRef" v-if="selectedDeudor" class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Ficha del Deudor: {{ selectedDeudor.nombre }} - {{
                        selectedDeudor.cedente }} -
                        {{ selectedDeudor.cedula }} </h2>
                    <span class="text-sm text-gray-500">ID-SOGESTTN</span>
                </div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Datos de Contacto -->
    <div>
        <h3 class="font-semibold text-gray-700 mb-2">Datos de Contacto</h3>
        <div class="space-y-1 text-sm text-gray-600">
            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>{{ selectedDeudor.contacto.telefono }}</p>
            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>{{ selectedDeudor.contacto.celular }}</p>
            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>{{ selectedDeudor.contacto.email }}</p>
            <p class="flex items-center"><svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>{{ selectedDeudor.contacto.direccion }}</p>
        </div>
        <div class="mt-4">
            <Button label="Ver Datos de Contacto" icon="pi pi-address-book"
                @click="showContactDialog = true" />
        </div>
    </div>

    <!-- Compromisos de Pago -->
    <div>
        <h3 class="font-semibold text-gray-700 mb-2">Compromisos de Pago</h3>
        <div class="space-y-2 text-sm">
            <div>
                <p class="text-gray-600">Valor del Pago</p>
                <p class="text-2xl font-bold text-blue-600">{{ selectedDeudor.compromisoPago.valorPago }}</p>
            </div>
            <div>
                <p class="text-gray-600">Tipo de Acuerdo</p>
                <p class="font-semibold text-gray-800">{{ selectedDeudor.compromisoPago.tipoAcuerdo }}</p>
            </div>
            <div>
                <p class="text-gray-600">Fecha de Pago</p>
                <p class="text-gray-800">{{ selectedDeudor.compromisoPago.fechaPago }}</p>
            </div>
            <div>
                <p class="text-gray-600">Estado</p>
                <p :class="{
                    'text-red-500 font-semibold': selectedDeudor.compromisoPago.estado === 'Incompleto',
                    'text-green-600 font-semibold': selectedDeudor.compromisoPago.estado === 'Completo',
                }">{{ selectedDeudor.compromisoPago.estado }}</p>
            </div>
            <div>
                <p class="text-gray-600">Situación</p>
                <span :class="{
                    'bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold': selectedDeudor.compromisoPago.situacion === 'En Mora',
                    'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold': selectedDeudor.compromisoPago.situacion === 'Al día',
                }">{{ selectedDeudor.compromisoPago.situacion }}</span>
            </div>
        </div>
        <div class="mt-4">
            <Button label="Ver Compromisos de Pago" icon="pi pi-money-bill"
                @click="showPagos = true" />
        </div>
    </div>

    <!-- Gestión Judicial -->
    <div class="p-4 border rounded-lg shadow-md bg-gray-50">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-lg">Gestión Judicial</h3>
            <Button label="Gestionar" icon="pi pi-gavel" size="small"
                @click="visibleProcesoJudicial = true" />
        </div>

        <div class="space-y-2 text-sm">
            <div>
                <p class="text-gray-800 font-semibold">Número de Juicio:</p>
                <p class="text-gray-600">{{ selectedDeudor.gestionJudicial.numeroJuicio }}</p>
            </div>
            <div>
                <p class="text-gray-800 font-semibold">Unidad Judicial:</p>
                <p class="text-gray-600">{{ selectedDeudor.gestionJudicial.unidadJudicial }}</p>
            </div>
            <div>
                <p class="text-gray-800 font-semibold">Juez:</p>
                <p class="text-gray-600">{{ selectedDeudor.gestionJudicial.juez }}</p>
            </div>
            <div>
                <p class="text-gray-800 font-semibold">Etapa Procesal:</p>
                <p class="text-gray-600">{{ selectedDeudor.gestionJudicial.etapaProcesal }}</p>
            </div>
            <div>
                <p class="text-gray-800 font-medium">Tipo de Procedimiento:</p>
                <p class="text-gray-600">{{ selectedDeudor.gestionJudicial.tipoProcedimiento }}</p>
            </div>
        </div>
    </div>

    <!-- Historial de Gestión -->
    <div>
        <h3 class="font-semibold text-gray-700 mb-2">Historial de Gestión</h3>
        <ul class="space-y-1 text-sm text-gray-600">
            <li v-for="(gestion, index) in selectedDeudor.historialGestion" :key="index"
                class="flex justify-between">
                <span>{{ gestion.tipo }}</span>
                <span class="text-gray-500">{{ gestion.fecha }}</span>
            </li>
        </ul>
    </div>
</div>


                <!-- Registrar Gestión -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Registrar Gestión</h3>
                    <Button class="mb-4" label="Registrar Gestión" icon="pi pi-plus"
                        @click="showGestionDialog = true" />



                    <!-- Detalle de Operaciones -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Detalle de Gestion</h3>

                        <!-- Botón para abrir el modal -->


                        <div class="overflow-x-auto">
                            <DataTable :value="gestiones.slice(0, 5)" tableStyle="min-width:100%">
                                <Column field="fechaHora" header="Fecha/Hora" />
                                <Column field="judicial" header="Judicial" />
                                <Column field="tipo" header="Tipo" />
                                <Column field="detalle" header="Detalle" />
                                <Column field="diagnostico" header="Diagnóstico" />
                                <Column field="proximaGestion" header="Próxima Gestión" />
                                <Column field="fechaAgenda" header="Fecha Agenda Gestión" />
                                <Column field="usuario" header="Usuario" />
                            </DataTable>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Fin de la Sección de Ficha del Deudor -->
            <!-- Componente Modal Nuevo Deudor -->
            <NuevoDeudorModal v-model:visible="visible" :cedentes="cedentes" @guardar="handleGuardarDeudor" />

            <!-- Modal agenda de contactos del deudor -->
            <ContactosDeudorModal v-model:visible="showContactDialog" :deudor="selectedDeudor" />

            <!-- Modal Compromisos de Pago -->
            <CompromisosPagoModal v-model:visible="showPagos" :deudor="selectedDeudor" />


            <!-- Modal con Tabs -->
            <RegistrarGestionModal v-model:visible="showGestionDialog" :deudor="selectedDeudor" />


            <ProcesoJudicialModal v-model:visible="visibleProcesoJudicial" :deudor="selectedDeudor" />


            <!-- Dialog de confirmación de eliminación de deudores-->
            <Dialog v-model:visible="showDeleteDialog" modal header="Confirmar Eliminación" :style="{ width: '450px' }">
                <div class="flex items-center gap-3">
                    <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 2rem"></i>
                    <span>¿Está seguro que desea eliminar este deudor?</span>
                </div>
                <template #footer>
                    <Button label="Cancelar" icon="pi pi-times" severity="secondary"
                        @click="showDeleteDialog = false" />
                    <Button label="Eliminar" icon="pi pi-check" severity="danger" @click="showDeleteDialog = false" />
                </template>
            </Dialog>


        </div>
    </AppLayout>
</template>

<style>
.p-datatable thead th {
    background-color: #F9FAFB;
    /* gray-50 */
}
</style>