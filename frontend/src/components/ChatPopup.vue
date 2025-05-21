<template>
    <div class="chat-popup">
        <!-- Your messages will go here -->
        <slot />
        <!-- Message input slot -->
        <div class="input-container">
          <slot name="input" />
        </div>
      </div>
    
  </template>
  
  <script>
  export default {
    name: "ChatPopup",
  };
  </script>
  
  <style scoped>
  .chat-popup {
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 400px;
    height: 460px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: 'Segoe UI', system-ui, sans-serif;
  }
  
  .message-area {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
    background: #e5ddd5;
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AkEEjIZJ6sKHAAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAANElEQVQ4y2NgGAXDFmzatMmAEsPAqAGjBowaMGrAqAGjBowaMGrAqAGjBowaMGrAqAGjBgxVAADe6QZARzGH0QAAAABJRU5ErkJggg==');
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  /* Custom scrollbar */
  .message-area::-webkit-scrollbar {
    width: 6px;
  }
  
  .message-area::-webkit-scrollbar-track {
    background: transparent;
  }
  
  .message-area::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }
  
  .message-area::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
  }
  </style>