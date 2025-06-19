<template>
  <div class="filter-container">
    <div class="filter-pills">
      <div 
        v-for="filter in filterOptions" 
        :key="filter.key"
        @click="openModal(filter.key)"
        :class="['filter-pill', { active: activeModal === filter.key }]">
        {{ filter.label }}
      </div>
    </div>

    <!-- Modal -->
    <div v-if="activeModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <header>
          <h3>{{ getModalTitle(activeModal) }}</h3>
          <button class="close-btn" @click="closeModal">&times;</button>
        </header>
        <section>
          <!-- Subject filter modal -->
          <div v-if="activeModal === 'type'" class="subject-filter">
  <label class="dropdown-label" for="level-select">
    Select Academic Level
    <button
      v-if="selectedLevel"
      type="button"
      class="clear-btn"
      @click.prevent="selectedLevel = ''; selectedSubcategory = ''; subcategories = []"
      aria-label="Clear Level selection"
    >
      &times;
    </button>
  </label>
  <select
    id="level-select"
    v-model="selectedLevel"
    class="custom-select"
  >
    <option value="" disabled>Select Teaching Level</option>
    <option v-for="level in levels" :key="level.id" :value="level.id">
      {{ level.name }}
    </option>
  </select>

  <label
    v-if="subcategories.length > 0"
    class="dropdown-label"
    for="subcategory-select"
  >
    Select Subject
    <button
      v-if="selectedSubcategory"
      type="button"
      class="clear-btn"
      @click.prevent="selectedSubcategory = ''"
      aria-label="Clear Subcategory selection"
    >
      &times;
    </button>
  </label>
  <select
    v-if="subcategories.length > 0"
    id="subcategory-select"
    v-model="selectedSubcategory"
    class="custom-select"
  >
    <option value="" disabled>Select subject</option>
    <option v-for="sub in subcategories" :key="sub.id" :value="sub.id">
      {{ sub.name }}
    </option>
  </select>
</div>

          <!-- Distance filter modal -->
          <div v-else-if="activeModal === 'distance'">
            <label>
              Distance (km):
              <input type="range" min="1" max="100" v-model="selectedDistance" />
              <span>{{ selectedDistance }} km</span>
            </label>
          </div>

          <!-- Price filter modal -->
          <div v-else-if="activeModal === 'price'">
            <label>
              Max Price (MAD):
              <input type="number" min="0" v-model="selectedPrice" />
            </label>
          </div>
        </section>
        <footer>
          <button class="btn-cancel" @click="applyFilters">Apply</button>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['filter-change']);
const props = defineProps({
  selectedCategoryId: [String, Number],
});

const activeModal = ref(null);
const selectedLevel = ref('');
const selectedSubcategory = ref('');
const selectedDistance = ref(10);
const selectedPrice = ref(50);

const levels = ref([]);
const subcategories = ref([]);

const filterOptions = [
  { key: 'type', label: 'Subject' },
  { key: 'distance', label: 'Distance' },
  { key: 'price', label: 'Price' },
];

const openModal = (filterKey) => {
  activeModal.value = filterKey;
};

const closeModal = () => {
  activeModal.value = null;
};

const getModalTitle = (key) => {
  const f = filterOptions.find(f => f.key === key);
  return f ? f.label : '';
};

// Fetch levels for the selected category
const fetchLevels = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/teaching-levels');
    levels.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch levels:', error);
  }
};

// Fetch subcategories for the selected level
const fetchSubcategories = async () => {
  if (!selectedLevel.value || !props.selectedCategoryId) {
    subcategories.value = [];
    return;
  }

  try {
    const response = await axios.get('http://localhost:8000/api/filtered-subcategories', {
      params: {
        levelId: selectedLevel.value,
        categoryId: props.selectedCategoryId,
      },
    });
    subcategories.value = response.data;
  } catch (error) {
    console.error('Failed to fetch subcategories:', error);
  }
};



// Re-fetch subcategories when level changes
watch(selectedLevel, () => {
  selectedSubcategory.value = '';
  fetchSubcategories();
});

// Re-fetch levels when category changes
watch(() => props.selectedCategoryId, () => {
  selectedLevel.value = '';
  selectedSubcategory.value = '';
  subcategories.value = [];
  fetchLevels();
});

