<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Dialog from 'primevue/dialog';
import { FilterMatchMode } from '@primevue/core/api';

interface CompromisoAgenda {
    id: number;
    fechaVencimiento: string;
    clienteDeudor: string;
    numCompromiso: number;
    totalCompromisos: number;
    montoPendiente: number;
    estado: string;
    responsable: string;
    cedente?: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Agenda de Pagos',
        href: 'agenda-pagos',
    },
];

// Datos de ejemplo
const compromisos = ref<CompromisoAgenda[]>([
    { id: 1, fechaVencimiento: '2025-10-01', clienteDeudor: 'Juan Pérez', numCompromiso: 1, totalCompromisos: 12, montoPendiente: 350.00, estado: 'Pendiente', responsable: 'Asesor A', cedente: 'Banco Guayaquil' },
    { id: 2, fechaVencimiento: '2025-10-03', clienteDeudor: 'EcoKakao S.A.', numCompromiso: 3, totalCompromisos: 6, montoPendiente: 2100.00, estado: 'Promesa de Pago', responsable: 'Asesor B', cedente: 'Banco Pichincha' },
    { id: 3, fechaVencimiento: '2025-10-05', clienteDeudor: 'Barry Callebaut', numCompromiso: 2, totalCompromisos: 8, montoPendiente: 1750.00, estado: 'Vencida (3 días)', responsable: 'Asesor C', cedente: 'Supermercado Tuti' },
    { id: 4, fechaVencimiento: '2025-10-07', clienteDeudor: 'Cargill Ecuador', numCompromiso: 1, totalCompromisos: 1, montoPendiente: 500.00, estado: 'Pendiente', responsable: 'Asesor A', cedente: 'Banco Guayaquil' },
    { id: 5, fechaVencimiento: '2025-10-08', clienteDeudor: 'María Fernández', numCompromiso: 4, totalCompromisos: 12, montoPendiente: 800.00, estado: 'Confirmada', responsable: 'Asesor D', cedente: 'Banco Pichincha' },
    { id: 6, fechaVencimiento: '2025-10-10', clienteDeudor: 'Grupo Torres', numCompromiso: 2, totalCompromisos: 10, montoPendiente: 1200.00, estado: 'Pendiente', responsable: 'Asesor B', cedente: 'Banco Guayaquil' },
    { id: 7, fechaVencimiento: '2025-10-12', clienteDeudor: 'Ana Gómez', numCompromiso: 1, totalCompromisos: 3, montoPendiente: 450.00, estado: 'Promesa de Pago', responsable: 'Asesor C', cedente: 'Supermercado Tuti' },
    { id: 8, fechaVencimiento: '2025-10-15', clienteDeudor: 'Carlos López', numCompromiso: 5, totalCompromisos: 12, montoPendiente: 950.00, estado: 'Vencida (1 día)', responsable: 'Asesor A', cedente: 'Banco Pichincha' },
    { id: 9, fechaVencimiento: '2025-10-18', clienteDeudor: 'Industrias Ruiz', numCompromiso: 1, totalCompromisos: 6, montoPendiente: 3200.00, estado: 'Pendiente', responsable: 'Asesor E', cedente: 'Banco Guayaquil' },
    { id: 10, fechaVencimiento: '2025-10-20', clienteDeudor: 'Laura Martínez', numCompromiso: 2, totalCompromisos: 4, montoPendiente: 600.00, estado: 'Confirmada', responsable: 'Asesor D', cedente: 'Supermercado Tuti' },
]);

// Estados disponibles
const estadosDisponibles = [
    'Pendiente',
    'Confirmada',
    'Promesa de Pago',
    'Vencida (1 día)',
    'Vencida (3 días)',
    'Vencida (7+ días)',
    'Pagada',
    'Incumplida'
];

