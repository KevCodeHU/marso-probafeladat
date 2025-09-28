<template>
  <div class="min-h-screen">
    <header class="bg-white shadow">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">MARSO Katalógus</h1>
        <nav class="flex gap-4">

        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-6">
      <router-view />
    </main>

    <footer class="bg-white border-t mt-8">
      <div class="container mx-auto px-4 py-4 text-sm text-gray-500">MARSO - Próbafeladat</div>
    </footer>
  </div>
</template>

<script setup>
</script>
