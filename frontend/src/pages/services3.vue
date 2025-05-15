<template>
    <div class="services-page">
      <header class="header">
        <div class="logo">HomeFixMapper</div>
        <div class="search-bar">
          <input type="text" placeholder="Search for services..." />
        </div>
        <div class="auth-buttons">
          <button class="sign-in">Sign In</button>
          <button class="sign-up">Sign Up</button>
        </div>
      </header>
  
      <main class="main-content">
        <h1>Find Local Services</h1>
        <p class="subtitle">Select a category and service to find trusted professionals near you</p>
        
        <div class="category-selector">
          <h2>Select a Category & Service</h2>
          
          <div class="category-tabs">
            <button
              v-for="category in categories"
              :key="category.id"
              @click="selectCategory(category)"
              :class="{ active: selectedCategory?.id === category.id }"
            >
              {{ category.name }}
            </button>
          </div>
          
          <div v-if="selectedCategory" class="services-container">
            <div class="category-info">
              <h3>{{ selectedCategory.name }}</h3>
              <p class="category-description" v-if="selectedCategory.description">
                {{ selectedCategory.description }}
              </p>
            </div>
            
            <div class="services-grid">
              <div v-for="service in services" :key="service.id" class="service-card">
                {{ service.name }}
              </div>
            </div>
          </div>
          
          <div v-else class="loading-message">
            <p>Loading services...</p>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  const categories = ref([])
  const services = ref([])
  const selectedCategory = ref(null)
  
  const loadCategories = async () => {
    try {
      const res = await axios.get('http://localhost:8000/api/categories')
      categories.value = res.data
      if (categories.value.length > 0) selectCategory(categories.value[0])
    } catch (err) {
      console.error('Failed to load categories:', err)
    }
  }
  
  const selectCategory = async (category) => {
    selectedCategory.value = category
    try {
      const res = await axios.get(`http://localhost:8000/api/categories/${category.id}/services`)
      services.value = res.data
    } catch (err) {
      console.error('Failed to load services:', err)
      services.value = []
    }
  }
  
  onMounted(loadCategories)
  </script>
  
  <style scoped>
  .services-page {
    font-family: 'Arial', sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 30px;
  }
  
  .logo {
    font-size: 24px;
    font-weight: bold;
    color: #333;
  }
  
  .search-bar input {
    padding: 8px 15px;
    width: 300px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .auth-buttons button {
    padding: 8px 15px;
    margin-left: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: pointer;
  }
  
  .sign-up {
    background: #4CAF50;
    color: white;
    border: none !important;
  }
  
  .main-content {
    text-align: center;
  }
  
  .main-content h1 {
    font-size: 32px;
    margin-bottom: 10px;
  }
  
  .subtitle {
    color: #666;
    margin-bottom: 30px;
  }
  
  .category-selector {
    margin-top: 40px;
  }
  
  .category-selector h2 {
    text-align: left;
    margin-bottom: 20px;
    font-size: 24px;
  }
  
  .category-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  
  .category-tabs button {
    padding: 10px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .category-tabs button:hover {
    background: #e0e0e0;
  }
  
  .category-tabs button.active {
    background: #4CAF50;
    color: white;
  }
  
  .services-container {
    text-align: left;
  }
  
  .category-info h3 {
    margin-top: 0;
    font-size: 20px;
  }
  
  .category-description {
    color: #666;
    margin-bottom: 15px;
  }
  
  .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
  }
  
  .service-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.2s;
    background: white;
  }
  
  .service-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }
  
  .loading-message {
    padding: 40px;
    text-align: center;
    color: #666;
  }
  </style>