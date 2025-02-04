<script setup>
import 'boxicons';

defineProps({
  label: {
    type: String,
    required: true
  }
});

</script>

<template>
  <div class="inline-flex items-center  h-full bg-gray-800 text-gray-300 rounded-lg  px-3 py-1 text-sm">
    <span>{{ label }}</span>
    <button
      @click="handleRemove"
      class="ml-2 focus:outline-none"
      aria-label="Remove filter"
    >
    <div class="flex cursor-pointer flex-end items-center">
    <box-icon name='x' color='#707070' ></box-icon>
</div>
    </button>
  </div>
</template>