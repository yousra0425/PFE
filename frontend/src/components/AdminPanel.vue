<template>
    <div class="admin-panel-container">
      <h2 class="title">Registered Users</h2>
      
      <!-- Table with improved design -->
      <div v-if="users.length" class="table-responsive">
        <table class="user-table">
          <thead>
            <tr>
              <th>CIN</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.cin }}</td>
              <td>{{ user.first_name }}</td>
              <td>{{ user.last_name }}</td>
              <td>{{ user.cin_status }}</td>
              <td>
                <!-- Approve and Reject buttons -->
                <button v-if="user.cin_status !== 'approved'" @click="approveUser(user.id)" class="btn-approve">Approve</button>
                <button v-if="user.cin_status !== 'rejected'" @click="rejectUser(user.id)" class="btn-reject">Reject</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <p v-else class="no-users-message">No users found or unauthorized.</p>
    </div>
  </template>
  
  <script>
  import api from '../api'; // ✅ use this
  
  export default {
    data() {
      return {
        users: [],
      };
    },
    async mounted() {
      try {
        const res = await api.get('/users'); // ✅ use api instance
        this.users = res.data;
      } catch (error) {
        console.error('Failed to fetch users:', error.response?.data || error.message);
      }
    },
    methods: {
      async approveUser(userId) {
        try {
          const res = await api.put(`/users/${userId}/approve`); // Make an API call to approve
          const user = this.users.find(u => u.id === userId);
          if (user) user.cin_status = 'approved'; // Update user status
        } catch (error) {
          console.error('Failed to approve user:', error.response?.data || error.message);
        }
      },
      async rejectUser(userId) {
        try {
          const res = await api.put(`/users/${userId}/reject`); // Make an API call to reject
          const user = this.users.find(u => u.id === userId);
          if (user) user.cin_status = 'rejected'; // Update user status
        } catch (error) {
          console.error('Failed to reject user:', error.response?.data || error.message);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .admin-panel-container {
    background-color: #f4f7fa;
    padding: 20px;
    min-height: 100vh;
  }
  
  .title {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #333;
  }
  
  .table-responsive {
    overflow-x: auto;
  }
  
  .user-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .user-table th,
  .user-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  .user-table th {
    background-color: var(--primary-color);
    color: #fff;
    font-weight: bold;
  }
  
  .user-table tr:hover {
    background-color: #f1f1f1;
  }
  
  .no-users-message {
    text-align: center;
    font-size: 1.1rem;
    color: #888;
    margin-top: 20px;
  }
  
  .btn-approve,
  .btn-reject {
    padding: 6px 12px;
    font-size: 0.9rem;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 8px;
  }
  
  .btn-approve {
    background-color: var(--primary-color);
    color: white;
    border: none;
  }
  
  .btn-approve:hover {
    background-color: var(--primary-hover);
  }
  
  .btn-reject {
    background-color: #c61c10;
    color: white;
    border: none;
  }
  
  .btn-reject:hover {
    background-color: #c61c10cd;
  }
  </style>
  