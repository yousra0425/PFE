<template>
  <div class="dashboard">
    <!-- Navigation -->
    <nav class="navbar">
      <div class="nav-links">
        <a href="#"
           @click.prevent="currentTab = 'bookings'"
           :class="{ active: currentTab === 'bookings' }">
          My Requests
        </a>
        <a href="#"
           @click.prevent="currentTab = 'messages'"
           :class="{ active: currentTab === 'messages' }">
          Messages
        </a>
        <a href="#"
           @click.prevent="currentTab = 'account'"
           :class="{ active: currentTab === 'account' }">
          My Account
        </a>

        <!-- Online/Offline toggle -->
        <div class="online-toggle">
          <label class="switch">
            <input type="checkbox" v-model="isOnline" @change="toggleOnlineStatus" />
            <span class="slider"></span>
          </label>
          <span class="status-text">{{ isOnline ? "Online" : "Offline" }}</span>
        </div>
      </div>
    </nav>

    <!-- Bookings Section -->
    <section v-if="currentTab === 'bookings'" class="bookings-section">
      <div class="filters">
        <button @click="setFilter('all')" :class="{ active: filterStatus === 'all' }">All</button>
        <button @click="setFilter('pending')" :class="{ active: filterStatus === 'pending' }">Pending</button>
        <button @click="setFilter('accepted')" :class="{ active: filterStatus === 'accepted' }">Accepted</button>
      </div>

      <div class="booking-list">
        <div
          v-for="booking in filteredBookings"
          :key="booking.id"
          class="booking-card"
        >
          <h3>
            {{ booking.user?.first_name && booking.user?.last_name
                ? booking.user.first_name + ' ' + booking.user.last_name
                : 'Unknown User' }}
          </h3>
          <p><strong>Date:</strong> {{ booking.date }}</p>
          <p><strong>Time:</strong> {{ booking.start_time }}</p>
          <p><strong>Duration:</strong> {{ booking.duration }} hours</p>
          <p><strong>Message:</strong> {{ booking.message }}</p>
          <p><strong>Status:</strong> {{ booking.status }}</p>

          <div v-if="booking.status === 'pending'" class="actions">
            <button class="accept" @click="acceptBooking(booking.id)">Accept</button>
            <button class="decline" @click="declineBooking(booking.id)">Decline</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Messages Section -->
    <section v-if="currentTab === 'messages'" class="messages-section">
      <div class="main">
        <aside class="sidebar">
          <div class="tabs">
            <button :class="{ tab: true, active: activeTab === 'All' }" @click="selectTab('All')">All</button>
            <button :class="{ tab: true, active: activeTab === 'Unread' }" @click="selectTab('Unread')">Unread</button>
          </div>

          <div class="tutor-list">
            <p class="list-title">Your Students</p>
            <ul>
              <li
                v-for="student in filteredStudents"
                :key="student.id"
                @click="selectStudent(student)"
                :class="{ active: selectedStudent?.id === student.id }"
              >
                {{ student.name }}
                <span v-if="student.unreadCount > 0" class="unread-count">{{ student.unreadCount }}</span>
              </li>
            </ul>
          </div>

          <div class="start-learning">
            <p class="heading">Ready to start teaching?</p>
            <p class="description">Students are looking for tutors like you to help them learn.</p>
          </div>
        </aside>

        <section class="chat-area">
          <div class="chat-header" v-if="selectedStudent">
            <h2 class="tutor-name">{{ selectedStudent.name }}</h2>
          </div>

          <div class="messages" v-if="selectedStudent" ref="messagesContainer">
            <div
              v-for="msg in filteredMessages"
              :key="msg.id"
              :class="['message-bubble', msg.sender === user.name ? 'sent' : 'received']"
            >
              <p class="message-text">{{ msg.text }}</p>
              <span class="message-sender">{{ msg.sender }}</span>
            </div>
          </div>

          <div class="chat-input" v-if="selectedStudent">
            <input
              type="text"
              v-model="newMessage"
              placeholder="Type a message..."
              @keyup.enter="sendMessage"
            />
            <button @click="sendMessage">Send</button>
          </div>

          <div v-else class="no-tutor-selected">
            <p>Please select a student to start chatting.</p>
          </div>
        </section>
      </div>
    </section>

    <!-- Account Section -->
    <section v-if="currentTab === 'account'" class="account-section">
  <MyTutorAccount />
