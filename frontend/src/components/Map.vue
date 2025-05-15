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
  
      <!-- Map -->
      <GMapMap
        :center="center"
        :zoom="13"
        style="width: 100%; height: 500px"
      >
        <!-- Your Location Marker -->
        <GMapMarker :position="center" label="You" />
  
        <!-- Service Providers -->
        <GMapMarker
          v-for="(provider, index) in providers"
          :key="index"
          :position="{ lat: provider.latitude, lng: provider.longitude }"
        >
          <GMapInfoWindow>
            <div>
              <strong>{{ provider.name }}</strong><br />
              Rating: {{ provider.rating }}<br />
              {{ provider.service_name }}
            </div>
          </GMapInfoWindow>
        </GMapMarker>
      </GMapMap>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, defineProps } from 'vue';
  
  const center = ref({ lat: 33.5731, lng: -7.5898 }); // Morocco (Casablanca) default
  
  const props = defineProps({
    providers: {
      type: Array,
      required: true,
    },
  });
  
  onMounted(() => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          center.value = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
          };
        },
        () => {
          console.warn('Using default Morocco location');
        }
      );
    }
  
    initAutocomplete();
  });
  
  function initAutocomplete() {
    const input = document.getElementById('location-input');
    if (!window.google || !window.google.maps) {
      console.warn('Google Maps JS API not loaded');
      return;
    }
  
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (!place.geometry) {
        alert('No details available for input: ' + place.name);
        return;
      }
  
      const location = place.geometry.location;
      center.value = {
        lat: location.lat(),
        lng: location.lng(),
      };
    });
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
    border-color: #3b82f6; /* blue */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
  }
  </style>
  