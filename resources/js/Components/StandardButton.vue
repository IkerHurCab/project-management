<script setup>
defineProps({
  type: {
    type: String,
    default: 'button'
  }
});
</script>

<template>
  <button
    :type="type"
    class="
      bg-white dark:bg-blue-500 dark:text-white text-black font-medium py-2 px-4 rounded-lg
      transition-all duration-200 ease-in-out
      hover:bg-gray-300 dark:hover:bg-blue-600 hover:shadow-md cursor-pointer
      focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50
      active:bg-gray-200
    "
  >
    <slot></slot>
  </button>
</template>