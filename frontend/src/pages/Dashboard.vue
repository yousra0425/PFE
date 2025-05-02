<template>
    <div class="dashboard-container">
      <div class="dashboard-header">
        <h2>Welcome, {{ userName }}</h2>
        <button @click="handleLogout" class="btn btn-danger">Logout</button>
      </div>
  
      <hr />
  
      <div class="dashboard-content">
        <div class="card" v-for="(item, index) in dashboardItems" :key="index">
          <div class="card-body">
            <h5 class="card-title">{{ item.title }}</h5>
            <p class="card-text">{{ item.description }}</p>
            <router-link :to="item.link" class="btn btn-primary">Go to {{ item.title }}</router-link>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'UserDashboard',
    data() {
      return {
        userName: 'John Doe', // Replace this with dynamic user data
        dashboardItems: [
          {
            title: 'My Courses',
            description: 'View and manage your enrolled courses.',
            link: '/my-courses'
          },
          {
            title: 'Account Settings',
            description: 'Update your profile and change your password.',
            link: '/settings'
          },
          {
            title: 'Support',
            description: 'Need help? Reach out to our support team.',
            link: '/support'
          }
        ]
      };
    },
    methods: {
      handleLogout() {
        // Example logout method
        localStorage.removeItem('auth_token'); // if using token
        this.$router.push('/login');
      }
    }
  };
  </script>
  
  <style scoped>
  .dashboard-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
  }
  
  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .dashboard-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }
  
  .card {
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  }
  </style>
  