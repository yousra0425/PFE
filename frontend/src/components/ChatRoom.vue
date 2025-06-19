<template>
    <div class="chat-container">
      <div class="chat-header">
        <h4>Live Chat</h4>
        <button @click="$emit('close')" class="close-btn" aria-label="Close chat">
          &times;
        </button>
      </div>
  
      <div ref="messagesContainer" class="messages">
        <div
          v-for="msg in messages"
          :key="msg.id"
          class="message"
          :class="{ 'own-message': msg.sender === currentUser }"
        >
          <div class="message-content">
            <span class="sender" v-if="msg.sender !== currentUser">{{ msg.sender }}</span>
            <div class="text">
              <template v-if="msg.fileUrl">
                📎
                <a :href="msg.fileUrl" target="_blank" rel="noopener noreferrer">
                  {{ msg.fileName }}
                </a>
              </template>
              <template v-else>
                {{ msg.text }}
              </template>
            </div>
            <div class="timestamp">{{ formatTimestamp(msg.timestamp) }}</div>
          </div>
        </div>
        <div v-if="loading" class="loading-indicator">Loading messages...</div>
      </div>
  
      <form @submit.prevent="sendMessage" class="chat-form">
        <input
          v-model="newMessage"
          placeholder="Type your message..."
          ref="messageInput"
        />
  
        <!-- Hidden file input -->
        <input
          ref="fileInput"
          type="file"
          @change="uploadFile"
          style="display: none;"
        />
  
        <!-- Paperclip button triggers file input -->
        <button
          type="button"
          @click="triggerFileInput"
          class="file-attach-btn"
          title="Attach file"
          aria-label="Attach file"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
          >
            <path
              d="M21.44 11.05l-9.19 9.19a5 5 0 0 1-7.07-7.07l9.2-9.19a3.5 3.5 0 0 1 4.95 4.95L9.88 16.22a1.5 1.5 0 0 1-2.12-2.12l6.72-6.72"
            ></path>
          </svg>
        </button>
  
        <!-- Send button -->
        <button type="submit" :disabled="!newMessage.trim()">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
          </svg>
        </button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted, nextTick } from 'vue'

  import { db } from '../firebase.js'
  import {
    collection,
    addDoc,
    query,
    orderBy,
    onSnapshot,
    serverTimestamp,
    limit,
  } from 'firebase/firestore'
  
  import {
    getStorage,
    ref as storageRef,
    uploadBytesResumable,
    getDownloadURL,
  } from 'firebase/storage'
  

  
  const storage = getStorage()
  
  const messages = ref([])
  const newMessage = ref('')
  const loading = ref(true)
  const messagesContainer = ref(null)
  const messageInput = ref(null)
  const fileInput = ref(null)
  
  // Replace with actual current user id/name from auth system
  const currentUser = ref('client_user_123')
  
  const messagesCollection = collection(db, 'chats', 'room1', 'messages')
  let unsubscribe
  
  onMounted(() => {
    const q = query(messagesCollection, orderBy('timestamp'), limit(100))
  
    unsubscribe = onSnapshot(q, (snapshot) => {
      messages.value = snapshot.docs.map((doc) => ({
        id: doc.id,
        ...doc.data(),
      }))
      loading.value = false
      scrollToBottom()
    })
  
    nextTick(() => {
      messageInput.value?.focus()
    })
  })
  
  onUnmounted(() => {
    if (unsubscribe) unsubscribe()
  })
  
  async function sendMessage() {
    if (!newMessage.value.trim()) return
  
    try {
      await addDoc(messagesCollection, {
        text: newMessage.value,
        sender: currentUser.value,
        timestamp: serverTimestamp(),
      })
      newMessage.value = ''
      scrollToBottom()
    } catch (error) {
      console.error('Error sending message: ', error)
    }
  }
  
  function scrollToBottom() {
    nextTick(() => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
      }
    })
  }
  
  function formatTimestamp(timestamp) {
    if (!timestamp) return ''
    const date = timestamp.toDate()
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  }
  
  function triggerFileInput() {
    fileInput.value?.click()
  }
  
  async function uploadFile(event) {
    const file = event.target.files[0]
    if (!file) return
  
    try {
      const fileRef = storageRef(storage, `chat_files/${Date.now()}_${file.name}`)
      const uploadTask = uploadBytesResumable(fileRef, file)
  
      uploadTask.on(
        'state_changed',
        (snapshot) => {
          // Optional: track progress here if needed
        },
        (error) => {
          console.error('Upload failed:', error)
        },
        async () => {
          const downloadURL = await getDownloadURL(uploadTask.snapshot.ref)
  
          await addDoc(messagesCollection, {
            sender: currentUser.value,
            timestamp: serverTimestamp(),
            fileUrl: downloadURL,
            fileName: file.name,
          })
  
          scrollToBottom()
        }
      )
    } catch (error) {
      console.error('Error uploading file:', error)
    }
  }
  </script>
  
  <style scoped>
  .chat-container {
    width: 100%;
    max-width: 500px;
    height: 500px;
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    font-family: 'Segoe UI', system-ui, sans-serif;
  }
  
  .chat-header {
    padding: 0.3rem;
    background: var(--primary-color);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .chat-header h4 {
    margin: 0;
    font-weight: 600;
  }
  
  .close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0 0.5rem;
  }
  
  .messages {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .message {
    display: flex;
    max-width: 80%;
  }
  
  .message.own-message {
    align-self: flex-end;
  }
  
  .message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    position: relative;
    animation: fadeIn 0.3s ease-out;
  }
  
  .message:not(.own-message) .message-content {
    background: #f1f5f9;
    border-top-left-radius: 0;
  }
  
  .message.own-message .message-content {
    background: var(--primary-color);
    color: white;
    border-top-right-radius: 0;
  }
  
  .sender {
    font-weight: 600;
    font-size: 0.8rem;
    display: block;
    margin-bottom: 0.25rem;
    color: #334155;
  }
  
  .text {
    word-break: break-word;
  }
  
  .message-content a {
    color: #3182ce;
    text-decoration: underline;
  }
  
  .message-content a:hover {
    color: #63b3ed;
  }
  
  .timestamp {
    font-size: 0.7rem;
    opacity: 0.8;
    text-align: right;
    margin-top: 0.25rem;
  }
  
  .chat-form {
    display: flex;
    padding: 1rem;
    gap: 0.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
  }
  
  .chat-form input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 2rem;
    outline: none;
    transition: border-color 0.2s;
  }
  
  .chat-form input:focus {
    border-color: var(--primary-color);
  }
  
  .chat-form button {
  background: none;
  border: none;
  cursor: pointer;
  margin-left: 0.5rem;
  color: var(--primary-color);
  padding: 0.2rem;
  border-radius: 50%;
  transition: background-color 0.3s ease;
}

.chat-form button:disabled {
  color: #ccc;
  cursor: not-allowed;
}

.chat-form button:hover:not(:disabled) {
  background-color: rgba(0, 123, 255, 0.1);
}

.file-attach-btn svg,
.chat-form button svg {
  display: block;
}
  
  .loading-indicator {
    text-align: center;
    padding: 1rem;
    color: #64748b;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Primary color variable */
  :root {
    --primary-color: #3b82f6;
  }
  </style>
  