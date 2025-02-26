<script setup>
import 'boxicons';
import { onMounted } from 'vue';

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    required: true
  }
});



const emit = defineEmits(['remove']);

const handleRemove = () => {
  emit('remove', props.type);
};
</script>

<template>
  <div class="inline-flex items-center h-full bg-gray-800 text-gray-300 rounded-lg px-3 py-1 text-sm">
    <span>{{ label }}</span>
    <button
      @click="handleRemove"
      class="ml-2 focus:outline-none cursor-pointer"
      aria-label="Remove filter"
    >
      <div class="flex  flex-end items-center">
        <box-icon name='x' color='#707070'></box-icon>
      </div>
    </button>
  </div>
</template>
