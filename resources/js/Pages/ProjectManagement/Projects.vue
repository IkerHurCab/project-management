<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import FilterTag from '@/Components/FilterTag.vue';
import Layout from '@/Layouts/Layout.vue';
import FilterModal from '@/Components/FilterModal.vue';
import 'boxicons';

const props = defineProps({
  projects: Array,
  search: String, 
  projectsUrl: String, 
  departmentHeads: Array,
  statuses: Array,
});

const searchQuery = ref(props.search || '');
const activeFilters = ref({
  dateRange: { start: '', end: '' },
  status: '',
  user: null
});
const filterTags = ref([]);

watch(searchQuery, (newValue) => {
  applyFilters();
});

const isFilterModalOpen = ref(false);
const showFilterModal = () => {
  isFilterModalOpen.value = !isFilterModalOpen.value;
};

const closeFilterModal = () => {
  isFilterModalOpen.value = false;
};

const updateFilters = (newFilters) => {
  activeFilters.value = newFilters;
  updateFilterTags();
  applyFilters();
};

const updateFilterTags = () => {
  filterTags.value = [];
  if (activeFilters.value.dateRange.start || activeFilters.value.dateRange.end) {
    filterTags.value.push({
      type: 'dateRange',
      label: `Start Date: ${activeFilters.value.dateRange.start }`
    });
  }
  if(activeFilters.value.dateRange.end){
    filterTags.value.push({
      type: 'dateRange',
      label: `End Date: ${activeFilters.value.dateRange.end}`
    });


  }
  if (activeFilters.value.status) {
    filterTags.value.push({
      type: 'status',
      label: `Status: ${activeFilters.value.status}`
    });
  }
  if (activeFilters.value.user) {
    filterTags.value.push({
      type: 'user',
      label: `User: ${activeFilters.value.user.name}`
    });
  }
};

const applyFilters = () => {
  const queryParams = new URLSearchParams();
  
  if (searchQuery.value) {
    queryParams.append('search', searchQuery.value);
  }
  
  if (activeFilters.value.dateRange.start) {
    queryParams.append('start_date', activeFilters.value.dateRange.start);
  }
  
  if (activeFilters.value.dateRange.end) {
    queryParams.append('end_date', activeFilters.value.dateRange.end);
  }
  
  if (activeFilters.value.status) {
    queryParams.append('status', activeFilters.value.status);
  }
  
  if (activeFilters.value.user) {
    queryParams.append('user_id', activeFilters.value.user.id);
  }

  const url = `${props.projectsUrl}?${queryParams.toString()}`;
  router.visit(url, {
    method: 'get',
    preserveState: true,
    replace: true,
  });
};

const removeFilterTag = (filterType) => {
  // Elimina el filtro de activeFilters
  if (filterType === 'dateRange') {
    activeFilters.value.dateRange = { start: '', end: '' };
  } else {
    activeFilters.value[filterType] = '';
  }
  // Llama a applyFilters para actualizar la URL con los filtros restantes
  applyFilters();
};

const handleRemoveFilter = (filterType) => {
  filterTags.value = filterTags.value.filter(tag => tag.type !== filterType);
  removeFilterTag(filterType); // Elimina el filtro de activeFilters
};

</script>

<template>
  <Layout pageTitle="Project Management">
    <div class="flex flex-row bg-black text-gray-300 min-h-screen">
      <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-white mb-4">All Projects</h1>
        <div class="mb-6 flex justify-between items-center">
          <div class="flex flex-row space-x-2">
            <div class="relative">
              <InputWithIcon icon="search" v-model="searchQuery" placeholder="Search projects..." class="h-10 w-full" type="text" />
            </div>
            <div v-if="filterTags.length > 0" class="flex flex-wrap gap-2">
              <FilterTag 
                v-for="tag in filterTags" 
                :key="tag.type" 
                :label="tag.label"
                @remove="handleRemoveFilter(tag.type)"
              />
            </div>
          </div>
          <div class="flex-row flex items-center justify-end gap-x-2 relative">
            <button
              class="group flex cursor-pointer items-center bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition duration-200"
              @click="showFilterModal">
              <box-icon name="filter" color="#ffffff" class="mr-2 transition-transform duration-300 group-hover:rotate-180"></box-icon>
              Filter
            </button>
            <FilterModal :departmentHeads="departmentHeads"  v-if="isFilterModalOpen" @close="closeFilterModal" @update:filters="updateFilters" class="absolute top-full mt-2 left-[] z-50" />
            <StandardButton @click="router.get(`/projects/create`)">New Project</StandardButton>
          </div>
        </div>

        <!-- Tabla con proyectos -->
        <div class="bg-gray-900 rounded-lg overflow-hidden">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-800 text-left">
                <th class="p-4 font-semibold text-gray-400">Project Name</th>
                <th class="p-4 font-semibold text-gray-400">Company</th>
                <th class="p-4 font-semibold text-gray-400">Project Leader</th>
                <th class="p-4 font-semibold text-gray-400">Start Date</th>
                <th class="p-4 font-semibold text-gray-400">End Date</th>
                <th class="p-4 font-semibold text-gray-400">Assigned Hours</th>
                <th class="p-4 font-semibold text-gray-400">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="projects.length === 0" class="border-b border-gray-900 bg-gray-950">
                <td colspan="7" class="p-4 text-center text-gray-400">No projects found</td>
              </tr>

              <tr v-for="project in projects" :key="project.id"
                class="border-b border-gray-900 bg-gray-950 hover:bg-gray-900 transition duration-200 cursor-pointer"
                @click="router.get(`/projects/${project.id}`)">
                <td class="p-4">{{ project.name || 'No hay texto' }}</td>
                <td class="p-4 text-gray-400">{{ project.company || 'No hay texto' }}</td>
                <td class="p-4 text-gray-400">{{ project.leader?.name ?? 'No asignado' }}</td>
                <td class="p-4 text-gray-400">{{ project.start_date || 'No hay texto' }}</td>
                <td class="p-4 text-gray-400">{{ project.end_date || 'No hay texto' }}</td>
                <td class="p-4 text-gray-400">{{ project.assigned_hours ? project.assigned_hours + ' h' : 'No hay texto' }}</td>
                <td class="p-4 flex">
                  <StatusBadge :status="project.status" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>

