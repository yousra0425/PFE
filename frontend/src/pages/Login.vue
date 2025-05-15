<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      error:''
    };
  },
  methods: {
    async handleLogin() {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: this.email,
      password: this.password
    }, {
      withCredentials: true
    });

    const token = response.data.access_token;
    localStorage.setItem('token', token); 
    
    this.$router.push('/services');
   
    console.log('Login successful', response.data);
    this.$router.push('/dashboard');
  } catch (err) {
    this.error = err.response?.data?.message || 'Login failed';
  }   
}

  }
};
</script>

<template>
  <div class="login-container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="login-box">
          <h3 class="login-title">Log in </h3>
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
    </div>
  </div>
</template>

<style scoped>
.login-container {
  background-color: #f4f7fa;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.row {
  width: 50%;
  margin: 0 auto; /* Center the row */
}

.login-box {
  background: #ffffff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  
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
  color: var(--primary-hover); /* if using theme variables */
}

hr {
  margin: 20px 0;
  border-color: #ddd;
}
</style>
