<template>
    <div class="chat-bubble-container">
      <button
        @click="$emit('toggle')"
        class="chat-button"
        aria-label="Open chat"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="chat-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </button>
    </div>
  </template>
  
  <script>
  export default {
    name: "ChatBubble",
  };
  </script>
  
  <style scoped>
  .chat-bubble-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1000;
  }
  
  .chat-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 50px;
    background-color: var(--primary-color); /* blue-600 */
    color: white;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 20px rgb(14, 235, 213);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }
  
  .chat-button:hover {
    background-color: var(--primary-hover); /* blue-700 */
    transform: scale(1.1);
    box-shadow: 0 6px 24px rgb(14, 235, 213);
  }
  
  .chat-button:active {
    transform: scale(0.95);
  }
  
  .chat-icon {
    width: 30px;
    height: 30px;
    stroke-width: 2;
  }
  
  /* Animation for attention */
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
  }
  
  .chat-button.pulse {
    animation: pulse 1.5s infinite;
  }
  </style>