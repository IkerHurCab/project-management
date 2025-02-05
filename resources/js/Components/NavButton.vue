<template>
    <button 
      class="p-3 relative cursor-pointer rounded-xl hover:text-white w-full flex items-center"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      @click="action"
    >
   
      <div 
      v-if="!noLine"
        class="absolute left-0 top-0 bottom-0 w-1 bg-gray-300 transition-all duration-300 ease-in-out"
        :style="lineStyle"
      ></div>
      
      <box-icon 
        :type="type" 
        :name="name" 
        class="w-7 h-8 relative z-10 transition-colors duration-300"
        :color="isHovered && noLine ? '#FF0101' : (isHovered ? '#ffffff' : '#AFADAD')"

      ></box-icon>
      
      <span 
        v-if="badge" 
        class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full z-20"
      ></span>
    </button>
  </template>
  
  <script setup>
  import 'boxicons'
  import { ref, computed } from 'vue'
  
  const isHovered = ref(false)
  
  const props = defineProps({
    name: String,
    badge: Boolean,
    type: String,
    noLine: Boolean,
    action: Function,
  
  })
  
 
  const lineStyle = computed(() => {
    if (isHovered.value) {
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
  })
  </script>
  