const responsables = ['Asesor A', 'Asesor B', 'Asesor C', 'Asesor D', 'Asesor E'];
const cedentes = ['Banco Guayaquil', 'Banco Pichincha', 'Supermercado Tuti'];

// Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    clienteDeudor: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    estado: { value: null, matchMode: FilterMatchMode.EQUALS },
    responsable: { value: null, matchMode: FilterMatchMode.EQUALS },
    cedente: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const loading = ref(false);

// Modal cambiar estado
const showCambiarEstadoDialog = ref(false);
const compromisoSeleccionado = ref<CompromisoAgenda | null>(null);
const nuevoEstado = ref<string | null>(null);
const observacionCambio = ref('');

const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.estado.value = null;
    filters.value.responsable.value = null;
    filters.value.cedente.value = null;
    filters.value.clienteDeudor.value = null;
};

// Formato de moneda
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

// Función para obtener clase del badge según estado
const getEstadoBadge = (estado: string) => {
    if (estado === 'Pendiente') return { class: 'bg-gray-200 text-gray-800', icon: '' };
    if (estado === 'Confirmada') return { class: 'bg-green-200 text-green-800', icon: 'pi-check' };
    if (estado === 'Promesa de Pago') return { class: 'bg-blue-200 text-blue-800', icon: 'pi-calendar' };
    if (estado.includes('Vencida')) return { class: 'bg-red-200 text-red-800', icon: 'pi-exclamation-triangle' };
    if (estado === 'Pagada') return { class: 'bg-emerald-200 text-emerald-800', icon: 'pi-check-circle' };
    if (estado === 'Incumplida') return { class: 'bg-orange-200 text-orange-800', icon: 'pi-times-circle' };
    return { class: 'bg-gray-200 text-gray-800', icon: '' };
};

// Abrir modal cambiar estado
const abrirCambiarEstado = (compromiso: CompromisoAgenda) => {
    compromisoSeleccionado.value = { ...compromiso };
    nuevoEstado.value = compromiso.estado;
    observacionCambio.value = '';
    showCambiarEstadoDialog.value = true;
};

// Guardar cambio de estado
const guardarCambioEstado = () => {
    if (!compromisoSeleccionado.value || !nuevoEstado.value) {
        alert('Debe seleccionar un estado');
        return;
    }

    const index = compromisos.value.findIndex(c => c.id === compromisoSeleccionado.value!.id);
    if (index !== -1) {
        compromisos.value[index].estado = nuevoEstado.value;
        // Aquí iría la llamada al backend para guardar el cambio
        console.log('Cambio de estado:', {
            compromiso: compromisoSeleccionado.value,
            nuevoEstado: nuevoEstado.value,
            observacion: observacionCambio.value
        });
    }

    showCambiarEstadoDialog.value = false;
    compromisoSeleccionado.value = null;
    nuevoEstado.value = null;
    observacionCambio.value = '';
};

// Funciones de acción (pendientes de implementación)
const copiarTabla = () => {
    alert('Funcionalidad de copiar tabla - En desarrollo');
};

const llamarCliente = (compromiso: CompromisoAgenda) => {
    alert(`Llamar a ${compromiso.clienteDeudor} - Funcionalidad en desarrollo`);
};

const notificarCliente = (compromiso: CompromisoAgenda) => {
    alert(`Notificar a ${compromiso.clienteDeudor} - Funcionalidad en desarrollo`);
};

const escalarCompromiso = (compromiso: CompromisoAgenda) => {
    alert(`Escalar compromiso de ${compromiso.clienteDeudor} - Funcionalidad en desarrollo`);
};

// Exportar
const exportarCSV = () => {
    alert('Funcionalidad de exportar CSV - En desarrollo');
};
</script>

