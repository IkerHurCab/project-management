<script setup>
import { ref, watch, onMounted } from 'vue';
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
  user: Object,
  isAdminOrDepartmentHead: Boolean
});

const searchQuery = ref('');
const activeFilters = ref({
  dateRange: { start: '', end: '' },
  status: '',
  user: null,
  myProjects: false,
});
const filterTags = ref([]);
const isFilterModalOpen = ref(false);

onMounted(() => {
  searchQuery.value = props.search || '';
  const urlParams = new URLSearchParams(window.location.search);

  activeFilters.value = {
    dateRange: {
      start: urlParams.get('start_date') || '',
      end: urlParams.get('end_date') || '',
    },
    status: urlParams.get('status') || '',
    user: props.departmentHeads.find(user => user.name === urlParams.get('user')) || null,
    myProjects: urlParams.get('my_projects') === 'true',
  };

  updateFilterTags();
});

watch(searchQuery, () => {
  applyFilters();
});

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
  if (activeFilters.value.dateRange.start) {
    filterTags.value.push({ type: 'dateRange', label: `Start Date: ${activeFilters.value.dateRange.start}` });
  }
  if (activeFilters.value.dateRange.end) {
    filterTags.value.push({ type: 'dateRange', label: `End Date: ${activeFilters.value.dateRange.end}` });
  }
  if (activeFilters.value.status) {
    filterTags.value.push({ type: 'status', label: `Status: ${activeFilters.value.status}` });
  }
  if (activeFilters.value.user) {
    filterTags.value.push({ type: 'user', label: `User: ${activeFilters.value.user.name}` });
  }
  if (activeFilters.value.myProjects) {
    filterTags.value.push({ type: 'myProjects', label: 'My Projects' });
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
    queryParams.append('user', activeFilters.value.user.name);
  }

  if (activeFilters.value.myProjects) {
    queryParams.append('my_projects', 'true');
  }

  const url = queryParams.toString() ? `${props.projectsUrl}?${queryParams.toString()}` : props.projectsUrl;

  router.get(url, {}, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
};

const removeFilterTag = (filterType) => {
  if (filterType === 'dateRange') {
    activeFilters.value.dateRange = { start: '', end: '' };
  } else if (filterType === 'myProjects') {
    activeFilters.value.myProjects = false;
  } else {
    activeFilters.value[filterType] = '';
  }
  updateFilterTags();
  applyFilters();
};

const isUserInProject = (project) => {
  return project.users && project.users.some(user => user.id === props.user.id) || project.project_leader_id === props.user.id;
};
</script>

<template>
  <Layout pageTitle="Project Management">
    <div class="p-4">
      <div class="flex flex-row  mt-4 rounded-lg">
        <div class="flex-1 px-6 py-4">
          <h1 class="text-3xl font-bold mb-4">All Projects</h1>
          <div class="mb-6 flex justify-between items-center">
            <div class="flex flex-row space-x-2">
              <div class="relative">
                <InputWithIcon icon="search" v-model="searchQuery" placeholder="Search projects..." class="h-10 w-full"
                  type="text" />
              </div>
              <div v-if="filterTags.length > 0" class="flex flex-wrap gap-2">
                <FilterTag v-for="tag in filterTags" :key="tag.type" :label="tag.label"
                  @remove="removeFilterTag(tag.type)" />
              </div>
            </div>
            <div class="flex-row flex items-center justify-end gap-x-2 relative">
              <button
                class="group flex cursor-pointer items-center bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition duration-200"
                @click="showFilterModal">
                <box-icon name="filter" color="#ffffff"
                  class="mr-2 transition-transform duration-300 group-hover:rotate-180"></box-icon>
                Filter
              </button>
              <FilterModal :departmentHeads="departmentHeads" v-if="isFilterModalOpen" @close="closeFilterModal"
                @update:filters="updateFilters" :initial-filters="activeFilters"
                class="absolute top-full mt-2 left-[] z-50" />
              <StandardButton v-if="isAdminOrDepartmentHead" @click="router.get(`/projects/create`)">New Project
              </StandardButton>
            </div>
          </div>

          <!-- Projects table -->
          <div class="bg-gray-900 dark:bg-gray-200 rounded-lg overflow-hidden dark:shadow-lg">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-800 dark:bg-gray-200 text-left">
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Project Name</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Departments</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Project Leader</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Start Date</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">End Date</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Assigned Hours</th>
                  <th class="p-4 font-semibold text-gray-400 dark:text-gray-700 ">Status</th>

                </tr>
              </thead>
              <tbody>
                <tr v-if="projects.length === 0" class="border-gray-900 dark:bg-gray-50 bg-gray-950">
                  <td colspan="7" class="p-4 text-center text-gray-100 dark:text-gray-500">No projects found</td>
                </tr>

                <tr v-for="(project, index) in projects" :key="project.id" :class="{
                  'bg-green-950 hover:bg-green-900 dark:bg-green-200 dark:hover:bg-green-300 cursor-pointer': isUserInProject(project),
                  'bg-gray-950 hover:bg-gray-900 dark:bg-gray-200 dark:hover:bg-gray-300 cursor-not-allowed': !isUserInProject(project),
                  'border-b border-gray-900 dark:border-gray-600': index !== projects.length - 1,
                  'transition duration-200': true,
                }" @click="router.get(`/projects/${project.id}`)">

                  <td class="p-4">{{ project.name || 'No hay texto' }}</td>
                  <td class="p-4 text-gray-400 dark:text-gray-700">
                    <span v-if="project.departments && project.departments.length > 0">
                      <ul>
                        <li v-for="department in project.departments" :key="department.id">
                          {{ department.name || 'No hay texto' }}
                        </li>
                      </ul>
                    </span>
                    <span v-else>No hay texto</span>
                  </td>
                  <td class="p-4 text-gray-400 dark:text-gray-700">{{ project.leader?.name ?? 'No asignado' }}</td>
                  <td class="p-4 text-gray-400 dark:text-gray-700">{{ project.start_date || 'No hay texto' }}</td>
                  <td class="p-4 text-gray-400 dark:text-gray-700">{{ project.end_date || 'No hay texto' }}</td>
                  <td class="p-4 text-gray-400 dark:text-gray-700">{{ project.assigned_hours ? project.assigned_hours +
                    ' h' : 'No hay texto' }}</td>
                  <td class="p-4 flex">
                    <StatusBadge :status="project.status" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>