<template>
  <div class="fixed top-0 left-0 h-full w-16 flex flex-col justify-between py-4 bg-black text-white">
     <!-- Sección superior  -->
    <div class="relative flex flex-col space-y-2" @mouseleave="activeTopIndex = null">
      <div class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out" :style="topLineStyle"></div>
      <div class="flex flex-col items-center">
        <NavButton 
          v-for="(button, index) in buttons" 
          :key="index" 
          v-bind="button"
          @mouseenter="activeTopIndex = index" 
          :isActive="activeTopIndex === index" 
        />
      </div>
    </div>

     <!-- Sección inferior  -->
    <div class="flex flex-col gap-y-4" @mouseleave="activeBottomIndex = null">
      <div class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out" :style="bottomLineStyle"></div>
      <div class="flex flex-col items-center">
        <NavButton 
          v-for="(button, index) in lowButtons" 
          :key="index" 
          v-bind="button"
          @mouseenter="activeBottomIndex = index" 
          :isActive="activeBottomIndex === index" 
        />
      </div>
      <div class="flex items-center justify-center">
        <div class="w-3/4 h-px bg-gray-600"></div>
      </div>
      <NavButton 
        name="exit" 
        @mouseenter="activeBottomIndex = null" 
        :isActive="false"
        :noLine="true" 
        class="mt-2" 
        @click="logout" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import NavButton from '../../Components/NavButton.vue'

const buttons = [
  { name: 'dashboard', type: 'solid' },
  { name: 'file' },
  { name: 'calendar' },
  { name: 'time-five' }
];

const lowButtons = [
  { name: 'cog' },
];

const logout = () => {
  window.location.href = '/logout';
};

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
    transform: `translateY(${activeIndex.value * 56}px)`,
    height: '48px'
  };
});

const topLineStyle = createLineStyle(activeTopIndex);
const bottomLineStyle = createLineStyle(activeBottomIndex);
</script>