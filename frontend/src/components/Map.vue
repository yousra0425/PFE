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
      <GMapMap
        :center="center"
        :zoom="13"
        class="map"
      >
        <!-- Current User Location -->
        <GMapMarker :position="center" label="You" />

        <!-- Tutors -->
<!-- Tutors -->
<GMapMarker
  v-for="(tutor, index) in tutors"
  :key="index"
  :position="{ lat: tutor.latitude, lng: tutor.longitude }"
  @click="handleMarkerClick(tutor)"
/>

<!-- Tutor Working Radius Circles -->
<GMapCircle
  v-for="(tutor, index) in tutors"
  :key="'circle-' + index"
  :center="{ lat: tutor.latitude, lng: tutor.longitude }"
  :radius="tutor.working_radius * 1000"  
  :options="{
    strokeColor: '#3b82f6',
    strokeOpacity: 0.5,
    strokeWeight: 2,
    fillColor: '#3b82f6',
    fillOpacity: 0.15
  }"
/>

      </GMapMap>

      <div v-if="selectedTutor" class="provider-sidebar">
        <div class="tutor-card">
          <div class="avatar">
  <img
    :src="selectedTutor.profile_picture || defaultImage"
    :alt="selectedTutor.first_name + ' ' + selectedTutor.last_name || 'Tutor image'"
    class="tutor-img"
  />
</div>


  <h3 class="name">{{ selectedTutor.first_name }} {{ selectedTutor.last_name }}</h3>
  
  <!-- ✅ New line to show category -->
  <p class="info"><i class="fas fa-book"></i> Subject: {{ selectedTutor.categoryLabel }}</p>

  <p class="info"><i class="fas fa-envelope"></i> {{ selectedTutor.email }}</p>
  <p class="info"><i class="fas fa-phone"></i> {{ selectedTutor.telephone }}</p>
  <p class="info"><i class="fas fa-money-bill-wave"></i> {{ selectedTutor.hourly_rate || 150 }} MAD/hr</p>


          <div class="rating">
            <i
              v-for="n in 5"
              :key="n"
              class="fa-star"
              :class="n <= Math.round(selectedTutor.rating || 4.2) ? 'fas filled' : 'far'"
            ></i>
            <span class="rating-number">{{ selectedTutor.rating || 4.2 }}/5</span>
          </div>

          <div class="actions">
      <button class="request" @click="requestService(selectedTutor)">Send Request</button>
      <button class="close" @click="selectedTutor = null">Close</button>
    </div>


<BookLesson
  v-if="showBookingModal"
  :show="showBookingModal"
  :tutor="selectedTutor"
  @close="handleCloseBooking"
  @submitted="handleCloseBooking"
