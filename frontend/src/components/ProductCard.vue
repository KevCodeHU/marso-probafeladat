<template>
  <div
    class="relative bg-white rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col h-full border border-gray-200 group overflow-hidden cursor-pointer">
    <!-- Akció badge -->
    <span v-if="product.discount"
      class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow z-10">AKCIÓ</span>

    <!-- Kép + név + badge-ek -->
    <router-link :to="{ name: 'product', params: { id: product.id } }" class="flex-1 flex flex-col" tabindex="0">
      <div
        class="flex justify-center items-center mb-5 h-44 rounded-xl overflow-hidden border border-gray-100 bg-white group-hover:scale-105 transition-transform duration-300">
        <img :src="product.image" alt=""
          class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-110" loading="lazy" />
      </div>
      <!-- Név 3 részben -->
      <div class="mb-1 flex flex-col gap-1">
        <span class="text-2xl font-extrabold text-red-700">{{ nameParts.brand }}</span>
        <span class="text-xl font-bold text-gray-900">{{ nameParts.category }}</span>
        <span class="text-xl font-mono text-gray-700">{{ nameParts.spec }}</span>
      </div>
      <div class="flex gap-2 mb-3">
        <span v-if="product.type"
          class="bg-black text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm uppercase tracking-wide">{{
            product.type }}</span>
        <span v-if="product.diameter"
          class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">{{ product.diameter
          }}”</span>
      </div>
    </router-link>

    <!-- Ár + Kosár -->
    <div class="mt-auto flex items-center justify-between">
      <div>
        <span class="text-4xl font-extrabold text-red-700 drop-shadow-sm">{{ formatPrice(product.price) }}</span>
        <span class="text-base font-normal text-gray-500 ml-1">Ft</span>
      </div>
      <button @click="$emit('add-to-cart', product.id)"
        class="bg-red-700 hover:bg-red-800 text-white px-6 py-2 rounded-xl text-base font-semibold shadow-md transition cursor-pointer"
        tabindex="0">
        Kosárba
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ product: Object })
const formatPrice = (p) =>
  p == null ? '' : Number(p).toLocaleString('hu-HU')

// Név feldarabolás: márka, kategória, specifikáció
function splitName(name) {
  if (!name) return { brand: '', category: '', spec: '' }
  const parts = name.split(' ')
  let specIndex = parts.findIndex(part => /\d/.test(part) && part.includes('/'))
  if (specIndex === -1) specIndex = parts.findIndex(part => /\d/.test(part))
  if (specIndex === -1) specIndex = parts.length
  const brand = parts[0] || ''
  const category = parts.slice(1, specIndex).join(' ')
  const spec = parts.slice(specIndex).join(' ')
  return { brand, category, spec }
}

const nameParts = splitName(props.product?.name)
</script>