<template>
  <div class="app-wrapper">
    <Navbar />

    <main class="main-content">
      <router-view />
    </main>

    <footer class="footer">
      <div class="copy">© 2025 Opinion. All rights reserved.</div>
    </footer>
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';

export default {
  components: {
    Navbar
  }
}
</script>
