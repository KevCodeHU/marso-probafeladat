<template>
  <div class="max-w-5xl mx-auto px-4 py-8">
    <router-link to="/products"
      class="inline-flex items-center gap-1 text-red-700 hover:text-black font-semibold mb-6 transition">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      Vissza
    </router-link>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 bg-white rounded-2xl shadow-xl p-8">
      <!-- Kép -->
      <div class="flex justify-center items-center">
        <img :src="product.image" alt=""
          class="w-full h-64 object-contain rounded-xl border border-gray-100 bg-gray-50 shadow" />
      </div>
      <!-- Részletek -->
      <div class="md:col-span-2 flex flex-col justify-center">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ product.name }}</h1>
        <div class="flex gap-3 mb-4">
          <span v-if="product.type"
            class="bg-black text-white text-xs px-3 py-1 rounded-full uppercase tracking-wide font-semibold">{{
              product.type }}</span>
          <span v-if="product.diameter" class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-semibold">{{
            product.diameter }}”</span>
        </div>
        <p class="mt-2 text-gray-600 text-base leading-relaxed">{{ product.description }}</p>
        <div class="mt-6 flex items-center gap-6">
          <span class="text-4xl font-extrabold text-red-700 drop-shadow-sm">{{ formatPrice(product.price) }}</span>
          <span class="text-lg font-normal text-gray-500">Ft</span>
        </div>
        <div class="mt-8 flex gap-4 items-center">
          <input v-model.number="qty" type="number" min="1"
            class="p-3 border border-gray-300 rounded-lg w-24 text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-red-500" />
          <button @click="add"
            class="bg-red-700 hover:bg-black text-white px-8 py-3 rounded-xl text-lg font-bold shadow-md transition cursor-pointer">
            Kosárba
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../services/api'
import { cartCount } from '../store'

const route = useRoute()
const router = useRouter()
const product = ref({})
const qty = ref(1)

const formatPrice = (p) =>
  p == null ? '' : Number(p).toLocaleString('hu-HU')

async function load() {
  try {
    product.value = await api.getProduct(route.params.id)
  } catch (e) { console.error(e) }
}

async function add() {
  try {
    await api.addToCart(product.value.id, qty.value)
    const items = await api.getCart()
    cartCount.value = items?.length || 0
    router.push('/cart')
  } catch (e) {
    alert('Hiba: ' + e.message)
  }
}

onMounted(load)
</script>