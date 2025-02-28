<script setup>
import { ref, computed } from 'vue';
import NavButton from '../Components/NavButton.vue';

// Función para redirigir a la página de proyectos
const projects = () => {
  window.location.href = '/projects';
};

const departments = () => {
  window.location.href = '/departments';
};

const dashboard = () => {
  window.location.href = '/dashboard';
};  


// Función para cerrar sesión
const logout = () => {
  window.location.href = '/logout';
};

// Botones de navegación
const buttons = [
  { name: 'dashboard', type: 'solid', action: dashboard },
  { name: 'folder', action: projects }, // Aseguramos que el botón de "Projects" tenga acción
  { name: 'message-square' },
  { name: 'calendar' },
  { name: 'buildings', action: departments },
  { name: 'time-five' }
];

const lowButtons = [{ name: 'cog' }];

// Estado para manejar la línea activa
const activeTopIndex = ref(null);
const activeBottomIndex = ref(null);

const createLineStyle = (activeIndex) => computed(() => {
  return activeIndex.value === null
    ? { opacity: 0, transform: 'translateY(0)' }
    : { opacity: 1, transform: `translateY(${activeIndex.value * 56}px)`, height: '48px' };
});

const topLineStyle = createLineStyle(activeTopIndex);
const bottomLineStyle = createLineStyle(activeBottomIndex);
</script>

<template>
  <div class="fixed top-0 left-0 h-full w-16 flex flex-col justify-between py-4 bg-black text-white">
    <!-- Sección superior -->
    <div class="relative flex flex-col space-y-2" @mouseleave="activeTopIndex = null">
      <div class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out" :style="topLineStyle"></div>
      <div class="flex flex-col items-center">
        <NavButton 
          v-for="(button, index) in buttons" 
          :key="index" 
          v-bind="button"
          @mouseenter="activeTopIndex = index" 
          :isActive="activeTopIndex === index"
          @click="button.action ? button.action() : null" 
        />
      </div>
    </div>

    <!-- Sección inferior -->
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
