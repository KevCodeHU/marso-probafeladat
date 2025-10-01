<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Főoldal</h2>
    <p class="mb-4">Véletlenszerű termékek</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <ProductCard v-for="p in products" :key="p.id" :product="p" @add-to-cart="addToCart" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import { api } from '../services/api'
import { cartCount } from '../store'

const products = ref([])

async function addToCart(productId) {
  await api.addToCart(productId, 1)
  const items = await api.getCart()
  cartCount.value = items?.length || 0
}

onMounted(async () => {
  try {
    const list = await api.listProducts(1, 50)
    if (Array.isArray(list)) {
      products.value = list.sort(() => 0.5 - Math.random()).slice(0, 6)
    } else if (list.items) {
      products.value = list.items.sort(() => 0.5 - Math.random()).slice(0, 6)
    }

    const items = await api.getCart()
    cartCount.value = items?.length || 0
  } catch (e) {
    console.error(e)
  }
})
</script>