<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full space-y-6">
      <h2 class="text-2xl font-bold text-center text-gray-800">Verify CIN</h2>

      <form @submit.prevent="submitForm" class="space-y-4">
        <input
          type="file"
          @change="handleFileChange"
          accept="image/*"
          class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400"
          required
        />

        <button
          type="submit"
          :disabled="!file || loading"
          class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200"
        >
          {{ loading ? 'Verifying...' : 'Upload & Verify' }}
        </button>
      </form>

      <div v-if="cin" class="mt-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
        <p class="text-center font-semibold">✅ CIN detected:</p>
        <p class="text-center text-xl mt-2">{{ cin }}</p>
      </div>

      <div v-if="errorMessage" class="mt-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
        <p class="text-center">❌ {{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      file: null,
      cin: null,
      errorMessage: null,
      loading: false
    };
  },
  methods: {
    handleFileChange(event) {
      this.file = event.target.files[0];
      this.cin = null;
      this.errorMessage = null;
    },
    async submitForm() {
      if (!this.file) return;

      this.loading = true;
      const formData = new FormData();
      formData.append('cin_image', this.file);

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/verify-cin', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          withCredentials: true,
        });

        this.cin = response.data.cin;
        this.errorMessage = null;
      } catch (error) {
        this.errorMessage = error.response?.data?.error || 'An unexpected error occurred.';
        this.cin = null;
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