</section>
  </div>
</template>

<script>
import axios from "axios";
import MyTutorAccount from "../components/TutorDashboard/MyTutorAccount.vue";
import echo from '../echo.js';  // Adjust path as needed

export default {
  components: {
    MyTutorAccount,
  },
  data() {
    return {
      currentTab: "bookings",
      bookings: [],
      filteredBookings: [],
      filterStatus: "all",
      token: localStorage.getItem("token"),
      tutorId: localStorage.getItem("user_id"),
      userRole: localStorage.getItem("role") || "client",
      isOnline: null,
      echoSubscribed: false,

      // Chat
      activeTab: 'All',
      selectedStudent: null,
      students: [
        { id: '1', name: 'Alice', unreadCount: 2 },
        { id: '2', name: 'Bob', unreadCount: 0 },
        { id: '3', name: 'Charlie', unreadCount: 1 },
      ],
      mockMessages: [
        { id: 'm1', sender: 'Alice', receiver: 'Tutor', text: 'Hi, can we start today?', studentId: '1' },
        { id: 'm2', sender: 'Tutor', receiver: 'Alice', text: 'Yes, I’m available.', studentId: '1' },
        { id: 'm3', sender: 'Bob', receiver: 'Tutor', text: 'When can we schedule?', studentId: '2' },
        { id: 'm4', sender: 'Charlie', receiver: 'Tutor', text: 'Thanks for yesterday.', studentId: '3' },
      ],
      user: {
        name: 'Tutor',
      },
      newMessage: "",
    };
  },
  computed: {
    filteredStudents() {
      if (this.activeTab === 'Unread') {
        return this.students.filter(s => s.unreadCount > 0);
      }
      return this.students;
    },
    filteredMessages() {
      if (!this.selectedStudent) return [];
      return this.mockMessages.filter(msg => msg.studentId === this.selectedStudent.id);
    }
  },
  methods: {
    selectTab(tab) {
      this.activeTab = tab;
    },
    selectStudent(student) {
      this.selectedStudent = student;
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    async sendMessage() {
  if (!this.newMessage.trim() || !this.selectedStudent) return;

  const payload = {
    receiver_id: this.selectedStudent.id,  // must be ID, not name
    content: this.newMessage.trim(),       // trim message for safety
  };

  try {
    const response = await axios.post('/api/messages', payload, {
      headers: { Authorization: `Bearer ${this.token}` },
    });

    // Add the returned message to mockMessages or messages list
    this.mockMessages.push(response.data.data || response.data);

    this.newMessage = '';

    this.$nextTick(() => {
      const container = this.$refs.messagesContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    });

    const student = this.students.find(s => s.id === this.selectedStudent.id);
    if (student) student.unreadCount = 0;

  } catch (e) {
    console.error('Error sending message:', e.response || e);
  }
},


    async fetchAvailability() {
      try {
        const { data } = await axios.get(`/api/tutors/${this.tutorId}`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const tutor = data.data || data;
        this.isOnline = tutor.available === 1;
      } catch (e) {
        console.error("fetchAvailability error", e.response || e);
      }
    },
    async toggleOnlineStatus() {
      const newStatus = this.isOnline ? 1 : 0;
      try {
        await axios.patch(`/api/tutors/${this.tutorId}`,
          { available: newStatus },
          { headers: { Authorization: `Bearer ${this.token}` } }
        );
        this.isOnline = newStatus === 1;
      } catch (e) {
        console.error("toggleOnlineStatus error:", e.response || e);
        this.isOnline = !this.isOnline;
      }
    },
    async fetchBookings() {
      try {
        const response = await axios.get("/api/bookings", {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        this.bookings = response.data.data || response.data;
        this.applyFilter();
      } catch (error) {
        console.error("Error fetching bookings:", error);
      }
    },
    async acceptBooking(id) {
  try {
    await axios.post(`http://localhost:8000/api/bookings/${id}/accept`, null, {
      headers: { Authorization: `Bearer ${this.token}` }
    });
    alert('Booking accepted');

    // Find and update the booking's status locally
    const booking = this.bookings.find(b => b.id === id);
    if (booking) {
      booking.status = 'accepted'; // or 'accepted', whichever your backend uses
    }
    this.applyFilter(); // refresh filteredBookings to reflect new status

  } catch (error) {
    alert('Error accepting booking');
    console.error(error);
  }
},
async declineBooking(id) {
  try {
    await axios.post(`http://localhost:8000/api/bookings/${id}/decline`, null, {
      headers: { Authorization: `Bearer ${this.token}` }
    });
    alert('Booking declined');

    // Remove the declined booking locally
    this.bookings = this.bookings.filter(b => b.id !== id);
    this.applyFilter();

  } catch (error) {
    alert('Error declining booking');
    console.error(error);
  }
},

    setFilter(status) {
      this.filterStatus = status;
      this.applyFilter();
    },
    applyFilter() {
      if (this.filterStatus === "all") {
        this.filteredBookings = this.bookings;
      } else {
        this.filteredBookings = this.bookings.filter(b => b.status === this.filterStatus);
      }
    },
  },
  mounted() {
    this.fetchBookings();
    this.fetchAvailability();

    if (!this.echoSubscribed) {
      echo.private(`tutor.${this.tutorId}.messages`)
        .listen('NewMessage', (event) => {
          if (event.message.studentId === this.selectedStudent?.id) {
            this.mockMessages.push(event.message);
            this.$nextTick(() => {
              const container = this.$refs.messagesContainer;
              if (container) {
                container.scrollTop = container.scrollHeight;
              }
            });
          }

          const student = this.students.find(s => s.id === event.message.studentId);
          if (student && event.message.sender !== this.user.name) {
            student.unreadCount = (student.unreadCount || 0) + 1;
          }
        });
      this.echoSubscribed = true;
    }
  },
  beforeDestroy() {
    // Leave the private channel and reset subscription flag
    echo.leave(`tutor.${this.tutorId}.messages`);
    this.echoSubscribed = false;
  },

};
</script>




<style scoped>
.dashboard {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  color: #333;
}

.navbar {
  display: flex;
  align-items: center;
  padding: 16px 24px;
  background-color: white;
  margin-bottom: 2%;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
  gap: 40px; /* spacing between logo and links */
}

.nav-links {
  display: flex;
  gap: 40px; /* spacing between links */
}

.nav-links a {
  text-decoration: none;
  color: #555;
  font-size: 16px;
  font-weight: bold;
}

.nav-links a.active {
  color: var(--primary-color);
  border-bottom: 2px solid var(--primary-color);
  padding-bottom: 2px;
}

.nav-links a:hover {
  color: var(--primary-hover);
}

.filters {
  margin-bottom: 20px;
}

.filters button {
  margin-right: 12px;
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  background: #ccc;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s ease;
}

.filters button.active {
  background-color: #00796b;
  color: white;
}

.filters button:hover {
  background-color: #9e9e9e;
}

.booking-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* --- Booking Card --- */
.booking-card {
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 18px 20px;
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease;
}

.booking-card:hover {
  transform: translateY(-2px);
}

.booking-card h3 {
  margin: 0 0 10px;
  font-size: 1.2rem;
  color: #00796b;
}

.booking-card p {
  margin: 4px 0;
  font-size: 0.95rem;
  line-height: 1.4;
}

.booking-card strong {
  color: #555;
}

.actions {
  margin-top: 15px;
}

.actions button {
  margin-right: 10px;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.accept {
  background-color: #4caf50;
  color: white;
}

.accept:hover {
  background-color: #43a047;
}

.decline {
  background-color: #f44336;
  color: white;
}

.decline:hover {
  background-color: #e53935;
}

.messages-section {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.main {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* Sidebar */
.sidebar {
  width: 300px;
  border-right: 1px solid #ddd;
  background-color: white;
  padding: 20px;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.tabs {
  display: flex;
  gap: 16px;
  border-bottom: 1px solid #ddd;
  margin-bottom: 24px;
}

.tab {
  background: none;
  border: none;
  font-size: 14px;
  color: #666;
  cursor: pointer;
  padding-bottom: 6px;
  font-weight: 500;
}

.tab.active {
  color: var(--primary-color);
  border-bottom: 2px solid var(--primary-color);
}

.tab:hover {
  color: var(--primary-hover);
}

.tutor-list {
  margin-top: 30px;
  flex: 1;
  overflow-y: auto;
}

.list-title {
  font-weight: bold;
  margin-bottom: 10px;
}

.tutor-list ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tutor-list li {
  padding: 8px 12px;
  margin-bottom: 6px;
  background-color: #f5f5f5;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tutor-list li:hover {
  background-color: #e0e0e0;
}

.tutor-list li.active {
  background-color: var(--primary-color);
  color: white;
  font-weight: bold;
}

.unread-count {
  background-color: var(--primary-color);
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}


.start-learning {
  text-align: center;
  margin-top: auto;
  padding: 20px 0;
}

.start-learning .heading {
  font-weight: bold;
  margin-bottom: 10px;
}

.start-learning .description {
  font-size: 14px;
  color: #666;
  margin-bottom: 16px;
}

/* Chat Area */
.chat-area {
  flex: 1;
  background-color: #f1f1f1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 16px;
  position: relative;
  overflow: hidden;
}

.chat-header {
  padding-bottom: 12px;
  border-bottom: 1px solid #ccc;
  margin-bottom: 12px;
}

.tutor-name {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.messages {
  flex: 1;
  overflow-y: auto;
  padding-right: 10px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.message-bubble {
  max-width: 60%;
  padding: 10px 14px;
  border-radius: 20px;
  font-size: 14px;
  position: relative;
  word-wrap: break-word;
}

.message-bubble.sent {
  background-color: #b7f8e2;
  align-self: flex-end;
  border-bottom-right-radius: 0;
}

.message-bubble.received {
  background-color: white;
  align-self: flex-start;
  border-bottom-left-radius: 0;
}

.message-text {
  margin: 0;
}

.message-sender {
  font-size: 10px;
  color: #999;
  margin-top: 4px;
  display: block;
  text-align: right;
}

.chat-input {
  display: flex;
  gap: 8px;
  padding-top: 12px;
  border-top: 1px solid #ccc;
  background-color: #f9f9f9;
}

.chat-input input {
  flex: 1;
  padding: 10px;
  border-radius: 20px;
  border: 1px solid var(--primary-color);
  font-size: 14px;
  outline: none;
}

.chat-input button {
  padding: 10px 16px;
  border: none;
  background-color: var(--primary-color);
  color: white;
  border-radius: 20px;
  cursor: pointer;
}

.chat-input button:hover {
  background-color: var(--primary-hover);
}

.no-tutor-selected {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #666;
  font-size: 16px;
  font-style: italic;
}

.online-toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: 340px; /* spacing from nav links */
  font-weight: 500;
  font-size: 14px;
  color: #555;
}

/* Toggle Switch Styling */
.switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 20px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: 0.3s;
  border-radius: 20px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #109351;
}

input:checked + .slider:before {
  transform: translateX(20px);
}

.status-text {
  user-select: none;
}
</style>
