<script>
import axios from 'axios';
export default {
  data() {
    return {
      first_name:'',
      last_name:'',
      email: '',
      password:'',
      confirmPassword:'',
      city:'',
      telephone:'',
      birth_date:'',
      error:'',
      showPassword: false,
      showConfirmPassword: false
    }; 
  },
  methods: {
    async handleSignup() {
      try {

        
        const response = await axios.post('http://127.0.0.1:8000/api/register', {
          first_name: this.first_name ,
          last_name: this.last_name,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
          country: 'Morocco',
          telephone: this.telephone,
          birth_date: this.birth_date,
          city: this.city
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
    <div class="row">
      <div class="col-md-6 offset-md-3">
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
    </div>
  </div>
</template>

<style scoped>
.signup-container {
  background-color:var(--background-light);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.row {
  width: 50%;
  margin-top: 0 auto;
}

.name-row {
  display: flex;
  gap: 1rem;
  width: 96%;
}

.name-row .form-group {
  flex: 1;
}

.signup-box {
  background: #ffffff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 600px;
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

/* Add some spacing between the name fields on small screens */
@media (max-width: 767px) {
  .col-md-6:first-child {
    margin-bottom: 15px;
  }
}
</style>