<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Navbar />
    <main class="flex-1">
      <router-view />
    </main>
    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-100">
© {{ new Date().getFullYear() }} Opinion · Share your thoughts    </footer>
  </div>
</template>
<script>
import Navbar from './src/components/Navbar.vue';
export default { components: { Navbar } };
</script>
