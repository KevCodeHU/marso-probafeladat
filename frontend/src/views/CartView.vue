<template>
  <div class="max-w-3xl mx-auto px-4 py-10">
    <h2 class="text-3xl font-extrabold text-red-700 mb-8 text-center">Kosár</h2>
    <div v-if="items.length === 0" class="bg-white rounded-2xl shadow p-8 text-center text-lg text-gray-500">
      A kosár üres.
    </div>
    <ul v-else class="bg-white rounded-2xl shadow-xl p-6 divide-y divide-gray-100 mb-8">
      <li v-for="i in items" :key="i.id" class="flex gap-6 items-center py-4">
        <!-- Termék kép -->
        <img :src="i.product?.image" alt=""
          class="h-16 w-16 object-contain rounded-lg border border-gray-200 bg-gray-50" />
        <!-- Termék név -->
        <div class="flex-1 font-semibold text-gray-900">{{ i.product?.name }}</div>
        <!-- Mennyiség -->
        <div class="flex items-center gap-2">
          <button @click="decrease(i)"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded font-bold text-lg transition cursor-pointer">-</button>
          <span class="font-bold text-base">{{ i.quantity }}</span>
          <button @click="increase(i)"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded font-bold text-lg transition cursor-pointer">+</button>
        </div>
        <!-- Törlés -->
        <button @click="remove(i.id)"
          class="bg-red-700 hover:bg-black text-white px-4 py-2 rounded-lg font-semibold shadow transition cursor-pointer">
          Töröl
        </button>
      </li>
    </ul>
    <div v-if="items.length > 0" class="flex justify-center gap-4">
      <router-link to="/checkout"
        class="bg-red-700 hover:bg-black text-white px-8 py-3 rounded-xl text-lg font-bold shadow-md transition cursor-pointer">
        Folytatás
      </router-link>
      <button @click="clear"
        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-xl text-lg font-bold shadow-md transition cursor-pointer">
        Kosár ürítése
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../services/api'
import { cartCount } from '../store'

const items = ref([])

async function load() {
  try {
    let cart = await api.getCart()
    if (!Array.isArray(cart)) cart = cart.items || []

    for (const item of cart) {
      item.product = await api.getProduct(item.productId)
    }
    items.value = cart
    cartCount.value = items.value.length
  } catch (e) { console.error(e); items.value = []; cartCount.value = 0 }
}

async function remove(id) {
  try {
    await api.removeCartItem(id);
    load()
  } catch (e) { alert(e.message) }
}
async function clear() {
  try {
    await api.clearCart(); load()
  } catch (e) { alert(e.message) }
}
async function increase(item) {
  try {
    await api.addToCart(item.productId, 1)
    load()
  } catch (e) { alert(e.message) }
}
async function decrease(item) {
  if (item.quantity > 1) {
    try {
      await api.addToCart(item.productId, -1)
      load()
    } catch (e) { alert(e.message) }
  } else {
    remove(item.id)
  }
}

onMounted(load)
</script>