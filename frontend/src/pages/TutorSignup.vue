<template>
  <div class="signup-container">
    <div class="signup-wrapper">
      <div class="signup-form-container">
        <div class="signup-box">
          <h3 class="signup-title">Become a Tutor</h3>
          <hr />
          <form @submit.prevent="handleSignup">
            <div class="name-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input v-model="first_name" type="text" class="form-control" id="firstName" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input v-model="last_name" type="text" class="form-control" id="lastName" required />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input v-model="email" type="email" class="form-control" id="email" required />
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input v-model="password" type="password" class="form-control" id="password" required />
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input v-model="confirmPassword" type="password" class="form-control" id="confirmPassword" required />
            </div>

            <p v-if="error" class="text-danger mt-2">{{ error }}</p>

            <button type="submit" class="btn btn-primary w-100 mt-3">Register as Tutor</button>

            <div class="text-center mt-3">
              Already registered?
              <router-link to="/login" class="login-link">Login</router-link>
            </div>
          </form>
        </div>
      </div>
      <div class="welcome-panel">
  <h2>Welcome to ProxiTutor!</h2>
  <p class="welcome-subtitle">
    Join our community of expert educators and share your knowledge with students in your area.
  </p>

  <div class="benefits-list">
    <div class="benefit-item">
      <i class="fas fa-chalkboard-teacher benefit-icon"></i>
      <div>
        <h4>Flexible Teaching</h4>
        <p>Set your own schedule and hourly rates</p>
      </div>
    </div>

    <div class="benefit-item">
      <i class="fas fa-map-marker-alt benefit-icon"></i>
      <div>
        <h4>Local Students</h4>
        <p>Connect with learners in your neighborhood</p>
      </div>
    </div>
  </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      confirmPassword: '',
      error: ''
    };
  },
  methods: {
    async handleSignup() {
      if (this.password !== this.confirmPassword) {
        this.error = "Passwords do not match.";
        return;
      }

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/register-tutor', {
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
        });

        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;

        // Get location and send it to backend
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(async (position) => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            try {
              await axios.post('http://127.0.0.1:8000/api/update-location', {
                latitude,
                longitude
              });
            } catch (err) {
              console.warn("Location save failed", err.message);
            }

            this.$router.push('/becometutor');
          }, (error) => {
            console.warn("Location permission denied:", error.message);
            this.$router.push('/becometutor');
          });
        } else {
          console.warn("Geolocation not supported.");
          this.$router.push('/becometutor');

        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Signup failed.';
        console.error(err);
      }
    }
  }
};
</script>

<style scoped>
.signup-container {
  background-color: var(--background-light);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.signup-wrapper {
  display: flex;
  width: 100%;
  max-width: 900px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  border-radius: 12px;
  overflow: hidden;
  background: white;
}

.signup-form-container {
  flex: 1;
  padding: 40px 30px;
}

.signup-title {
  text-align: center;
  color: var(--primary-color);
  font-weight: bold;
}
.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #333;
}
.form-control {
  border-radius: 8px;
  border: 1px solid #ddd;
  padding: 10px;
  font-size: 1rem;
  margin-top: 5px;
  transition: border-color 0.3s ease;
}
.form-control:focus {
  border-color: var(--primary-color);
  outline: none;
}
.btn-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  font-weight: bold;
  padding: 10px;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}
.btn-primary:hover {
  background-color: var(--primary-hover);
  border-color: var(--primary-hover);
}
.login-link {
  color: var(--primary-color);
  font-weight: bold;
  text-decoration: none;
}
.login-link:hover {
  text-decoration: underline;
  color: var(--primary-hover);
}
hr {
  margin: 20px 0;
  border-color: #ddd;
}
@media (max-width: 767px) {
  .col-md-6:first-child {
    margin-bottom: 15px;
  }
}

.welcome-panel {
  flex: 1;
  background: linear-gradient(135deg, #2c5044 0%, #38a38e 100%);
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 30px;
  text-align: center;
}

.welcome-panel h2 {
  font-size: 2.2rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.welcome-subtitle {
  font-size: 1.1rem;
  margin-bottom: 2rem;
  font-weight: 400;
  max-width: 320px;
  margin-left: auto;
  margin-right: auto;
}

.benefits-list {
  text-align: left;
  max-width: 300px;
  margin: 0 auto 2rem;
}

.benefit-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.benefit-icon {
  font-size: 1.5rem;
  margin-right: 1rem;
  color: #ffffff;
  opacity: 0.8;
}

.benefit-item h4 {
  font-size: 1.1rem;
  margin-bottom: 0.3rem;
  font-weight: 600;
}

.benefit-item p {
  font-size: 0.9rem;
  opacity: 0.9;
  margin: 0;
}

.welcome-illustration {
  margin: 2rem auto 0;
}

/* Add Font Awesome if not already included */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
</style>
