<template>
    <div class="step-four-container">
      <!-- Stepper -->
      <div class="stepper-wrapper">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: '80%' }"></div>
        </div>
        <div class="stepper">
          <div class="step completed">
            <div class="circle"><span class="checkmark">✓</span></div>
            <div class="label">Personal Info</div>
          </div>
          <div class="step completed">
            <div class="circle"><span class="checkmark">✓</span></div>
            <div class="label">Teaching Info</div>
          </div>
          <div class="step completed">
            <div class="circle"><span class="checkmark">✓</span></div>
            <div class="label">Documents</div>
          </div>
          <div class="step active pulse">
            <div class="circle">4</div>
            <div class="label">Finish</div>
          </div>
        </div>
      </div>
  
      <!-- Glass Card -->
      <div class="info-card fade-slide-enter-active">
        <div class="card-header">
          <h2 class="card-title">Finalize Your Profile</h2>
          <p class="card-subtitle">Review and confirm your information before submission</p>
        </div>
  
        <form class="profile-form" @submit.prevent="handleSubmit">
          <div class="form-group">
            <label class="form-label">Additional Notes (Optional)</label>
            <textarea
              class="form-control"
              rows="4"
              v-model="form.notes"
              placeholder="Any extra information you'd like to add..."
            ></textarea>
          </div>
  
          <div class="form-actions">
            <button class="btn btn-secondary" @click.prevent="goBack">
              <i class="btn-icon">←</i> Back
            </button>
            <button class="btn btn-primary" :disabled="loading" @click.prevent="handleSubmit">
              <span v-if="loading" class="spinner"></span>
              <span v-else>Submit</span>
              <i class="btn-icon">✓</i>
            </button>
          </div>
        </form>
  
        <div class="form-progress">
          <div class="progress-text">Step 4 of 4</div>
          <div class="progress-dots">
            <span class="dot completed"></span>
            <span class="dot completed"></span>
            <span class="dot completed"></span>
            <span class="dot active"></span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const emit = defineEmits(['submit', 'back'])
  
  const form = ref({
    notes: ''
  })
  
  const loading = ref(false)
  
  function handleSubmit() {
    loading.value = true
    setTimeout(() => {
      loading.value = false
      emit('submit', { notes: form.value.notes })
    }, 1000)
  }
  
  function goBack() {
    emit('back')
  }
  </script>
  
  <style scoped>
  /* Reuse most styles from step 3, minimal changes */
  :root {
    --success-color: #10b981;
    --error-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-light: #f9fafb;
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  }
  
  .step-four-container {
    padding: 2rem 1rem;
    max-width: 800px;
    margin: auto;
    background: var(--primary-color);
  }
  
  .stepper-wrapper {
    margin-bottom: 2rem;
  }
  
  .progress-bar {
    height: 4px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    margin-bottom: 1rem;
    overflow: hidden;
  }
  
  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-color), var(--success-color));
    transition: width 0.6s;
    border-radius: 2px;
  }
  
  .stepper {
    display: flex;
    justify-content: space-between;
  }
  
  .step {
    flex: 1;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
  }
  
  .step .circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin: auto;
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }
  
  .step.active .circle {
    background-color: white;
    color: var(--primary-color);
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
  }
  
  .step.completed .circle {
    background-color: var(--success-color);
    color: white;
  }
  
  .step.pulse .circle {
    animation: pulse 2s infinite;
  }
  
  .step .label {
    margin-top: 12px;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
  }
  
  .step.active .label {
    color: white;
    font-weight: 600;
  }
  
  .checkmark {
    font-size: 1.2rem;
    font-weight: bold;
  }
  
  .info-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: var(--shadow-xl);
    padding: 2.5rem;
    transition: all 0.4s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .card-header {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .card-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
  }
  
  .card-subtitle {
    color: var(--text-secondary);
  }
  
  .profile-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }
  
  .form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  
  .form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    background-color: white;
    resize: vertical;
  }
  
  .form-actions {
    display: flex;
    justify-content: space-between;
  }
  
  .btn {
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
  }
  
  .btn-primary {
    background: var(--primary-color);
    color: white;
    flex: 1;
    justify-content: center;
  }
  
  .btn-primary:hover {
    background: var(--primary-hover);
  }
  
  .btn-secondary {
    background-color: rgb(219, 219, 219);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
  }
  
  .btn-secondary:hover {
    background: #d5d6da;
  }
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: var(--primary-color);
  }
  
  .spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  
  .form-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
  }
  
  .progress-text {
    color: var(--text-secondary);
    font-size: 0.9rem;
  }
  
  .progress-dots {
    display: flex;
    gap: 0.5rem;
  }
  
  .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--border-color);
  }
  
  .dot.active {
    background-color: var(--success-color);
    transform: scale(1.2);
  }
  
  .dot.completed {
    background-color: var(--success-color);
  }
  
  .fade-slide-enter-active {
    animation: fadeInUp 0.6s ease;
  }
  
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes pulse {
    0%, 100% {
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
      box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
  }
  </style>
  