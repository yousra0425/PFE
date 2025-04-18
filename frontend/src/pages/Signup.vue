<script setup>
// Add any required logic or bindings here later
</script>

<template>
  <div class="signup-container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="signup-box">
          <h3 class="signup-title">Signup with email</h3>
          <hr />
          <form @submit.prevent="handleSignup">
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input
                type="text"
                class="form-control"
                id="fullName"
                placeholder="Enter your full name"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input
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
                type="password"
                class="form-control"
                id="confirmPassword"
                placeholder="Confirm your password"
                required
              />
            </div>

            <div class="form-group">
              <label for="city">City</label>
              <input
                type="text"
                class="form-control"
                id="city"
                placeholder="Enter your city"
                required
              />
            </div>

            <div class="form-group">
              <label for="country">Country</label>
              <select class="form-control" id="country" required>
                <option value="" disabled selected>Select your country</option>
                <option value="US">United States</option>
                <option value="CA">Canada</option>
                <option value="GB">United Kingdom</option>
              </select>
            </div>

            <div class="my-3">
              <button type="submit" class="btn btn-primary w-100">Signup</button>
            </div>

            <div class="text-center mt-3">
              <p>
                 Already have an account?
                 <router-link to="/login" class="login-link">Login here</router-link>
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

.signup-box {
  background: #ffffff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
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
  color: var(--primary-hover); /* if using theme variables */
}

hr {
  margin: 20px 0;
  border-color: #ddd;
}
</style>
