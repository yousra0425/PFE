<template>
  <div class="tutors-container" ref="containerRef">
    <FilterBar
      @filter-change="handleFilterChange"
      :selected-category-id="route.query.category"  
    />

    <h2>Available Online Tutors</h2>

    <div v-if="error" class="error-message">{{ error }}</div>

    <div class="tutor-list">
      <div
        class="tutor-card"
        v-for="tutor in tutors"
        :key="tutor.id"
        @mouseenter="handleMouseEnter(tutor, $event)"
        @mouseleave="handleMouseLeave"
      >
        <div class="card-content">
          <div class="media-section">
            <img
              :src="tutor.profile_picture || defaultImage"
              :alt="tutor.name || 'Tutor image'"
              class="tutor-img"
            />
          </div>

          <div class="tutor-info">
            <div
              class="availability-badge"
              :class="tutor.is_online ? 'online' : 'offline'"
            >
              {{ tutor.is_online ? 'Online' : 'Offline' }}
            </div>
            <h3>{{ tutor.name || 'No Name' }}</h3>
            <p class="category">{{ tutor.category }}</p>
            <p class="bio">{{ tutor.bio }}</p>

            <div class="skills">
              <span v-for="skill in tutor.subcategories" :key="skill" class="skill-tag">{{ skill }}</span>
            </div>

            <div class="meta">
              <div class="left-meta">
                <span class="price">{{ tutor.hourly_rate }}MAD/hr</span>
                <span class="experience">{{ tutor.experience_years }} years exp</span>
              </div>
              <div class="rating">⭐ {{ tutor.rating }} ({{ tutor.reviews || 13 }})</div>
            </div>

            <div class="actions">
              <button class="btn-secondary" @click="openMessageModal(tutor)">Send Message</button>
              <button class="btn-primary" @click="openBookingModal(tutor)" :disabled="!tutor.is_online">
                {{ tutor.is_online ? 'Send Request' : 'Unavailable' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

<!-- Floating Preview -->
<div
      v-if="hoveredTutor && hoveredTutor.intro_video && !videoModalOpen"
      class="floating-preview"
      :style="floatingPreviewStyle"
      @click="openVideoModal"
      @mouseenter="handleFloatingMouseEnter"
      @mouseleave="handleFloatingMouseLeave"
    >
      <video
        ref="floatingVideoRef"
        :src="hoveredTutor.intro_video"
        muted
        playsinline
        preload="none"
        :poster="hoveredTutor.profile_picture || defaultImage"
        class="preview-video"
      ></video>
      <div class="play-overlay">▶</div>
    </div>

    <!-- Video Modal -->
    <div v-if="videoModalOpen" class="modal-overlay" @click.self="closeVideoModal">
      <div class="modal-content video-modal">
        <button class="close-btn" @click="closeVideoModal">✕</button>
        <video
  ref="modalVideoRef"
  :src="hoveredTutor?.intro_video"
  :poster="hoveredTutor?.profile_picture || defaultImage"
  controls
  playsinline
  class="modal-video"
></video>
      </div>
    </div>

    <!-- Message Modal -->
    <div v-if="showMessageModal" class="modal-overlay" @click.self="closeMessageModal">
      <div class="modal-content">
        <h3>Contact {{ selectedTutor?.name }}</h3>
        <textarea
          v-model="messageText"
          placeholder="Type your message here..."
          rows="5"
          maxlength="500"
        ></textarea>
        <div class="modal-actions">
          <button class="btn-secondary" @click="closeMessageModal">Cancel</button>
          <button class="btn-primary" @click="sendMessage" :disabled="!messageText.trim()">Send</button>
        </div>
      </div>
    </div>

    <!-- Booking Modal -->
    <BookLesson
      :show="showBookingModal"
      :tutor="selectedTutor"
      @close="showBookingModal = false"
      @submitted="handleBookingSubmitted"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import FilterBar from '../components/FilterBar.vue';
import BookLesson from '../components/BookLesson.vue';

import { db } from '../firebase';
import { collection, addDoc, serverTimestamp } from 'firebase/firestore';
import { getAuth } from 'firebase/auth';

const auth = getAuth();
const currentUserId = auth.currentUser?.uid || 'anonymous_user';

const route = useRoute();

const hoveredTutor = ref(null);
const hoveredTutorEvent = ref(null);
const hoveredTimeout = ref(null);
const floatingVideoRef = ref(null);
const modalVideoRef = ref(null);
const containerRef = ref(null);
const tutors = ref([]);
const activeFilters = ref({});
const error = ref(null);

const showBookingModal = ref(false);
const showMessageModal = ref(false);
const videoModalOpen = ref(false);

const selectedTutor = ref(null);
const messageText = ref('');


const floatingPreviewRef = ref(null);

const defaultImage = 'https://via.placeholder.com/220x140.png?text=No+Image';

const handleFilterChange = (filters) => {
  activeFilters.value = filters;
  fetchTutors(filters);
};

const openBookingModal = (tutor) => {
  selectedTutor.value = tutor;
  showBookingModal.value = true;
};

const closeBookingModal = () => {
  selectedTutor.value = null;
  showBookingModal.value = false;
};

const openMessageModal = (tutor) => {
  selectedTutor.value = tutor;
  messageText.value = '';
  showMessageModal.value = true;
};

const closeMessageModal = () => {
  selectedTutor.value = null;
  messageText.value = '';
  showMessageModal.value = false;
};

const sendMessage = async () => {
  if (!messageText.value.trim()) return;

  try {
    const tutorId = selectedTutor.value.id.toString();
    const conversationId = [currentUserId, tutorId].sort().join('_');
    const messagesCollection = collection(db, 'messages', conversationId, 'chat');

    await addDoc(messagesCollection, {
      senderId: currentUserId,
      receiverId: tutorId,
      text: messageText.value.trim(),
      timestamp: serverTimestamp()
    });

    messageText.value = '';
    closeMessageModal();
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const handleMouseEnter = async (tutor, event) => {
  clearTimeout(hoveredTimeout.value);
  hoveredTutor.value = tutor;
  hoveredTutorEvent.value = event;
  
  await nextTick();
  if (floatingVideoRef.value && tutor.intro_video) {
    floatingVideoRef.value.play().catch(e => console.log("Preview autoplay prevented:", e));
  }
};

const handleMouseLeave = () => {
  if (floatingVideoRef.value) {
    floatingVideoRef.value.pause();
    floatingVideoRef.value.currentTime = 0;
  }
  hoveredTimeout.value = setTimeout(() => {
    if (!videoModalOpen.value) {
      hoveredTutor.value = null;
      hoveredTutorEvent.value = null;
    }
  }, 300);
};

const handleFloatingMouseEnter = () => {
  clearTimeout(hoveredTimeout.value);
};

const handleFloatingMouseLeave = () => {
  if (!videoModalOpen.value) {
    hoveredTutor.value = null;
    hoveredTutorEvent.value = null;
  }
};

const handleBookingSubmitted = () => {
  showBookingModal.value = false;
  fetchTutors(activeFilters.value);
};

const floatingPreviewStyle = computed(() => {
  if (!hoveredTutorEvent.value || !containerRef.value) return { display: 'none' };

  const cardRect = hoveredTutorEvent.value.currentTarget.getBoundingClientRect();
  const containerRect = containerRef.value.getBoundingClientRect();

  const top = cardRect.top - containerRect.top + window.scrollY;
  const left = cardRect.right - containerRect.left + 16 + window.scrollX;

  const maxLeft = window.innerWidth - 340;
  const adjustedLeft = Math.min(left, maxLeft);

  return {
    position: 'absolute',
    top: `${top}px`,
    left: `${adjustedLeft}px`,
    zIndex: 1000,
  };
});

const openVideoModal = async () => {
  videoModalOpen.value = true;
  await nextTick();
  if (modalVideoRef.value) {
    modalVideoRef.value.play().catch(e => console.log("Modal autoplay prevented:", e));
  }
};

const closeVideoModal = () => {
  if (modalVideoRef.value) {
    modalVideoRef.value.pause();
  modalVideoRef.value.currentTime = 0;
  }
  videoModalOpen.value = false;
  hoveredTutor.value = null;
  hoveredTutorEvent.value = null;
};

const fetchTutors = async (filters = {}) => {
  try {
    const params = {};
    if (route.query.category) params.category_id = Number(route.query.category);
    if (filters.level) params.level_id = Number(filters.level);
    if (filters.subcategory) params.subcategory_id = Number(filters.subcategory);
    if (filters.distance) params.max_distance_km = Number(filters.distance);
    if (filters.price) params.max_price = Number(filters.price);

    console.log('Fetching tutors with params:', params);

    const response = await axios.get('http://127.0.0.1:8000/api/tutors/verified-online', { params });
    tutors.value = response.data;
    error.value = null;
  } catch (err) {
    console.error('Error fetching tutors:', err);
    error.value = 'Failed to fetch tutors.';
  }
};

onMounted(() => {
  fetchTutors();
});

watch(() => route.query, () => {
  fetchTutors(activeFilters.value);
});
</script>




<style scoped>
.tutors-container {
  padding: 2rem;
  max-width: 1200px;
  margin: auto;
  position: relative; /* needed for floating preview absolute positioning */
}

h2 {
  font-size: 2rem;
  text-align: center;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #333;
}

.tutor-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.tutor-card {
  display: flex;
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  border: 1px solid #f0f0f0;
  transition: all 0.3s ease;
  position: relative;
  transform: translateY(0);
}

.tutor-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border-color: #e0e0e0;
}


.card-content {
  display: flex;
  flex-direction: row;
  width: 100%;
}

.media-section {
  position: relative;
  flex: 0 0 220px;
  height: 300px;
  background: #fafafa;
  overflow: hidden;
}

.tutor-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: opacity 0.3s ease-in-out;
  position: absolute;
  top: 0;
  left: 0;
}


/* Floating preview styles */
.floating-preview {
  width: 320px;
  height: 180px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  background: black;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  transition: opacity 0.3s ease;
  z-index: 1000;
}

.floating-preview video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
}

.floating-preview .play-overlay {
  position: absolute;
  font-size: 4rem;
  color: white;
  opacity: 0.8;
  pointer-events: none;
}

/* Video Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content.video-modal {
  position: relative;
  background: black;
  border-radius: 1rem;
  padding: 0;
  max-width: 50%;
  max-height: 80%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.modal-video {
  width: 100%;
  height: 100%;
  max-height: 80vh;
  object-fit: contain;
}

.close-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(255,255,255,0.2);
  border: none;
  font-size: 1.5rem;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.3s ease;
}

.close-btn:hover {
  background: rgba(255,255,255,0.5);
}

/* Availability badge */
.availability-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.availability-badge.online {
  background: #e8f5e8;
  color: #2d5a2d;
  border: 1px solid #c3e6c3;
}

.availability-badge.offline {
  background: #ffeaea;
  color: #8b2635;
  border: 1px solid #f5c6cb;
}

.tutor-info {
  padding: 1.2rem;
  flex: 1;
}

.tutor-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.3rem;
  color: #222;
  font-weight: 600;
}

