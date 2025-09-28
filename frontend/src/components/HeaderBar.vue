<template>
  <header class="bg-white shadow">
    <div class="container mx-auto flex items-center justify-between px-4 py-4">
      <div class="text-xl font-bold">MARSO Katalógus</div>

      <nav class="flex gap-6">
        <router-link to="/" class="hover:text-blue-600">Főoldal</router-link>
        <router-link to="/products" class="hover:text-blue-600">Termékek</router-link>
        <router-link to="/cart" class="hover:text-blue-600 relative">
          Kosár
          <span v-if="cartCount > 0" class="ml-1 inline-block bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
            {{ cartCount }}
          </span>
        </router-link>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../services/api'

const cartCount = ref(0)

async function loadCart() {
  try {
    const items = await api.getCart()
    cartCount.value = items?.length || 0
  } catch (err) {
    console.error('Error fetching cart:', err)
    cartCount.value = 0
  }
}

onMounted(loadCart)
</script>