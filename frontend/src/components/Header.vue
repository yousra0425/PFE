<!-- Header.vue -->
<template>
  <header class="header">
    <div class="logo-container">
      <span class="logo">DomiFix</span>
      <span class="logo-tagline">Your trusted home service partners</span>
    </div>
    <div class="search-bar">
      <input type="text" placeholder="Search for services..." class="search-input" />
      <button class="search-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>
    </div>
    <div class="auth-buttons">
      <router-link to="/login" class="auth-btn login">Login</router-link>
      <router-link to="/signup" class="auth-btn sign-up">Sign Up</router-link>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Header'
}
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
    
</style>