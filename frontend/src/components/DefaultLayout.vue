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

</style>
