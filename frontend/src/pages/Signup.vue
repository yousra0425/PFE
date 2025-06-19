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
      city: '',
      telephone: '',
      birth_date: '',
      latitude: null,
      longitude: null,
      error: '',
      showPassword: false,
      showConfirmPassword: false
    };
  },
  mounted() {
    this.getUserLocation();
  },
  methods: {
    getUserLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            this.latitude = position.coords.latitude;
            this.longitude = position.coords.longitude;
          },
          (error) => {
            console.error("Geolocation error:", error.message);
            this.error = "Geolocation permission is required to register.";
          }
        );
      } else {
        this.error = "Geolocation is not supported by this browser.";
      }
    },

    async handleSignup() {
      try {
        if (this.latitude === null || this.longitude === null) {
          this.error = "Waiting for location... please allow location access.";
          return;
        }

        const response = await axios.post('http://127.0.0.1:8000/api/register', {
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
          country: 'Morocco',
          telephone: this.telephone,
          birth_date: this.birth_date,
          city: this.city,
          latitude: this.latitude,
          longitude: this.longitude
        });

        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;

        this.$router.push('/services');
        console.log('Signup successful', response.data);
      } catch (err) {
        console.error("Signup error:", err.response);
        this.error = err.response?.data?.message || 'Signup failed';
      }
    }
  }
};
</script>

<template>
  <div class="signup-container">
    <div class="signup-wrapper">
      <div class="signup-form-container">
        <div class="signup-box">
          <h3 class="signup-title">Signup with email</h3>
          <hr />
          <form @submit.prevent="handleSignup">
            <!-- Updated name fields row -->
            <div class="name-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input
                    v-model="first_name"
                    type="text"
                    class="form-control"
                    id="firstName"
                    placeholder="Enter your first name"
                    required
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input
                    v-model="last_name"
                    type="text"
                    class="form-control"
                    id="lastName"
                    placeholder="Enter your last name"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input
                v-model="email"
                type="email"
                class="form-control"
                id="email"
                placeholder="Enter your email"
                required
              />
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input
                v-model="password"
                type="password"
                class="form-control"
                id="password"
                placeholder="Enter your password"
                required
              />
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input
                v-model="confirmPassword"
                type="password"
                class="form-control"
                id="confirmPassword"
                placeholder="Confirm your password"
                required
              />
            </div>

            <div class="form-group">
              <label for="telephone">Telephone</label>
              <input
                v-model="telephone"
                type="tel"
                class="form-control"
                id="telephone"
                placeholder="Enter your phone number"
                required
              />
            </div>

            <div class="form-group">
              <label for="birthDate">Date of Birth</label>
              <input
                v-model="birth_date"
                type="date"
                class="form-control"
                id="birthDate"
                required
              />
            </div>

            <div class="form-group">
             <label for="city">City</label>
              <select v-model="city" class="form-control" id="city" required>
                 <option value="" disabled selected>Select your city</option>
                 <option value="Casablanca">Casablanca</option>
                 <option value="Rabat">Rabat</option>
                 <option value="Marrakech">Marrakech</option>
                 <option value="Fes">Fès</option>
                 <option value="Tangier">Tangier</option>
                 <option value="Agadir">Agadir</option>
                 <option value="Oujda">Oujda</option>
                 <option value="Kenitra">Kenitra</option>
                 <option value="Tetouan">Tétouan</option>   
              </select>
            </div>

            <p v-if="error" class="text-danger mt-2">{{ error }}</p>

            <div class="my-3">
              <button type="submit" class="btn btn-primary w-100">Signup</button>
            </div>

            <div class="text-center mt-3">
              <p>
                Already have an account?
                <router-link to="/login" class="login-link">Login</router-link>
              </p>
            </div>
          </form>
        </div>
      </div>

      <div class="welcome-panel">
  <h2>Welcome to ProxiTutor!</h2>
  <p>
    Connect with qualified tutors near you and boost your learning journey.
    Whether you're catching up or getting ahead, we're here to support your success.
  </p>


        <div class="welcome-illustration">
          <!-- Simple abstract SVG or shape -->
          <svg
            width="180"
            height="180"
            viewBox="0 0 200 200"
            xmlns="http://www.w3.org/2000/svg"
          >
            <circle cx="100" cy="100" r="90" fill="#6c63ff" opacity="0.15" />
            <circle cx="60" cy="70" r="40" fill="#6c63ff" opacity="0.3" />
            <circle cx="140" cy="130" r="50" fill="#6c63ff" opacity="0.25" />
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

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

/* Form side */
.signup-form-container {
  flex: 1;
  padding: 40px 30px;
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

.signup-title {
  text-align: center;
  color: var(--primary-color);
  font-weight: bold;
}
/* Welcome side */
.welcome-panel {
  flex: 1;
  background: linear-gradient(135deg, #348573 0%, #8bf7de 100%);
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 30px;
  text-align: center;
}

.welcome-panel h2 {
  font-size: 2.4rem;
  margin-bottom: 1rem;
  font-weight: 700;
  line-height: 1.2;
}

.welcome-panel p {
  font-size: 1.1rem;
  line-height: 1.5;
  margin-bottom: 2rem;
  font-weight: 400;
  max-width: 320px;
  margin-left: auto;
  margin-right: auto;
}

.welcome-illustration {
  margin: 0 auto;
}

/* Responsive */
@media (max-width: 768px) {
  .signup-wrapper {
    flex-direction: column;
  }
  .welcome-panel {
    padding: 30px 20px;
    order: -1;
  }
  .signup-form-container {
    padding: 30px 20px;
  }
}
</style>
