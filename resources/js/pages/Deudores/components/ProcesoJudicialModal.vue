<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from '@primevue/core/api';

interface EtapaJudicial {
    id: number;
    etapaJudicial: string;
    fecha: string;
    observacion: string;
    demandado?: string;
    fechaGestion: Date;
    creadoPor: string;
    fechaCreacion: string;
}

interface ProcesoJudicial {
    numeroJuicio: string;
    unidadJudicial: string;
    juez: string;
    fechaPresentacion: Date;
    tipoProcedimiento: string;
    capital: number;
    cuantia: number;
    codeudorGarante: string;
    cedula: string;
    garantia: string;
}

interface Deudor {
    nombre: string;
    cedula: string;
    cedente: string;
    [key: string]: any;
}

const props = defineProps<{
    visible: boolean;
    deudor: Deudor;
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean];
}>();

// Opciones para los selects
// Opciones reactivas para los selects
const tiposProcedimiento = ref([
    { label: 'Ejecutivo', value: 'Ejecutivo' },
    { label: 'Ordinario', value: 'Ordinario' },
    { label: 'Monitorio', value: 'Monitorio' },
    { label: 'Sumario', value: 'Sumario' },
    { label: 'Ejecución', value: 'Ejecución' },
    { label: 'Concurso de Acreedores', value: 'Concurso de Acreedores' }
]);

const etapasJudiciales = ref([
    { label: 'Presentación Demanda', value: 'Presentación Demanda' },
    { label: 'Completar Demanda', value: 'Completar Demanda' },
    { label: 'Calificación', value: 'Calificación' },
    { label: 'En Citación', value: 'En Citación' },
    { label: 'Citación Realizada', value: 'Citación Realizada' },
    { label: 'Audiencia', value: 'Audiencia' },
    { label: 'Sentencia', value: 'Sentencia' },
    { label: 'Apelación', value: 'Apelación' },
    { label: 'Casación', value: 'Casación' },
    { label: 'Sentencia Ejecutoriada', value: 'Sentencia Ejecutoriada' },
    { label: 'Liquidación', value: 'Liquidación' },
    { label: 'Mandamiento de Ejecución', value: 'Mandamiento de Ejecución' },
    { label: 'Archivo de la Causa', value: 'Archivo de la Causa' },
    { label: 'Embargo', value: 'Embargo' },
    { label: 'Avaluo', value: 'Avaluo' },
    { label: 'Remate', value: 'Remate' },
    { label: 'Calificación de Postura', value: 'Calificación de Postura' },
    { label: 'Adjudicación', value: 'Adjudicación' },
    { label: 'Consignación de valores', value: 'Consignación de valores' },
    { label: 'Juicio Concluido', value: 'Juicio Concluido' }
]);

// Datos de ejemplo del proceso judicial
const procesoJudicial = ref<ProcesoJudicial>({
    numeroJuicio: '09332-2024-00123',
    unidadJudicial: 'Unidad Judicial Civil Norte de Quito',
    juez: 'Dr. Carlos Mendoza',
    fechaPresentacion: new Date('2024-09-15'),
    tipoProcedimiento: 'Ejecutivo',
    capital: 50000,
    cuantia: 65000,
    codeudorGarante: 'Pedro Sánchez',
    cedula: '0987654321',
    garantia: 'Hipoteca sobre bien inmueble'
});

// Datos de ejemplo de etapas judiciales
const etapas = ref<EtapaJudicial[]>([
    { id: 1, etapaJudicial: 'Presentación Demanda', fecha: '2024-09-15', observacion: 'Demanda presentada exitosamente', demandado: 'Si', fechaGestion: new Date('2024-09-15'), creadoPor: 'Admin', fechaCreacion: '2024-09-15' },
    { id: 2, etapaJudicial: 'Calificación', fecha: '2024-09-20', observacion: 'Demanda calificada y admitida', demandado: 'No', fechaGestion: new Date('2024-09-20'), creadoPor: 'Abg. López', fechaCreacion: '2024-09-20' },
    { id: 3, etapaJudicial: 'En Citación', fecha: '2024-09-25', observacion: 'Proceso de citación iniciado', demandado: 'Si', fechaGestion: new Date('2024-09-25'), creadoPor: 'Abg. López', fechaCreacion: '2024-09-25' },
]);

