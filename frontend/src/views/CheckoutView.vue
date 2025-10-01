<template>
  <div class="max-w-2xl mx-auto px-4 py-10">
    <h2 v-if="!success" class="text-3xl font-extrabold text-red-700 mb-8 text-center">Számlázási adatok</h2>
    <form v-if="!success" @submit.prevent="submit"
      class="bg-white rounded-2xl shadow-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      <input v-model="firstName" :class="inputClass('firstName')" placeholder="Vezetéknév" />
      <input v-model="lastName" :class="inputClass('lastName')" placeholder="Keresztnév" />
      <input v-model="zipCode" :class="inputClass('zipCode')" placeholder="Irányítószám" />
      <input v-model="city" :class="inputClass('city')" placeholder="Város" />
      <input v-model="street" :class="inputClass('street')" placeholder="Utca, házszám" />
      <input v-model="phone" :class="inputClass('phone')" placeholder="Telefonszám" />
      <input v-model="email" :class="inputClass('email')" placeholder="E-mail" />
      <div class="md:col-span-2 flex justify-center mt-4">
        <button
          class="bg-red-700 hover:bg-black text-white px-8 py-3 rounded-xl text-lg font-bold shadow-md transition cursor-pointer">
          Megrendelés
        </button>
      </div>
      <div v-if="message" class="md:col-span-2 mt-4 text-center">
        <span v-if="error" class="text-red-700 font-semibold text-lg">{{ message }}</span>
        <span v-else class="text-green-700 font-semibold text-lg">{{ message }}</span>
      </div>
    </form>
    <div v-if="success" class="flex flex-col items-center justify-center h-[400px]">
      <span class="text-green-700 text-3xl font-bold mb-4">Sikeres rendelés!</span>
      <span class="text-lg text-gray-700">{{ message }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../services/api'
const router = useRouter()

const firstName = ref('')
const lastName = ref('')
const zipCode = ref('')
const city = ref('')
const street = ref('')
const phone = ref('')
const email = ref('')

const message = ref('')
const error = ref(false)
const errorFields = ref([])
const success = ref(false)

function inputClass(field) {
  return [
    'p-4 rounded-lg text-lg focus:outline-none focus:border-black',
    errorFields.value.includes(field)
      ? 'border border-red-600'
      : 'border border-gray-300'
  ].join(' ')
}

async function submit() {
  message.value = ''
  error.value = false
  errorFields.value = []
  try {
    const res = await api.createOrder({
      firstName: firstName.value,
      lastName: lastName.value,
      zipCode: zipCode.value,
      city: city.value,
      street: street.value,
      phone: phone.value,
      email: email.value
    })
    message.value = 'Rendelés sikeres leadva! Rendelés azonosító: ' + res.orderId
    error.value = false
    success.value = true
    setTimeout(() => {
      router.push('/')
    }, 2000)
  } catch (e) {
    message.value = e.message || 'Hiba történt a rendelés leadása során.'
    error.value = true
    const msg = message.value.toLowerCase()
    errorFields.value = []
    if (msg.includes('minden mező kitöltése kötelező')) {
      const fields = [
        { name: 'firstName', value: firstName.value },
        { name: 'lastName', value: lastName.value },
        { name: 'zipCode', value: zipCode.value },
        { name: 'city', value: city.value },
        { name: 'street', value: street.value },
        { name: 'phone', value: phone.value },
        { name: 'email', value: email.value }
      ]
      errorFields.value = fields.filter(f => !f.value).map(f => f.name)
    } else {
      if (msg.includes('vezetéknév')) errorFields.value.push('firstName')
      if (msg.includes('keresztnév')) errorFields.value.push('lastName')
      if (msg.includes('irányítószám')) errorFields.value.push('zipCode')
      if (msg.includes('város')) errorFields.value.push('city')
      if (msg.includes('utca')) errorFields.value.push('street')
      if (msg.includes('telefonszám')) errorFields.value.push('phone')
      if (msg.includes('e-mail')) errorFields.value.push('email')
    }
  }
}
</script>