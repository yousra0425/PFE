<template>
  <div v-if="show" class="modal-overlay">
    <div class="modal-content">
      <h3>Book a Lesson with {{ tutor?.name }}</h3>

      <label for="date">Date:</label>
      <Datepicker
        v-model="form.date"
        :min-date="new Date()"
        :allowed-dates="allowedDates"
        :format="'yyyy-MM-dd'"
      />

      <label for="startTime">Start Time:</label>
      <select v-model="form.start_time">
        <option value="" disabled>Select a time</option>
        <option
          v-for="time in availableTimes"
          :key="time"
          :value="time"
        >
          {{ time }}
        </option>
      </select>

      <label>Duration (hours):</label>
      <input type="number" v-model.number="form.duration" min="1" />

      <label>Message (optional):</label>
      <textarea v-model="form.message" placeholder="Write a message..."></textarea>

      <div class="modal-actions">
        <button class="btn-primary" @click="submitBooking">Submit</button>
        <button class="btn-secondary" @click="$emit('close')">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
  show: Boolean,
  tutor: Object,
});
const emit = defineEmits(['close', 'submitted']);

const form = reactive({
  date: '',
  start_time: '',
  duration: 1,
  message: '',
});

const availableSlots = ref({});
const availableTimes = ref([]);
const allowedDates = (date) => {
  const formatted = date.toISOString().slice(0, 10);
  return formatted in availableSlots.value;
};

// Fetch available slots on modal open
watch(
  () => props.show,
  async (visible) => {
    if (visible && props.tutor?.id) {
      resetForm();
      await fetchAvailableSlots();
    }
  }
);

// Update available times when date changes
watch(
  () => form.date,
  (newDate) => {
    if (!newDate) return;
    const formatted = newDate instanceof Date ? newDate.toISOString().slice(0, 10) : newDate;
    availableTimes.value = availableSlots.value[formatted] || [];
    if (!availableTimes.value.includes(form.start_time)) form.start_time = '';
  }
);

async function fetchAvailableSlots() {
  try {
    const res = await axios.get(`http://localhost:8000/api/tutor/${props.tutor.id}/available-slots`);
    availableSlots.value = res.data.available_slots || {};
  } catch (err) {
    console.error('Error fetching available slots:', err);
    availableSlots.value = {};
  }
}

async function submitBooking() {
  if (!form.date || !form.start_time || form.duration < 1) {
    alert('Please fill in all required fields');
    return;
  }

  const formattedDate =
    form.date instanceof Date ? form.date.toISOString().slice(0, 10) : form.date;
  const [hours, minutes] = form.start_time.split(':').map(Number);
  const startDate = new Date(form.date);
  startDate.setHours(hours, minutes, 0);

  const endDate = new Date(startDate.getTime() + form.duration * 60 * 60 * 1000);
  const end_time = `${endDate.getHours().toString().padStart(2, '0')}:${endDate
    .getMinutes()
    .toString()
    .padStart(2, '0')}`;

  const payload = {
    tutor_id: props.tutor.id,
    date: formattedDate,
    start_time: form.start_time,
    end_time,
    message: form.message,
  };

  try {
    await axios.post('http://localhost:8000/api/bookings', payload);
    alert('Booking sent successfully!');
    emit('submitted');
    emit('close');
  } catch (err) {
    console.error(err);
    alert(err.response?.data?.message || 'Booking failed.');
  }
}

function resetForm() {
  form.date = '';
  form.start_time = '';
  form.duration = 1;
  form.message = '';
  availableTimes.value = [];
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 999;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 450px;
  animation: fadeIn 0.3s ease-in-out;
}

.modal-content h3 {
  font-size: 1.25rem;
  margin-bottom: 1.25rem;
  text-align: center;
}

.modal-content label {
  display: block;
  margin-top: 1rem;
  font-weight: 600;
}

.modal-content input,
.modal-content select,
.modal-content textarea {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.25rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.modal-content textarea {
  min-height: 80px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-primary:hover {
  background: var(--primary-hover);
}

.btn-secondary {
  background: #e5e7eb;
  color: #111827;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}
</style>
