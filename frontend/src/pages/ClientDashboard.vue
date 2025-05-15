<template>
    <div class="p-4">
      <h1 class="text-2xl font-bold mb-4">Welcome, {{ user.name }}</h1>
      
      <ServiceCategoryList @selectCategory="handleCategorySelect" />
      <NearbyProvidersMap v-if="selectedCategory" :category="selectedCategory" />
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import ServiceCategoryList from '../components/ServiceCategoryList.vue'
  import NearbyProvidersMap from '../components/NearbyProvidersMap.vue'
  
  const router = useRouter()
  const user = ref({})
  const selectedCategory = ref(null)
  
  onMounted(async () => {
    const response = await axios.get('/api/user') // get logged-in user
    user.value = response.data
  })
  
  const handleCategorySelect = (category) => {
    selectedCategory.value = category
  }
  </script>
  