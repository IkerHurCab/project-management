<script setup>
import { ref, watch } from 'vue';
import 'boxicons';
import FilterTag from '@/Components/FilterTag.vue';

const emit = defineEmits(['close', 'update:filters']);

const activeMenu = ref('');
const isHoveringPrimary = ref(false);
const isHoveringSecondary = ref(false);
const selectedFilters = ref({
  dateRange: { start: '', end: '' },
  status: '',
  user: ''
});

const activeFilterTags = ref([]);

const menuItems = [
  { id: 'dates', icon: 'calendar', label: 'Date Range', value: 'dateRange' },
  { id: 'status', icon: 'check-square', label: 'Status', value: 'status' },
  { id: 'user', icon: 'user', label: 'User', value: 'user' }
];

const props = defineProps({
  departmentHeads: Array,
});

const statuses = [
  { value: 'pending', label: 'Pendiente' },
  { value: 'in_progress', label: 'En progreso' },
  { value: 'finished', label: 'Finalizado' },
  { value: 'inactive', label: 'Inactivo' },
];


const showMenu = (menu) => {
  activeMenu.value = menu;
};

const hideMenu = () => {
  if (!isHoveringPrimary.value && !isHoveringSecondary.value) {
    activeMenu.value = '';
  }
};

const updateFilters = () => {
  emit('update:filters', selectedFilters.value);
  updateFilterTags();
};

const updateFilterTags = () => {
  activeFilterTags.value = [];
  if (selectedFilters.value.dateRange.start || selectedFilters.value.dateRange.end) {
    activeFilterTags.value.push({
      type: 'dateRange',
      label: `Date: ${selectedFilters.value.dateRange.start} - ${selectedFilters.value.dateRange.end}`
    });
  }
  if (selectedFilters.value.status) {
    // Buscar el 'label' correspondiente al 'value' en el array 'statuses'
    const statusLabel = statuses.find(status => status.value === selectedFilters.value.status)?.label;
    activeFilterTags.value.push({
      type: 'status',
      label: `Status: ${statusLabel || 'Not selected'}`  // Si no se encuentra, muestra 'Not selected'
    });
  }
  if (selectedFilters.value.user) {
    activeFilterTags.value.push({
      type: 'user',
      label: `User: ${selectedFilters.value.user.name}`
    });
  }
};

const removeFilter = (filterType) => {
  if (filterType === 'dateRange') {
    selectedFilters.value.dateRange = { start: '', end: '' };
  } else {
    selectedFilters.value[filterType] = '';
  }
  updateFilters();
};

const closeModal = () => {
  emit('close');
  activeMenu.value = '';
};

watch(activeFilterTags, (newTags) => {
  emit('update:filterTags', newTags);
}, { deep: true });
</script>

<template>
  <div class="flex flex-col bg-gray-900 shadow-xl rounded-lg overflow-hidden">
    <div class="flex items-center justify-between p-4 border-b border-gray-800">
      <h2 class="text-xl font-semibold text-white">Filters</h2>
      <button @click="closeModal" class="cursor-pointer text-gray-400 hover:text-white">
        <box-icon name='x' color='#ffffff'></box-icon>
      </button>
    </div>

    <div class="flex">
      <!-- Menú secundario (a la izquierda, condicional) -->
      <div 
        v-if="activeMenu" 
        class="w-64 p-4 bg-gray-800 rounded-l-lg"
        @mouseenter="isHoveringSecondary = true"
        @mouseleave="isHoveringSecondary = false; hideMenu()"
      >
        <h3 class="text-lg font-semibold text-white mb-4">{{ activeMenu }}</h3>
        <div v-if="activeMenu === 'dates'" class="space-y-4">
          <div>
            <label class="block text-sm text-gray-400 mb-1">Start Date</label>
            <input 
              v-model="selectedFilters.dateRange.start"
              type="date"
              class="w-full bg-gray-700 text-white p-2 rounded-lg border border-gray-600 focus:outline-none focus:border-gray-500"
              @change="updateFilters"
            >
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">End Date</label>
            <input 
              v-model="selectedFilters.dateRange.end"
              type="date"
              class="w-full bg-gray-700 text-white p-2 rounded-lg border border-gray-600 focus:outline-none focus:border-gray-500"
              @change="updateFilters"
            >
          </div>
        </div>
        <div v-else-if="activeMenu === 'status'" class="space-y-2">
  <button
    v-for="status in statuses"
    :key="status.value"
    @click="selectedFilters.status = status.value; updateFilters()"
    class="w-full text-left p-2 rounded-lg hover:bg-gray-700 text-white"
    :class="{ 'bg-gray-700': selectedFilters.status === status.value }"
  >
    {{ status.label }}
  </button>
</div>
        <div v-else-if="activeMenu === 'user'" class="space-y-2">
          <button
            v-for="user in props.departmentHeads"
            :key="user.id"
            @click="selectedFilters.user = user; updateFilters()"
            class="w-full text-left p-2 rounded-lg hover:bg-gray-700 text-white"
            :class="{ 'bg-gray-700': selectedFilters.user === user }"
          >
            {{ user.name }}
          </button>
        </div>
      </div>

      <!-- Menú principal -->
      <div class="w-64 bg-gray-900">
        <div class="">
          <button 
            v-for="item in menuItems" 
            :key="item.id"
            @mouseenter="isHoveringPrimary = true; showMenu(item.id)"
            @mouseleave="isHoveringPrimary = false; hideMenu()"
            class="w-full flex items-center justify-between px-5 py-3 hover:bg-gray-800 text-white"
          >
            <div class="flex items-center gap-x-2">
              <box-icon :name="item.icon" color='#ffffff'></box-icon>
              <span>{{ item.label }}</span>
            </div>
          </button>
        </div>
      </div>
    </div>

   
  </div>
</template>