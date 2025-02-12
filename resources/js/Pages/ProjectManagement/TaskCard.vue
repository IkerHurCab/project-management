<script setup>
import { computed } from 'vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const priorityColor = computed(() => {
  switch (props.task.priority) {
    case 1:
      return 'bg-red-500';
    case 2:
      return 'bg-yellow-500';
    case 3:
      return 'bg-green-500';
    default:
      return 'bg-gray-500';
  }
});

const priorityFirstChar = computed(() => 
  String(props.task.priority).charAt(0).toUpperCase()
);
</script>

<template>
  <div
    class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-move"
  >
    <div class="flex justify-between items-start mb-2">
      <h3 class="text-white font-medium text-lg">{{ task.name }}</h3>
 
    </div>
    <p class="text-gray-300 text-sm mb-3">{{ task.description }}</p>
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <div
          class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
          :class="priorityColor"
        >
          {{ priorityFirstChar }}
        </div>
        <span class="text-gray-400 text-xs">{{ task.estimated_hours }}h</span>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
          <!-- Aquí puedes agregar el avatar o las iniciales del empleado asignado -->
        </div>
        <span class="text-gray-300 text-sm">{{ task.assigned_to }}</span>
      </div>
    </div>
  </div>
</template>