// Opciones para demandado
const opcionesDemandado = ref([
    { label: 'Si', value: 'Si' },
    { label: 'No', value: 'No' }
]);

// Última etapa judicial vigente
const ultimaEtapaVigente = computed(() => {
    if (etapas.value.length === 0) return null;
    return [...etapas.value].sort((a, b) => new Date(b.fecha).getTime() - new Date(a.fecha).getTime())[0];
});

// Formulario
const form = ref<{
    etapaJudicial: string | '';
    fecha: Date | null;
    observacion: string;
    demandado: string | '';
    fechaGestion: Date;
}>({
    etapaJudicial: '',
    fecha: null,
    observacion: '',
    demandado: '',
    fechaGestion: new Date()
});

// Formulario de información general
const formGeneral = ref<ProcesoJudicial>({ ...procesoJudicial.value });
const editingGeneral = ref(false);

const editingId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const etapaToDelete = ref<number | null>(null);
const formularioRef = ref<HTMLElement | null>(null);

// Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const loading = ref(false);

// Funciones CRUD
const resetForm = () => {
    form.value = {
        etapaJudicial: '',
        fecha: null,
        observacion: '',
        demandado: '',
        fechaGestion: new Date()
    };
    editingId.value = null;
};

const validarFormulario = (): boolean => {
    if (!form.value.etapaJudicial || !form.value.fecha || !form.value.demandado) {
        alert('Por favor complete todos los campos obligatorios (Etapa Judicial, Fecha y Demandado)');
        return false;
    }
    return true;
};

const guardarEtapa = () => {
    if (!validarFormulario()) return;

    const fechaStr = form.value.fecha ? form.value.fecha.toISOString().split('T')[0] : '';

    if (editingId.value) {
        // Editar
        const index = etapas.value.findIndex(e => e.id === editingId.value);
        if (index !== -1) {
            etapas.value[index] = {
                ...etapas.value[index],
                etapaJudicial: form.value.etapaJudicial,
                fecha: fechaStr,
                observacion: form.value.observacion || '',
                demandado: form.value.demandado,
                fechaGestion: form.value.fechaGestion
            };
        }
    } else {
        // Crear
        const nuevaEtapa: EtapaJudicial = {
            id: Math.max(...etapas.value.map(e => e.id), 0) + 1,
            etapaJudicial: form.value.etapaJudicial,
            fecha: fechaStr,
            observacion: form.value.observacion || '',
            demandado: form.value.demandado,
            fechaGestion: form.value.fechaGestion,
            creadoPor: 'Usuario Actual',
            fechaCreacion: new Date().toISOString().split('T')[0]
        };
        etapas.value.unshift(nuevaEtapa);
    }

    resetForm();
};

