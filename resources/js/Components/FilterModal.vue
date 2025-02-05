<script setup>
import { ref } from 'vue';


const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'update:filters']);

const activeMenu = ref('main');
const selectedFilters = ref({
  dateRange: {
    start: '',
    end: ''
  },
  status: '',
  user: ''
});

const statuses = ['Pending', 'In Progress', 'Finished', 'Inactive'];
const users = ['User 1', 'User 2', 'User 3', 'User 4'];

const showMenu = (menu) => {
  activeMenu.value = menu;
};

const goBack = () => {
  activeMenu.value = 'main';
};

const updateFilters = () => {
  emit('update:filters', selectedFilters.value);
};

const closeModal = () => {
  emit('close');
  activeMenu.value = 'main';
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50">
    <!-- Overlay semi-transparente -->
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>

    <!-- Modal principal -->
    <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[500px] bg-gray-900 shadow-xl">
      <!-- Header del modal -->
      <div class="flex items-center justify-between p-4 border-b border-gray-800">
        <h2 class="text-xl font-semibold text-white">Filters</h2>
        <button @click="closeModal" class="text-gray-400 hover:text-white">
          <X size="24" />
        </button>
      </div>

      <!-- Contenido del modal -->
      <div class="relative h-[calc(100%-64px)]">
        <!-- Menú principal -->
        <div 
          class="absolute inset-0 bg-gray-900 transition-transform duration-300"
          :class="activeMenu !== 'main' ? '-translate-x-full' : 'translate-x-0'"
        >
          <div class="p-4 space-y-2">
            <!-- Filtro de Fechas -->
            <button 
              @click="showMenu('dates')"
              class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-800 text-white"
            >
              <div class="flex items-center">
                <Calendar class="w-5 h-5 mr-2" />
                <span>Date Range</span>
              </div>
              <span class="text-sm text-gray-400">
                {{ selectedFilters.dateRange.start || selectedFilters.dateRange.end ? 'Selected' : '' }}
              </span>
            </button>

            <!-- Filtro de Status -->
            <button 
              @click="showMenu('status')"
              class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-800 text-white"
            >
              <div class="flex items-center">
                <Tag class="w-5 h-5 mr-2" />
                <span>Status</span>
              </div>
              <span class="text-sm text-gray-400">
                {{ selectedFilters.status || '' }}
              </span>
            </button>

            <!-- Filtro de Usuario -->
            <button 
              @click="showMenu('user')"
              class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-800 text-white"
            >
              <div class="flex items-center">
                <User class="w-5 h-5 mr-2" />
                <span>User</span>
              </div>
              <span class="text-sm text-gray-400">
                {{ selectedFilters.user || '' }}
              </span>
            </button>
          </div>
        </div>

        <!-- Menú de fechas -->
        <div 
          class="absolute inset-0 bg-gray-900 transition-transform duration-300"
          :class="activeMenu === 'dates' ? 'translate-x-0' : 'translate-x-full'"
        >
          <div class="p-4">
            <button 
              @click="goBack"
              class="mb-4 text-gray-400 hover:text-white flex items-center"
            >
              ← Back
            </button>
            <h3 class="text-lg font-semibold text-white mb-4">Date Range</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm text-gray-400 mb-1">Start Date</label>
                <input 
                  v-model="selectedFilters.dateRange.start"
                  type="date"
                  class="w-full bg-gray-800 text-white p-2 rounded-lg border border-gray-700 focus:outline-none focus:border-gray-600"
                  @change="updateFilters"
                >
              </div>
              <div>
                <label class="block text-sm text-gray-400 mb-1">End Date</label>
                <input 
                  v-model="selectedFilters.dateRange.end"
                  type="date"
                  class="w-full bg-gray-800 text-white p-2 rounded-lg border border-gray-700 focus:outline-none focus:border-gray-600"
                  @change="updateFilters"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Menú de status -->
        <div 
          class="absolute inset-0 bg-gray-900 transition-transform duration-300"
          :class="activeMenu === 'status' ? 'translate-x-0' : 'translate-x-full'"
        >
          <div class="p-4">
            <button 
              @click="goBack"
              class="mb-4 text-gray-400 hover:text-white flex items-center"
            >
              ← Back
            </button>
            <h3 class="text-lg font-semibold text-white mb-4">Status</h3>
            <div class="space-y-2">
              <button
                v-for="status in statuses"
                :key="status"
                @click="selectedFilters.status = status; updateFilters()"
                class="w-full text-left p-3 rounded-lg hover:bg-gray-800 text-white"
                :class="{ 'bg-gray-800': selectedFilters.status === status }"
              >
                {{ status }}
              </button>
            </div>
          </div>
        </div>

        <!-- Menú de usuarios -->
        <div 
          class="absolute inset-0 bg-gray-900 transition-transform duration-300"
          :class="activeMenu === 'user' ? 'translate-x-0' : 'translate-x-full'"
        >
          <div class="p-4">
            <button 
              @click="goBack"
              class="mb-4 text-gray-400 hover:text-white flex items-center"
            >
              ← Back
            </button>
            <h3 class="text-lg font-semibold text-white mb-4">User</h3>
            <div class="space-y-2">
              <button
                v-for="user in users"
                :key="user"
                @click="selectedFilters.user = user; updateFilters()"
                class="w-full text-left p-3 rounded-lg hover:bg-gray-800 text-white"
                :class="{ 'bg-gray-800': selectedFilters.user === user }"
              >
                {{ user }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>