// Apply filter and emit to parent
const applyFilters = () => {
  emit('filter-change', {
  category_id: props.selectedCategoryId,
  level: selectedLevel.value,
  subcategory: selectedSubcategory.value,
  distance: selectedDistance.value,
  price: selectedPrice.value,

});
  closeModal();
};

// Initial fetch
onMounted(() => {
  fetchLevels();
});
</script>


<style scoped>
:root {
  --primary-color: #1ca79b;
  --primary-color-light: #1ca79bdc;
  --primary-color-dark: #1d8b82;
  --pill-bg: #fff;
  --pill-border: #e5e7eb;
  --pill-hover-bg: #eff6ff;
  --pill-active-bg: #24d0c2ba;
  --pill-active-border: #1ca79b;
  --pill-active-color: #040303;
  --modal-bg: #fff;
  --modal-shadow: rgba(0, 0, 0, 0.12);
  --overlay-bg: rgba(0, 0, 0, 0.5);
  --text-primary: #111827;
  --text-secondary: #6b7280;
  --btn-border: #d1d5db;
  --btn-hover-bg: #f3f4f6;
}

.filter-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem 1rem;
  text-align: center;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.title {
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 2rem;
  line-height: 1.2;
  user-select: none;
}

.filter-pills {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
}

.filter-pill {
  position: relative;
  padding: 0.85rem 1.75rem;
  background: rgba(221, 220, 220, 0.453);
  border: 2px solid var(--pill-border);
  border-radius: 50px;
  font-size: 0.95rem;
  font-weight: 500;
  color: gray;
  cursor: pointer;
  user-select: none;
  white-space: nowrap;
  transition:
    color 0.3s ease,
    border-color 0.3s ease,
    box-shadow 0.3s ease,
    transform 0.2s ease;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.05);
  overflow: hidden;
}

.filter-pill::before {
  content: '';
  position: absolute;
  top: 0;
  left: -120%;
  width: 120%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(59, 130, 246, 0.15),
    transparent
  );
  transition: left 0.6s ease;
  pointer-events: none;
  z-index: 0;
}

.filter-pill:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(59, 130, 246, 0.2);
}

.filter-pill:hover::before {
  left: 100%;
}

.filter-pill.active {
  background: var(--pill-active-bg);
  border-color: var(--pill-active-border);
  color: var(--pill-active-color);
  box-shadow: 0 14px 40px rgba(36, 208, 194, 0.45);
  transform: translateY(-3px);
}

.filter-pill.active::before {
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.25),
    transparent
  );
  left: 100%;
}

.filter-pill:active {
  transform: translateY(0);
  transition: transform 0.1s ease;
}

/* Accessibility: keyboard focus */
.filter-pill:focus-visible {
  outline: 3px solid var(--primary-color-light);
  outline-offset: 3px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: var(--overlay-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeInOverlay 0.25s ease forwards;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 20px;
  padding: 1.5rem 2rem;
  width: 420px;
  max-width: 95vw;
  max-height: 40vh;
  box-shadow: 0 18px 50px rgba(36, 208, 194, 0.6);
  position: relative;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: var(--primary-color-light) transparent;
}

.modal::-webkit-scrollbar {
  width: 6px;
}

.modal::-webkit-scrollbar-track {
  background: transparent;
}

.modal::-webkit-scrollbar-thumb {
  background-color: var(--primary-color-light);
  border-radius: 10px;
}

.modal header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}

.modal header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--text-primary);
  user-select: none;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  line-height: 1;
  color: var(--text-secondary);
  transition: color 0.25s ease;
  padding: 0 0.5rem;
}

.close-btn:hover,
.close-btn:focus-visible {
  color: #ef4444; /* bright red */
  outline-offset: 4px;
  outline: 3px solid #ef4444;
  border-radius: 6px;
}

/* Modal content */
.modal section {
  font-size: 1rem;
  color: var(--text-secondary);
  margin-bottom: 1.2rem;
}

.modal label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 500;
  gap: 0.5rem;
}

.modal input[type="range"] {
  flex-grow: 1;
  margin: 0 0.5rem;
}

.modal input[type="number"] {
  width: 100%;
  padding: 0.4rem 0.5rem;
  border: 1.5px solid var(--pill-border);
  border-radius: 6px;
  font-size: 1rem;
  color: var(--text-primary);
  transition: border-color 0.3s ease;
}

