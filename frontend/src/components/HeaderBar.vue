<template>
  <header class="bg-red-700 shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex items-center justify-between px-6 py-4">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-1 group">
        <img src="/marso-logo.svg" alt="MARSO logo" class="h-9 w-auto" />
        <span class="text-2xl font-extrabold tracking-tight text-white">MARSO - Próbafeladat</span>
      </router-link>

      <!-- Navigáció -->
      <nav class="flex gap-8 items-center">
        <router-link to="/" class="text-base font-medium text-white hover:text-black transition">Főoldal</router-link>
        <router-link to="/products" class="text-base font-medium text-white hover:text-black transition">Termékek</router-link>
        <router-link to="/cart" class="relative flex items-center text-base font-medium text-white hover:text-black transition">
          <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="9" cy="19" r="2" />
            <circle cx="17" cy="19" r="2" />
          </svg>
          Kosár
          <span v-if="cartCount > 0"
            class="ml-2 inline-block bg-white text-red-700 text-xs font-bold px-2 py-0.5 rounded-full shadow animate-bounce">
            {{ cartCount }}
          </span>
        </router-link>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { onMounted } from 'vue'
import { api } from '../services/api'
import { cartCount } from '../store'

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