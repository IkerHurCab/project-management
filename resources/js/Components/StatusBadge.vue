<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['pending', 'in_progress', 'finished', 'suspended', 'to_do', 'review', 'done'].includes(value)
  }
});

const statusConfig = computed(() => {
  const configs = {
    pending: {
      text: 'Pending',
      bgColor: 'bg-yellow-600',
      textColor: 'text-white'
    },
    to_do: {
      text: 'To Do',
      bgColor: 'bg-blue-500',
      textColor: 'text-blue-200'
    },
    in_progress: {
      text: 'In Progress',
      bgColor: 'bg-yellow-500',
      textColor: 'text-yellow-900'
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
    suspended: {
      text: 'Suspended',
      bgColor: 'bg-red-500',
      textColor: 'text-white-500'
    },
    review: {
      text: 'Review',
      bgColor: 'bg-purple-800',
      textColor: 'text-purple-300'
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