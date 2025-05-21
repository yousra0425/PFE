<template>
  <div>
    <h3>Find Providers Near You</h3>
    <Map :providers="providers" />
    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Map from './Map.vue';



const providers = ref([]);
const route = useRoute();

onMounted(async () => {
  const serviceId = route.params.id;
  try {
    // Include serviceId as query param or in URL depending on your backend API design
    const response = await fetch(`http://localhost:8000/api/service-providers/map?service_type=${serviceId}`);
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