const editarEtapa = (etapa: EtapaJudicial) => {
    form.value = {
        etapaJudicial: etapa.etapaJudicial,
        fecha: new Date(etapa.fecha),
        observacion: etapa.observacion,
        demandado: etapa.demandado || '',
        fechaGestion: etapa.fechaGestion
    };
    editingId.value = etapa.id;

    nextTick(() => {
        formularioRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
};

const confirmarEliminar = (id: number) => {
    etapaToDelete.value = id;
    showDeleteDialog.value = true;
};

const eliminarEtapa = () => {
    if (etapaToDelete.value) {
        etapas.value = etapas.value.filter(e => e.id !== etapaToDelete.value);
        showDeleteDialog.value = false;
        etapaToDelete.value = null;
    }
};

const editarInformacionGeneral = () => {
    editingGeneral.value = true;
    formGeneral.value = { ...procesoJudicial.value };
};

const guardarInformacionGeneral = () => {
    procesoJudicial.value = { ...formGeneral.value };
    editingGeneral.value = false;
};

const cancelarEdicionGeneral = () => {
    formGeneral.value = { ...procesoJudicial.value };
    editingGeneral.value = false;
};

const clearFilter = () => {
    filters.value.global.value = null;
};

// Formato de moneda
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

// Badge de etapa
const getEtapaBadge = (etapa: string) => {
    const badges: Record<string, { class: string }> = {
        'Presentación Demanda': { class: 'bg-blue-100 text-blue-800' },
        'Completar Demanda': { class: 'bg-cyan-100 text-cyan-800' },
        'Calificación': { class: 'bg-teal-100 text-teal-800' },
        'En Citación': { class: 'bg-indigo-100 text-indigo-800' },
        'Citación Realizada': { class: 'bg-violet-100 text-violet-800' },
        'Audiencia': { class: 'bg-purple-100 text-purple-800' },
        'Sentencia': { class: 'bg-fuchsia-100 text-fuchsia-800' },
        'Apelación': { class: 'bg-pink-100 text-pink-800' },
        'Casación': { class: 'bg-rose-100 text-rose-800' },
        'Sentencia Ejecutoriada': { class: 'bg-red-100 text-red-800' },
        'Liquidación': { class: 'bg-orange-100 text-orange-800' },
        'Mandamiento de Ejecución': { class: 'bg-amber-100 text-amber-800' },
        'Archivo de la Causa': { class: 'bg-yellow-100 text-yellow-800' },
        'Embargo': { class: 'bg-lime-100 text-lime-800' },
        'Avaluo': { class: 'bg-green-100 text-green-800' },
        'Remate': { class: 'bg-emerald-100 text-emerald-800' },
        'Calificación de Postura': { class: 'bg-teal-200 text-teal-900' },
        'Adjudicación': { class: 'bg-sky-100 text-sky-800' },
        'Consignación de valores': { class: 'bg-blue-200 text-blue-900' },
        'Juicio Concluido': { class: 'bg-slate-100 text-slate-800' }
    };
    return badges[etapa] || { class: 'bg-gray-100 text-gray-800' };
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="emit('update:visible', $event)" modal maximizable
        :header="`Gestión Extrajudicial - ${deudor.nombre} (${deudor.cedula})`" :style="{ width: '75rem' }">
        <div class="space-y-6">
            <!-- Panel Información General del Proceso Judicial -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-lg border border-indigo-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="pi pi-briefcase text-indigo-600"></i>
                        Información del Proceso Judicial
                    </h3>
                    <Button 
                        v-if="!editingGeneral"
                        label="Editar" 
                        icon="pi pi-pencil" 
                        size="small" 
                        @click="editarInformacionGeneral" 
                    />
                    <div v-else class="flex gap-2">
                        <Button 
                            label="Cancelar" 
                            icon="pi pi-times" 
                            severity="secondary" 
                            size="small" 
                            @click="cancelarEdicionGeneral" 
                        />
                        <Button 
                            label="Guardar" 
                            icon="pi pi-check" 
                            size="small" 
                            @click="guardarInformacionGeneral" 
                        />
                    </div>
                </div>

                <div v-if="!editingGeneral" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Número de Juicio</p>
                        <p class="text-lg font-semibold text-gray-800">{{ procesoJudicial.numeroJuicio }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Unidad Judicial</p>
                        <p class="text-sm font-medium text-gray-800">{{ procesoJudicial.unidadJudicial }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Juez</p>
                        <p class="text-sm font-medium text-gray-800">{{ procesoJudicial.juez }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Fecha Presentación Demanda</p>
                        <p class="text-sm font-medium text-gray-800">{{ procesoJudicial.fechaPresentacion }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tipo de Procedimiento</p>
                        <p class="text-sm font-semibold text-indigo-700">{{ procesoJudicial.tipoProcedimiento }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Capital</p>
                        <p class="text-lg font-bold text-blue-600">{{ formatCurrency(procesoJudicial.capital) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cuantía</p>
                        <p class="text-lg font-bold text-green-600">{{ formatCurrency(procesoJudicial.cuantia) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Codeudor / Garante</p>
                        <p class="text-sm font-medium text-gray-800">{{ procesoJudicial.codeudorGarante }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cédula</p>
                        <p class="text-sm font-medium text-gray-800">{{ procesoJudicial.cedula }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <p class="text-sm text-gray-600 mb-1">Garantía</p>
                        <p class="text-sm text-gray-700">{{ procesoJudicial.garantia }}</p>
                    </div>
                </div>

                <!-- Formulario de Edición -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Número de Juicio</label>
                        <InputText v-model="formGeneral.numeroJuicio" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Unidad Judicial</label>
                        <InputText v-model="formGeneral.unidadJudicial" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Juez</label>
                        <InputText v-model="formGeneral.juez" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Fecha Presentación Demanda</label>
                        <Calendar v-model="formGeneral.fechaPresentacion" dateFormat="yy-mm-dd" 
                            :showIcon="true" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Tipo de Procedimiento</label>
                        <Dropdown v-model="formGeneral.tipoProcedimiento" :options="tiposProcedimiento" 
                            optionLabel="label" optionValue="value" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Capital</label>
                        <InputNumber v-model="formGeneral.capital" mode="currency" currency="USD" 
                            locale="es-EC" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Cuantía</label>
                        <InputNumber v-model="formGeneral.cuantia" mode="currency" currency="USD" 
                            locale="es-EC" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Codeudor / Garante</label>
                        <InputText v-model="formGeneral.codeudorGarante" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block mb-2 font-medium text-sm">Cédula</label>
                        <InputText v-model="formGeneral.cedula" class="w-full" />
                    </div>
                    <div class="field md:col-span-2 lg:col-span-3">
                        <label class="block mb-2 font-medium text-sm">Garantía</label>
                        <Textarea v-model="formGeneral.garantia" rows="2" class="w-full" />
                    </div>
                </div>
            </div>

            <!-- Panel Última Etapa Vigente -->
            <div v-if="ultimaEtapaVigente" class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-lg border border-purple-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
                    <i class="pi pi-flag text-purple-600"></i>
                    Última Etapa Judicial Registrada
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Etapa Judicial</p>
                        <span :class="`px-3 py-1 rounded-full text-sm font-semibold inline-block ${getEtapaBadge(ultimaEtapaVigente.etapaJudicial).class}`">
                            {{ ultimaEtapaVigente.etapaJudicial }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Fecha</p>
                        <p class="text-lg font-semibold text-gray-800">{{ ultimaEtapaVigente.fecha }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Observación</p>
                        <p class="text-sm text-gray-700">{{ ultimaEtapaVigente.observacion || '-' }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="bg-gray-50 p-6 rounded-lg border border-gray-200 text-center">
                <p class="text-gray-500">No hay etapas judiciales registradas</p>
            </div>

            <!-- Formulario de Registro de Etapa -->
            <div ref="formularioRef" class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    {{ editingId ? 'Actualizar Etapa Judicial' : 'Registrar Nueva Etapa Judicial' }}
                </h3>

                <form @submit.prevent="guardarEtapa" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Etapa Judicial -->
                    <div class="field">
                        <label for="etapaJudicial" class="block mb-2 font-medium text-sm">
                            Etapa Judicial <span class="text-red-500">*</span>
                        </label>
                        <Dropdown id="etapaJudicial" v-model="form.etapaJudicial" :options="etapasJudiciales" 
                            optionLabel="label" optionValue="value" placeholder="Seleccione etapa" 
                            class="w-full" :filter="true" />
                    </div>

                    <!-- Fecha -->
                    <div class="field">
                        <label for="fecha" class="block mb-2 font-medium text-sm">
                            Fecha <span class="text-red-500">*</span>
                        </label>
                        <Calendar id="fecha" v-model="form.fecha" dateFormat="yy-mm-dd" 
                            :showIcon="true" class="w-full" placeholder="Seleccione fecha" />
                    </div>

                    <!-- Demandado -->
                    <div class="field">
                        <label for="demandado" class="block mb-2 font-medium text-sm">
                            Demandado <span class="text-red-500">*</span>
                        </label>
                        <Dropdown id="demandado" v-model="form.demandado" :options="opcionesDemandado"
                            optionLabel="label" optionValue="value" placeholder="Seleccione Si/No"
                            class="w-full" />
                    </div>

                    <!-- Fecha de Gestión (Automática) -->
                    <div class="field">
                        <label for="fechaGestion" class="block mb-2 font-medium text-sm">
                            Fecha de Gestión
                        </label>
                        <Calendar id="fechaGestion" v-model="form.fechaGestion" 
                            :showIcon="true" dateFormat="yy-mm-dd" class="w-full" :disabled="true" />
                    </div>

                    <!-- Observación -->
                    <div class="field md:col-span-2">
                        <label for="observacion" class="block mb-2 font-medium text-sm">
                            Observación
                        </label>
                        <Textarea id="observacion" v-model="form.observacion" rows="3"
                            placeholder="Ingrese observaciones de esta etapa judicial" class="w-full" />
                    </div>

                    <!-- Botones -->
                    <div class="field md:col-span-2 flex justify-end gap-2 mt-4">
                        <Button label="Cancelar" icon="pi pi-times" severity="secondary" @click="resetForm"
                            type="button" />
                        <Button :label="editingId ? 'Actualizar' : 'Guardar'" icon="pi pi-check" type="submit" />
                    </div>
                </form>
            </div>

            <!-- Tabla de Etapas Judiciales -->
            <div class="card">
                <DataTable v-model:filters="filters" :value="etapas" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="row" :loading="loading"
                    :globalFilterFields="['etapaJudicial', 'observacion', 'creadoPor']" size="small" stripedRows>
                    <template #header>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h3 class="text-lg font-semibold">Historial de Etapas Judiciales</h3>

                            <div class="flex flex-wrap items-center gap-2">
                                <Button icon="pi pi-filter-slash" label="Limpiar" severity="secondary" size="small"
                                    @click="clearFilter()" />
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-search"></i>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar..."
                                        class="w-full md:w-auto" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <Column field="etapaJudicial" header="Etapa Judicial" sortable style="min-width: 14rem">
                        <template #body="{ data }">
                            <span :class="`px-3 py-1 rounded-full text-xs font-semibold ${getEtapaBadge(data.etapaJudicial).class}`">
                                {{ data.etapaJudicial }}
                            </span>
                        </template>
                    </Column>

                    <Column field="fecha" header="Fecha" sortable style="min-width: 10rem"></Column>

                    <Column field="demandado" header="Demandado" style="min-width: 10rem">
                        <template #body="{ data }">
                            <span :class="data.demandado === 'Si' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'" 
                                class="px-2 py-1 rounded-full text-xs font-semibold inline-block">
                                {{ data.demandado }}
                            </span>
                        </template>
                    </Column>
                    
                    <Column field="fechaGestion" header="Fecha Gestión" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div class="text-sm">{{ new Date(data.fechaGestion).toLocaleDateString() }}</div>
                        </template>
                    </Column>

                    <Column field="observacion" header="Observación" style="min-width: 16rem">
                        <template #body="{ data }">
                            <span class="text-sm">{{ data.observacion || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="creadoPor" header="Creado Por" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div class="text-xs">
                                <div class="font-semibold text-gray-700">{{ data.creadoPor }}</div>
                                <div class="text-gray-500">{{ data.fechaCreacion }}</div>
                            </div>
                        </template>
                    </Column>

                    <Column header="Acciones" style="min-width: 8rem">
                        <template #body="{ data }">
                            <div class="flex gap-2">
                                <Button icon="pi pi-pencil" severity="info" size="small" text rounded
                                    @click="editarEtapa(data)" v-tooltip.top="'Editar'" />
                                <Button icon="pi pi-trash" severity="danger" size="small" text rounded
                                    @click="confirmarEliminar(data.id)" v-tooltip.top="'Eliminar'" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Dialog de confirmación de eliminación -->
        <Dialog v-model:visible="showDeleteDialog" modal header="Confirmar Eliminación" :style="{ width: '450px' }">
            <div class="flex items-center gap-3">
                <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 2rem"></i>
                <span>¿Está seguro que desea eliminar esta etapa judicial?</span>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" severity="secondary" @click="showDeleteDialog = false" />
                <Button label="Eliminar" icon="pi pi-check" severity="danger" @click="eliminarEtapa" />
            </template>
        </Dialog>
    </Dialog>
</template>

<style scoped>
.field {
    margin-bottom: 0;
}
</style>