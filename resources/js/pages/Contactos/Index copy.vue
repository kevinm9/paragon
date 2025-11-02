<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

// PrimeVue Components
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import SelectButton from 'primevue/selectbutton'; // Para validacion y principal
import Button from 'primevue/button';
import Tag from 'primevue/tag'; // Para mostrar el estado de validacion/principal
import Dialog from 'primevue/dialog'; // Para futuros modales (edición, creación)

// DataTable Components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator'; // Aunque DataTable ya lo incluye, si quieres más control

// Definición de tipos para los contactos y las propiedades de la página
interface Contact {
  id: number;
  debtor_id: number;
  categoria: 'Personal' | 'Trabajo' | 'Referencia' | 'Familiar' | 'Legal' | 'Cobranza';
  tipo: 'Celular' | 'Telefono_fijo' | 'Email' | 'Direccion' | 'Otro';
  detalle: string;
  validacion: boolean;
  principal: boolean;
  observacion: string | null;
  usuario_validacion_id: number | null;
  fecha_validacion: string | null; // ISO Date string
  created_at: string;
  updated_at: string;
  debtor: {
    id: number;
    // ... otros campos del deudor que quieras mostrar
    name: string; // Asumiendo que el deudor tiene un campo 'name'
  };
  usuario_validacion?: { // Opcional, si siempre se carga la relación
    id: number;
    name: string;
  }
}

