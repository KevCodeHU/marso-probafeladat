<template>
  <div class="max-w-2xl mx-auto px-4 py-10">
    <h2 class="text-3xl font-extrabold text-red-700 mb-8 text-center">Számlázási adatok</h2>
    <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      <input v-model="firstName" placeholder="Vezetéknév" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="lastName" placeholder="Keresztnév" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="zipCode" placeholder="Irányítószám" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="city" placeholder="Város" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="street" placeholder="Utca, házszám" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="phone" placeholder="Telefonszám" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <input v-model="email" placeholder="E-mail" class="p-4 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-500" />
      <div class="md:col-span-2 flex justify-center mt-4">
        <button class="bg-red-700 hover:bg-black text-white px-8 py-3 rounded-xl text-lg font-bold shadow-md transition cursor-pointer">
          Megrendelés
        </button>
      </div>
    </form>
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

async function submit() {
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
    alert('Rendelés leadva. ID: ' + res.orderId)
    router.push('/')
  } catch (e) {
    alert('Hiba: ' + e.message)
  }
}
</script>