<template>
    <Head title="Agenda de Pagos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 bg-gray-50 min-h-screen">
            <!-- Encabezado -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <i class="pi pi-calendar text-3xl text-blue-600"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Agenda de Pagos</h2>
                </div>
                <p class="text-gray-600">Lista ordenada automáticamente por fecha de vencimiento más próxima</p>
            </div>

            <!-- Tabla de Agenda -->
            <div class="card bg-white rounded-lg shadow">
                <DataTable 
                    v-model:filters="filters" 
                    :value="compromisos" 
                    paginator 
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" 
                    dataKey="id" 
                    filterDisplay="row" 
                    :loading="loading"
                    :globalFilterFields="['clienteDeudor', 'estado', 'responsable', 'cedente']" 
                    size="small"
                    stripedRows
                    sortField="fechaVencimiento"
                    :sortOrder="1"
                    class="agenda-table">
                    
                    <template #header>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 p-4">
                            <h3 class="text-lg font-semibold text-gray-800">Compromisos de Pago Próximos</h3>

                            <div class="flex flex-wrap items-center gap-2">
                                <Button 
                                    icon="pi pi-filter-slash" 
                                    label="Limpiar" 
                                    severity="secondary" 
                                    size="small"
                                    @click="clearFilter()" />
                                <Button 
                                    icon="pi pi-download" 
                                    label="Plantilla" 
                                    severity="info" 
                                    size="small"
                                    @click="copiarTabla" />
                                <Button 
                                    icon="pi pi-file-export" 
                                    label="Exportar" 
                                    severity="success" 
                                    size="small"
                                    @click="exportarCSV" />
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-search"></i>
                                    <InputText 
                                        v-model="filters['global'].value" 
                                        placeholder="Buscar..." 
                                        class="w-full md:w-auto" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Fecha Vencimiento -->
                    <Column 
                        field="fechaVencimiento" 
                        header="Fecha Vencimiento" 
                        sortable 
                        style="min-width: 12rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-blue-600"></i>
                                <span class="font-semibold">{{ data.fechaVencimiento }}</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Cliente/Deudor -->
                    <Column 
                        field="clienteDeudor" 
                        header="Cliente / Deudor" 
                        sortable 
                        style="min-width: 14rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText 
                                v-model="filterModel.value" 
                                type="text" 
                                @input="filterCallback()"
                                placeholder="Buscar cliente..." />
                        </template>
                        <template #body="{ data }">
                            <div>
                                <div class="font-semibold text-gray-800">{{ data.clienteDeudor }}</div>
                                <div class="text-xs text-gray-500">{{ data.cedente }}</div>
                            </div>
                        </template>
                    </Column>

                    <!-- Número Compromiso -->
                    <Column 
                        header="Compromiso" 
                        style="min-width: 10rem">
                        <template #body="{ data }">
                            <span class="font-mono font-semibold text-indigo-600">
                                {{ data.numCompromiso }} / {{ data.totalCompromisos }}
                            </span>
                        </template>
                    </Column>

                    <!-- Monto Pendiente -->
                    <Column 
                        field="montoPendiente" 
                        header="Monto Pendiente" 
                        sortable 
                        style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="font-bold text-lg text-emerald-600">
                                {{ formatCurrency(data.montoPendiente) }}
                            </span>
                        </template>
                    </Column>

                    <!-- Estado -->
                    <Column 
                        field="estado" 
                        header="Estado" 
                        sortable 
                        style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <Select 
                                v-model="filterModel.value" 
                                @change="filterCallback()" 
                                :options="estadosDisponibles"
                                placeholder="Filtrar estado" 
                                class="w-full" />
                        </template>
                        <template #body="{ data }">
                            <span :class="`px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 ${getEstadoBadge(data.estado).class}`">
                                <i v-if="getEstadoBadge(data.estado).icon" :class="`pi ${getEstadoBadge(data.estado).icon}`"></i>
                                {{ data.estado }}
                            </span>
                        </template>
                    </Column>

                    <!-- Responsable -->
                    <Column 
                        field="responsable" 
                        header="Responsable" 
                        sortable 
                        style="min-width: 10rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <Select 
                                v-model="filterModel.value" 
                                @change="filterCallback()" 
                                :options="responsables"
                                placeholder="Filtrar" 
                                class="w-full" />
                        </template>
                    </Column>

                    <!-- Acciones Rápidas -->
                    <Column 
                        header="Acción Rápida" 
                        style="min-width: 14rem">
                        <template #body="{ data }">
                            <div class="flex flex-wrap gap-1">
                                <!-- Botón Llamar -->
                                <Button 
                                    icon="pi pi-phone" 
                                    severity="info" 
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Llamar'"
                                    @click="llamarCliente(data)" />
                                
                                <!-- Botón Cambiar Estado (con operatividad) -->
                                <Button 
                                    icon="pi pi-sync" 
                                    severity="warning" 
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Cambiar Estado'"
                                    @click="abrirCambiarEstado(data)" />
                                
                                <!-- Botón Notificar -->
                                <Button 
                                    icon="pi pi-envelope" 
                                    severity="success" 
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Notificar (Email/WhatsApp)'"
                                    @click="notificarCliente(data)" />
                                
                                <!-- Botón Escalar -->
                                <Button 
                                    icon="pi pi-exclamation-triangle" 
                                    severity="danger" 
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Escalar'"
                                    @click="escalarCompromiso(data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Modal Cambiar Estado -->
        <Dialog 
            v-model:visible="showCambiarEstadoDialog" 
            modal 
            header="Cambiar Estado del Compromiso"
            :style="{ width: '500px' }">
            <div v-if="compromisoSeleccionado" class="space-y-4">
                <!-- Información del compromiso -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="font-semibold text-gray-800 mb-2">{{ compromisoSeleccionado.clienteDeudor }}</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Compromiso:</span>
                            <span class="font-semibold ml-2">{{ compromisoSeleccionado.numCompromiso }}/{{ compromisoSeleccionado.totalCompromisos }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Monto:</span>
                            <span class="font-semibold ml-2">{{ formatCurrency(compromisoSeleccionado.montoPendiente) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Vencimiento:</span>
                            <span class="font-semibold ml-2">{{ compromisoSeleccionado.fechaVencimiento }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Estado actual:</span>
                            <span :class="`ml-2 px-2 py-0.5 rounded text-xs font-semibold ${getEstadoBadge(compromisoSeleccionado.estado).class}`">
                                {{ compromisoSeleccionado.estado }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Selector de nuevo estado -->
                <div class="field">
                    <label for="nuevoEstado" class="block mb-2 font-semibold text-sm">
                        Nuevo Estado <span class="text-red-500">*</span>
                    </label>
                    <Select 
                        id="nuevoEstado" 
                        v-model="nuevoEstado" 
                        :options="estadosDisponibles"
                        placeholder="Seleccione el nuevo estado" 
                        class="w-full" />
                </div>

                <!-- Observación -->
                <div class="field">
                    <label for="observacion" class="block mb-2 font-semibold text-sm">
                        Observación
                    </label>
                    <Textarea 
                        id="observacion" 
                        v-model="observacionCambio" 
                        rows="3"
                        placeholder="Ingrese observaciones sobre el cambio de estado..."
                        class="w-full" />
                </div>
            </div>

            <template #footer>
                <Button 
                    label="Cancelar" 
                    icon="pi pi-times" 
                    severity="secondary" 
                    @click="showCambiarEstadoDialog = false" />
                <Button 
                    label="Guardar Cambio" 
                    icon="pi pi-check" 
                    severity="success"
                    @click="guardarCambioEstado" />
            </template>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.agenda-table :deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 0;
}

.agenda-table :deep(.p-datatable-thead > tr > th) {
    background-color: #F9FAFB;
    font-weight: 600;
}

@media (max-width: 768px) {
    .agenda-table :deep(.p-datatable-tbody > tr > td) {
        text-align: left;
    }
}
</style>