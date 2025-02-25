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
    const colors = {
      1: 'bg-blue-200',    // Low - Color claro, azul claro
      2: 'bg-yellow-300',  // Medium - Color intermedio, amarillo claro
      3: 'bg-orange-500',  // High - Color más oscuro, naranja
      4: 'bg-red-700'
    };
    return colors[props.task.priority] || 'bg-gray-500';
  });

  const priorityLabel = computed(() => {
    const labels = {
      1: 'Low',
      2: 'Medium',
      3: 'High',
      4: 'Urgent'
    };
    return labels[props.task.priority] || 'Unknown';
  });

  const getInitials = (name) => {

  };
</script>

<template>
  <div
    class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-move border border-gray-700 hover:border-gray-600">
    <div class="flex justify-between items-start mb-3">
      <h3 class="text-white font-medium text-lg">{{ task.name }}</h3>

    </div>
    <p class="text-gray-300 text-sm mb-4 line-clamp-2">{{ task.description }}</p>
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <div class="px-2 py-1 rounded text-xs font-medium text-black" :class="priorityColor">
          {{ priorityLabel }}
        </div>
        <span class="text-gray-400 text-xs flex items-center">
          <box-icon name='time' color='#9CA3AF' class="w-4 h-4 mr-1"></box-icon>
          {{ task.estimated_hours }}h
        </span>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
          {{ getInitials(task.assigned_to) }}
        </div>
        <span class="text-gray-300 text-sm hidden md:inline">{{ task.assigned_to }}</span>
      </div>
    </div>
  </div>
</template>