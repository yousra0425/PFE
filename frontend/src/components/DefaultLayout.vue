<script setup>
import Header from './Header.vue'
import Footer from './Footer.vue'
</script>

<template>
  <div class="layout">
    <Header />

    <main class="content">
      <RouterView />
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content {
  flex: 1;
  padding-bottom: 2rem; /* Optional: to avoid overlap with footer */
}
</style>
