<template>
  <div>
    <Header />
    <h3>Find Providers Near You</h3>
    <Map :providers="providers" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Map from './Map.vue';
import Header from './Header.vue'

const providers = ref([]);

onMounted(async () => {
  try {
    const response = await fetch('/api/service-providers/map'); // your API endpoint
    if (!response.ok) throw new Error('Failed to fetch providers');
    providers.value = await response.json();
  } catch (error) {
    console.error('Error loading providers:', error);
  }
});
</script>

<style scoped>

h3 {
  font-weight: 700;
  font-size: 1.6rem;
  color: #2c3e50;
  margin: 0;
  text-align: center;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--primary-color); /* subtle underline */
}


</style>
