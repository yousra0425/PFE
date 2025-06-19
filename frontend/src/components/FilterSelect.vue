<template>
    <div class="filter-select">
      <label class="label">{{ label }}</label>
      <select v-model="localValue">
        <option value="" disabled hidden>Choose...</option>
        <option v-for="option in options" :key="option" :value="option">
          {{ option }}
        </option>
      </select>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    label: String,
    options: Array,
    modelValue: String,
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  const localValue = ref(props.modelValue);
  
  // Watch for parent updates
  watch(() => props.modelValue, (newVal) => {
    localValue.value = newVal;
  });
  
  // Emit when user changes selection
  watch(localValue, (newVal) => {
    emit('update:modelValue', newVal);
  });
  </script>
  
  <style scoped>
  .filter-select {
    display: flex;
    flex-direction: column;
    font-size: 0.85rem;
  }
  
  .label {
    margin-bottom: 0.3rem;
    color: #555;
  }
  
  select {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    min-width: 160px;
  }
  </style>
  