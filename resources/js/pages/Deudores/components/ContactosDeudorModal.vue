<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from '@primevue/core/api';

interface Contacto {
    id: number;
    categoria: string;
    tipo: string;
    detalle: string;
    validado: boolean;
    principal: boolean;
    observacion: string;
    fechaValidacion?: string;
    usuarioValidacion?: string;
    fecha?: string;
    etapa?: string;
    medidaCautelar?: boolean;
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

// Opciones para los combos - Catálogo correcto
const tipos = ['Celular', 'Teléfono fijo', 'Email', 'Dirección', 'Otro'];
const categorias = ['Personal', 'Trabajo', 'Referencia', 'Familiar', 'Legal', 'Cobranza'];
const etapas = [
    'Contactado',
    'Inubicable',
    'Compromiso de Pago',
    'Negativa de Pago',
    'Contacto con Terceros',
    'Fuera del País',
    'Acepta Refinanciamiento',
    'Deuda Cancelada'
];

// Datos de ejemplo
const contactos = ref<Contacto[]>([
    { id: 1, categoria: 'Personal', tipo: 'Teléfono fijo', detalle: '25456789', validado: true, principal: true, observacion: 'Teléfono verificado', fechaValidacion: '2025-09-15', usuarioValidacion: 'Admin' },
    { id: 2, categoria: 'Personal', tipo: 'Celular', detalle: '0985456788', validado: true, principal: true, observacion: 'Contacto directo', fechaValidacion: '2025-09-15', usuarioValidacion: 'Admin' },
    { id: 3, categoria: 'Personal', tipo: 'Email', detalle: 'm.fernandez@mail.com', validado: true, principal: true, observacion: 'Email corporativo', fechaValidacion: '2025-09-15', usuarioValidacion: 'Admin' },
    { id: 4, categoria: 'Personal', tipo: 'Dirección', detalle: 'Av. Principal #123, Guayaquil', validado: false, principal: true, observacion: 'Pendiente verificación' },
    { id: 5, categoria: 'Trabajo', tipo: 'Teléfono fijo', detalle: '24567890', validado: false, principal: true, observacion: 'Línea directa oficina' },
    { id: 6, categoria: 'Referencia', tipo: 'Celular', detalle: '0987654321', validado: true, principal: false, observacion: 'Hermano - contacto secundario', fechaValidacion: '2025-09-20', usuarioValidacion: 'Gestor A' },
    { id: 7, categoria: 'Trabajo', tipo: 'Email', detalle: 'mfernandez@empresa.com', validado: true, principal: true, observacion: 'Email institucional', fechaValidacion: '2025-09-16', usuarioValidacion: 'Gestor B' },
    { id: 8, categoria: 'Familiar', tipo: 'Celular', detalle: '0991234567', validado: true, principal: true, observacion: 'Esposa', fechaValidacion: '2025-09-17', usuarioValidacion: 'Admin' },
    { id: 9, categoria: 'Trabajo', tipo: 'Dirección', detalle: 'Cdla. Kennedy Norte, Mz 45 Solar 12', validado: true, principal: true, observacion: 'Oficina principal', fechaValidacion: '2025-09-18', usuarioValidacion: 'Gestor C' },
    { id: 10, categoria: 'Personal', tipo: 'Celular', detalle: '0982345678', validado: false, principal: false, observacion: 'Segundo celular - apagado' },
    { id: 11, categoria: 'Referencia', tipo: 'Teléfono fijo', detalle: '24123456', validado: true, principal: true, observacion: 'Madre del deudor', fechaValidacion: '2025-09-19', usuarioValidacion: 'Gestor A' },
    { id: 12, categoria: 'Legal', tipo: 'Email', detalle: 'abogado@bufete.com', validado: true, principal: true, observacion: 'Abogado defensor', fechaValidacion: '2025-09-21', usuarioValidacion: 'Admin' },
    { id: 13, categoria: 'Cobranza', tipo: 'Celular', detalle: '0995678901', validado: true, principal: true, observacion: 'Contacto exclusivo cobranza', fechaValidacion: '2025-09-22', usuarioValidacion: 'Gestor D' },
    { id: 14, categoria: 'Familiar', tipo: 'Email', detalle: 'hermano.fernandez@mail.com', validado: false, principal: false, observacion: 'Hermano - email alternativo' },
    { id: 15, categoria: 'Personal', tipo: 'Otro', detalle: '@mariafernandez (WhatsApp Business)', validado: true, principal: false, observacion: 'Canal preferido', fechaValidacion: '2025-09-23', usuarioValidacion: 'Gestor E' },
    { id: 16, categoria: 'Referencia', tipo: 'Dirección', detalle: 'Urb. Los Almendros, Casa 45', validado: false, principal: false, observacion: 'Casa de padres' },
    { id: 17, categoria: 'Trabajo', tipo: 'Celular', detalle: '0986543210', validado: true, principal: false, observacion: 'Jefe inmediato', fechaValidacion: '2025-09-24', usuarioValidacion: 'Gestor B' },
    { id: 18, categoria: 'Legal', tipo: 'Teléfono fijo', detalle: '24998877', validado: true, principal: false, observacion: 'Bufete de abogados', fechaValidacion: '2025-09-25', usuarioValidacion: 'Admin' },
    { id: 19, categoria: 'Cobranza', tipo: 'Email', detalle: 'pagos.fernandez@mail.com', validado: false, principal: false, observacion: 'Email para notificaciones' },
    { id: 20, categoria: 'Familiar', tipo: 'Teléfono fijo', detalle: '24556677', validado: true, principal: false, observacion: 'Casa de hermana', fechaValidacion: '2025-09-26', usuarioValidacion: 'Gestor C' },
]);

// Formulario
const form = ref<Partial<Contacto>>({
    categoria: undefined,
    tipo: undefined,
    detalle: '',
    validado: false,
    principal: false,
    observacion: '',
    fecha: undefined,
    etapa: undefined,
    medidaCautelar: false
});

const editingId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const contactoToDelete = ref<number | null>(null);
const formularioRef = ref<HTMLElement | null>(null);

// Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    categoria: { value: null, matchMode: FilterMatchMode.EQUALS },
    tipo: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const loading = ref(false);

// Funciones CRUD
const resetForm = () => {
    form.value = {
        categoria: null,
        tipo: null,
        detalle: '',
        validado: false,
        principal: false,
        observacion: ''
    };
    editingId.value = null;
};

const validarFormulario = (): boolean => {
    if (!form.value.categoria || !form.value.tipo || !form.value.detalle) {
        alert('Por favor complete todos los campos obligatorios');
        return false;
    }

    // Validar que solo haya un principal por tipo
    if (form.value.principal) {
        const existePrincipal = contactos.value.some(c =>
            c.tipo === form.value.tipo &&
            c.principal &&
            c.id !== editingId.value
        );

        if (existePrincipal) {
            alert(`Ya existe un contacto principal para el tipo "${form.value.tipo}". Por favor, desmarque el otro primero.`);
            return false;
        }
    }

    return true;
};

const guardarContacto = () => {
    if (!validarFormulario()) return;

    if (editingId.value) {
        // Editar
        const index = contactos.value.findIndex(c => c.id === editingId.value);
        if (index !== -1) {
            contactos.value[index] = {
                ...contactos.value[index],
                ...form.value as Contacto,
                fechaValidacion: form.value.validado ? new Date().toISOString().split('T')[0] : contactos.value[index].fechaValidacion,
                usuarioValidacion: form.value.validado ? 'Usuario Actual' : contactos.value[index].usuarioValidacion
            };
        }
    } else {
        // Crear
        const nuevoContacto: Contacto = {
            id: Math.max(...contactos.value.map(c => c.id), 0) + 1,
            categoria: form.value.categoria!,
            tipo: form.value.tipo!,
            detalle: form.value.detalle!,
            validado: form.value.validado || false,
            principal: form.value.principal || false,
            observacion: form.value.observacion || '',
            fechaValidacion: form.value.validado ? new Date().toISOString().split('T')[0] : undefined,
            usuarioValidacion: form.value.validado ? 'Usuario Actual' : undefined
        };
        contactos.value.push(nuevoContacto);
    }

    resetForm();
};

const editarContacto = (contacto: Contacto) => {
    form.value = { ...contacto };
    editingId.value = contacto.id;

    // Hacer scroll al formulario
    nextTick(() => {
        formularioRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
};

const confirmarEliminar = (id: number) => {
    contactoToDelete.value = id;
    showDeleteDialog.value = true;
};

const eliminarContacto = () => {
    if (contactoToDelete.value) {
        contactos.value = contactos.value.filter(c => c.id !== contactoToDelete.value);
        showDeleteDialog.value = false;
        contactoToDelete.value = null;
    }
};

// Exportar funciones (maquetado)
const exportarCSV = () => {
    alert('Funcionalidad de exportar CSV - En desarrollo');
};

const descargarPlantilla = () => {
    alert('Funcionalidad de descargar plantilla - En desarrollo');
    // TODO: Implementar descarga de plantilla
};

const importarCSV = () => {
    alert('Funcionalidad de importar CSV - En desarrollo');
    // TODO: Implementar importación desde CSV
};

const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.categoria.value = null;
    filters.value.tipo.value = null;
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="emit('update:visible', $event)" modal maximizable
        :header="`Datos de Contacto - ${deudor.nombre} (${deudor.cedula})`" :style="{ width: '70rem' }">
        <div class="space-y-6">
            <!-- Formulario de Registro -->
            <div ref="formularioRef" class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    {{ editingId ? 'Actualizar Contacto' : 'Nuevo Contacto' }}
                </h3>

                <form @submit.prevent="guardarContacto" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Categoría -->
                    <div class="field">
                        <label for="categoria" class="block mb-2 font-medium text-sm">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <Select id="categoria" v-model="form.categoria" :options="categorias"
                            placeholder="Seleccione categoría" class="w-full" />
                    </div>

                    <!-- Tipo -->
                    <div class="field">
                        <label for="tipo" class="block mb-2 font-medium text-sm">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <Select id="tipo" v-model="form.tipo" :options="tipos" placeholder="Seleccione tipo"
                            class="w-full" />
                    </div>

                    <!-- Detalle -->
                    <div class="field">
                        <label for="detalle" class="block mb-2 font-medium text-sm">
                            Detalle <span class="text-red-500">*</span>
                        </label>
                        <InputText id="detalle" v-model="form.detalle" placeholder="Ingrese el detalle"
                            class="w-full" />
                    </div>

                    <!-- Fecha -->
                    <div class="field">
                        <label for="fecha" class="block mb-2 font-medium text-sm">
                            Fecha
                        </label>
                        <InputText type="date" id="fecha" v-model="form.fecha" class="w-full" />
                    </div>

                    <!-- Etapa -->
                    <div class="field">
                        <label for="etapa" class="block mb-2 font-medium text-sm">
                            Etapa
                        </label>
                        <Select id="etapa" v-model="form.etapa" :options="etapas" 
                            placeholder="Seleccione etapa" class="w-full" />
                    </div>

                    <!-- Medida Cautelar -->
                    <div class="field flex items-center gap-2">
                        <Checkbox id="medidaCautelar" v-model="form.medidaCautelar" :binary="true" />
                        <label for="medidaCautelar" class="font-medium text-sm cursor-pointer">
                            Medida Cautelar
                        </label>
                    </div>

                    <!-- Observación -->
                    <div class="field md:col-span-2 lg:col-span-3">
                        <label for="observacion" class="block mb-2 font-medium text-sm">
                            Observación
                        </label>
                        <Textarea id="observacion" v-model="form.observacion" rows="2"
                            placeholder="Ingrese observaciones adicionales" class="w-full" />
                    </div>

                    <!-- Checkboxes -->
                    <div class="field flex items-center gap-2">
                        <Checkbox id="validado" v-model="form.validado" :binary="true" />
                        <label for="validado" class="font-medium text-sm cursor-pointer">
                            Validado
                        </label>
                    </div>

                    <div class="field flex items-center gap-2">
                        <Checkbox id="principal" v-model="form.principal" :binary="true" />
                        <label for="principal" class="font-medium text-sm cursor-pointer">
                            Principal (único por tipo)
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

            <!-- Tabla de Contactos -->
            <div class="card">
                <DataTable v-model:filters="filters" :value="contactos" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="row" :loading="loading"
                    :globalFilterFields="['categoria', 'tipo', 'detalle', 'observacion']" size="small" stripedRows
                    class="contactos-table">
                    <template #header>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h3 class="text-lg font-semibold">Lista de Contactos</h3>

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

                    <Column field="categoria" header="Categoría" sortable style="min-width: 10rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <Select v-model="filterModel.value" @change="filterCallback()" :options="categorias"
                                placeholder="Filtrar" class="w-full" />
                        </template>
                    </Column>

                    <Column field="tipo" header="Tipo" sortable style="min-width: 10rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <Select v-model="filterModel.value" @change="filterCallback()" :options="tipos"
                                placeholder="Filtrar" class="w-full" />
                        </template>
                    </Column>

                    <Column field="detalle" header="Detalle" sortable style="min-width: 14rem"></Column>

                    <Column field="validado" header="Validado" style="min-width: 8rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i
                                    :class="data.validado ? 'pi pi-check-circle text-green-600' : 'pi pi-times-circle text-gray-400'"></i>
                                <span v-if="data.validado && data.fechaValidacion" class="text-xs text-gray-500">
                                    {{ data.fechaValidacion }}
                                </span>
                            </div>
                        </template>
                    </Column>

                    <Column field="principal" header="Principal" style="min-width: 8rem">
                        <template #body="{ data }">
                            <i
                                :class="data.principal ? 'pi pi-star-fill text-yellow-500' : 'pi pi-star text-gray-300'"></i>
                        </template>
                    </Column>

                    <Column field="observacion" header="Observación" style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="text-sm">{{ data.observacion || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="usuarioValidacion" header="Usuario Val." style="min-width: 10rem">
                        <template #body="{ data }">
                            <span class="text-xs text-gray-600">{{ data.usuarioValidacion || '-' }}</span>
                        </template>
                    </Column>

                    <Column header="Acciones" style="min-width: 8rem">
                        <template #body="{ data }">
                            <div class="flex gap-2">
                                <Button icon="pi pi-pencil" severity="info" size="small" text rounded
                                    @click="editarContacto(data)" v-tooltip.top="'Editar'" />
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
                <span>¿Está seguro que desea eliminar este contacto?</span>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" severity="secondary" @click="showDeleteDialog = false" />
                <Button label="Eliminar" icon="pi pi-check" severity="danger" @click="eliminarContacto" />
            </template>
        </Dialog>
    </Dialog>
</template>

<style scoped>
.contactos-table :deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 1rem 0;
}

@media (max-width: 768px) {
    .contactos-table :deep(.p-datatable-tbody > tr > td) {
        text-align: left;
    }
}
</style>