<template>
    <div class="step-one-container">
      <!-- Enhanced Stepper with Progress Bar -->
      <div class="stepper-wrapper">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressWidth }"></div>
        </div>
        <div class="stepper">
          <div 
            v-for="(step, index) in steps" 
            :key="index" 
            class="step" 
            :class="{ 
              active: index === currentStep, 
              completed: index < currentStep,
              pulse: index === currentStep 
            }"
          >
            <div class="circle">
              <i v-if="index < currentStep" class="checkmark">✓</i>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span class="label">{{ step.title }}</span>
            <div class="connector" v-if="index < steps.length - 1"></div>
          </div>
        </div>
      </div>
  
      <!-- Animated Card with Glass Effect -->
      <transition name="fade-slide" mode="out-in">
        <div class="info-card glass-effect">
          <div class="card-header">
            
            <h2 class="card-title">{{ steps[currentStep].title }}</h2>
            <p class="card-subtitle">{{ steps[currentStep].description }}</p>
          </div>
  
          <!-- Enhanced Avatar Upload with Drag & Drop -->
          <div class="avatar-section">
            <div 
              class="avatar-upload" 
              :class="{ 'drag-over': isDragOver }"
              @dragover.prevent="isDragOver = true"
              @dragleave.prevent="isDragOver = false"
              @drop.prevent="handleDrop"
            >
              <input 
                type="file" 
                id="avatarInput" 
                @change="handleAvatarUpload" 
                accept="image/*"
                hidden 
              />
              <label for="avatarInput" class="avatar-label">
                <div class="avatar-container">
                  <img 
                    :src="avatarPreview || defaultAvatar" 
                    class="avatar-preview"
                    :class="{ 'has-image': avatarPreview }"
                  />
                  <div class="upload-overlay">
                    <span class="upload-text">{{ avatarPreview ? 'Change Photo' : 'Upload Photo' }}</span>
                  </div>
                </div>
              </label>
            </div>
            <div class="avatar-hints">
              <small>Drag & drop or click to upload • Max 5MB • JPG, PNG, GIF</small>
            </div>
          </div>
  
          <!-- Enhanced Form with Better Validation -->
          <form @submit.prevent="submitStep" class="profile-form" novalidate>
            <div class="form-grid">
              <div class="form-group">
                <label for="first_name" class="form-label">
                  First Name *
                  <span class="required-indicator"></span>
                </label>
                <div class="input-wrapper">
                  <input
                    v-model="form.first_name"
                    type="text"
                    class="form-control"
                    :class="{ 'error': errors.first_name, 'success': form.first_name && !errors.first_name }"
                    id="first_name"
                    placeholder="Enter your first name"
                    @blur="validateField('first_name')"
                    @input="clearError('first_name')"
                    required
                  />
                </div>
                <div v-if="errors.first_name" class="error-message">{{ errors.first_name }}</div>
              </div>
  
              <div class="form-group">
                <label for="last_name" class="form-label">
                  Last Name *
                  <span class="required-indicator"></span>
                </label>
                <div class="input-wrapper">
                  <input
                    v-model="form.last_name"
                    type="text"
                    class="form-control"
                    :class="{ 'error': errors.last_name, 'success': form.last_name && !errors.last_name }"
                    id="last_name"
                    placeholder="Enter your last name"
                    @blur="validateField('last_name')"
                    @input="clearError('last_name')"
                    required
                  />
                  
                </div>
                <div v-if="errors.last_name" class="error-message">{{ errors.last_name }}</div>
              </div>
            </div>
  
            <div class="form-group">
              <label for="location" class="form-label">
                Your Location
                <span class="info-tooltip" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
                  <div class="tooltip" v-show="showTooltip">
                    This helps students find tutors in their area or timezone
                  </div>
                </span>
              </label>
              <div class="input-wrapper">
                <input
                  v-model="form.location"
                  type="text"
                  class="form-control"
                  id="location"
                  placeholder="City, Country"
                />
                <div class="field-icon location-icon">🌍</div>
              </div>
            </div>
  
            <div class="form-group">
              <label for="bio" class="form-label">
                Professional Bio
                <span class="char-count">{{ bioCharCount }}/500</span>
              </label>
              <div class="textarea-wrapper">
                <textarea
                  v-model="form.bio"
                  class="form-control"
                  id="bio"
                  rows="4"
                  maxlength="500"
                  placeholder="Tell students about your teaching experience, qualifications, and what makes you unique as a tutor..."
                  @input="updateCharCount"
                ></textarea>
                <div class="textarea-footer">
                  <small class="bio-hint">
                    💡 Tip: Mention your experience, teaching style, and subjects you're passionate about
                  </small>
                </div>
              </div>
            </div>
  
            <!-- Enhanced Action Buttons -->
            <div class="form-actions">
              <button 
                type="button" 
                class="btn btn-secondary" 
                @click="goBack"
                :disabled="currentStep === 0"
              >
                <i class="btn-icon">←</i>
                Back
              </button>
              
              <button 
                class="btn btn-primary" 
                type="submit"
                :disabled="!isFormValid || isSubmitting"
                :class="{ 'loading': isSubmitting }"
              >
                <template v-if="isSubmitting">
                  <i class="spinner"></i>
                  Processing...
                </template>
                <template v-else>
                  Continue
                  <i class="btn-icon">→</i>
                </template>
              </button>
            </div>
          </form>
  
          <!-- Progress Indicator -->
          <div class="form-progress">
            <div class="progress-text">
              Step {{ currentStep + 1 }} of {{ steps.length }}
            </div>
            <div class="progress-dots">
              <div 
                v-for="n in steps.length" 
                :key="n" 
                class="dot" 
                :class="{ active: n - 1 === currentStep, completed: n - 1 < currentStep }"
              ></div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </template>
  
  <script>
  export default {
    name: 'EnhancedProfileSetup',
    data() {
      return {
        currentStep: 0,
        steps: [
          {
            title: 'Personal Info',
            description: 'Tell us about yourself to build your public profile'
          },
          {
            title: 'Teaching Info',
            description: 'What subjects do you teach?'
          },
          {
            title: 'Documents',
            description: 'When are you available to teach?'
          },
          {
            title: 'Finish',
            description: 'Review your information and complete setup'
          }
        ],
        form: {
          first_name: '',
          last_name: '',
          location: '',
          bio: ''
        },
        errors: {},
        avatarPreview: null,
        defaultAvatar: 'https://ui-avatars.com/api/?name=Tutor&background=6366f1&color=fff&size=200',
        isDragOver: false,
        showTooltip: false,
        isSubmitting: false,
        bioCharCount: 0
      };
    },
    computed: {
      progressWidth() {
        return `${((this.currentStep + 1) / this.steps.length) * 100}%`;
      },
      isFormValid() {
        return (
          this.form.first_name.trim() &&
          this.form.last_name.trim() &&
          Object.keys(this.errors).length === 0
        );
      }
    },
    methods: {
      submitStep() {
        if (!this.validateForm()) return;
        
        this.isSubmitting = true;
        
        // Simulate API call
        setTimeout(() => {
          this.isSubmitting = false;
          this.$emit('next', this.form);
        }, 1000);
      },
      
      goBack() {
        this.currentStep = Math.max(this.currentStep - 1, 0);
      },
      
      validateForm() {
        this.errors = {};
        
        if (!this.form.first_name.trim()) {
          this.errors.first_name = 'First name is required';
        } else if (this.form.first_name.length < 2) {
          this.errors.first_name = 'First name must be at least 2 characters';
        }
        
        if (!this.form.last_name.trim()) {
          this.errors.last_name = 'Last name is required';
        } else if (this.form.last_name.length < 2) {
          this.errors.last_name = 'Last name must be at least 2 characters';
        }
        
        return Object.keys(this.errors).length === 0;
      },
      
      validateField(field) {
        if (field === 'first_name') {
          if (!this.form.first_name.trim()) {
            this.errors.first_name = 'First name is required';
          } else if (this.form.first_name.length < 2) {
            this.errors.first_name = 'First name must be at least 2 characters';
          } else {
            delete this.errors.first_name;
          }
        } else if (field === 'last_name') {
          if (!this.form.last_name.trim()) {
            this.errors.last_name = 'Last name is required';
          } else if (this.form.last_name.length < 2) {
            this.errors.last_name = 'Last name must be at least 2 characters';
          } else {
            delete this.errors.last_name;
          }
        }
      },
      
      clearError(field) {
        if (this.errors[field]) {
          delete this.errors[field];
        }
      },
      
      handleAvatarUpload(e) {
        const file = e.target.files[0];
        this.processAvatarFile(file);
      },
      
      handleDrop(e) {
        this.isDragOver = false;
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
          this.processAvatarFile(file);
        }
      },
      
      processAvatarFile(file) {
        if (!file) return;
        
        if (file.size > 5 * 1024 * 1024) {
          alert('File size must be less than 5MB');
          return;
        }
        
        const reader = new FileReader();
        reader.onload = (event) => {
          this.avatarPreview = event.target.result;
        };
        reader.readAsDataURL(file);
      },
      
      updateCharCount() {
        this.bioCharCount = this.form.bio.length;
      }
    }
  };
  </script>
  
  <style scoped>
  :root {
    --success-color: #10b981;
    --error-color: #ef4444;
    --warning-color: #f59e0b;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-light: #f9fafb;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  }
  
  .step-one-container {
    padding: 2rem 1rem;
    max-width: 800px;
    margin: auto;
    min-height: 70vh;
    background: var(--primary-color);
  }
  
  /* Enhanced Stepper */
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
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 2px;
  }
  
  .stepper {
    display: flex;
    justify-content: space-between;
    position: relative;
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
  
  .connector {
    position: absolute;
    top: 24px;
    left: 60%;
    right: -40%;
    height: 2px;
    background-color: rgba(254, 254, 254, 0.844);
    z-index: -1;
  }
  
  .checkmark {
    font-size: 1.2rem;
    font-weight: bold;
  }
  
  /* Glass Effect Card */
  .info-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: var(--shadow-xl);
    padding: 2.5rem;
    transition: all 0.4s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
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
    font-size: 1rem;
    margin: 0;
  }
  
  /* Enhanced Avatar Upload */
  .avatar-section {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .avatar-upload {
    display: inline-block;
    position: relative;
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .avatar-upload.drag-over {
    transform: scale(1.05);
  }
  
  .avatar-container {
    position: relative;
    display: inline-block;
  }
  
  .avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
  }
  
  .avatar-preview.has-image:hover {
    transform: scale(1.05);
  }
  
  .upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
  }
  
  .avatar-container:hover .upload-overlay {
    opacity: 1;
  }
  
  .upload-icon {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
  }
  
  .upload-text {
    font-size: 0.8rem;
    font-weight: 500;
  }
  
  .avatar-hints {
    margin-top: 0.75rem;
  }
  
  .avatar-hints small {
    color: var(--text-secondary);
    font-size: 0.8rem;
  }
  
  /* Enhanced Form */
  .profile-form {
    margin-bottom: 2rem;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  .form-group {
    margin-bottom: 1.5rem;
  }
  
  .form-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  
  .required-indicator {
    width: 6px;
    height: 6px;
    background-color: var(--error-color);
    border-radius: 50%;
    margin-left: 4px;
  }
  
  .char-count {
    margin-left: auto;
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 400;
  }
  
  .input-wrapper {
    position: relative;
  }
  
  .form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    padding-right: 2.5rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: white;
  }
  
  .form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }
  
  .form-control.success {
    border-color: var(--success-color);
  }
  
  .form-control.error {
    border-color: var(--error-color);
  }
  
  .field-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
  }
  
  .success-icon {
    color: var(--success-color);
  }
  
  .error-icon {
    color: var(--error-color);
  }
  
  .location-icon {
    opacity: 0.5;
  }
  
  .error-message {
    color: var(--error-color);
    font-size: 0.8rem;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
  }
  
  .textarea-wrapper {
    position: relative;
  }
  
  .textarea-footer {
    margin-top: 0.5rem;
  }
  
  .bio-hint {
    color: var(--text-secondary);
    font-size: 0.8rem;
  }
  
  /* Tooltip */
  .info-tooltip {
    position: relative;
    cursor: help;
    margin-left: 0.5rem;
  }
  
  .tooltip {
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background-color: var(--text-primary);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    white-space: nowrap;
    z-index: 10;
    box-shadow: var(--shadow-md);
  }
  
  .tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: var(--text-primary);
  }
  
  /* Enhanced Buttons */
  .form-actions {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
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
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    flex: 1;
    justify-content: center;
  }
  
  .btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  
  .btn-primary.loading {
    pointer-events: none;
  }
  
  .btn-secondary {
    background-color: white;
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
  }
  
  .btn-secondary:hover:not(:disabled) {
    background-color: var(--background-light);
    border-color: var(--text-secondary);
  }
  
  .btn-icon {
    font-size: 1rem;
  }
  
  .spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  /* Progress Indicator */
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
    transition: all 0.3s ease;
  }
  
  .dot.active {
    background-color: var(--primary-color);
    transform: scale(1.2);
  }
  
  .dot.completed {
    background-color: var(--success-color);
  }
  
  /* Animations */
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .fade-slide-enter-active {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .fade-slide-leave-active {
    animation: fadeOutDown 0.3s ease-in;
  }
  
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(40px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  @keyframes fadeOutDown {
    from {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
    to {
      opacity: 0;
      transform: translateY(-20px) scale(0.98);
    }
  }
  
  /* Responsive Design */
  @media (max-width: 768px) {
    .step-one-container {
      padding: 1rem;
    }
    
    .info-card {
      padding: 1.5rem;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    
    .form-actions {
      flex-direction: column;
    }
    
    .stepper {
      flex-wrap: wrap;
    }
    
    .step {
      flex-basis: 50%;
      margin-bottom: 1rem;
    }
    
    .connector {
      display: none;
    }
  }
  
  @media (max-width: 480px) {
    .card-title {
      font-size: 1.5rem;
    }
    
    .avatar-preview {
      width: 100px;
      height: 100px;
    }
    
    .step .circle {
      width: 40px;
      height: 40px;
      font-size: 1rem;
    }
  }</style>