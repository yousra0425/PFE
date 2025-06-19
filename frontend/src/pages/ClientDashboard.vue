<template>
  <div class="dashboard">
    <nav class="navbar">
      <div class="nav-links">
        <router-link to="/services" :class="{ active: activeNavLink === 'Home' }" @click.native.prevent="selectNavLink('Home')">Home</router-link>
        <a href="#" :class="{ active: activeNavLink === 'Messages' }" @click.prevent="selectNavLink('Messages')">Messages</a>
        <a href="#" :class="{ active: activeNavLink === 'My Account' }" @click.prevent="selectNavLink('My Account')">My Account</a>
      </div>
    </nav>

    <div class="main">
      <MyAccount v-if="activeNavLink === 'My Account'" />

      <template v-else>
        <aside class="sidebar">
          <div class="tabs">
            <button :class="{ tab: true, active: activeTab === 'All' }" @click="selectTab('All')">All</button>
            <button :class="{ tab: true, active: activeTab === 'Unread' }" @click="selectTab('Unread')">Unread</button>
          </div>

          <div class="tutor-list">
            <p class="list-title">Your Tutors</p>
            <ul>
              <li
                v-for="tutor in tutors"
                :key="tutor.id"
                @click="selectTutor(tutor)"
                :class="{ active: selectedTutor?.id === tutor.id }"
              >
                {{ tutorFullName(tutor) }}
              </li>
            </ul>
          </div>

          <div class="start-learning">
            <p class="heading">Ready to start learning?</p>
            <p class="description">Find a tutor and book a lesson to start improving your skills.</p>
            <button class="find-button" @click="goToServices">Find your tutor</button>
          </div>
        </aside>

        <section class="chat-area">
          <div class="chat-header" v-if="selectedTutor">
            <h2 class="tutor-name">{{ selectedTutorFullName }}</h2>
          </div>

          <div class="messages" v-if="selectedTutor" ref="messagesContainer">
            <div
  v-for="msg in filteredMessages"
  :key="msg.id"
  :class="[
    'message-bubble',
    msg.sender === user.name ? 'sent' : msg.sender === 'System' ? 'system' : 'received'
  ]"
>
  <p class="message-text" v-html="formatMessageText(msg.text)"></p>
  <span class="message-sender">{{ msg.sender }}</span>
</div>

          </div>

          <div class="chat-input" v-if="selectedTutor">
            <input
              type="text"
              v-model="newMessage"
              placeholder="Type a message..."
              @keyup.enter="sendMessage"
            />
            <button @click="sendMessage">Send</button>
          </div>

          <div v-else class="no-tutor-selected">
            <p>Please select a tutor to start chatting.</p>
          </div>
        </section>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import MyAccount from '../components/MyAccount.vue'
import { db } from '../firebase'
import {
  collection,
  addDoc,
  serverTimestamp
} from 'firebase/firestore'

// Router and user
const router = useRouter()
const goToServices = () => router.push('/onlinetutors')

const user = ref({ name: 'You' })
onMounted(() => {
  // replace with real authenticated user fetch if applicable
  user.value = { name: 'You' }
})

// Tutors list from backend API
const tutors = ref([])

async function fetchTutors() {
  try {
    const response = await axios.get('http://localhost:8000/api/tutors')
    tutors.value = response.data.tutors  // <-- use tutors here
  } catch (error) {
    console.error('Failed to load tutors:', error)
  }
}


// Navigation & tabs
const activeNavLink = ref('Messages')
const activeTab = ref('All')
const selectNavLink = link => (activeNavLink.value = link)
const selectTab = tab => (activeTab.value = tab)
const tutorFullName = (tutor) => `${tutor.first_name} ${tutor.last_name}`

const selectedTutor = ref(null)
const selectedTutorFullName = computed(() => {
  return selectedTutor.value ? tutorFullName(selectedTutor.value) : ''
})

const messagesContainer = ref(null)
const newMessage = ref('')
const messages = ref([])

function loadMessages () {
  if (!selectedTutor.value || !user.value.name) return

  // Mock conversation data
  messages.value = [
    /*{
      id: 1,
      text: `Hi ${user.value.name}, I'm available for math lessons this week.`,
      sender: selectedTutorFullName.value,
      participants: [user.value.name, selectedTutorFullName.value]
    },
    {
      id: 2,
      text: 'That sounds great! How about Thursday at 4pm?',
      sender: user.value.name,
      participants: [user.value.name, selectedTutorFullName.value]
    },
    {
      id: 3,
      text: "Perfect. I'll send you a link before we start.",
      sender: selectedTutorFullName.value,
      participants: [user.value.name, selectedTutorFullName.value]
    },*/
    // New system message with Zoom link
    {
      id: 4,
      text: "Your booking has been accepted! Join your lesson using this Zoom link: https://us04web.zoom.us/j/77684042161?pwd=bRoa1YYbCSRqdlHfitDoDSH3eaMCR0.1",
      sender: "System",
      participants: [user.value.name, selectedTutorFullName.value]
    }
  ]

  // Auto-scroll
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

function formatMessageText(text) {
  // Simple regex to find URLs and wrap them in <a> tags
  const urlRegex = /(https?:\/\/[^\s]+)/g
  return text.replace(urlRegex, url => `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`)
}


function selectTutor(tutor) {
  selectedTutor.value = tutor
  loadMessages()
}

async function sendMessage () {
  if (!newMessage.value.trim() || !selectedTutor.value) return
  await addDoc(collection(db, 'messages'), {
    text: newMessage.value.trim(),
    sender: user.value.name,
    participantIds: [user.value.name, selectedTutorFullName.value],
    participants: [user.value.name, selectedTutorFullName.value],
    timestamp: serverTimestamp()
  })
  newMessage.value = ''
}

// Computed: filter if using tabs later
const filteredMessages = computed(() => messages.value)

// Fetch tutors on component mount
onMounted(() => {
  fetchTutors()
})
</script>




<style scoped>
/* Layout */
.dashboard {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.navbar {
  display: flex;
  align-items: center;
  padding: 16px 24px;
  background-color: white;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
  gap: 40px; /* spacing between logo and links */
}

.nav-links {
  display: flex;
  gap: 40px; /* spacing between links */
}

.nav-links a,
.router-link-active {
  text-decoration: none;
  color: #555;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}

.nav-links a.active {
  color: var(--primary-color);
  border-bottom: 2px solid var(--primary-color);
  padding-bottom: 2px;
}

.nav-links a:hover {
  color: var(--primary-hover);
}

/* Main layout */
.main {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* Sidebar */
.sidebar {
  width: 500px;
  border-right: 1px solid #ddd;
  background-color: white;
  padding: 20px;
  max-height: 90%;
  box-sizing: border-box;
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

/* Learning prompt */
.start-learning {
  text-align: center;
  margin-top: 100px;
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

.find-button {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
}

.find-button:hover {
  background-color: var(--primary-hover);
}

/* Chat area */
.chat-area {
  flex: 1;
  background-color: #f1f1f1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 16px;
  position: relative;
  overflow: hidden;
  max-height: 90%;
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

/* Input area */
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

.tutor-list {
  margin-top: 30px;
}

.tutor-list .list-title {
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
}

.tutor-list li:hover {
  background-color: #e0e0e0;
}

.tutor-list li.active {
  background-color: var(--primary-color);
  color: white;
  font-weight: bold;
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

.message-bubble.system {
  background-color: #f0f4f8;
  color: #000000;
  font-style: italic;
  border: 1px dashed #37a7a1;
  text-align: center;
}

.message-bubble.system a {
  color: #3c91e6;
  text-decoration: underline;
}

</style>
