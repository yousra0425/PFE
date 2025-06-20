<script>
import axios from 'axios';
import { registerFCMToken } from '../fcm';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: ''
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email: this.email,
          password: this.password
        });

        const token = response.data.access_token;
        const user = response.data.user;

        // ✅ Save token and user info
        localStorage.setItem('token', token);
        localStorage.setItem('user_id', user.id);
        localStorage.setItem('user_role', user.role);

        // ✅ Set default Authorization header for future requests
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        // ✅ Register FCM Token
        await registerFCMToken();

        // ✅ Redirect based on role
        if (user.role === 'admin') {
          this.$router.push('/admindashboard');
          return;
        }

        // ✅ For tutors and clients, update location then redirect
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            async position => {
              const { latitude, longitude } = position.coords;
              await axios.post('http://127.0.0.1:8000/api/update-location', {
                latitude,
                longitude
              });

              this.$router.push(user.role === 'tutor' ? '/tutordashboard' : '/clientdashboard');
            },
            () => {
              this.$router.push(user.role === 'tutor' ? '/tutordashboard' : '/clientdashboard');
            }
          );
        } else {
          this.$router.push(user.role === 'tutor' ? '/tutordashboard' : '/clientdashboard');
        }

      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed';
      }
    },
  mounted() {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('user_role');

    if (token) {
      if (role === 'admin') this.$router.push('/admindashboard');
      else if (role === 'tutor') this.$router.push('/tutordashboard');
      else this.$router.push('/clientdashboard');
    }
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('token');
    }
  }
}

};
</script>




<template>
  <div class="login-container">
    <div class="login-wrapper">
      <!-- Left: Login Form -->
      <div class="login-form-container">
        <div class="login-box">
          <h3 class="login-title">Log in</h3>
          <hr />
          <form @submit.prevent="handleLogin">
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

            <p v-if="error" class="text-danger mt-2">{{ error }}</p>

            <div class="my-3">
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </div>

            <div class="text-center mt-3">
              <p>
                Don't have an account?
                <router-link to="/signup" class="signup-link">Signup here</router-link>
              </p>
            </div>
          </form>
        </div>
      </div>

      <!-- Right: Welcome Panel -->
      <div class="welcome-panel">
  <h2>Welcome Back to ProxiTutor!</h2>
  <p>
    Log in to connect with expert tutors near you, manage your sessions, and continue your learning journey with ease.
  </p>



        <div class="welcome-illustration">
          <svg width="180" height="180" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
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
.login-container {
  background-color: var(--background-light);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.login-wrapper {
  display: flex;
  width: 100%;
  max-width: 900px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  border-radius: 12px;
  overflow: hidden;
  background: white;
}


.login-form-container {
  flex: 1;
  padding: 40px 30px;
}

.login-title {
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
  width: 100%;
  margin-top: 5px;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  border-color: var(--primary-color);
  outline: none;
}

.form-group p {
  color: #333;
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

.signup-link {
  color: var(--primary-color);
  font-weight: bold;
  text-decoration: none;
}

.signup-link:hover {
  text-decoration: underline;
  color: var(--primary-hover);
}

hr {
  margin: 20px 0;
  border-color: #ddd;
}

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
</style>
