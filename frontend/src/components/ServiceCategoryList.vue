<template>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4">
      <div v-for="service in services" :key="service.id" @click="selectService(service.name)" class="cursor-pointer p-4 shadow rounded-xl hover:bg-blue-100">
        <img :src="service.icon" class="h-12 mx-auto" />
        <p class="text-center mt-2">{{ service.name }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  
  const services = [
    { id: 1, name: 'Plumbing', icon: '/icons/plumbing.png' },
    { id: 2, name: 'Electrical', icon: '/icons/electrical.png' },
    { id: 3, name: 'Cleaning', icon: '/icons/cleaning.png' },
    // Add more services
  ];
  
  const router = useRouter();
  const selectService = (service) => {
    router.push({ path: '/map', query: { service } });
  };
  </script>