<template> 
  <div class="my-account">
    <header>
      <h1>My Account</h1>
      <p>Manage your profile information and preferences</p>
    </header>

    <section class="profile-picture-section">
      <div class="picture-container">
        <img v-if="previewImage" :src="previewImage" alt="Profile Picture" />
        <div v-else class="placeholder-avatar">upload Photo</div>
        <div class="picture-overlay">
          <label for="profilePicture" class="upload-btn">
            Change Photo
          </label>
          <input 
            id="profilePicture" 
            ref="fileInput"
            type="file" 
            accept="image/*" 
            @change="onFileChange" 
            hidden 
          />
          <button v-if="previewImage" @click="removeProfilePicture" type="button" class="remove-btn">
            Remove
          </button>
        </div>
      </div>
      <p class="picture-info">Upload a profile picture (max 2MB) • JPG, PNG, GIF</p>
    </section>

    <form @submit.prevent="updateProfile" class="profile-form">
      <div class="input-group">
        <label for="fullName">Full Name *</label>
        <input 
          id="fullName"
          v-model.trim="form.name" 
          type="text" 
          placeholder="Full Name *" 
          required 
          class="form-input"
        />
      </div>

      <div class="input-group">
        <label for="emailAddress">Email Address *</label>
        <input 
          id="emailAddress"
          v-model.trim="form.email" 
          type="email" 
          placeholder="Email Address *" 
          required 
          class="form-input"
        />
      </div>

      <div class="input-group">
        <label for="phoneNumber">Phone Number</label>
        <input 
          id="phoneNumber"
          v-model.trim="form.phone" 
          type="tel" 
          placeholder="Phone Number" 
          class="form-input"
        />
      </div>

      <div class="input-group">
        <label for="location">Location</label>
        <input 
          id="location"
          v-model.trim="form.location" 
          type="text" 
          placeholder="Location" 
          class="form-input"
        />
      </div>

      <div class="textarea-group">
        <div class="textarea-header">
          <label for="aboutMe">About Me</label>
          <span class="char-counter">{{ form.bio.length || 0 }}/{{ bioMaxLength }}</span>
        </div>
        <textarea 
          id="aboutMe"
          v-model="form.bio" 
          :maxlength="bioMaxLength" 
          placeholder="Tell us about yourself..."
          class="form-textarea"
        ></textarea>
      </div>

      <div class="skills-section">
        <label class="skills-label" for="newSkillInput">Skills & Subjects</label>
        <div class="skills-input">
          <input 
            id="newSkillInput"
            v-model.trim="newSkill" 
            @keyup.enter.prevent="addSkill" 
            placeholder="Add skill or subject" 
            class="form-input"
          />
          <button @click.prevent="addSkill" type="button" class="add-skill-btn">Add</button>
        </div>
        <div class="skills-list" v-if="form.skills.length">
          <span v-for="(skill, i) in form.skills" :key="i" class="skill-tag">
            {{ skill }}
            <button @click.prevent="removeSkill(i)" type="button" class="remove-skill">×</button>
          </span>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" @click="resetForm" :disabled="loading" class="btn btn-secondary">
          Reset Changes
        </button>
        <button type="submit" :disabled="loading || !isFormValid" class="btn btn-primary">
          <span v-if="loading">Saving...</span>
          <span v-else>Save Changes</span>
        </button>
      </div>

      <transition name="fade">
        <div v-if="error" class="alert alert-error">
          <span>{{ error }}</span>
          <button @click="error = null" class="alert-close">×</button>
        </div>
      </transition>

      <transition name="fade">
        <div v-if="success" class="alert alert-success">
          <span>{{ success }}</span>
          <button @click="success = null" class="alert-close">×</button>
        </div>
      </transition>
    </form>
  </div>
