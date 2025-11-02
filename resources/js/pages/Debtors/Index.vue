<script setup lang="ts">
import { ref, reactive } from "vue";
import { router, usePage, Link, useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// Formulario
const formconctact = useForm({
  tipo: "celular",
  categoria: "personal",
  detalle: null,
})

// Tipos

interface Debtor {
  id: number;
  nombre: string;
  identificacion: string;
  nombre_deudor: string;
}

interface Contact {
  id: number;
  nombre: string;
  email: string;
  telefono: string;
}

const props = defineProps<{
  debtors: Debtor[];
  contacts: Contact[];
  debtor: Debtor;
  kevinindexdebtor: string;
}>();

// Estado del deudor seleccionado
const selectedDebtor = ref<Debtor | null>(props.debtor);

// Estado de los contactos asociados
const contacts = ref<Contact[]>(props.contacts);

// Estado de filtros
const debtorFilter = ref("");
const contactFilter = ref("");

// Estado modal
const showContactModal = ref(false);
const editingContact = ref<Contact | null>(null);

// Datos formulario
const form = reactive({
  nombre: "",
  email: "",
  telefono: "",
});



// ------------------------------
// 👁️ Ver contactos de un deudor
// ------------------------------
function viewContacts(debtor: Debtor) {
  selectedDebtor.value = debtor;
  reloadContacts(debtor.id);
}

// ------------------------------
// 🔄 Recargar contactos de un deudor
// ------------------------------
function reloadContacts(debtorId: number) {

  router.get("/debtors/" + debtorId + "/contacts",
    {},
    {
      preserveScroll: false,
      preserveState: true,
      replace: false,
      preserveUrl: true,
      only: ["contacts"],
      onBefore: () => confirm('recargando contactos?'),
      onSuccess: (page) => {
        console.log("Contacts reloaded:", page.props.contacts);
        contacts.value = page.props.contacts as any[];
      },
    }
  );
}

// ------------------------------
// ➕ Abrir modal nuevo contacto
// ------------------------------
function openCreateModal() {
  editingContact.value = null;
  form.nombre = "";
  form.email = "";
  form.telefono = "";
  showContactModal.value = true;
}

// ✏️ Abrir modal editar contacto
function openEditModal(contact: any) {
  editingContact.value = contact;
  formconctact.tipo = contact.tipo;
  formconctact.categoria = contact.categoria;
  formconctact.detalle = contact.detalle;
  showContactModal.value = true;
}

// ------------------------------
// 💾 Guardar contacto (create o update)
// ------------------------------
function saveContact() {
  if (!selectedDebtor.value) return;

  if (editingContact.value) {
    // update
    formconctact.put("/debtors/" + selectedDebtor.value.id + "/contacts/" + editingContact.value.id,
      {
        preserveScroll: true,
        preserveUrl: true,
        preserveState: true,
        replace: false,
        onError: (errors) => console.log('error: ' + JSON.stringify(errors)),
        onSuccess: () => {
          showContactModal.value = false;
          //reloadContacts(selectedDebtor.value!.id);
        },
      }
    );
  } else {
    // create
    formconctact.post(
      "debtors/" + selectedDebtor.value.id + "/contacts",
      {
        preserveScroll: true,
        preserveUrl: true,
        preserveState: true,
        replace: false,
        only: ["contacts"],
        onBefore: () => confirm('guardar contacto?'),
        onError: (errors) => confirm('error: ' + JSON.stringify(errors)),
        onSuccess: () => {
          showContactModal.value = false;
          //reloadContacts(selectedDebtor.value!.id);
        },
      }
    );
  }
}

// ------------------------------
// 🗑️ Eliminar contacto
// ------------------------------
function deleteContact(contact: Contact) {
  if (!selectedDebtor.value) return;

  if (confirm("¿Seguro deseas eliminar este contacto?")) {
    router.delete("/debtors/" + selectedDebtor.value.id + "/contacts/" + contact.id, {
      preserveScroll: true,
      preserveUrl: true,
      preserveState: true,
      replace: false,
      onSuccess: () => {
        //reloadContacts(selectedDebtor.value!.id);
      },
    });
  }
}

import { computed } from "vue";

const page = usePage()


const filteredDebtors = computed(() => {
  const debtors: any[] = (page.props.debtors as any[]) || [];
  // si props.debtors no está definido aún, usamos array vacío
  return (debtors).filter(d => (d.nombre_deudor ?? "").toLowerCase().includes(debtorFilter.value.toLowerCase()));
});




const filteredContacts = computed(() => {
  const data: any[] = (page.props.contacts as any[]) || [];
  return (data).filter(c => (c.nombre ?? "").toLowerCase().includes(contactFilter.value.toLowerCase()));
});


</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Tabla de Deudores -->
    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-bold mb-3">Deudores</h2>

      <InputText v-model="debtorFilter" placeholder="Buscar deudor..." class="mb-3 w-full" />

      <DataTable :value="filteredDebtors">
        <Column field="tipo_cedente" header="Nombre" />
        <Column field="nombre_deudor" header="Identificación" />
        <Column field="monto_deuda_total" header="Monto deuda total" />

        <Column header="Opciones">

          <template #body="{ data }">
            <Button label="Ver" icon="pi pi-eye" size="small" @click="viewContacts(data)" />
          </template>
        </Column>

      </DataTable>
    </div>

    <!-- Tabla de Contactos -->
    <div v-if="selectedDebtor" class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-bold mb-3">
        Contactos de {{ selectedDebtor.nombre_deudor }}
      </h2>

      <div class="flex justify-between mb-3">
        <InputText v-model="contactFilter" placeholder="Buscar contacto..." class="w-1/2" />
        <Button label="Nuevo contacto" icon="pi pi-plus" @click="openCreateModal" />
      </div>

      <DataTable :value="filteredContacts">
        <Column field="categoria" header="categoria" />
        <Column field="detalle" header="detalle" />
        <Column field="tipo" header="tipo" />

        <Column header="Opciones">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" size="small" @click="openEditModal(data)" />
              <Button icon="pi pi-trash" severity="danger" size="small" @click="deleteContact(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- Modal de Crear/Editar Contacto -->
    <Dialog v-model:visible="showContactModal" modal header="Crear o Editar Contacto" :style="{ width: '30rem' }">
      <div class="space-y-3">
        que pasa aqui
        <InputText v-model="formconctact.tipo" placeholder="Nombre" class="w-full" />
        <InputText v-model="formconctact.categoria" placeholder="Email" class="w-full" />
        <InputText v-model="formconctact.detalle" placeholder="Teléfono" class="w-full" />
        aqui
        <div v-if="formconctact.errors.detalle">{{ formconctact.errors.detalle }}</div>

      </div>

      <template #footer>
        <Button label="Cancelar" text @click="showContactModal = false" />
        <Button label="Guardar" icon="pi pi-check" @click="saveContact" />
      </template>
    </Dialog>
  </div>
</template>
