<script setup lang="ts">
import { ref } from 'vue'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import Button from 'primevue/button'

interface Props {
  visible: boolean
  cedentes: string[]
}
interface Emits {
  (e: 'update:visible', v: boolean): void
  (e: 'guardar', data: DeudorForm): void
}
interface DeudorForm {
  cedula: string
  nombre: string
  cedente: string | null
  tipoCedente: string | null
  monto: number | null
  telefono: string
  celular: string
  email: string
  direccion: string
  gestor: string | null
  judicial: string | null
  observaciones: string
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const formData = ref<DeudorForm>({
  cedula: '', nombre: '', cedente: null, tipoCedente: null, monto: null,
  telefono: '', celular: '', email: '', direccion: '',
  gestor: null, judicial: null, observaciones: ''
})

const opciones = {
  tiposCedente: ['Institución Financiera', 'Privada', 'Pública'],
  gestores: ['Gestor A', 'Gestor B', 'Gestor C'],
  judicial: ['Sí', 'No']
}

const closeModal = () => emit('update:visible', false)

const guardar = () => {
  const f = formData.value
  if (!f.cedula || !f.nombre || !f.cedente || !f.monto) {
    alert('Complete los campos obligatorios')
    return
  }
  emit('guardar', f)
  reset()
  closeModal()
}

const reset = () => Object.assign(formData.value, {
  cedula: '', nombre: '', cedente: null, tipoCedente: null, monto: null,
  telefono: '', celular: '', email: '', direccion: '',
  gestor: null, judicial: null, observaciones: ''
})
</script>

<template>
  <Dialog :visible="visible" @update:visible="closeModal" modal maximizable header="Nuevo Deudor" :style="{ width: '45rem' }">
    <form @submit.prevent="guardar" class="p-fluid space-y-4">
      <!-- Información Básica -->
      <section>
        <h3 class="font-semibold text-lg mb-2">Información Básica</h3>
        <div class="grid md:grid-cols-2 gap-3">
          <InputText v-model="formData.cedula" placeholder="Cédula / RUC *" />
          <InputText v-model="formData.nombre" placeholder="Nombre / Razón Social *" />
          <Select v-model="formData.cedente" :options="cedentes" placeholder="Cliente *" />
          <Select v-model="formData.tipoCedente" :options="opciones.tiposCedente" placeholder="Tipo de Cliente *" />
          <InputText v-model="formData.monto" type="number" placeholder="Monto Total *" />
        </div>
      </section>

      <!-- Detalle de la deuda -->
      <section>
        <h3 class="font-semibold text-lg mb-2">Detalle de la deuda</h3>
        <div class="grid md:grid-cols-2 gap-3">
          <InputText placeholder="Capital" />
          <InputText placeholder="Cuantía total" />
          <InputText placeholder="N° Operación" />
          <InputText placeholder="Origen de la deuda" />
          <InputText placeholder="Días de mora" />
          <InputText placeholder="Fecha de vencimiento" type="date" />
        </div>
      </section>

      <!-- Información de contacto -->
      <section>
        <h3 class="font-semibold text-lg mb-2">Información de Contacto</h3>
        <div class="grid md:grid-cols-2 gap-3">
          <InputText v-model="formData.telefono" placeholder="Teléfono" />
          <InputText v-model="formData.celular" placeholder="Celular" />
          <InputText v-model="formData.email" placeholder="Email" type="email" />
          <Textarea v-model="formData.direccion" rows="2" placeholder="Dirección" />
        </div>
      </section>

      <!-- Información adicional -->
      <section>
        <h3 class="font-semibold text-lg mb-2">Información Adicional</h3>
        <div class="grid md:grid-cols-2 gap-3">
          <Select v-model="formData.gestor" :options="opciones.gestores" placeholder="Gestor Asignado" />
          <Select v-model="formData.judicial" :options="opciones.judicial" placeholder="¿Proceso Judicial?" />
          <Textarea v-model="formData.observaciones" rows="2" class="md:col-span-2" placeholder="Observaciones" />
        </div>
      </section>

      <!-- Botones -->
      <div class="flex justify-end gap-2 mt-4">
        <Button type="button" label="Cancelar" severity="secondary" @click="closeModal" />
        <Button type="submit" label="Guardar" icon="pi pi-check" />
      </div>
    </form>
  </Dialog>
</template>
