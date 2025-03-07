<template>
  <button 
    class="p-3 relative cursor-pointer rounded-xl hover:text-white dark:hover:text-blue-600 w-full flex items-center"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
 
    <div 
    v-if="!noLine"
      class="absolute left-0 top-0 bottom-0 w-1 bg-gray-300 dark:bg-blue-600 transition-all duration-300 ease-in-out"
      :style="lineStyle"
    ></div>
    
    <box-icon 
      :type="type" 
      :name="name" 
      class="w-7 h-8 relative z-10 transition-colors duration-300"
      :color="(isHovered || isActive) && noLine ? '#FF0101' : ((isHovered || isActive) ? (!checkDarkMode() ? '#ffffff' : '#2a7dd1') : (!checkDarkMode() ? '#AFADAD' : '#969595'))"
    ></box-icon>
    
    <span 
      v-if="badge" 
      class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full z-20"
    ></span>
  </button>
</template>

<script setup>
import 'boxicons'
import { ref, computed, onMounted } from 'vue'

const isHovered = ref(false);
const isDarkMode = ref(false);


function checkDarkMode() {
  isDarkMode.value = document.documentElement.classList.contains('dark');
  return isDarkMode.value;
}

const props = defineProps({
  name: String,
  badge: Boolean,
  type: String,
  noLine: Boolean,
  isActive: {
    type: Boolean,
    default: false
  }
});


const lineStyle = computed(() => {
  if (isHovered.value || props.isActive) {
    return {
      opacity: 1,
      height: '100%',
      transform: 'scaleY(1)',
    }
  }
  return {
    opacity: 0,
    height: '100%',
    transform: 'scaleY(0)',
  }
});
</script>