.category {
  font-weight: 600;
  color: #ff5e5e;
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.bio {
  font-size: 0.9rem;
  color: #555;
  margin: 0 0 1rem 0;
  line-height: 1.4;
}

.skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.skill-tag {
  background: #f8f9fa;
  color: #495057;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid #e9ecef;
}

.meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.85rem;
}

.left-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.price {
  font-weight: bold;
  color: #2c3e50;
  font-size: 1rem;
}

.experience {
  color: #666;
  font-size: 0.8rem;
}

.rating {
  color: #f39c12;
  font-weight: 500;
}

.actions {
  display: flex;
  gap: 0.75rem;
}

.btn-primary, .btn-secondary {
  flex: 1;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: var(--primary-hover);
}

.btn-primary:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-secondary {
  background: transparent;
  color: #666;
  border: 1px solid #ddd;
}

.btn-secondary:hover {
  background: #f8f9fa;
  color: #333;
}

@media (max-width: 768px) {
  .tutors-container {
    padding: 1rem;
  }
  
  .tutor-list {
    grid-template-columns: 1fr;
  }
  
  .actions {
    flex-direction: column;
  }

  .floating-preview {
    display: none !important; /* Hide floating preview on small screens */
  }
}

/* Modal styles for message modal remain unchanged */

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-content {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modal-content h3 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
  color: #222;
}

textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  border-radius: 12px;
  border: 1px solid #ccc;
  resize: vertical;
  font-family: inherit;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.modal-content.video-modal {
  position: relative;
  width: 90vw;        /* 90% viewport width */
  max-width: 800px;   /* max width */
  aspect-ratio: 16 / 9; /* maintain 16:9 aspect ratio */
  background: black;
  border-radius: 8px;
  overflow: hidden;
}

/* Video fills the modal */
.modal-video {
  width: 100%;
  height: 100%;
  object-fit: contain; /* or 'cover' depending on preference */
  display: block;
  border-radius: 8px;
}
</style>
