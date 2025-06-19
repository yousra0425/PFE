<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section about">
        <h3>ProxiTutor</h3>
        <p>Your go-to platform to find trusted tutors online or nearby. Learn anything, anytime.</p>
      </div>

      <div class="footer-section links">
        <h4>Quick Links</h4>
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/findtutors">Find Tutors</router-link></li>
          <li><router-link to="/services">Services</router-link></li>
          <li><router-link to="/login">Login</router-link></li>
          <li><router-link to="/signup">Sign Up</router-link></li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      &copy; 2025 SuperTutor. All rights reserved.
    </div>
  </footer>
</template>

<script setup>
// No script needed here
</script>

<style scoped>
.footer {
  background-color: #000000;
  color: #fff;
  padding: 2rem 1rem;
  font-family: Arial, sans-serif;
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-section {
  flex: 1 1 250px;
}

.footer-section h3,
.footer-section h4 {
  margin-bottom: 0.8rem;
  color: var(--primary-color);
}

.footer-section p {
  font-size: 0.9rem;
  line-height: 1.4;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section ul li a {
  color: #ecf0f1;
  text-decoration: none;
}

.footer-section ul li a:hover {
  text-decoration: underline;
}

.social-icons a {
  margin-right: 1rem;
  color: #ecf0f1;
  font-size: 1.4rem;
}

.footer-bottom {
  text-align: center;
  margin-top: 2rem;
  font-size: 0.9rem;
  border-top: 1px solid #7f8c8d;
  padding-top: 1rem;
}
</style>
