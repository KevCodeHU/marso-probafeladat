<template>
  <div>
    <!-- Keresés és szűrés -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <h2 class="text-2xl font-bold">Termékek</h2>
      <div class="flex gap-2 flex-wrap">
        <input v-model="q" placeholder="Keresés..." class="p-2 border rounded" @keyup.enter="search" />
        <select v-model="type" class="p-2 border rounded">
          <option value="">Évszak</option>
          <option value="winter">Téli</option>
          <option value="summer">Nyári</option>
          <option value="all-season">4 évszak</option>
        </select>
        <select v-model.number="diameter" class="p-2 border rounded w-32">
          <option value="">Átmérő</option>
          <option v-for="d in diameters" :key="d" :value="d">{{ d }}</option>
        </select>
        <button @click="search" class="bg-blue-600 text-white px-4 py-2 rounded">Szűrés</button>
      </div>
    </div>

    <!-- Termékek listája -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
      <ProductCard v-for="p in pagedProducts" :key="p.id" :product="p" @add-to-cart="addToCart" />
    </div>

    <!-- Lapozás -->
    <div class="mt-6 flex justify-center gap-2">
      <button @click="prevPage" :disabled="page === 1"
        class="px-3 py-1 border rounded disabled:opacity-50">Előző</button>
      <span class="px-3 py-1">{{ page }} / {{ totalPages }}</span>
      <button @click="nextPage" :disabled="page === totalPages"
        class="px-3 py-1 border rounded disabled:opacity-50">Következő</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import { api } from '../services/api'
import { cartCount } from '../store'

const q = ref('')
const type = ref('')
const diameter = ref(null)
const allProducts = ref([])
const page = ref(1)
const perPage = 20

const filterQ = ref('')
const filterType = ref('')
const filterDiameter = ref(null)

// Termékek szűrése
const filteredProducts = computed(() => {
  return allProducts.value.filter(p => {
    // név szerinti keresés
    if (filterQ.value && !p.name.toLowerCase().includes(filterQ.value.toLowerCase())) return false
    // évszak
    if (filterType.value && p.type?.toLowerCase() !== filterType.value.toLowerCase()) return false
    // átmérő
    if (filterDiameter.value && p.diameter !== filterDiameter.value) return false
    return true
  })
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage))
const pagedProducts = computed(() => {
  const start = (page.value - 1) * perPage
  const end = start + perPage
  return filteredProducts.value.slice(start, end)
})

async function load() {
  try {
    const res = await api.listProducts()
    if (Array.isArray(res)) allProducts.value = res
    else if (res.items) allProducts.value = res.items
    else allProducts.value = []
  } catch (e) { console.error(e) }
}

function search() {
  filterQ.value = q.value
  filterType.value = type.value
  filterDiameter.value = diameter.value
  page.value = 1
}
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function nextPage() {
  if (page.value < totalPages.value) {
    page.value++
    scrollToTop()
  }
}
function prevPage() {
  if (page.value > 1) {
    page.value--
    scrollToTop()
  }
}

async function addToCart(productId) {
  await api.addToCart(productId, 1)
  const items = await api.getCart()
  cartCount.value = items?.length || 0
}

onMounted(load)

search()

const diameters = computed(() => {
  const set = new Set(
    allProducts.value
      .map(p => {
        if (p.diameter) return Number(p.diameter)
      })
      .filter(Boolean)
  )
  return Array.from(set).sort((a, b) => a - b)
})
</script>
