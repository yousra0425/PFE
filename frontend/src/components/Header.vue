<template>
  <header class="header">
    <div class="logo-container">
      <router-link to="/services" class="logo-link">
        <span class="logo">ProxiTutor</span>
      </router-link>
      <span class="logo-tagline">Find trusted tutors near you</span>
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Search for subjects or tutors..." class="search-input" />
      <button class="search-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>
    </div>

    <div class="auth-buttons">
  <!-- If user is NOT logged in -->
  <template v-if="!isLoggedIn">
    <router-link to="/tutor-signup" class="auth-btn become-provider">Become a Tutor</router-link>
    <router-link to="/login" class="auth-btn login">Login</router-link>
    <router-link to="/signup" class="auth-btn sign-up">Sign Up</router-link>
  </template>

  <!-- If user IS logged in and is an admin -->
  <template v-else-if="userRole === 'admin'">
    <router-link to="/admindashboard" class="auth-btn login">Dashboard</router-link>
    <button @click="logout" class="auth-btn sign-up">Logout</button>
  </template>

  <!-- If user IS logged in and is a tutor -->
  <template v-else-if="userRole === 'tutor'">
    <router-link to="/tutordashboard" class="auth-btn login">Dashboard</router-link>
    <button @click="logout" class="auth-btn sign-up">Logout</button>
  </template>

  <!-- If user IS logged in and is a client -->
  <template v-else-if="userRole === 'client'">
    <router-link to="/tutor-signup" class="auth-btn become-provider">Become a Tutor</router-link>
    <router-link to="/clientdashboard" class="auth-btn login">Dashboard</router-link>
    <button @click="logout" class="auth-btn sign-up">Logout</button>
  </template>
</div>

  </header>
</template>

<script>
export default {
  name: 'Header',
  data() {
    return {
      isLoggedIn: false,
      userRole: null,
    };
  },
  created() {
    this.checkAuth();
  },
  watch: {
    // When route changes, check auth again
    $route() {
      this.checkAuth();
    }
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      const role = localStorage.getItem('user_role');
      this.isLoggedIn = !!token;
      this.userRole = role;
    },
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user_id');
      localStorage.removeItem('user_role');

      this.checkAuth();

      this.$router.push('/services');
    }
  }
};

</script>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: white;
  box-shadow: var(--shadow-sm);
  position: sticky;
  top: 0;
  z-index: 100;
}

.logo-container {
  display: flex;
  flex-direction: column;
}

.logo {
  font-size: 1.7rem;
  font-weight: 700;
  color: var(--primary-color);
  letter-spacing: -0.5px;
}

.logo-tagline {
  font-size: 0.75rem;
  color: var(--gray);
  margin-top: 0.25rem;
}

.logo-link {
  text-decoration: none;
}

.logo-link .logo {
  cursor: pointer;
  transition: color 0.3s ease;
}

.logo-link .logo:hover {
  color: var(--primary-hover);
}


.search-bar {
  display: flex;
  align-items: center;
  width: 40%;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--light-gray);
  border-radius: 8px 0 0 8px;
  font-size: 0.9rem;
  transition: var(--transition);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-hover);
  box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.search-button {
  padding: 0.75rem 1rem;
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 0 8px 8px 0;
  cursor: pointer;
  transition: var(--transition);
}

.search-button:hover {
  background-color: var(--primary-hover);
}

.auth-buttons {
  display: flex;
  gap: 0.75rem;
}

.auth-btn {
  padding: 0.75rem 1.25rem;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
}

.login {
  background-color: transparent;
  border: 1px solid var(--primary);
  color: rgb(0, 0, 0);
}

.login:hover {
  background-color: var(--primary-hover);
  border-color: var(--primary-hover);
}

.sign-up {
  background-color: transparent;
  border: 1px solid var(--primary);
  color: rgb(0, 0, 0);
}

.sign-up:hover {
  background-color: var(--primary-hover);
  border-color: var(--primary-hover);
}

.become-provider {
  background-color: #1ca79b;
  color: white;
  border: none;
}

.become-provider:hover {
  background-color: var(--primary-hover);
}
</style>
