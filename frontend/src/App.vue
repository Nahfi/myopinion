<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Navbar />
    <main class="flex-1">
      <router-view />
    </main>
    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-100">
      © 2025 Opinion · Share your thoughts
    </footer>
  </div>
</template>
<script>
import Navbar from './components/Navbar.vue';
export default { components: { Navbar } };
</script>
