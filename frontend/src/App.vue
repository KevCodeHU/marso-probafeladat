<template>
  <div class="min-h-screen">

          <HeaderBar />


    <main class="container mx-auto px-4 py-6">

      <router-view />
    </main>

    <footer class="bg-white border-t mt-8">
      <div class="container mx-auto px-4 py-4 text-sm text-gray-500">MARSO - Próbafeladat</div>
    </footer>
  </div>
</template>

<script setup>
import HeaderBar from './components/HeaderBar.vue';
</script>
