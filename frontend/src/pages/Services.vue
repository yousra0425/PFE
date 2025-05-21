<template>
  <div class="services-page">
    <Hero />
    <div id="how-it-works">
     <HowItWorks />
    </div>
    

    <main class="main-content">
      <div class="hero-section">
        <h1 class="hero-title">Find Trusted Home Service Professionals</h1>
        <p class="hero-subtitle">Browse our categories to find the perfect service for your needs</p>
      </div>
      
      <div class="services-container">
        <div 
          v-for="category in categories" 
          :key="category.id" 
          class="category-card"
          :class="{ expanded: activeCategory === category.id }"
          @click="toggleCategory(category.id)"
        >
          <div class="category-header">
            <div class="category-info">
              <h2 class="category-title">{{ category.label }}</h2>
              <p class="category-description">{{ category.description }}</p>
            </div>
            <span class="toggle-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline v-if="activeCategory === category.id" points="18 15 12 9 6 15"></polyline>
                <polyline v-else points="6 9 12 15 18 9"></polyline>
              </svg>
            </span>
          </div>
          
          <transition name="slide">
            <div v-show="activeCategory === category.id" class="services-list">
              <div 
                v-for="service in category.services" 
                :key="service.id" 
                class="service-card"
                @click.stop="selectService(service)"
              >
                <div class="service-details">
                  <h6 class="service-title">{{ service.name }}</h6>
                  <p class="service-description">{{ service.description }}</p>
                </div>
                <div class="service-cta">
                  <button class="select-btn">Select Service</button>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </main>
    <section id="provider-form">
       <BecomeProvider />
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import HowItWorks from '../components/HowItWorks.vue'
import Hero from '../components/Hero.vue'
import BecomeProvider from '../components/ProviderForm.vue'

const activeCategory = ref(null)
const selectedService = ref(null)
const categories = ref([])
const router = useRouter()

// Fetch categories with services from backend
const fetchCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/categories-with-services') 
    categories.value = response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  }
}

// Fetch on component mount
onMounted(() => {
  fetchCategories()
})

const toggleCategory = (categoryId) => {
  activeCategory.value = activeCategory.value === categoryId ? null : categoryId
}

const selectService = (service) => {
  selectedService.value = service
  console.log('Selected service:', service)
  router.push({ name: 'nearbyprovidersmap', params: { id: service.id } })
}
</script>

<style scoped>
:root {
  --primary-color: #1ca79b;
  --primary-hover: #1ca79bce;
  --primary-light: #4895ef;
  --secondary: #3f37c9;
  --dark: #1b263b;
  --light: #f8f9fa;
  --gray: #6c757d;
  --light-gray: #e9ecef;
  --success: #4cc9f0;
  --warning: #f72585;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
  --shadow-lg: 0 10px 15px rgba(0,0,0,0.1), 0 4px 6px rgba(0,0,0,0.05);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --border-radius: 12px;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.services-page {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  min-height: 100vh;
  background-color: #f5f7fa;
  color: var(--dark);
  line-height: 1.6;
}

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

.main-content {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.hero-section {
  text-align: center;
  margin-bottom: 3rem;
  padding: 2rem 0;
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--dark);
  margin-bottom: 1rem;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: var(--gray);
  max-width: 600px;
  margin: 0 auto;
}

.services-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.category-card {
  background-color: white;
  border-radius: 10px;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: var(--transition);
}

.category-card:hover {
  box-shadow: var(--shadow-md);
}

.category-card.expanded {
  box-shadow: var(--shadow-lg);
  border: 2px solid var(--primary);
  background-color: #d7f0ef;
}

.category-header {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  cursor: pointer;
}

.category-info {
  flex: 1;
}

.category-title {
  font-size: 1.2rem;
  font-weight: bolder;
  color: var(--dark);
  margin-bottom: 0.5rem;
}

.category-description {
  font-size: 0.9rem;
  color: var(--gray);
  line-height: 1.5;
}

.toggle-icon {
  color: var(--gray);
  transition: var(--transition);
  margin-left: 1rem;
}

.category-card.expanded .toggle-icon {
  color: var(--primary-color);
}

.services-list {
  padding: 0 1.5rem 1.5rem;
}

.service-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem;
  border-radius: 8px;
  margin-bottom: 0.75rem;
  transition: var(--transition);
  background-color: var(--light);
  border-left: 3px solid transparent;
}

.service-card:hover {
  background-color: white;
  box-shadow: var(--shadow-sm);
  border-left-color: var(--primary);
}

.service-details {
  flex: 1;
  padding-right: 1rem;
}

.service-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--dark);
  margin-bottom: 0.25rem;
}

.service-description {
  font-size: 0.85rem;
  color: var(--gray);
  line-height: 1.5;
}

.select-btn {
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  font-weight: 500;
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: var(--transition);
  white-space: nowrap;
}

.select-btn:hover {
  background-color: var(--primary-hover);
  transform: translateY(-1px);
}

/* Animations */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
  overflow: hidden;
}

.slide-enter-from, .slide-leave-to {
  max-height: 0;
  opacity: 0;
  padding-bottom: 0;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  .search-bar {
    width: 100%;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .services-container {
    grid-template-columns: 1fr;
  }
  
  .category-header {
    padding: 1.25rem;
  }
  
  .service-card {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .service-cta {
    width: 100%;
    margin-top: 0.75rem;
  }
  
  .book-btn {
    width: 100%;
  }

  .how-it-works {
  margin: 3rem 0;
}
}
</style>