.modal input[type="number"]:focus {
  border-color: var(--primary-color);
  outline: none;
}

select[multiple] {
  width: 100%;
  height: 7.5rem;
  padding: 0.5rem;
  border: 1.5px solid var(--pill-border);
  border-radius: 6px;
  font-size: 1rem;
  color: var(--text-primary);
  transition: border-color 0.3s ease;
  resize: none;
}

select[multiple]:focus {
  border-color: var(--primary-color);
  outline: none;
}

.modal footer {
  display: flex;
  justify-content: flex-end;
  padding-top: 0.2rem;
}

.btn-cancel {
  font-weight: 700;
  color: var(--primary-color-dark);
  border: 2px solid var(--primary-color);
  background-color: #e0f2fe;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  cursor: pointer;
  transition: background-color 0.3s ease, border-color 0.3s ease;
  user-select: none;
}

.btn-cancel:hover,
.btn-cancel:focus-visible {
  background-color: var(--primary-color);
  color: white;
  border-color: var(--primary-color-dark);
  outline: none;
}

/* Custom select styling */
.custom-select {
  appearance: none; /* Remove native arrow */
  -webkit-appearance: none;
  -moz-appearance: none;
  background-color: #fff;
  border: 1.5px solid #d1d5db;
  border-radius: 10px;
  padding: 0.5rem 1.5rem 0.5rem 1rem;
  font-size: 1rem;
  color: #111827;
  cursor: pointer;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3csvg fill='gray' height='12' viewBox='0 0 24 24' width='12' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 12px 12px;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.custom-select:hover {
  border-color: var(--primary-color);
}

.custom-select:focus {
  outline: none;
  border-color: var(--primary-color-dark);
  box-shadow: 0 0 6px var(--primary-color-light);
}

/* Clear button for dropdown */
.dropdown-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.3rem;
}

.clear-btn {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0 0.25rem;
  user-select: none;
  transition: color 0.3s ease;
}

.clear-btn:hover,
.clear-btn:focus {
  color: #ef4444;
  outline: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .title {
    font-size: 1.875rem;
    margin-bottom: 1.5rem;
  }

  .filter-pills {
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .filter-pill {
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
  }

  .modal {
    width: 340px;
    max-height: 40vh;
    padding: 1rem 1.25rem;
  }
}

@media (max-width: 480px) {
  .filter-container {
    padding: 1.5rem 1rem;
  }

  .title {
    font-size: 1.625rem;
    margin-bottom: 1.25rem;
  }

  .filter-pills {
    gap: 0.5rem;
    margin-top: 1.25rem;
  }

  .filter-pill {
    padding: 0.625rem 1.25rem;
    font-size: 0.85rem;
    flex: 1 1 auto;
    min-width: 120px;
  }

  .modal {
    width: 90vw;
    max-height: 50vh;
  }
}

/* Animations */
@keyframes fadeInOverlay {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Clear button for dropdown */
.dropdown-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.3rem;
  user-select: none;
}

.clear-btn {
  background: transparent;
  border: none;
  font-size: 1.4rem;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0 0.3rem;
  user-select: none;
  transition: color 0.3s ease;
  line-height: 1;
  font-weight: bold;
}

.clear-btn:hover,
.clear-btn:focus {
  color: #ef4444; /* bright red */
  outline: none;
}

/* Improved custom select styling */
.custom-select {
  appearance: none; /* Remove native arrow */
  -webkit-appearance: none;
  -moz-appearance: none;
  background-color: #fff;
  border: 1.5px solid #d1d5db;
  border-radius: 12px;
  padding: 0.5rem 2.5rem 0.5rem 1rem; /* space for arrow */
  font-size: 1rem;
  color: #111827;
  cursor: pointer;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3csvg fill='%23999' height='12' viewBox='0 0 24 24' width='12' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 12px 12px;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.custom-select:hover {
  border-color: var(--primary-color);
}

.custom-select:focus {
  outline: none;
  border-color: var(--primary-color-dark);
  box-shadow: 0 0 6px var(--primary-color-light);
}

/* Add some margin between selects */
.subject-filter select {
  margin-bottom: 1rem;
}

/* Label styling for clarity */
.subject-filter label {
  user-select: none;
}

/* Optionally add subtle box-shadow on focus for inputs */
.subject-filter select:focus {
  box-shadow: 0 0 6px var(--primary-color-light);
}

</style>
