<template>
  <div class="map-container">
    <!-- Search Input -->
    <div class="search-bar">
      <input
        id="location-input"
        type="text"
        placeholder="Search for a location..."
        class="location-input"
      />
    </div>

    <div class="content-wrapper">
      <!-- Map on the left -->
      <GMapMap
        :center="center"
        :zoom="13"
        class="map"
      >
        <!-- Your Location Marker -->
        <GMapMarker :position="center" label="You" />

        <!-- Service Providers -->
        <GMapMarker
          v-for="(provider, index) in providers"
          :key="index"
          :position="{ lat: provider.latitude, lng: provider.longitude }"
          @click="handleMarkerClick(provider)"
        />
      </GMapMap>

      <!-- Provider info card on the right -->
      <div v-if="selectedProvider" class="provider-sidebar">
        <h3>{{ selectedProvider.name }}</h3>
        <p><strong>Service:</strong> {{ selectedProvider.service_type }}</p>
        <p><strong>Rating:</strong> {{ selectedProvider.rating }}</p>
        <p><strong>Email:</strong> {{ selectedProvider.email }}</p>
        <p><strong>Phone:</strong> {{ selectedProvider.phone }}</p>

        <button @click="requestService(selectedProvider)">Request Service</button>
        <button class="close" @click="selectedProvider = null">Close</button>
      </div>
    </div>
  </div>
</template>

  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const center = ref({ lat: 33.5731, lng: -7.5898 }) // Default: Casablanca
  const providers = ref([])
  const selectedProvider = ref(null)
  
  onMounted(async () => {
    // Get current location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          center.value = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
          }
        },
        () => {
          console.warn('Using default Morocco location')
        }
      )
    }
  
    // Fetch providers
    try {
      const res = await fetch('/api/service-providers/map')
      if (!res.ok) throw new Error('Failed to fetch providers')
      providers.value = await res.json()
    } catch (error) {
      console.error('Error fetching providers:', error)
    }
  
    // Init Google Places Autocomplete
    initAutocomplete()
  })
  
  function initAutocomplete() {
    const input = document.getElementById('location-input')
    if (!input || !window.google || !window.google.maps) {
      console.warn('Google Maps API or input element not ready')
      return
    }
  
    const autocomplete = new google.maps.places.Autocomplete(input)
    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace()
      if (!place.geometry) {
        alert('No details available for input: ' + place.name)
        return
      }
  
      const location = place.geometry.location
      center.value = {
        lat: location.lat(),
        lng: location.lng(),
      }
    })
  }
  
  function handleMarkerClick(provider) {
    selectedProvider.value = provider
    center.value = {
      lat: provider.latitude,
      lng: provider.longitude,
    }
  }
  
  function requestService(provider) {
    alert(`Request sent to ${provider.name}!`)
    // Later: replace with API call
  }
  </script>
  
  <style scoped>
.map-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 1rem;
}

.search-bar {
  margin-bottom: 1rem;
}

.location-input {
  width: 100%;
  padding: 12px 16px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  outline: none;
  transition: border-color 0.2s ease-in-out;
}

.location-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* Flex container for map and sidebar */
.content-wrapper {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

/* Map takes 2/3 width */
.map {
  flex: 2;
  height: 500px;
  border-radius: 12px;
  overflow: hidden;
}

/* Sidebar takes 1/3 width */
.provider-sidebar {
  flex: 1;
  background-color: white;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  min-width: 250px;
}

.provider-sidebar h3 {
  margin-top: 0;
}

.provider-sidebar button {
  margin-top: 12px;
  padding: 8px 12px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.provider-sidebar .close {
  background-color: #ccc;
  margin-left: 8px;
}

  </style>
  