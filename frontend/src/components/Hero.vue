<template>
  <section class="hero-wrapper">
    <div class="hero-content">
      <h1>Find your<br /><strong>perfect tutor</strong></h1>

      <div class="search-bar">
        <!-- Category Dropdown -->
        <div class="input-group">
          <select v-model="selectedCategory" class="category-dropdown">
  <option disabled value="">Select a Subject</option>
  <option
    v-for="category in categories"
    :key="category.id"
    :value="category.id"
  >
    {{ category.label }}
  </option>
</select>

        </div>

        <div class="separator"></div>

        <!-- Location Popup -->
        <div class="input-group location-group" @click="toggleDropdown" @blur="hideDropdown" tabindex="0">
          <input type="text" placeholder="Online or Nearby" :value="locationInput" readonly />
          <div v-if="showDropdown" class="popup-options">
            <div class="option" @click.stop="selectOption('Around me')">Around me</div>
            <div class="option" @click.stop="selectOption('Online')">Online</div>
          </div>
        </div>

        <!-- Search Button -->
        <button class="search-button" @click="onSearch">Search</button>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      showDropdown: false,
      selectedCategory: '',
      locationMode: '',
      locationInput: '',
      categories: [],
      coordinates: {
        lat: null,
        lng: null
      }
    };
  },
  mounted() {
    axios.get('http://localhost:8000/api/categories')
      .then(response => {
        this.categories = response.data;
      })
      .catch(error => {
        console.error("Failed to load categories:", error);
      });
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    hideDropdown() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
    selectOption(option) {
  this.locationMode = option === 'Around me' ? 'around' : 'online';
  this.locationInput = option;
  this.showDropdown = false;

  if (this.locationMode === 'around') {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          this.coordinates.lat = position.coords.latitude;
          this.coordinates.lng = position.coords.longitude;

          // Optionally, if you want to auto-search immediately here:
          // this.onSearch();
        },
        (error) => {
          alert("Unable to access your location.");
          console.error(error);
        }
      );
    } else {
      alert("Geolocation is not supported by your browser.");
    }
  }
},

    onSearch() {
      if (this.locationMode === 'around') {
    if (this.coordinates.lat && this.coordinates.lng) {
      this.$router.push({
        name: 'FindTutors',
        query: {
          category_id: this.selectedCategory,
          mode: this.locationMode,
          lat: this.coordinates.lat,
          lng: this.coordinates.lng,
        },
      });
    } else {
      alert("Waiting for your location. Please try again.");
    }
  } else if (this.locationMode === 'online') {
    this.$router.push({
  name: 'OnlineTutors',
  query: {
    category: this.selectedCategory,
    mode: this.locationMode,
    location: this.locationInput
  }
});

  } else {
    alert("Please select a location option.");
  }
},

deleteCategory(categoryId) {
    axios.delete(`http://localhost:8000/api/categories/${categoryId}`)
      .then(() => {
        // Refetch categories after successful delete
        return axios.get('http://localhost:8000/api/categories');
      })
      .then(response => {
        this.categories = response.data;
      })
      .catch(error => {
        console.error("Failed to delete or refresh categories:", error);
      });
  }


  }
};
</script>


<style scoped>
.hero-wrapper {
  background: linear-gradient(to bottom, #fff, #1ca79b55);
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  text-align: center;
  font-family: 'Arial', sans-serif;
}

.hero-content h1 {
  font-size: 4rem;
  font-weight: 700;
  margin-bottom: 2rem;
  color: #222;
}

.search-bar {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  max-width: 900px;
  margin: 0 auto 2rem;
  background: white;
  border-radius: 50px;
  padding: 0.5rem 1rem;
  box-shadow: 0 5px 15px rgba(255, 94, 94, 0.1);
}

.input-group {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  flex: 1;
  min-width: 200px;
}

.input-group input {
  border: none;
  outline: none;
  margin-left: 0.5rem;
  flex: 1;
}

.category-dropdown {
  border: none;
  padding: 0.5rem;
  border-radius: 10px;
  flex: 1;
  outline: none;
  font-size: 1rem;
  background-color: #fff;
  width: 100%;
}

.search-button {
  background: var(--primary-color, #ff5e5e);
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 30px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease;
}

.search-button:hover {
  background: var(--primary-hover, #ff4040);
}

.separator {
  width: 3px;
  background-color: #e5e7eb;
  height: 2rem;
  align-self: center;
}

.location-group {
  position: relative;
}

.popup-options {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 0.5rem;
  background: white;
  border-radius: 1rem;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
  padding: 0.75rem 1rem;
  z-index: 10;
  width: max-content;
  min-width: 150px;
}

.popup-options .option {
  padding: 0.5rem 0;
  cursor: pointer;
  font-size: 0.95rem;
  white-space: nowrap;
}

.popup-options .option:hover {
  color: var(--primary-color);
}
</style>
