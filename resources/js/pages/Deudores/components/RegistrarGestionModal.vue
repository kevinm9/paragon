<script setup lang="ts">
import { ref, computed } from 'vue';
import Dialog from 'primevue/dialog';
import Calendar from 'primevue/calendar';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { FilterMatchMode } from '@primevue/core/api';

// Props
const props = defineProps({
    visible: {
        type: Boolean,
        required: true
    },
    deudor: {
        type: Object,
        default: () => ({})
    },
    gestiones: {
        type: Array,
        default: () => [
            {
                id: 1,
                fechaHora: '2025-10-27T09:30:00',
                demandado: 'si',
                etapa: 'Llamadas',
                detalle: 'Se realizó llamada al deudor. Indica que realizará un pago parcial la próxima semana.',
                etapaExtrajudicial: 'Compromiso de Pago',
                fechaCompromiso: '2025-11-03',
                fechaRecordatorio: '2025-11-01',
                usuario: 'María López'
            },
            {
                id: 2,
                fechaHora: '2025-10-25T14:15:00',
                demandado: 'si',
                etapa: 'WhatsApp',
                detalle: 'Se envió mensaje por WhatsApp con detalle de la deuda.',
                etapaExtrajudicial: 'Contactado',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-28',
                usuario: 'Juan Pérez'
            },
            {
                id: 3,
                fechaHora: '2025-10-23T11:20:00',
                demandado: 'no',
                etapa: 'Visita',
                detalle: 'Se visitó domicilio. No se encontró al deudor.',
                etapaExtrajudicial: 'Inubicable',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-30',
                usuario: 'Carlos Ruiz'
            },
            {
                id: 4,
                fechaHora: '2025-10-20T16:45:00',
                demandado: 'si',
                etapa: 'Email',
                detalle: 'Se envió correo con propuesta de refinanciamiento.',
                etapaExtrajudicial: 'Acepta Refinanciamiento',
                fechaCompromiso: '2025-10-25',
                fechaRecordatorio: '2025-10-24',
                usuario: 'Ana Torres'
            },
            {
                id: 5,
                fechaHora: '2025-10-18T10:00:00',
                demandado: 'si',
                etapa: 'Reunión',
                detalle: 'Reunión con el deudor para discutir opciones de pago.',
                etapaExtrajudicial: 'Contactado',
                fechaCompromiso: '2025-10-25',
                fechaRecordatorio: '2025-10-24',
                usuario: 'Pedro Ramírez'
            },
            {
                id: 6,
                fechaHora: '2025-10-15T13:30:00',
                demandado: 'no',
                etapa: 'Notificación',
                detalle: 'Se dejó notificación en domicilio registrado.',
                etapaExtrajudicial: 'Contacto con Terceros',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-22',
                usuario: 'Laura Mendoza'
            },
            {
                id: 7,
                fechaHora: '2025-10-12T15:20:00',
                demandado: 'si',
                etapa: 'Llamadas',
                detalle: 'Deudor informa que está fuera del país por trabajo.',
                etapaExtrajudicial: 'Fuera del País',
                fechaCompromiso: '2025-12-15',
                fechaRecordatorio: '2025-12-10',
                usuario: 'María López'
            },
            {
                id: 8,
                fechaHora: '2025-10-10T09:15:00',
                demandado: 'si',
                etapa: 'WhatsApp',
                detalle: 'Deudor indica que no reconoce la deuda.',
                etapaExtrajudicial: 'Negativa de Pago',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-17',
                usuario: 'Juan Pérez'
            },
            {
                id: 9,
                fechaHora: '2025-10-05T11:45:00',
                demandado: 'si',
                etapa: 'Email',
                detalle: 'Se envió estado de cuenta actualizado.',
                etapaExtrajudicial: 'Contactado',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-12',
                usuario: 'Carlos Ruiz'
            },
            {
                id: 10,
                fechaHora: '2025-10-01T14:00:00',
                demandado: 'si',
                etapa: 'Reunión',
                detalle: 'Primera reunión de acercamiento con el deudor.',
                etapaExtrajudicial: 'Contactado',
                fechaCompromiso: null,
                fechaRecordatorio: '2025-10-08',
                usuario: 'Ana Torres'
            }
        ]
    }
});

// Emits
const emit = defineEmits(['update:visible', 'guardar', 'eliminar']);

// Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    etapa: { value: null, matchMode: FilterMatchMode.EQUALS },
    etapaExtrajudicial: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const loading = ref(false);
const showDeleteDialog = ref(false);
const gestionToDelete = ref<number | null>(null);
const editingId = ref<number | null>(null);

const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.etapa.value = null;
    filters.value.etapaExtrajudicial.value = null;
};

// Funciones para importación/exportación
const exportarCSV = () => {
    alert('Funcionalidad de exportar CSV - En desarrollo');
};

const descargarPlantilla = () => {
    alert('Funcionalidad de descargar plantilla - En desarrollo');
};

const importarCSV = () => {
    alert('Funcionalidad de importar CSV - En desarrollo');
};

const editarGestion = (gestion: any) => {
    form.value = {
        demandado: gestion.demandado,
        fechaHora: new Date(gestion.fechaHora),
        etapa: gestion.etapa,
        detalle: gestion.detalle,
        etapaExtrajudicial: gestion.etapaExtrajudicial,
        fechaCompromiso: gestion.fechaCompromiso ? new Date(gestion.fechaCompromiso) : new Date(),
        fechaRecordatorio: gestion.fechaRecordatorio ? new Date(gestion.fechaRecordatorio) : new Date()
    };
    editingId.value = gestion.id;
};

const confirmarEliminar = (id: number) => {
    gestionToDelete.value = id;
    showDeleteDialog.value = true;
};

const eliminarGestion = () => {
    if (gestionToDelete.value) {
        // Aquí deberías emitir un evento para eliminar la gestión
        emit('eliminar', gestionToDelete.value);
        showDeleteDialog.value = false;
        gestionToDelete.value = null;
    }
};

// Reactive data
const form = ref({
    demandado: null as string | null,
    fechaHora: new Date(),
    etapa: null as string | null,
    detalle: '',
    etapaExtrajudicial: null as string | null,
    fechaCompromiso: new Date(),
    fechaRecordatorio: new Date()
});

// Opciones para los selects
const opcionesDemandado = ['Si', 'No'];
const opcionesEtapa = ['Llamadas', 'WhatsApp', 'Email', 'Visita', 'Notificación', 'Reunión'];
const opcionesEtapaExtrajudicial = [
    'Contactado',
    'Inubicable',
    'Compromiso de Pago',
    'Negativa de Pago',
    'Contacto con Terceros',
    'Fuera del País',
    'Acepta Refinanciamiento',
    'Deuda Cancelada'
];

// Computed para manejar el v-model del dialog
const dialogVisible = computed({
    get: () => props.visible,
    set: (value: boolean) => emit('update:visible', value)
});

// Métodos
const guardarGestion = () => {
    if (!form.value.demandado || !form.value.etapa || !form.value.etapaExtrajudicial) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }

    const nuevaGestion = {
        fechaHora: form.value.fechaHora.toISOString(),
        demandado: form.value.demandado.toLowerCase(),
        etapa: form.value.etapa,
        detalle: form.value.detalle,
        etapaExtrajudicial: form.value.etapaExtrajudicial,
        fechaCompromiso: form.value.fechaCompromiso.toISOString().split('T')[0],
        fechaRecordatorio: form.value.fechaRecordatorio.toISOString().split('T')[0],
        usuario: 'Usuario Actual' // Aquí deberías obtener el usuario logueado
    };

    emit('guardar', nuevaGestion);
    limpiarFormulario();
    dialogVisible.value = false;
};

const cancelar = () => {
    limpiarFormulario();
    dialogVisible.value = false;
};

const limpiarFormulario = () => {
    form.value = {
        demandado: null,
        fechaHora: new Date(),
        etapa: null,
        detalle: '',
        etapaExtrajudicial: null,
        fechaCompromiso: new Date(),
        fechaRecordatorio: new Date()
    };
};

