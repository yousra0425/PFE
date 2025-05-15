<!-- components/admin/CINVerificationPanel.vue -->
<template>
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">Pending CIN Verifications</h2>
  
      <div v-if="loading">Loading...</div>
      <div v-else-if="users.length === 0">No pending requests.</div>
  
      <div
        v-for="user in users"
        :key="user.id"
        class="border p-4 rounded-lg mb-4 shadow"
      >
        <p><strong>Name:</strong> {{ user.first_name }} {{ user.last_name }}</p>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>CIN:</strong> {{ user.cin }}</p>
        <img
          v-if="user.cin_photo"
          :src="`/storage/${user.cin_photo}`"
          alt="CIN"
          class="w-64 my-2 border"
        />
  
        <div class="flex gap-2 mt-2">
          <button @click="verify(user.id, 'approved')" class="bg-green-500 text-white px-4 py-1 rounded">Approve</button>
          <button @click="verify(user.id, 'rejected')" class="bg-red-500 text-white px-4 py-1 rounded">Reject</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios'; // adjust path if needed
  
  const users = ref([]);
  const loading = ref(true);
  
  const fetchPendingUsers = async () => {
    const res = await axios.get('/admin/pending-cin');
    users.value = res.data;
    loading.value = false;
  };
  
  const verify = async (id, status) => {
    await axios.post(`/admin/verify-cin/${id}`, { status });
    users.value = users.value.filter(user => user.id !== id);
  };
  
  onMounted(fetchPendingUsers);
  </script>
  