/>

          <div class="reviews" v-if="selectedTutor.reviews && selectedTutor.reviews.length">
            <h4 class="reviews-title">Reviews</h4>
            <div v-for="(review, index) in selectedTutor.reviews" :key="index" class="review">
              <p class="review-comment">“{{ review.comment }}”</p>
              <div class="review-rating">
                <i
                  v-for="n in 5"
                  :key="n"
                  class="fa-star"
                  :class="n <= review.rating ? 'fas filled' : 'far'"
                ></i>
                <span class="reviewer-name">- {{ review.reviewer_name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="noTutorsFound" class="no-providers">
        <p>No tutors found near this location.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import BookLesson from './BookLesson.vue'

const center = ref({ lat: 35.7833, lng: -5.8000 }) // default center (Tangier)
const tutors = ref([])
const categories = ref([])
const selectedTutor = ref(null)
const noTutorsFound = ref(false)
const route = useRoute()
const showBookingModal = ref(false)

// Fetch categories from backend
async function fetchCategories() {
  try {
    const response = await axios.get('http://localhost:8000/api/categories', { withCredentials: true })
    categories.value = response.data || []
    console.log('Categories fetched:', categories.value)
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

watch(
  () => route.query,
  (newQuery) => {
    const lat = parseFloat(newQuery.lat)
    const lng = parseFloat(newQuery.lng)
    const categoryId = Number(newQuery.category || newQuery.category_id)
    if (!isNaN(lat) && !isNaN(lng) && categoryId) {
      center.value = { lat, lng }
      fetchTutors(lat, lng, categoryId)
    }
  },
  { immediate: true }
)

async function fetchTutors(lat, lng, categoryId) {
  try {
    const response = await axios.get('http://localhost:8000/api/tutors', {
      params: {
        category_id: categoryId,
        latitude: lat,
        longitude: lng,
      },
      withCredentials: true,
    });

    console.log("Tutors API response:", response.data.tutors);

    tutors.value = (response.data.tutors || []).map(tutor => {
  let radiusKm = Number(tutor.working_radius);
  if (isNaN(radiusKm) || radiusKm <= 0) {
    radiusKm = 5;
  }
  if (radiusKm > 20) radiusKm = 10;

  return {
    ...tutor,
    latitude: Number(tutor.latitude),
    longitude: Number(tutor.longitude),
    working_radius: radiusKm,
    categoryLabel: tutor.category || 'Unknown',
    rating: tutor.rating || 4.2,
    profile_picture: fixProfilePictureUrl(tutor.profile_picture),
    // you can keep your reviews mock here or fetch real ones
    reviews: [
      {
        reviewer_name: 'Fatima Z.',
        rating: 5,
        comment: 'Excellent tutor, very patient and helpful!'
      },
      {
        reviewer_name: 'Youssef M.',
        rating: 4,
        comment: 'Good explanations and always on time.'
      }
    ]
  }
})


    noTutorsFound.value = tutors.value.length === 0;
  } catch (err) {
    console.error('Fetch error:', err);
    tutors.value = [];
    noTutorsFound.value = true;
  }
}

function fixProfilePictureUrl(url) {
  if (!url) return null

  // If the url contains 'http' more than once (double URL)
  const httpCount = (url.match(/http/g) || []).length

  if (httpCount > 1) {
    // Extract the second http and onwards (the real external URL)
    const secondHttpIndex = url.indexOf('http', url.indexOf('http') + 1)
    return url.substring(secondHttpIndex)
  }

  // If url is a proper full URL, return as is
  if (url.startsWith('http')) {
    return url
  }

  // Otherwise assume relative URL and prepend backend base url
  return 'http://127.0.0.1:8000' + url
}


function initAutocomplete() {
  const input = document.getElementById('location-input')
  if (!input || !window.google?.maps) return

  const autocomplete = new google.maps.places.Autocomplete(input)
  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace()
    if (!place.geometry) return alert('No geometry found')
    const location = place.geometry.location
    center.value = { lat: location.lat(), lng: location.lng() }
    fetchTutors(location.lat(), location.lng(), Number(route.query.category))
  })
}

onMounted(async () => {
  await fetchCategories() // fetch categories first
  initAutocomplete()

  // If no lat/lng query, try to get user location
  if (!route.query.lat || !route.query.lng) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const { latitude, longitude } = pos.coords
          center.value = { lat: latitude, lng: longitude }
          fetchTutors(latitude, longitude, Number(route.query.category))
        },
        () => {
          console.warn("Geolocation failed or denied, using default location")
        }
      )
    }
  }
})

function handleMarkerClick(tutor) {
  selectedTutor.value = tutor
}

function requestService(tutor) {
  selectedTutor.value = tutor
  showBookingModal.value = true
}

function handleCloseBooking() {
  showBookingModal.value = false
  selectedTutor.value = null
}

onMounted(async () => {
  await fetchCategories()
  initAutocomplete()

  if (!route.query.lat || !route.query.lng) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const { latitude, longitude } = pos.coords
          center.value = { lat: latitude, lng: longitude }
          fetchTutors(latitude, longitude, Number(route.query.category))
        },
        () => {
          console.warn("Geolocation failed or denied, using default location")
        }
      )
    }
  }
})
</script>


<style scoped>
.map-container {
  max-width: 1400px;
  margin: 0 auto;
  margin-bottom: 3rem;
  padding: 1rem;
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

.tutor-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
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
.map {
  flex: 3;
  height: 500px;
  border-radius: 12px;
}
.provider-sidebar {
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
  user-select: none;
}
.name {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
  color: #1a1a1a;
}
.info i{
  font-size: 1rem;
  color: var(--primary-color);
  margin: 6px 0;
}
.rating {
  margin-top: 12px;
  margin-bottom: 16px;
  font-size: 1.4rem;
  color: #f5a623;
}
.fa-star {
  margin-right: 4px;
}
.filled {
  color: #fac751;
}
.rating-number {
  margin-left: 8px;
  font-weight: 600;
  color: #444;
}
.actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1rem;
}
.actions button {
  padding: 12px 24px;
  border-radius: 30px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  transition: background-color 0.3s ease;
}
.request {
  background-color: var(--primary-color);
  color: white;
}
.request:hover {
  background-color: var(--primary-hover);
}
.close {
  background-color: #d6d3d3;
  color: #333;
}
.close:hover {
  background-color: #e0e0e0;
}
.reviews {
  text-align: left;
  margin-top: 16px;
  max-height: 200px;
  overflow-y: auto;
}
.reviews-title {
  font-size: 1.3rem;
  margin-bottom: 12px;
  font-weight: 600;
}
.review {
  margin-bottom: 10px;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}
.review-comment {
  font-style: italic;
  margin-bottom: 6px;
  color: #555;
}
.review-rating {
  color: #f5a623;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 6px;
}
.reviewer-name {
  font-weight: 600;
  color: #777;
  margin-left: 8px;
}
.no-providers {
  flex: 2;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.4rem;
  font-weight: 600;
  color: #777;
  background-color: #f7f7f7;
  border-radius: 12px;
  padding: 20px;
  height: 500px;
}
</style>
