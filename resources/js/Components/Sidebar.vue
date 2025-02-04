<template>
    <div class="relative flex flex-col h-screen w-fit fixed justify-between py-4">
      <!-- Sección superior -->
      <div class="relative flex flex-col space-y-2" @mouseleave="activeTopIndex = null">
        <div 
          class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out"
          :style="topLineStyle"
        ></div>
        <div class="flex flex-col items-center ">
          <NavButton 
            v-for="(button, index) in buttons" 
            :key="index"
            v-bind="button"
            @mouseenter="activeTopIndex = index"
            :isActive="activeTopIndex === index"
          />
        </div>
      </div>
  
      <!-- Sección inferior -->
      <div class="flex flex-col gap-y-4 pb-4" @mouseleave="activeBottomIndex = null">
        <div 
          class="absolute left-0 w-1  bg-white transition-all duration-300 ease-in-out"
          :style="bottomLineStyle"
        ></div>
        <div class="flex flex-col items-center ">
          <NavButton 
            v-for="(button, index) in lowButtons" 
            :key="index"
            v-bind="button"
            @mouseenter="activeBottomIndex = index"
            :isActive="activeBottomIndex === index"
          />
        </div>
        <div class="flex items-center justify-center ">
        <div class="w-[60%] flex  h-0.75 bg-[#AFADAD]"></div>
    </div>
        <NavButton 
  :name="'exit'"
   @mouseenter="activeBottomIndex = null"
  :isActive="activeBottomIndex === index"
  :noLine="true"
  class="mt-2" 
  @click="$inertia.get('/logout')"  
/>
      </div>
      
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import NavButton from '@/Components/NavButton.vue';
 
  
  const buttons = [
    { name: 'dashboard', type: 'solid' },
    { name: 'file' },
    { name: 'calendar' },
    { name: 'time-five' }
  ];
  
  const lowButtons = [
    { name: 'cog' },
  ];
  
  
  
  const activeTopIndex = ref(null);
  const activeBottomIndex = ref(null);
  
  const createLineStyle = (activeIndex) => computed(() => {
    if (activeIndex.value === null) {
      return { 
        opacity: 0,
        transform: 'translateY(0)'
      };
    }
    return {
      opacity: 1,
      transform: `translateY(${activeIndex.value * 56}px)`, // Ajusta 56 al alto de tus botones
      height: '48px' // Ajusta esto según el tamaño de tus botones
    };
  });
  
  const topLineStyle = createLineStyle(activeTopIndex);
  const bottomLineStyle = createLineStyle(activeBottomIndex);
  </script>