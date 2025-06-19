<template>
    <div class="map-container">
        <div class="search-bar">
      <input
        id="location-input"
        type="text"
        placeholder="Search for a location..."
        class="location-input"
      />
    </div>
      <div class="content-wrapper">
        <GMapMap :center="mapCenter" :zoom="13" class="map">
  <template v-for="booking in bookings" :key="booking.id">
    <GMapCircle
      :center="{ lat: booking.user.latitude, lng: booking.user.longitude }"
      :radius="500"
      :options="circleOptions"
      @click="selectedBooking = booking"
    />
    <GMapMarker
      :position="{ lat: booking.user.latitude, lng: booking.user.longitude }"
      @click="selectedBooking = booking"
    />
  </template>
</GMapMap>


  
        <div v-if="selectedBooking" class="tutor-sidebar">
  <div class="tutor-card enhanced-card">
    <div class="avatar">
      <span>{{ selectedBooking.user.name[0] }}</span>
    </div>

    <h3 class="name">{{ selectedBooking.user.name }}</h3>

    <div class="details">
      <p class="info"><i class="fas fa-book"></i> {{ selectedBooking.subject }}</p>
      <p class="info"><i class="fas fa-calendar-alt"></i> {{ selectedBooking.date }}</p>
      <p class="info"><i class="fas fa-clock"></i> {{ selectedBooking.time }}</p>
      <p class="info"><i class="fas fa-hourglass-half"></i> {{ selectedBooking.duration }} hrs</p>
      <p v-if="selectedBooking.message" class="info"><i class="fas fa-comment"></i> {{ selectedBooking.message }}</p>

      <p class="info"><i class="fas fa-envelope"></i> {{ selectedBooking.user.email }}</p>
      <p class="info"><i class="fas fa-phone"></i> {{ selectedBooking.user.phone }}</p>
    </div>

    <div class="actions">
      <button class="request" @click="acceptBooking(selectedBooking.id)">Accept</button>
      <button class="close" @click="declineBooking(selectedBooking.id)">Decline</button>
      <button class="close" @click="selectedBooking = null">Close</button>
    </div>
  </div>
</div>


  
        <div v-if="!bookings.length" class="no-providers">
          <p>No pending bookings found.</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  const bookings = ref([])
  const selectedBooking = ref(null)
  const mapCenter = ref({ lat: 35.7833, lng: -5.8000 }) // Default to Tangier, Morocco
  
  const circleOptions = {
  fillColor: '#4285F4',
  fillOpacity: 0.3,
  strokeColor: '#4285F4',
  strokeOpacity: 0.8,
  strokeWeight: 2,
  clickable: true,
  draggable: false,
  editable: false,
  visible: true,
  radius: 500, // default radius (in meters)
  zIndex: 1
}


  onMounted(() => {
  bookings.value = [
    {
      id: 1,
      user: {
        name: 'Alice Johnson',
        email: 'alice@example.com',
        phone: '+212612345678',
        latitude: 35.767234,
        longitude: -5.799751,
      },
      subject: 'Math',
      status: 'pending',
      date: '2025-06-05',
      time: '15:00',
      duration: 1.5,
      message: 'Please bring practice questions.',
    },
    {
      id: 2,
      user: {
        name: 'Mohamed El Amrani',
        email: 'mohamed@example.com',
        phone: '+212612345679',
        latitude: 35.769812,
        longitude: -5.811234,
      },
      subject: 'Physics',
      status: 'pending',
      date: '2025-06-06',
      time: '11:00',
      duration: 2,
      message: 'My son is in 9th grade, we need help with mechanics.',
    },
    {
      id: 3,
      user: {
        name: 'Fatima Zahra',
        email: 'fatima@example.com',
        phone: '+212612345680',
        latitude: 35.765420,
        longitude: -5.794132,
      },
      subject: 'Chemistry',
      status: 'pending',
      date: '2025-06-07',
      time: '09:30',
      duration: 1,
      message: '',
    }
  ]

  mapCenter.value = {
    lat: 35.767234,
    lng: -5.799751,
  }
})

  
  /*const acceptBooking = async (id) => {
    try {
      await axios.post(`http://localhost:8000/api/bookings/${id}/accept`)
      alert('Booking accepted!')
      bookings.value = bookings.value.filter(b => b.id !== id)
      selectedBooking.value = null
    } catch (e) {
      alert('Failed to accept booking.')
    }
  }*/

  /*const declineBooking = async (id) => {
  try {
    await axios.post(`http://localhost:8000/api/bookings/${id}/decline`)
    alert('Booking declined!')
    bookings.value = bookings.value.filter(b => b.id !== id)
    selectedBooking.value = null
  } catch (e) {
    alert('Failed to decline booking.')
  }
}*/
const acceptBooking = async (id) => {
  alert(`Mock accept booking ${id}`)
  bookings.value = bookings.value.filter(b => b.id !== id)
  selectedBooking.value = null
}

const declineBooking = async (id) => {
  alert(`Mock decline booking ${id}`)
  bookings.value = bookings.value.filter(b => b.id !== id)
  selectedBooking.value = null
}

  </script>
  
  <style scoped>
  .map-container {
    max-width: 1400px;
    margin: 0 auto;
    margin-bottom: 3rem;
    padding: 1rem;
  }
  .content-wrapper {
    display: flex;
    flex-direction: row;
    gap: 2rem;
  }
  @media (max-width: 768px) {
    .content-wrapper {
      flex-direction: column;
    }
    .map {
      height: 300px;
    }
  }

  .search-bar {
  margin-bottom: 1rem;
}
.location-input {
  width: 100%;
  padding: 14px 18px;
  font-size: 16px;
  border: 2px solid #d1d5db;
  border-radius: 12px;
  outline: none;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.location-input:focus {
  border-color: #9e9e9e;
  box-shadow: 0 0 0 4px rgba(83, 221, 231, 0.2);
}
  .map {
    flex: 3;
    height: 500px;
    border-radius: 12px;
  }
  
  .tutor-sidebar {
    flex: 2;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .tutor-card {
    width: 100%;
    background: linear-gradient(135deg, #f9f9f9, #ffffff);
    padding: 24px;
    border-radius: 20px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    text-align: center;
    transition: transform 0.3s ease;
  }
  .tutor-card:hover {
    transform: translateY(-2px);
  }
  .avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    background: #000000;
    color: white;
    font-weight: bold;
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  .name {
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
  }
  .info {
    margin-bottom: 0.3rem;
  }
  .actions {
    margin-top: 1rem;
  }
  .request,
  .close {
    margin: 0 6px;
    padding: 10px 16px;
    font-size: 14px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  .request {
    background-color: var(--primary-color);
    color: white;
  }
  .close {
    background-color: #e5e5e5;
  }
  .request:hover {
    background-color: var(--primary-hover);
  }
  .close:hover {
    background-color: #cfcfcf;
  }
  .no-providers {
    text-align: center;
    margin-top: 2rem;
    flex: 1;
  }

  .enhanced-card {
  background: #fff;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.details {
  margin-top: 1rem;
  font-size: 0.95rem;
  line-height: 1.5;
}
.info {
  font-size: 14px;
  margin-bottom: 6px;
  color: #444;
}
.info i {
  margin-right: 6px;
  color: var(--primary-color);
}
.actions {
  margin-top: 1.5rem;
  display: flex;
  gap: 0.5rem;
}
.actions button {
  flex: 1;
}

  </style>
  