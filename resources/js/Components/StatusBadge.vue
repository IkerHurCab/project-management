<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['pending', 'in_progress', 'finished', 'inactive', 'to_do', 'review', 'done'].includes(value)
  }
});

const statusConfig = computed(() => {
  const configs = {
    pending: {
      text: 'Pending',
      bgColor: 'bg-yellow-500',
      textColor: 'text-yellow-900'
    },
    to_do: {
      text: 'To Do',
      bgColor: 'bg-blue-500',
      textColor: 'text-white-900'
    },
    in_progress: {
      text: 'In Progress',
      bgColor: 'bg-blue-500',
      textColor: 'text-gray-300'
    },
    finished: {
      text: 'Finished',
      bgColor: 'bg-green-500',
      textColor: 'text-green-900'
    },
    done: {
      text: 'Done',
      bgColor: 'bg-green-500',
      textColor: 'text-green-900'
    },
    inactive: {
      text: 'Inactive',
      bgColor: 'bg-gray-500',
      textColor: 'text-gray-900'
    },
    review: {
      text: 'Review',
      bgColor: 'bg-purple-800',
      textColor: 'text-white-900'
    }
  };

  return configs[props.status];
});
</script>

<template>
  <span 
    :class="[
      'px-2 py-2 rounded-lg text-xs font-bold',
      statusConfig.bgColor,
      statusConfig.textColor
    ]"
  >
    {{ statusConfig.text }}
  </span>
</template>