<template>
    <div class="tutors-listing">
      <!-- Header -->
      <div class="header-section">
        <h1>Online Danish tutors & teachers for private lessons</h1>
        <div class="level-indicator">Level 15: Item Danish</div>
  
        <!-- Filters -->
        <div class="filters-section">
          <!-- Subject Dropdown -->
          <div class="dropdown filter-item">
            <button class="btn btn-filter dropdown-toggle" type="button" id="learnDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              I want to learn <strong>{{ selectedSubject || 'English' }}</strong>
            </button>
            <ul class="dropdown-menu" aria-labelledby="learnDropdown">
              <li v-for="subject in subjects" :key="subject">
                <a class="dropdown-item" href="#" @click.prevent="selectSubject(subject)">
                  {{ subject }}
                </a>
              </li>
            </ul>
          </div>
  
          <!-- Price Slider -->
          <div class="filter-item price-filter">
            <div class="filter-label">Price per lesson</div>
            <div class="price-slider-container">
              <input
                type="range"
                v-model="minPrice"
                :min="minRange"
                :max="fixedMaxPrice"
                step="10"
                class="single-slider"
              />
              <div class="price-display">
                <span>{{ minPrice }}dh – {{ fixedMaxPrice }}dh</span>
              </div>
            </div>
          </div>
  
          <!-- Other Filters (Static) -->
          <div class="filter-item" v-for="(label, index) in extraFilters" :key="index">
            <div class="filter-label">{{ label }}</div>
            <div>▼</div>
          </div>
  
          <div class="filter-item sort-options">
            <div class="filter-label">Sort by:</div>
            <div>Our top picks ▼</div>
            <input type="text" placeholder="Search by name or keyword" class="search-input">
          </div>
        </div>
      </div>
  
      <!-- Tutors List -->
      <div class="tutors-list">
        <h2>{{ filteredTutors.length }} Danish teachers that match your needs</h2>
  
        <div class="tutor-card" v-for="tutor in filteredTutors" :key="tutor.id">
          <div class="tutor-header">
            <span class="tutor-name">{{ tutor.name }}</span>
            <span class="badges">
              <span class="badge badge-primary" v-if="tutor.professional">Professional</span>
              <span class="badge badge-success" v-if="tutor.expert">Expert Tutor</span>
            </span>
          </div>
  
          <div class="tutor-subject">{{ tutor.subject }}</div>
  
          <div class="tutor-stats">
            <span>{{ tutor.activeStudents }} active students</span>
            <span>{{ tutor.lessons }} lessons</span>
          </div>
  
          <div class="tutor-languages">
            Speaks:
            <span v-for="(lang, index) in tutor.languages" :key="index">
              {{ lang.language }} ({{ lang.proficiency }})<span v-if="index < tutor.languages.length - 1">, </span>
            </span>
          </div>
  
          <div class="tutor-bio">{{ tutor.bio }}</div>
  
          <div class="tutor-pricing">
            <span class="price">{{ tutor.price }}dh</span>
            <span class="reviews">{{ tutor.reviews }} reviews</span>
            <span class="lesson-duration">{{ tutor.lessonDuration }}-min lesson</span>
          </div>
  
          <div class="tutor-actions">
            <button class="btn btn-primary trial-btn">Book trial lesson</button>
            <button class="btn btn-outline-secondary message-btn">Send message</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'TutorsListing',
    data() {
      return {
        selectedSubject: '',
        subjects: ['English', 'Danish', 'Math', 'Science'],
        fixedMaxPrice: 300,
        minRange: 0,
        minPrice: 50,
        extraFilters: ['Country of birth', "I'm available", 'Specialties', 'Also speaks', 'Native speaker', 'Tutor categories'],
        tutors: [
          {
            id: 1,
            name: 'Jens K. ● ■',
            professional: true,
            expert: true,
            subject: 'Danish',
            activeStudents: 34,
            lessons: 195,
            languages: [
              { language: 'Danish', proficiency: 'Native' },
              { language: 'English', proficiency: 'Proficient' },
              { language: 'German', proficiency: 'Basic' }
            ],
            bio: 'Native Danish expert here to take your Danish to the next level.',
            price: 250,
            reviews: 11,
            lessonDuration: 50
          },
          {
            id: 2,
            name: 'Tokyo M. ● ■',
            expert: false,
            professional: false,
            subject: 'Danish',
            activeStudents: 17,
            lessons: 3466,
            languages: [
              { language: 'Danish', proficiency: 'Native' },
              { language: 'English', proficiency: 'Native' },
              { language: 'Spanish', proficiency: 'Intermediate' }
            ],
            bio: 'Learn English or Danish from a physiotherapist.',
            price: 150,
            reviews: 35,
            lessonDuration: 50
          },
          {
            id: 3,
            name: 'Anna P. ● ■',
            expert: false,
            professional: false,
            subject: 'Danish',
            activeStudents: 5,
            lessons: 120,
            languages: [
              { language: 'Danish', proficiency: 'Native' },
              { language: 'French', proficiency: 'Intermediate' }
            ],
            bio: 'Helping beginners learn Danish with ease.',
            price: 90,
            reviews: 9,
            lessonDuration: 30
          }
        ]
      };
    },
    computed: {
      filteredTutors() {
        return this.tutors.filter(t => t.price >= this.minPrice && t.price <= this.fixedMaxPrice);
      }
    },
    methods: {
      selectSubject(subject) {
        this.selectedSubject = subject;
      }
    }
  };
  </script>
  
  <style scoped>
  .tutors-listing {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    font-family: Arial, sans-serif;
  }
  .header-section h1 {
    font-size: 24px;
    font-weight: bold;
  }
  .filters-section {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 20px 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
  }
  .filter-item {
    min-width: 180px;
  }
  .price-slider-container {
    width: 200px;
  }
  .single-slider {
    width: 100%;
  }
  .price-display {
    margin-top: 5px;
    font-weight: bold;
  }
  .tutors-list h2 {
    margin: 20px 0;
  }
  .tutor-card {
    border: 1px solid #eee;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
  }
  .tutor-header {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .tutor-name {
    font-weight: bold;
  }
  .badge {
    font-size: 12px;
    padding: 4px 6px;
  }
  .tutor-actions {
    margin-top: 10px;
  }
  .btn {
    padding: 6px 12px;
    margin-right: 10px;
  }
  .search-input {
    margin-top: 5px;
    padding: 6px;
    width: 100%;
  }
  </style>
  