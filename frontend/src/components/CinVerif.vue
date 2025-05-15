<template>
  <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light p-4">
    <div class="card shadow p-4 w-100" style="max-width: 500px;">
      <h2 class="text-center mb-4 fw-bold text-success">Verify CIN</h2>

      <form @submit.prevent="submitForm" class="d-grid gap-3">
        <input
          type="file"
          @change="handleFileChange"
          accept="image/*"
          class="form-control"
          required
        />

        <button
          type="submit"
          class="btn btn-success fw-semibold"
          :disabled="!file || loading"
        >
          {{ loading ? 'Verifying...' : 'Upload & Verify' }}
        </button>
      </form>

      <div v-if="cin" class="alert alert-success mt-4 text-center">
        <strong>✅ CIN detected:</strong>
        <div class="fs-5 mt-2">{{ cin }}</div>
      </div>

      <div v-if="errorMessage" class="alert alert-danger mt-4 text-center">
        ❌ {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

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
          }
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
