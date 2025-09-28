<template>
  <div class="bg-white p-4 rounded shadow">
    <img :src="product.image" alt="" class="w-full h-40 object-contain mb-3" />
    <h3 class="font-semibold text-sm">{{ product.name }}</h3>
    <p class="text-xs text-gray-500">{{ product.category }}</p>
    <div class="mt-3 flex items-center justify-between">
      <div class="text-lg font-bold">{{ formatPrice(product.price) }} Ft</div>
      <router-link :to="{ name: 'product', params: { id: product.id } }" class="text-blue-600 text-sm">Részletek</router-link>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ product: Object })
const formatPrice = (p) => (p == null ? '' : Number(p).toLocaleString('hu-HU'))
</script>