</template>

  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        form: {
          name: '',
          email: '',
          phone: '',
          location: '',
          bio: '',
          profile_picture: null,
          skills: [],
        },
        originalForm: {},
        previewImage: null,
        newSkill: '',
        loading: false,
        error: null,
        success: null,
        bioMaxLength: 500,
        token: localStorage.getItem('token'),
      };
    },
    computed: {
      isFormValid() {
        return (
          this.form.name &&
          this.form.email &&
          this.validateEmail(this.form.email)
        );
      },
    },
    mounted() {
      this.loadProfile();
    },
    methods: {
      async loadProfile() {
        this.loading = true;
        this.error = null;
        try {
          const { data } = await axios.get('/api/profile', {
            headers: { Authorization: `Bearer ${this.token}` },
          });
          this.form = {
            name: data.name || '',
            email: data.email || '',
            phone: data.phone || '',
            location: data.location || '',
            bio: data.bio || '',
            profile_picture: null,
            skills: data.skills || [],
          };
          this.originalForm = JSON.parse(JSON.stringify(this.form));
          this.previewImage = data.profile_picture ? `/storage/${data.profile_picture}` : null;
        } catch {
          this.error = 'Failed to load profile. Please try again.';
        } finally {
          this.loading = false;
        }
      },
      onFileChange(e) {
        const file = e.target.files[0];
        if (!file) return;
  
        if (file.size > 2 * 1024 * 1024) {
          this.error = 'Profile picture must be less than 2MB';
          return;
        }
  
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
          this.error = 'Profile picture must be JPG, PNG, or GIF format';
          return;
        }
  
        this.form.profile_picture = file;
        this.previewImage = URL.createObjectURL(file);
        this.error = null;
      },
      removeProfilePicture() {
        this.form.profile_picture = null;
        this.previewImage = null;
        this.$refs.fileInput.value = '';
      },
      addSkill() {
        const skill = this.newSkill.trim();
        if (skill && !this.form.skills.includes(skill)) {
          this.form.skills.push(skill);
          this.newSkill = '';
        }
      },
      removeSkill(index) {
        this.form.skills.splice(index, 1);
      },
      validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      },
      resetForm() {
        this.form = JSON.parse(JSON.stringify(this.originalForm));
        this.error = null;
        this.success = null;
        this.newSkill = '';
        this.previewImage = this.originalForm.profile_picture
          ? `/storage/${this.originalForm.profile_picture}`
          : null;
        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
      },
      async updateProfile() {
        if (!this.isFormValid) {
          this.error = 'Please fix the errors before saving.';
          return;
        }
  
        this.loading = true;
        this.error = null;
        this.success = null;
  
        try {
          const formData = new FormData();
          for (const key of ['name', 'email', 'phone', 'location', 'bio']) {
            formData.append(key, this.form[key]);
          }
          formData.append('skills', JSON.stringify(this.form.skills));
          if (this.form.profile_picture) {
            formData.append('profile_picture', this.form.profile_picture);
          }
  
          await axios.post('/api/profile', formData, {
            headers: {
              Authorization: `Bearer ${this.token}`,
              'Content-Type': 'multipart/form-data',
            },
          });
  
          this.success = 'Profile updated successfully!';
          this.originalForm = JSON.parse(JSON.stringify(this.form));
          this.form.profile_picture = null;
  
          setTimeout(() => (this.success = null), 5000);
        } catch (err) {
          this.error = err.response?.data?.message || 'Failed to update profile.';
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .my-account {
    max-width: 750px;
    margin: 2rem auto;
    padding: 2rem;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #333;
  }
  
  /* Header */
  header {
    text-align: center;
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
  }
  
  header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    background: black;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  header p {
    font-size: 0.8rem;
    color: #666;
    margin: 0;
  }

  /* Profile Picture Section */
  .profile-picture-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 2.5rem;
    padding: 1.5rem;
    background: rgba(251, 251, 251, 0);
    border-radius: 16px;
  }
  
  .picture-container {
    position: relative;
    margin-bottom: 1rem;
  }
  
  .picture-container > img,
  .placeholder-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  }
  
  .placeholder-avatar {
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #999;
  }
  
  .picture-overlay {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .upload-btn,
  .remove-btn {
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .upload-btn {
    background: var(--primary-hover);
    color: white;
    box-shadow: 0 4px 15px rgba(158, 158, 158, 0.4);
  }
  
  .upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(160, 160, 160, 0.6);
  }
  
  .remove-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
  }
  
  .remove-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
  }
  
  .picture-info {
    font-size: 0.9rem;
    color: #666;
    text-align: center;
    margin: 0;
  }
  
  /* Form Styles */
  .profile-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .input-group {
    position: relative;
    display: flex;
    align-items: center;
  }

  .form-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #f0f0f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fafafa;
  }
  
  .form-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  label {
    font-weight: 600;
    margin-bottom: 0.4rem;
    color: #444;
    cursor: pointer;
    user-select: none;
    font-size: 0.95rem;
    display: block;
  }
  
  .textarea-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .textarea-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .textarea-header label {
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }
  
  .char-counter {
    font-size: 0.85rem;
    color: #999;
    font-weight: 500;
    user-select: none;
  }
  
  .form-textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #f0f0f0;
    border-radius: 12px;
    font-size: 1rem;
    min-height: 100px;
    resize: vertical;
    font-family: inherit;
    background: #fafafa;
    transition: all 0.3s ease;
  }
  
  .form-textarea:focus {
    outline: none;
    border-color: #83ffe0;
    background: white;
    box-shadow: 0 0 0 3px rgba(153, 154, 158, 0.1);
  }
  
  /* Skills Section */
  .skills-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .skills-label {
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }
  
  .skills-input {
    display: flex;
    gap: 0.75rem;
  }
  
  .skills-input .form-input {
    flex: 1;
    padding-left: 1rem;
  }
  
  .add-skill-btn {
    padding: 1rem 1.5rem;
    background: white;
    color: var(--primary-color);
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
  }
  
  .add-skill-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(147, 147, 147, 0.4);
  }
  
  .skills-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .skill-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #2d3748;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    user-select: none;
  }
  
  .remove-skill {
    background: none;
    border: none;
    color: #e53e3e;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.1rem;
    line-height: 1;
    padding: 0;
    margin-left: 0.25rem;
    transition: color 0.2s ease;
  }
  
  .remove-skill:hover {
    color: #c53030;
  }
  
  /* Form Actions */
  .form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    justify-content: flex-end;
  }
  
  .btn {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
  }
  
  .btn-secondary {
    background: rgb(190, 188, 188);
    color: white;
    box-shadow: 0 4px 15px rgba(165, 169, 168, 0.4);
  }
  
  .btn-secondary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(113, 112, 113, 0.6);
  }
  
  .btn-primary {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 15px rgba(145, 145, 147, 0.6);
  }
  
  .btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(145, 145, 147, 0.6);
  }
  
  /* Alerts */
  .alert {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    font-weight: 500;
    margin-top: 1rem;
  }
  
  .alert-error {
    background: linear-gradient(135deg, #ffcccb 0%, #ff9a9e 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
  
  .alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #a8e6cf 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  
  .alert-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
    padding: 0;
    margin-left: 1rem;
  }
  
  .alert-close:hover {
    opacity: 1;
  }
  
  /* Transitions */
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .my-account {
      margin: 1rem;
      padding: 1.5rem;
    }
  
    .form-actions {
      flex-direction: column;
    }
  
    .skills-input {
      flex-direction: column;
    }
  
    .picture-container > img,
    .placeholder-avatar {
      width: 100px;
      height: 100px;
    }
  
    header h1 {
      font-size: 2rem;
    }
  }
  
  @media (max-width: 480px) {
    .my-account {
      padding: 1rem;
    }
  
    .picture-overlay {
      flex-direction: column;
      align-items: center;
    }
  
    .upload-btn,
    .remove-btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>
