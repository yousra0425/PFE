<template>
  <div class="become-provider-section">
    <div class="content">
      <h2>Become a Service Provider</h2>
      <p>Want to offer your services? Join us as a trusted service provider!</p>
      <button @click="showForm = !showForm" class="apply-btn">
        {{ showForm ? 'Close Application Form' : 'Apply Now' }}
      </button>

      <div v-if="showForm" class="form-container">
        <div class="form-box">
          <h3>Fill out the form to apply</h3>
          <form @submit.prevent="submitApplication">
            <label for="name">Full Name</label>
            <input v-model="form.name" id="name" type="text" required />

            <label for="service">Service Type</label>
            <select v-model="form.service" id="service" required>
              <option value="Plumbing">Plumbing</option>
              <option value="Electrical">Electrical</option>
              <option value="Cleaning">Cleaning</option>
              <option value="Home Improvement">Home Improvement</option>
            </select>

            <label for="experience">Years of Experience</label>
            <input v-model="form.experience" id="experience" type="number" min="1" required />

            <label for="description">Short Description</label>
            <textarea v-model="form.description" id="description" required></textarea>

            <label for="cin-upload">Upload Your CIN (National ID)</label>
            <input
              type="file"
              id="cin-upload"
              accept="image/*"
              @change="handleFileUpload"
              required
            />

            <button type="submit" class="submit-btn">Submit Application</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const showForm = ref(false)
const form = ref({
  name: '',
  service: '',
  experience: '',
  description: '',
  cinFile: null,  // Adding cinFile to store the uploaded file
})

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.cinFile = file // Storing the selected file
  }
}

const submitApplication = () => {
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('service', form.value.service)
  formData.append('experience', form.value.experience)
  formData.append('description', form.value.description)
  formData.append('cin', form.value.cinFile) // Appending the CIN image file

  // Now you can handle sending the form data (including the file) to your backend
  console.log('Form submitted:', formData)

  // Simulate a form submission response (alert for demonstration)
  alert('Your application has been submitted!')

  // Reset form after submission
  form.value = { name: '', service: '', experience: '', description: '', cinFile: null }
  showForm.value = false
}
</script>

<style scoped>
.become-provider-section {
  background-color: var(--light);
  padding: 2rem;
  margin-top: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  text-align: center;
}

h2 {
  font-weight: bolder;
}

.apply-btn {
  background-color: var(--primary-color);
  color: white;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 1rem;
  transition: var(--transition);
}

.apply-btn:hover {
  background-color: var(--primary-hover);
}

.form-container {
  margin-top: 2rem;
  text-align: left;
}

.form-box {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--light-gray);
  max-width: 600px;
  margin: 0 auto;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

label {
  font-size: 0.9rem;
  font-weight: 500;
}

input,
select,
textarea {
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #7c79793b;
  border-radius: 8px;
}

textarea {
  resize: vertical;
  min-height: 120px;
}

.submit-btn {
  background-color: var(--primary-color);
  color: white;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
}

.submit-btn:hover {
  background-color: var(--primary-hover);
}
</style>