// Formatear fecha para mostrar
const formatearFecha = (fecha: string) => {
    try {
        return new Date(fecha).toLocaleString('es-EC', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return fecha;
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" maximizable modal header="Gestión Extrajudicial" :style="{ width: '70rem' }">
        <div class="space-y-6">
            <!-- Información del Deudor -->
            <div class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Información del Deudor</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-semibold ml-2">{{ deudor?.nombre || 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Cédula:</span>
                        <span class="font-semibold ml-2">{{ deudor?.cedula || 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                <form @submit.prevent="guardarGestion" class="p-fluid grid gap-6">
                    <!-- Demandado -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="demandado" class="w-full sm:w-1/3 font-semibold">
                            Demandado <span class="text-red-500">*</span>
                        </label>
                        <Select 
                            id="demandado" 
                            v-model="form.demandado" 
                            :options="opcionesDemandado"
                            placeholder="Seleccione" 
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                    <!-- Fecha/Hora -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="fechaHora" class="w-full sm:w-1/3 font-semibold">Fecha/Hora</label>
                        <Calendar 
                            id="fechaHora" 
                            v-model="form.fechaHora" 
                            showTime 
                            :showIcon="true"
                            dateFormat="yy-mm-dd"
                            class="w-full sm:w-2/3"
                            :disabled="true"
                        />
                    </div>

                    <!-- Etapa -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="etapa" class="w-full sm:w-1/3 font-semibold">
                            Etapa <span class="text-red-500">*</span>
                        </label>
                        <Select 
                            id="etapa" 
                            v-model="form.etapa"
                            :options="opcionesEtapa"
                            placeholder="Seleccione etapa" 
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                    <!-- Detalle -->
                    <div class="field flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4">
                        <label for="detalle" class="w-full sm:w-1/3 font-semibold">Detalle</label>
                        <Textarea 
                            id="detalle" 
                            v-model="form.detalle" 
                            rows="3"
                            placeholder="Escriba el detalle de la gestión..."
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                    <!-- Etapa Extrajudicial -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="etapaExtrajudicial" class="w-full sm:w-1/3 font-semibold">
                            Etapa Extrajudicial <span class="text-red-500">*</span>
                        </label>
                        <Select 
                            id="etapaExtrajudicial" 
                            v-model="form.etapaExtrajudicial"
                            :options="opcionesEtapaExtrajudicial"
                            placeholder="Seleccione etapa" 
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                    <!-- Fecha de Compromiso -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="fechaCompromiso" class="w-full sm:w-1/3 font-semibold">Fecha de Compromiso</label>
                        <Calendar 
                            id="fechaCompromiso" 
                            v-model="form.fechaCompromiso" 
                            :showIcon="true" 
                            dateFormat="yy-mm-dd"
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                    <!-- Fecha de Recordatorio -->
                    <div class="field flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <label for="fechaRecordatorio" class="w-full sm:w-1/3 font-semibold">Fecha de Recordatorio</label>
                        <Calendar 
                            id="fechaRecordatorio" 
                            v-model="form.fechaRecordatorio" 
                            :showIcon="true"
                            dateFormat="yy-mm-dd"
                            class="w-full sm:w-2/3" 
                        />
                    </div>

                            <!-- Botones -->
                            <div class="flex justify-end gap-4 mt-6">
                                <Button 
                                    label="Cancelar" 
                                    icon="pi pi-times" 
                                    severity="secondary"
                                    @click="cancelar"
                                    type="button"
                                />
                                <Button 
                                    label="Guardar Gestión" 
                                    icon="pi pi-check" 
                                    type="submit" 
                                />
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de Gestiones -->
                    <div class="card">
                        <DataTable
                            v-model:filters="filters"
                            :value="props.gestiones"
                            paginator
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            dataKey="id"
                            filterDisplay="menu"
                            :loading="loading"
                            :globalFilterFields="['demandado', 'etapa', 'detalle', 'etapaExtrajudicial']"
                            class="gestiones-table"
                            size="small"
                            stripedRows
                            tableStyle="min-width: 100%">
                            <template #header>
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <h3 class="text-lg font-semibold">Historial de Gestiones</h3>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <Button icon="pi pi-filter-slash" label="Limpiar" severity="secondary" 
                                            size="small" @click="clearFilter()" />
                                        <Button icon="pi pi-download" label="Plantilla" severity="info" 
                                            size="small" @click="descargarPlantilla" />
                                        <Button icon="pi pi-upload" label="Importar" severity="help" 
                                            size="small" @click="importarCSV" />
                                        <Button icon="pi pi-file-export" label="Exportar" severity="success" 
                                            size="small" @click="exportarCSV" />
                                        <div class="flex items-center gap-2">
                                            <i class="pi pi-search"></i>
                                            <InputText v-model="filters['global'].value" 
                                                placeholder="Buscar..." class="w-full md:w-auto" />
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <Column field="fechaHora" header="Fecha/Hora" sortable style="min-width: 12rem">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <i class="pi pi-calendar mr-2 text-gray-500"></i>
                                        <span class="text-sm">{{ formatearFecha(data.fechaHora) }}</span>
                                    </div>
                                </template>
                            </Column>

                            <Column field="demandado" header="Demandado" sortable style="min-width: 8rem">
                                <template #body="{ data }">
                                    <span v-if="data.demandado === 'si'" class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-sm font-semibold">
                                        Si
                                    </span>
                                    <span v-else class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm font-semibold">
                                        No
                                    </span>
                                </template>
                                <template #filter="{ filterModel }">
                                    <Select v-model="filterModel.value" :options="opcionesDemandado" 
                                        placeholder="Seleccione" class="p-column-filter" />
                                </template>
                            </Column>

                            <Column field="etapa" header="Etapa" sortable style="min-width: 10rem">
                                <template #body="{ data }">
                                    <span class="font-semibold">{{ data.etapa }}</span>
                                </template>
                                <template #filter="{ filterModel }">
                                    <Select v-model="filterModel.value" :options="opcionesEtapa" 
                                        placeholder="Seleccione" class="p-column-filter" />
                                </template>
                            </Column>

                            <Column field="detalle" header="Detalle" sortable style="min-width: 15rem">
                                <template #body="{ data }">
                                    <span class="text-sm text-gray-700">{{ data.detalle || '-' }}</span>
                                </template>
                            </Column>

                            <Column field="etapaExtrajudicial" header="Etapa Extrajudicial" sortable style="min-width: 12rem">
                                <template #body="{ data }">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">
                                        {{ data.etapaExtrajudicial }}
                                    </span>
                                </template>
                                <template #filter="{ filterModel }">
                                    <Select v-model="filterModel.value" :options="opcionesEtapaExtrajudicial" 
                                        placeholder="Seleccione" class="p-column-filter" />
                                </template>
                            </Column>

                            <Column field="fechaCompromiso" header="Fecha Compromiso" sortable style="min-width: 10rem">
                                <template #body="{ data }">
                                    {{ data.fechaCompromiso ? new Date(data.fechaCompromiso).toLocaleDateString() : '-' }}
                                </template>
                            </Column>

                            <Column field="fechaRecordatorio" header="Fecha Recordatorio" sortable style="min-width: 10rem">
                                <template #body="{ data }">
                                    {{ data.fechaRecordatorio ? new Date(data.fechaRecordatorio).toLocaleDateString() : '-' }}
                                </template>
                            </Column>

                            <Column field="usuario" header="Usuario" sortable style="min-width: 10rem">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <i class="pi pi-user mr-2 text-gray-500"></i>
                                        <span class="text-sm">{{ data.usuario }}</span>
                                    </div>
                                </template>
                            </Column>

                            <Column header="Acciones" style="min-width: 8rem">
                                <template #body="{ data }">
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-pencil" severity="info" 
                                            size="small" text rounded
                                            @click="editarGestion(data)" 
                                            v-tooltip.top="'Editar'" />
                                        <Button icon="pi pi-trash" severity="danger" 
                                            size="small" text rounded
                                            @click="confirmarEliminar(data.id)" 
                                            v-tooltip.top="'Eliminar'" />
                                    </div>
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8">
                                    <i class="pi pi-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No hay gestiones registradas</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
    </Dialog>

    <!-- Dialog de confirmación de eliminación -->
    <Dialog v-model:visible="showDeleteDialog" modal header="Confirmar Eliminación" :style="{ width: '450px' }">
        <div class="flex items-center gap-3">
            <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 2rem"></i>
            <span>¿Está seguro que desea eliminar esta gestión?</span>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" severity="secondary" 
                @click="showDeleteDialog = false" />
            <Button label="Eliminar" icon="pi pi-check" severity="danger" 
                @click="eliminarGestion" />
        </template>
    </Dialog>
</template>

<style scoped>
.p-fluid .field {
    margin-bottom: 0;
}

.p-datatable .p-datatable-thead > tr > th {
    background-color: #f9fafb;
    font-weight: 600;
}

.gestiones-table :deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 1rem 0;
}

@media (max-width: 768px) {
    .gestiones-table :deep(.p-datatable-tbody > tr > td) {
        text-align: left;
    }
}
</style>