<script setup>
import { defineProps, ref, defineEmits } from 'vue';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import FilterTag from '@/Components/FilterTag.vue';
import Layout from '@/Layouts/Layout.vue';
import FilterModal from '@/Components/FilterModal.vue'; 

import 'boxicons';

// Props de proyectos
defineProps({
  projects: Array,
});

const filterModal = ref(null);
const activeFilters = ref([]);

// Métodos para mostrar y cerrar el modal de filtros
const showFilterModal = () => {
  filterModal.value.isOpen = true;
};

const closeFilterModal = () => {
  if (filterModal.value) {
    filterModal.value.isOpen = false;
  }
};
</script>

<template>
  <Layout pageTitle="Projects">
    <div class="flex flex-row bg-black text-gray-300 min-h-screen">
      <div class="flex-1 p-8">
        <div class="mb-6 flex justify-between items-center">
          <div class="flex flex-row space-x-2">
            <div class="relative">
              <InputWithIcon
                icon="search"
                placeholder="Search projects..."
                class="h-10 w-full"
                type="text"
              />
            </div>
            <div>
              <FilterTag />
            </div>
          </div>
          <div class="flex-row flex items-center justify-end gap-x-2">
          
            <button
              class="group flex items-center bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition duration-200"
              @click="showFilterModal"
            >
              <box-icon
                name="filter"
                color="#ffffff"
                class="mr-2 transition-transform duration-300 group-hover:rotate-180"
              ></box-icon>
              Filter
            </button>

         
            <StandardButton  @click="$inertia.get(`/projects/create`)">New Project</StandardButton>
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
  <td colspan="7" class="p-4 text-center text-gray-400">No hay proyectos</td>
</tr>

<!-- Mostrar proyectos si hay datos -->
<tr
  v-for="project in projects"
  :key="project.id"
  class="border-b border-gray-900 bg-gray-950 hover:bg-gray-900 transition duration-200 cursor-pointer"
  @click="$inertia.get(`/projects/${project.id}`)"
>
  <td class="p-4">{{ project.name || 'No hay texto' }}</td>
  <td class="p-4 text-gray-400">{{ project.company || 'No hay texto' }}</td>
  <td class="p-4 text-gray-400">{{ project.projectLeader || 'No hay texto' }}</td>
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

  <!-- Modal de Filtros -->
  <FilterModal :show="isFilterModalOpen" @close="isFilterModalOpen = false" />
</template>
