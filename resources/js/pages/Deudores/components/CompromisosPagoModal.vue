<script setup lang="ts">
import { ref, computed, nextTick, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from '@primevue/core/api';

interface Compromiso {
    id: number;
    tipoAcuerdo: string;
    fechaCompromiso: string;
    monto: number;
    cumplido: boolean;
    acuerdoAlDia: boolean;
    diasMora: number;
    fechaCumplimiento?: string;
    observacion: string;
    creadoPor: string;
    fechaCreacion: string;
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

// Opciones para Tipo de Acuerdo
const tiposAcuerdo = [
    { label: 'Pago Total', value: 'Pago Total' },
    { label: 'Abono', value: 'Abono' },
    { label: 'Acta Transaccional', value: 'Acta Transaccional' },
    { label: 'Mediación', value: 'Mediación' }
];

// Datos de ejemplo
const compromisos = ref<Compromiso[]>([
    { id: 1, tipoAcuerdo: 'Abono', fechaCompromiso: '2025-09-20', monto: 2000, cumplido: false, acuerdoAlDia: false, diasMora: 37, observacion: 'No pudo pagar', creadoPor: 'Admin', fechaCreacion: '2025-09-15' },
    { id: 2, tipoAcuerdo: 'Pago Total', fechaCompromiso: '2025-08-15', monto: 1500, cumplido: true, acuerdoAlDia: true, diasMora: 0, fechaCumplimiento: '2025-08-15', observacion: 'Pago completo', creadoPor: 'Gestor A', fechaCreacion: '2025-08-01' },
    { id: 3, tipoAcuerdo: 'Acta Transaccional', fechaCompromiso: '2025-10-05', monto: 3000, cumplido: false, acuerdoAlDia: false, diasMora: 22, observacion: 'Pendiente', creadoPor: 'Gestor B', fechaCreacion: '2025-09-28' },
    { id: 4, tipoAcuerdo: 'Mediación', fechaCompromiso: '2025-07-20', monto: 1000, cumplido: true, acuerdoAlDia: true, diasMora: 0, fechaCumplimiento: '2025-07-22', observacion: 'Pago parcial realizado', creadoPor: 'Admin', fechaCreacion: '2025-07-10' },
    { id: 5, tipoAcuerdo: 'Pago Total', fechaCompromiso: '2025-06-10', monto: 2500, cumplido: true, acuerdoAlDia: true, diasMora: 0, fechaCumplimiento: '2025-06-10', observacion: 'Cumplido a tiempo', creadoPor: 'Gestor C', fechaCreacion: '2025-06-01' },
]);

// Calcular resumen del último compromiso vigente
const ultimoCompromisoVigente = computed(() => {
    const hoy = new Date();
    const vigentes = compromisos.value
        .filter(c => !c.cumplido)
        .sort((a, b) => new Date(b.fechaCompromiso).getTime() - new Date(a.fechaCompromiso).getTime());
    
    if (vigentes.length === 0) {
        const cumplidos = compromisos.value
            .filter(c => c.cumplido)
            .sort((a, b) => new Date(b.fechaCompromiso).getTime() - new Date(a.fechaCompromiso).getTime());
        return cumplidos[0] || null;
    }

    const ultimo = vigentes[0];
    const fechaVencimiento = new Date(ultimo.fechaCompromiso);
    
    let cumplimiento = 'Incompleto';
    if (ultimo.cumplido) {
        cumplimiento = 'Completo';
    } else if (fechaVencimiento < hoy) {
        cumplimiento = 'Vencido';
    }

    return {
        ...ultimo,
        cumplimiento
    };
});

// Formulario
const form = ref<Partial<Compromiso>>({
    tipoAcuerdo: null,
    fechaCompromiso: null,
    monto: null,
    cumplido: false,
    acuerdoAlDia: false,
    diasMora: 0,
    fechaCumplimiento: null,
    observacion: ''
});

const editingId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const compromisoToDelete = ref<number | null>(null);
const formularioRef = ref<HTMLElement | null>(null);

// Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const loading = ref(false);

// Calcular días de mora automáticamente
const calcularDiasMora = () => {
    if (!form.value.fechaCompromiso || form.value.cumplido || form.value.acuerdoAlDia) {
        form.value.diasMora = 0;
        return;
    }

    const hoy = new Date();
    const fechaCompromiso = form.value.fechaCompromiso instanceof Date 
        ? form.value.fechaCompromiso 
        : new Date(form.value.fechaCompromiso);

    if (fechaCompromiso < hoy) {
        const diffTime = Math.abs(hoy.getTime() - fechaCompromiso.getTime());
        form.value.diasMora = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    } else {
        form.value.diasMora = 0;
    }
};

// Watchers para recalcular días de mora
watch(() => form.value.fechaCompromiso, calcularDiasMora);
watch(() => form.value.cumplido, calcularDiasMora);
watch(() => form.value.acuerdoAlDia, calcularDiasMora);

// Funciones CRUD
const resetForm = () => {
    form.value = {
        tipoAcuerdo: null,
        fechaCompromiso: null,
        monto: null,
        cumplido: false,
        acuerdoAlDia: false,
        diasMora: 0,
        fechaCumplimiento: null,
        observacion: ''
    };
    editingId.value = null;
};

const validarFormulario = (): boolean => {
    if (!form.value.tipoAcuerdo || !form.value.fechaCompromiso || !form.value.monto) {
        alert('Por favor complete todos los campos obligatorios (Tipo de Acuerdo, Fecha Compromiso y Monto)');
        return false;
    }

    // Si está marcado como cumplido, debe tener fecha de cumplimiento
    if (form.value.cumplido && !form.value.fechaCumplimiento) {
        alert('Para marcar como cumplido debe ingresar la Fecha de Cumplimiento');
        return false;
    }

    return true;
};

const guardarCompromiso = () => {
    if (!validarFormulario()) return;

    // Calcular días de mora antes de guardar
    calcularDiasMora();

    const fechaCompromisoStr = form.value.fechaCompromiso instanceof Date 
        ? form.value.fechaCompromiso.toISOString().split('T')[0]
        : form.value.fechaCompromiso;

    const fechaCumplimientoStr = form.value.fechaCumplimiento instanceof Date
        ? form.value.fechaCumplimiento.toISOString().split('T')[0]
        : form.value.fechaCumplimiento || undefined;

    if (editingId.value) {
        // Editar
        const index = compromisos.value.findIndex(c => c.id === editingId.value);
        if (index !== -1) {
            compromisos.value[index] = {
                ...compromisos.value[index],
                tipoAcuerdo: form.value.tipoAcuerdo!,
                fechaCompromiso: fechaCompromisoStr!,
                monto: form.value.monto!,
                cumplido: form.value.cumplido || false,
                acuerdoAlDia: form.value.acuerdoAlDia || false,
                diasMora: form.value.diasMora || 0,
                fechaCumplimiento: fechaCumplimientoStr,
                observacion: form.value.observacion || ''
            };
        }
    } else {
        // Crear
        const nuevoCompromiso: Compromiso = {
            id: Math.max(...compromisos.value.map(c => c.id), 0) + 1,
            tipoAcuerdo: form.value.tipoAcuerdo!,
            fechaCompromiso: fechaCompromisoStr!,
            monto: form.value.monto!,
            cumplido: form.value.cumplido || false,
            acuerdoAlDia: form.value.acuerdoAlDia || false,
            diasMora: form.value.diasMora || 0,
            fechaCumplimiento: fechaCumplimientoStr,
            observacion: form.value.observacion || '',
            creadoPor: 'Usuario Actual',
            fechaCreacion: new Date().toISOString().split('T')[0]
        };
        compromisos.value.unshift(nuevoCompromiso);
    }

    resetForm();
};

const editarCompromiso = (compromiso: Compromiso) => {
    form.value = {
        tipoAcuerdo: compromiso.tipoAcuerdo,
        fechaCompromiso: compromiso.fechaCompromiso,
        monto: compromiso.monto,
        cumplido: compromiso.cumplido,
        acuerdoAlDia: compromiso.acuerdoAlDia,
        diasMora: compromiso.diasMora,
        fechaCumplimiento: compromiso.fechaCumplimiento,
        observacion: compromiso.observacion
    };
    editingId.value = compromiso.id;

    // Hacer scroll al formulario
    nextTick(() => {
        formularioRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
};

const confirmarEliminar = (id: number) => {
    compromisoToDelete.value = id;
    showDeleteDialog.value = true;
};

const eliminarCompromiso = () => {
    if (compromisoToDelete.value) {
        compromisos.value = compromisos.value.filter(c => c.id !== compromisoToDelete.value);
        showDeleteDialog.value = false;
        compromisoToDelete.value = null;
    }
};

// Exportar/Importar funciones
const exportarXLS = () => {
    alert('Funcionalidad de exportar XLS - En desarrollo');
    // TODO: Implementar exportación a Excel
};

const descargarPlantilla = () => {
    alert('Descargando plantilla XLS - En desarrollo');
    // TODO: Implementar descarga de plantilla Excel
};

const importarXLS = () => {
    alert('Funcionalidad de importar XLS - En desarrollo');
    // TODO: Implementar importación desde Excel
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

// Badge de cumplimiento
const getCumplimientoBadge = (compromiso: Compromiso) => {
    if (compromiso.cumplido) return { class: 'bg-green-100 text-green-800', text: 'Completo' };
    
    const hoy = new Date();
    const fecha = new Date(compromiso.fechaCompromiso);
    
    if (fecha < hoy) return { class: 'bg-red-100 text-red-800', text: 'Vencido' };
    return { class: 'bg-yellow-100 text-yellow-800', text: 'Pendiente' };
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="emit('update:visible', $event)" modal maximizable
        :header="`Compromisos de Pago - ${deudor.nombre} (${deudor.cedula})`" :style="{ width: '75rem' }">
        <div class="space-y-6">
            <!-- Panel Resumen -->
            <div v-if="ultimoCompromisoVigente" class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    Último Compromiso Vigente
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tipo de Acuerdo</p>
                        <p class="text-lg font-semibold text-gray-800">{{ ultimoCompromisoVigente.tipoAcuerdo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Monto</p>
                        <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(ultimoCompromisoVigente.monto) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Vencimiento</p>
                        <p class="text-lg font-semibold text-gray-800">{{ ultimoCompromisoVigente.fechaCompromiso }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cumplimiento</p>
                        <span :class="`px-3 py-1 rounded-full text-sm font-semibold inline-block ${
                            ultimoCompromisoVigente.cumplimiento === 'Completo' ? 'bg-green-100 text-green-800' :
                            ultimoCompromisoVigente.cumplimiento === 'Vencido' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-yellow-800'
                        }`">
                            {{ ultimoCompromisoVigente.cumplimiento }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Días en Mora</p>
                        <p class="text-2xl font-bold" :class="ultimoCompromisoVigente.diasMora > 0 ? 'text-red-600' : 'text-green-600'">
                            {{ ultimoCompromisoVigente.diasMora }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-else class="bg-gray-50 p-6 rounded-lg border border-gray-200 text-center">
                <p class="text-gray-500">No hay compromisos registrados</p>
            </div>

            <!-- Formulario de Registro -->
            <div ref="formularioRef" class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    {{ editingId ? 'Actualizar Compromiso' : 'Nuevo Compromiso de Pago' }}
                </h3>

                <form @submit.prevent="guardarCompromiso" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Tipo de Acuerdo -->
                    <div class="field">
                        <label for="tipoAcuerdo" class="block mb-2 font-medium text-sm">
                            Tipo de Acuerdo <span class="text-red-500">*</span>
                        </label>
                        <Dropdown id="tipoAcuerdo" v-model="form.tipoAcuerdo" :options="tiposAcuerdo" 
                            optionLabel="label" optionValue="value" placeholder="Seleccione tipo" class="w-full" />
                    </div>

                    <!-- Fecha Compromiso -->
                    <div class="field">
                        <label for="fechaCompromiso" class="block mb-2 font-medium text-sm">
                            Fecha Compromiso <span class="text-red-500">*</span>
                        </label>
                        <Calendar id="fechaCompromiso" v-model="form.fechaCompromiso" dateFormat="yy-mm-dd" 
                            :showIcon="true" class="w-full" placeholder="Seleccione fecha" />
                    </div>

                    <!-- Monto -->
                    <div class="field">
                        <label for="monto" class="block mb-2 font-medium text-sm">
                            Monto <span class="text-red-500">*</span>
                        </label>
                        <InputNumber id="monto" v-model="form.monto" mode="currency" currency="USD" locale="es-EC"
                            placeholder="Ingrese monto" class="w-full" />
                    </div>

                    <!-- Fecha Cumplimiento -->
                    <div class="field">
                        <label for="fechaCumplimiento" class="block mb-2 font-medium text-sm">
                            Fecha Cumplimiento
                        </label>
                        <Calendar id="fechaCumplimiento" v-model="form.fechaCumplimiento" dateFormat="yy-mm-dd"
                            :showIcon="true" class="w-full" placeholder="Seleccione fecha"
                            :disabled="!form.cumplido" />
                    </div>

                    <!-- Acuerdo al Día -->
                    <div class="field flex items-center gap-2 pt-6">
                        <Checkbox id="acuerdoAlDia" v-model="form.acuerdoAlDia" :binary="true" />
                        <label for="acuerdoAlDia" class="font-medium text-sm cursor-pointer">
                            Acuerdo al Día
                        </label>
                    </div>

                    <!-- Días en Mora (readonly) -->
                    <div class="field">
                        <label for="diasMora" class="block mb-2 font-medium text-sm">
                            Días en Mora
                        </label>
                        <InputNumber id="diasMora" v-model="form.diasMora" class="w-full" readonly 
                            :class="form.diasMora > 0 ? 'text-red-600 font-bold' : 'text-green-600 font-bold'" />
                    </div>

                    <!-- Observación -->
                    <div class="field md:col-span-2 lg:col-span-3">
                        <label for="observacion" class="block mb-2 font-medium text-sm">
                            Observación
                        </label>
                        <Textarea id="observacion" v-model="form.observacion" rows="2"
                            placeholder="Ingrese observaciones adicionales" class="w-full" />
                    </div>

                    <!-- Checkbox Cumplido -->
                    <div class="field flex items-center gap-2">
                        <Checkbox id="cumplido" v-model="form.cumplido" :binary="true" />
                        <label for="cumplido" class="font-medium text-sm cursor-pointer">
                            Marcar como Cumplido
                        </label>
                    </div>

                    <!-- Botones -->
                    <div class="field md:col-span-2 lg:col-span-3 flex justify-end gap-2 mt-4">
                        <Button label="Cancelar" icon="pi pi-times" severity="secondary" @click="resetForm"
                            type="button" />
                        <Button :label="editingId ? 'Actualizar' : 'Guardar'" icon="pi pi-check" type="submit" />
                    </div>
                </form>
            </div>

            <!-- Tabla de Compromisos -->
            <div class="card">
                <DataTable v-model:filters="filters" :value="compromisos" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="row" :loading="loading"
                    :globalFilterFields="['tipoAcuerdo', 'fechaCompromiso', 'observacion', 'creadoPor']" size="small" stripedRows
                    class="compromisos-table">
                    <template #header>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h3 class="text-lg font-semibold">Lista de Compromisos</h3>

                            <div class="flex flex-wrap items-center gap-2">
                                <Button icon="pi pi-filter-slash" label="Limpiar" severity="secondary" size="small"
                                    @click="clearFilter()" />
                                <Button icon="pi pi-download" label="Plantilla XLS" severity="info" size="small"
                                    @click="descargarPlantilla" />
                                <Button icon="pi pi-upload" label="Importar XLS" severity="help" size="small"
                                    @click="importarXLS" />
                                <Button icon="pi pi-file-excel" label="Exportar XLS" severity="success" size="small"
                                    @click="exportarXLS" />
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-search"></i>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar..."
                                        class="w-full md:w-auto" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <Column field="tipoAcuerdo" header="Tipo de Acuerdo" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="font-medium text-gray-700">{{ data.tipoAcuerdo }}</span>
                        </template>
                    </Column>

                    <Column field="fechaCompromiso" header="Fecha Compromiso" sortable style="min-width: 10rem"></Column>

                    <Column field="monto" header="Monto" sortable style="min-width: 10rem">
                        <template #body="{ data }">
                            <span class="font-semibold text-blue-600">{{ formatCurrency(data.monto) }}</span>
                        </template>
                    </Column>

                    <Column field="acuerdoAlDia" header="Al Día" style="min-width: 6rem">
                        <template #body="{ data }">
                            <i :class="data.acuerdoAlDia ? 'pi pi-check-circle text-green-600 text-xl' : 'pi pi-times-circle text-red-400 text-xl'"></i>
                        </template>
                    </Column>

                    <Column field="diasMora" header="Días Mora" sortable style="min-width: 8rem">
                        <template #body="{ data }">
                            <span :class="data.diasMora > 0 ? 'font-bold text-red-600' : 'font-semibold text-green-600'">
                                {{ data.diasMora }}
                            </span>
                        </template>
                    </Column>

                    <Column field="cumplido" header="Cumplido" style="min-width: 8rem">
                        <template #body="{ data }">
                            <i :class="data.cumplido ? 'pi pi-check-circle text-green-600 text-xl' : 'pi pi-times-circle text-gray-400 text-xl'"></i>
                        </template>
                    </Column>

                    <Column field="fechaCumplimiento" header="Fecha Cumplimiento" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span v-if="data.fechaCumplimiento" class="text-sm">{{ data.fechaCumplimiento }}</span>
                            <span v-else class="text-gray-400 text-sm">-</span>
                        </template>
                    </Column>

                    <Column header="Estado" style="min-width: 10rem">
                        <template #body="{ data }">
                            <span :class="`px-3 py-1 rounded-full text-xs font-semibold ${getCumplimientoBadge(data).class}`">
                                {{ getCumplimientoBadge(data).text }}
                            </span>
                        </template>
                    </Column>

                    <Column field="observacion" header="Observación" style="min-width: 14rem">
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
                                    @click="editarCompromiso(data)" v-tooltip.top="'Editar'" />
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
                <span>¿Está seguro que desea eliminar este compromiso de pago?</span>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" severity="secondary" @click="showDeleteDialog = false" />
                <Button label="Eliminar" icon="pi pi-check" severity="danger" @click="eliminarCompromiso" />
            </template>
        </Dialog>
    </Dialog>
</template>

<style scoped>
.compromisos-table :deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 1rem 0;
}

@media (max-width: 768px) {
    .compromisos-table :deep(.p-datatable-tbody > tr > td) {
        text-align: left;
    }
}
</style>