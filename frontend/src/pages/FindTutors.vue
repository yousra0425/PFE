<template>
  <div>
    <h3>Find Tutors Near You</h3>
    <FilterBar :selectedCategoryId="selectedCategory" @filter-change="handleFilterChange" />
    <Map :tutors="tutors" />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Map from '../components/Map.vue';
import FilterBar from '../components/FilterBar.vue';

const route = useRoute();

const parseCoordinate = (coord) => {
  const n = Number(coord);
  return isNaN(n) ? null : n;
};

const tutors = ref([]);
const userPosition = ref({
  lat: parseCoordinate(route.query.lat),
  lng: parseCoordinate(route.query.lng),
});

const selectedCategory = ref(null);

const fetchTutors = async (filters = {}) => {
  // Prioritize category_id, fallback to category from query
  const categoryId = Number(route.query.category_id || route.query.category);
  const latitude = userPosition.value.lat;
  const longitude = userPosition.value.lng;

  if (!categoryId || latitude === null || longitude === null) {
    console.warn('Missing required params:', { categoryId, latitude, longitude });
    tutors.value = [];
    return;
  }

  try {
    const response = await axios.get('http://localhost:8000/api/tutors', {
      params: {
        category_id: categoryId,
        latitude,
        longitude,
        ...filters
      },
      withCredentials: true,
    });
    
    tutors.value = (response.data.tutors || []).map(tutor => ({
      ...tutor,
      latitude: Number(tutor.latitude),
      longitude: Number(tutor.longitude),
      working_radius: Number(tutor.working_radius) || 5,
      rating: tutor.rating || 4.2,
      email: tutor.email,
      telephone: tutor.telephone,
      reviews: tutor.reviews || [],
    }));
  } catch (err) {
    console.error('Error fetching tutors:', err);
    tutors.value = [];
  }
};

// Set selectedCategory based on route query on mount and watch route for changes
onMounted(() => {
  selectedCategory.value = Number(route.query.category_id || route.query.category || null);

  const mode = route.query.mode;
  const hasCoords = userPosition.value.lat !== null && userPosition.value.lng !== null;

  if (mode === 'around' && !hasCoords) {
    if (!navigator.geolocation) {
      console.error('Geolocation not supported');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        userPosition.value = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };
        fetchTutors();
      },
      (error) => {
        console.error('Geolocation error:', error);
      }
    );
  } else if (hasCoords) {
    fetchTutors();
  } else {
    console.warn('No valid coordinates to fetch tutors');
  }
});

// Optional: react to route query changes (e.g., category change) to refetch tutors
watch(
  () => route.query,
  (newQuery, oldQuery) => {
    const newCategory = Number(newQuery.category_id || newQuery.category || null);
    if (newCategory !== selectedCategory.value) {
      selectedCategory.value = newCategory;
      fetchTutors();
    }
  }
);

const handleFilterChange = (filters) => {
  fetchTutors(filters);
};
</script>

<style scoped>
h3 {
  font-weight: 700;
  font-size: 1.6rem;
  color: #2c3e50;
  margin: 0;
  text-align: center;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--primary-color);
}
</style>
