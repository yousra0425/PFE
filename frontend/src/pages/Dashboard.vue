<template>
  <div class="dashboard-wrapper">
    <h2 class="dashboard-title">Welcome, {{ profile.first_name }} {{ profile.last_name }} </h2>

    <!-- Tabs -->
    <div class="nav-tabs">
      <span :class="{ active: activeTab === 'appointments' }" @click="activeTab = 'appointments'">Appointments</span>
      <span :class="{ active: activeTab === 'profile' }" @click="activeTab = 'profile'">Profile</span>
    </div>

    <!-- APPOINTMENTS SECTION -->
    <div v-if="activeTab === 'appointments'">
      <h3>Upcoming Appointments</h3>
      <div class="card" v-for="appointment in appointments" :key="appointment.id">
        <div>
          <strong>{{ appointment.title }}</strong><br />
          {{ appointment.provider }}<br />
          Date: {{ appointment.date }}<br />
          Time: {{ appointment.time }}
        </div>
        <button @click="reschedule(appointment.id)">Reschedule</button>
      </div>
    </div>

    <!-- PROFILE SECTION -->
    <!-- Profile Card -->
    <div v-if="activeTab === 'profile'" class="profile-card">
      <div class="card-header">
        <h4>Profile Information</h4>
        <button v-if="!isEditing" @click="startEditing" class="edit-btn">Edit Profile</button>
      </div>

      <!-- View Mode -->
      <div v-if="!isEditing" class="card-body">
        <div class="info-grid">
          <div>
            <small class="label">Full Name</small>
            <div class="value"><strong>{{ profile.first_name }} {{ profile.last_name }}</strong></div>
          </div>
          <div>
            <small class="label">Email</small>
            <div class="value"><strong>{{ profile.email }}</strong></div>
          </div>
          <div>
            <small class="label">Phone</small>
            <div class="value"><strong>{{ profile.telephone }}</strong></div>
          </div>
          <div>
            <small class="label">Address</small>
            <div class="value"><strong>{{ profile.address }}</strong></div>
          </div>
        </div>
      </div>

      <!-- Edit Mode -->
      <div v-else class="card-body">
        <div class="form-grid">
          <div>
            <label>First Name</label>
            <input v-model="editProfile.first_name" type="text" class="form-control" />
          </div>
          <div>
            <label>Last Name</label>
            <input v-model="editProfile.last_name" type="text" class="form-control" />
          </div>
          <div>
            <label>Email</label>
            <input v-model="editProfile.email" type="email" class="form-control" />
          </div>
          <div>
            <label>Phone</label>
            <input v-model="editProfile.telephone" type="text" class="form-control" />
          </div>
          <div class="col-12">
            <label>Address</label>
            <textarea v-model="editProfile.address" class="form-control"></textarea>
          </div>
        </div>
        <div class="edit-actions">
          <button @click="saveProfile" class="btn btn-primary">Save Changes</button>
          <button @click="cancelEditing" class="btn btn-secondary">Cancel</button>
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
      isEditing: false,
      profile: {
        first_name: '',
        last_name: '',
        telephone: '',
        email: '',
        address: ''
      },
      activeTab: 'appointments', // default tab
      appointments: [
        {
          id: 1,
          title: 'House Cleaning',
          provider: 'CleanTeam Services',
          date: '6/10/2023',
          time: '10:00 AM - 12:00 PM',
        },
        {
          id: 2,
          title: 'Lawn Mowing',
          provider: 'Green Gardens',
          date: '6/15/2023',
          time: '2:00 PM - 3:00 PM',
        },
        {
          id: 3,
          title: 'AC Maintenance',
          provider: 'CoolAir HVAC',
          date: '6/22/2023',
          time: '9:00 AM - 11:00 AM',
        },
      ],
      editProfile: {}
    };
  },
  methods: {
    startEditing() {
      this.editProfile = { ...this.profile };
      this.isEditing = true;
    },
    cancelEditing() {
      this.isEditing = false;
    },
    saveProfile() {
      this.profile = { ...this.editProfile };
      localStorage.setItem('user', JSON.stringify(this.profile));
      this.isEditing = false;
    },
    loadProfile() {
      const user = JSON.parse(localStorage.getItem('user'));
      if (user) {
        this.profile = {
          first_name: user.first_name || '',
          last_name: user.last_name || '',
          telephone: user.telephone || '',
          email: user.email || '',
          address: user.address || ''
        };
      } else {
        this.$router.push('/login');
      }
    }
  },
  created() {
    this.loadProfile();
  }
};
</script>

<style scoped>
.dashboard-wrapper {
  max-width: 900px;
  margin: 40px auto;
  padding: 0 20px;
  font-family: Arial, sans-serif;
}

.dashboard-title {
  font-weight: bolder;
  font-size: 40px;
  margin-bottom: 30px;
}

.nav-tabs {
  display: flex;
  background-color: #f4f6f8;
  padding: 10px;
  border-radius: 8px;
  width: fit-content;
  gap: 20px;
  margin-bottom: 20px;
}

.nav-tabs span {
  cursor: pointer;
  padding: 6px 14px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

.nav-tabs span.active {
  background-color: white;
  font-weight: 600;
  color: #1f2937;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

h3{
  font-weight: bolder ;
}
.appointments-list {
  list-style: none;
  padding: 0;
}

.appointment-item {
  background: #f9f9f9;
  border-left: 5px solid #2196F3;
  padding: 10px;
  margin: 10px 0;
}

.profile-card {
  background: white;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.card {
  background: white;
  padding: 15px 20px;
  margin-bottom: 12px;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

button {
  background-color: white;
  border: 1px solid #cbd5e0;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #1f2937;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #f1f5f9;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

h4 {
  font-weight: bolder;
}

.edit-btn {
  padding: 6px 12px;
  background-color: transparent;
  border: 1px solid #0d6efd;
  color: #0d6efd;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
}

.edit-btn:hover {
  background-color: #0d6efd;
  color: white;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.label {
  font-size: 12px;
  color: #777;
}

.value {
  margin-top: 5px;
}

.edit-actions {
  display: flex;
  gap: 10px;
}
</style>
