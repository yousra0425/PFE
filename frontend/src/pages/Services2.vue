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
        
        <div class="category-grid">
          <div class="category-card" v-for="category in categories" :key="category.name">
            <h3>{{ category.name }}</h3>
            <p class="category-description" v-if="category.description">{{ category.description }}</p>
            <ul class="service-list">
              <li v-for="service in category.services" :key="service">{{ service }}</li>
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'ServicesPage',
  data() {
    return {
      categories: [
        {
          name: 'Home Services',
          description: 'Professional services for your home maintenance and improvement needs',
          services: ['House Cleaning', 'Plumbing', 'Electrical', 'Handyman', 'Painting', 'Gardening']
        },
        {
          name: 'Deliveries',
          services: ['Food Delivery', 'Groceries', 'Package Delivery']
        }
        // Add more categories as needed
      ]
    }
  }
}
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

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.category-card {
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 20px;
  text-align: left;
  transition: transform 0.2s;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.category-card h3 {
  margin-top: 0;
  color: #333;
}

.category-description {
  color: #666;
  font-size: 14px;
  margin: 10px 0;
}

.service-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.service-list li {
  padding: 8px 0;
  border-bottom: 1px solid #f5f5f5;
}

.service-list li:last-child {
  border-bottom: none;
}
</style>