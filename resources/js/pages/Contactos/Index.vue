<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, usePage, router ,Form} from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

// ✅ Datos y filtros
const { props } = usePage<{ contacts: any, filters: any }>();
const contacts = ref(props.contacts);
const filters = ref({
  search: props.filters.search || '',
  categoria: props.filters.categoria || [],
  tipo: props.filters.tipo || [],
  validacion: props.filters.validacion ?? null,
  principal: props.filters.principal ?? null,
  debtor_id: props.filters.debtor_id || null,
});

// 🎯 Función para aplicar filtros
async function applyFilters() {
  try {
    // Convertir el objeto filters a parámetros de URL
    const params = new URLSearchParams();

    Object.entries(filters.value).forEach(([key, value]) => {
      if (value !== null && value !== "" && value.length !== 0) {
        if (Array.isArray(value)) {
          value.forEach((v) => params.append(`${key}[]`, v));
        } else {
          params.append(key, value);
        }
      }
    });

    const url = `/contactos?${params.toString()}`;

    const response = await fetch(url, {
      method: "GET",
      headers: {
        "Accept": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`Error: ${response.status}`);
    }

    const data = await response.json();
    console.log("✅ Datos recibidos:", data);

  } catch (error) {
    console.error("❌ Error en fetch:", error);
  }
}

// Opciones para dropdowns
const categorias = [
  'personal', 'trabajo', 'referencia', 'familiar', 'legal', 'cobranza'
];
const tipos = [
  'celular', 'telefono_fijo', 'email', 'direccion', 'otro'
];
</script>

<template>

  <Head title="Contacts" />

  <div class="p-4 space-y-4">

    <!-- Filtros -->
    <div class="flex flex-wrap gap-4 items-end">
      <InputText v-model="filters.search" placeholder="Buscar detalle u observación" class="w-64" />
      <Dropdown v-model="filters.categoria" :options="categorias" multiple placeholder="Categoría" class="w-48" />
      <Dropdown v-model="filters.tipo" :options="tipos" multiple placeholder="Tipo" class="w-48" />
      <Checkbox v-model="filters.validacion" :true-value="true" :false-value="false" label="Validación" />
      <Checkbox v-model="filters.principal" :true-value="true" :false-value="false" label="Principal" />
      <Button label="Aplicar filtros" icon="pi pi-filter" class="p-button-success" @click="applyFilters" />
    </div>

    <!-- Tabla -->
    <DataTable :value="contacts.data" paginator :rows="10" :totalRecords="contacts.total">
      <Column field="tipo" header="Tipo"></Column>
      <Column field="categoria" header="Categoría"></Column>
      <Column field="detalle" header="Detalle"></Column>
      <Column field="validacion" header="Validación">
        <template #body="slot">
          <span class="px-2 py-1 rounded text-white" :class="slot.data.validacion ? 'bg-green-500' : 'bg-gray-400'">
            {{ slot.data.validacion ? 'Sí' : 'No' }}
          </span>
        </template>
      </Column>
      <Column field="principal" header="Principal">
        <template #body="slot">
          <span class="px-2 py-1 rounded text-white" :class="slot.data.principal ? 'bg-blue-500' : 'bg-gray-400'">
            {{ slot.data.principal ? 'Sí' : 'No' }}
          </span>
        </template>
      </Column>
      <Column field="observacion" header="Observación"></Column>
    </DataTable>

  </div>
</template>

<style scoped>
/* Opcional: estilos extra */
</style>