interface PageProps {
  contacts: {
    data: Contact[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
  };
  filters: {
    search: string;
    categoria: string[];
    tipo: string[];
    validacion: boolean | null;
    principal: boolean | null;
    debtor_id: number | null;
  };
}

const page = usePage<PageProps>();

// Propiedades recibidas del controlador
const contacts = computed(() => page.props.contacts.data);
const pagination = computed(() => page.props.contacts); // Contiene current_page, last_page, total, etc.

// Opciones para los filtros de tipo Enum
const categoriasOptions = ['Personal', 'Trabajo', 'Referencia', 'Familiar', 'Legal', 'Cobranza'];
const tiposOptions = ['Celular', 'Telefono_fijo', 'Email', 'Direccion', 'Otro'];
const booleanOptions = [
  { label: 'Sí', value: true },
  { label: 'No', value: false }
];

// Estado local para los filtros, inicializado con los valores del backend
const localFilters = ref({
  search: page.props.filters.search || '',
  categoria: page.props.filters.categoria || [],
  tipo: page.props.filters.tipo || [],
  validacion: page.props.filters.validacion, // Puede ser true, false o null
  principal: page.props.filters.principal,   // Puede ser true, false o null
  debtor_id: page.props.filters.debtor_id || null,
});

// Función para aplicar los filtros
const applyFilters = (pageNumber = 1) => {
  router.get(route('contacts.index'), {
    ...localFilters.value,
    page: pageNumber,
    // Limpiar valores nulos o vacíos para no enviarlos como cadenas
    categoria: localFilters.value.categoria.length ? localFilters.value.categoria : undefined,
    tipo: localFilters.value.tipo.length ? localFilters.value.tipo : undefined,
    // Convertir booleanos a 0/1 si el backend lo espera así, o dejar como está
    validacion: localFilters.value.validacion !== null ? localFilters.value.validacion : undefined,
    principal: localFilters.value.principal !== null ? localFilters.value.principal : undefined,
    debtor_id: localFilters.value.debtor_id || undefined,
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
};

// Observar cambios en los filtros para aplicar automáticamente (opcional, si no quieres un botón)
// watch(localFilters, () => {
//   applyFilters();
// }, { deep: true });

// Paginación: cuando se hace click en un enlace de paginación
const onPageChange = (event: { page: number; pageCount: number; }) => {
  applyFilters(event.page + 1); // PrimeVue pages son 0-based
};

// Función para limpiar todos los filtros
const clearFilters = () => {
  localFilters.value = {
    search: '',
    categoria: [],
    tipo: [],
    validacion: null,
    principal: null,
    debtor_id: null,
  };
  applyFilters();
};

</script>

<template>
  <AppLayout title="Contactos">
    <Head title="Contactos" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestión de Contactos
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros de Contactos</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 🔍 Búsqueda General -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700">Buscar (Detalle/Observación)</label>
              <InputText
                id="search"
                v-model="localFilters.search"
                placeholder="Buscar..."
                class="mt-1 block w-full"
                @keyup.enter="applyFilters()"
              />
            </div>

            <!-- 🎯 Filtro por Categoría -->
            <div>
              <label for="categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
              <MultiSelect
                id="categoria"
                v-model="localFilters.categoria"
                :options="categoriasOptions"
                placeholder="Seleccionar categoría(s)"
                display="chip"
                class="mt-1 block w-full"
              />
            </div>

            <!-- 🎯 Filtro por Tipo -->
            <div>
              <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
              <MultiSelect
                id="tipo"
                v-model="localFilters.tipo"
                :options="tiposOptions"
                placeholder="Seleccionar tipo(s)"
                display="chip"
                class="mt-1 block w-full"
              />
            </div>

            <!-- 🎯 Filtro por Validación -->
            <div>
              <label for="validacion" class="block text-sm font-medium text-gray-700">Validado</label>
              <SelectButton
                id="validacion"
                v-model="localFilters.validacion"
                :options="[{label: 'Sí', value: true}, {label: 'No', value: false}]"
                optionLabel="label"
                optionValue="value"
                class="mt-1 block w-full"
              />
              <Button v-if="localFilters.validacion !== null" icon="pi pi-times" class="p-button-rounded p-button-text p-button-sm" @click="localFilters.validacion = null" />
            </div>

            <!-- 🎯 Filtro por Principal -->
            <div>
              <label for="principal" class="block text-sm font-medium text-gray-700">Principal</label>
              <SelectButton
                id="principal"
                v-model="localFilters.principal"
                :options="[{label: 'Sí', value: true}, {label: 'No', value: false}]"
                optionLabel="label"
                optionValue="value"
                class="mt-1 block w-full"
              />
              <Button v-if="localFilters.principal !== null" icon="pi pi-times" class="p-button-rounded p-button-text p-button-sm" @click="localFilters.principal = null" />
            </div>

            <!-- 🎯 Filtro por ID de Deudor -->
            <div>
              <label for="debtor_id" class="block text-sm font-medium text-gray-700">ID Deudor</label>
              <InputText
                id="debtor_id"
                v-model.number="localFilters.debtor_id"
                type="number"
                placeholder="ID del Deudor"
                class="mt-1 block w-full"
                @keyup.enter="applyFilters()"
              />
            </div>

          </div>

          <div class="flex justify-end space-x-2 mb-6">
            <Button label="Limpiar Filtros" icon="pi pi-filter-slash" class="p-button-outlined" @click="clearFilters" />
            <Button label="Aplicar Filtros" icon="pi pi-filter" @click="applyFilters()" />
          </div>

          <DataTable
            :value="contacts"
            dataKey="id"
            responsiveLayout="scroll"
            class="p-datatable-sm"
            :rows="pagination.per_page"
            :first="(pagination.current_page - 1) * pagination.per_page"
            :totalRecords="pagination.total"
            @page="onPageChange"
            paginator
            :lazy="true"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
            currentPageReportTemplate="{first}-{last} de {totalRecords}"
            :rowsPerPageOptions="[10, 25, 50]"
          >
            <Column field="id" header="ID" sortable></Column>
            <Column header="Deudor">
              <template #body="{ data }">
                <span v-if="data.debtor">{{ data.debtor.name }} (ID: {{ data.debtor.id }})</span>
                <span v-else>N/A</span>
              </template>
            </Column>
            <Column field="categoria" header="Categoría" sortable></Column>
            <Column field="tipo" header="Tipo" sortable></Column>
            <Column field="detalle" header="Detalle" sortable></Column>
            <Column field="validacion" header="Validado">
              <template #body="{ data }">
                <Tag :value="data.validacion ? 'Sí' : 'No'" :severity="data.validacion ? 'success' : 'danger'" />
              </template>
            </Column>
            <Column field="principal" header="Principal">
              <template #body="{ data }">
                <Tag :value="data.principal ? 'Sí' : 'No'" :severity="data.principal ? 'info' : 'secondary'" />
              </template>
            </Column>
            <Column field="observacion" header="Observación"></Column>
            <Column header="Validado por">
              <template #body="{ data }">
                <span v-if="data.usuario_validacion">{{ data.usuario_validacion.name }}</span>
                <span v-else>N/A</span>
              </template>
            </Column>
            <Column field="fecha_validacion" header="Fecha Validación"></Column>
            <!-- Aquí puedes añadir columnas para acciones como Editar/Eliminar -->
            <Column header="Acciones">
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" class="p-button-sm p-button-rounded p-button-info mr-2" />
                    <Button icon="pi pi-trash" class="p-button-sm p-button-rounded p-button-danger" />
                </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.p-datatable .p-datatable-thead > tr > th {
    background-color: #F9FAFB; /* Tailwind gray-50 */
    color: #4B5563; /* Tailwind gray-700 */
    font-weight: 600; /* Tailwind font-semibold */
    padding: 0.75rem 1rem;
}
.p-datatable .p-datatable-tbody > tr > td {
    padding: 0.75rem 1rem;
}
</style>