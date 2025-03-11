<script setup>
import { ref, computed } from 'vue';
import { toast } from 'vue3-toastify';

import Layout from '@/Layouts/Layout.vue';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import SelectWithIcon from '@/Components/SelectWithIcon.vue';
import { router } from '@inertiajs/vue3'


// Props
const props = defineProps({
  departmentHead: Array,
  userDepartments: Array,
});

const errors = ref({});

const selectedDepartments = ref([]);
const departmentInput = ref('');

const createProject = () => {
  // Datos a enviar al servidor
  const projectData = {
    name: projectName.value,
    project_leader_id: projectLeader.value,
    start_date: startDate.value,
    end_date: endDate.value,
    assigned_hours: assignedHours.value,
    status: 'in_progress',
    description: description.value,
    priority: priority.value,
    is_public: !is_private.value,
    attachments: attachments.value,
    department_ids: selectedDepartments.value,
  };

  // Enviar el formulario usando Inertia
  router.post('/projects', projectData, {
    onSuccess: () => {
      toast.success('Project created successfully');
    },
    onError: (err) => {
      toast.error('An error occurred while creating the project');
      errors.value = err
    }
  });
};

const projectName = ref('');
const company = ref('');
const projectLeader = ref('');
const startDate = ref(getCurrentDate());
const endDate = ref('');
const assignedHours = ref('');
const status = ref('');
const description = ref('');
const priority = ref(1);
const is_private = ref(false);
const attachments = ref([]);

function getCurrentDate() {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

const handleFileUpload = (event) => {
  attachments.value = Array.from(event.target.files);
};

const addDepartment = () => {
  if (departmentInput.value && !selectedDepartments.value.includes(departmentInput.value)) {
    selectedDepartments.value.push(departmentInput.value);
    departmentInput.value = '';
  }
};

const removeDepartment = (dept) => {
  selectedDepartments.value = selectedDepartments.value.filter(d => d !== dept);
};

const filteredDepartments = computed(() => {
  if (departmentInput.value === '') {
    return [];
  }
  return props.userDepartments.filter(dept =>
    !selectedDepartments.value.includes(dept.id) &&
    dept.name.toLowerCase().includes(departmentInput.value.toLowerCase())
  );
});

const handleKeydown = (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    addDepartment();
  }
};

const isDarkMode = ref(false);

function checkDarkMode() {
  isDarkMode.value = document.documentElement.classList.contains('dark');
  return isDarkMode.value;
}
</script>

<template>
  <Layout pageTitle="Project Management">

    <div
      class="flex flex-col  dark:bg-gray-100 dark:border-none rounded-lg  text-gray-300 dark:text-gray-700 min-h-screen px-6 py-4">
      <div class="flex flex-row  gap-x-4 ">
        <div class="cursor-pointer gap-x-2 flex items-center " @click="$inertia.visit('/projects')">


          <box-icon name='arrow-back' :color="checkDarkMode() ? '#000000' : '#fffdfd'"></box-icon> Back to Projects
        </div>
        <h1 class="text-2xl font-bold text-white dark:text-gray-700">Create Project</h1>
      </div>
      <div
        class="bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl border border-gray-700 mt-5 rounded-lg overflow-hidden">
        <div class="border-b border-gray-700 p-6">
          <h3 class="text-xl font-semibold">Project Details</h3>
        </div>

        <form @submit.prevent="createProject" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="projectName" class="block text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Project
                Name</label>
              <InputWithIcon v-model="projectName" icon="folder" placeholder="Enter project name" class="w-full" />
              <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
            </div>

            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="department"
                class="block text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Departments</label>
              <div class="relative">
                <InputWithIcon v-model="departmentInput" icon="group" placeholder="Select departments" class="w-full"
                  @keydown="handleKeydown" />
                <div v-if="filteredDepartments.length > 0"
                  class="absolute z-10 w-full mt-1 bg-gray-900 dark:bg-gray-300 border border-gray-700 rounded-lg shadow-lg">
                  <div v-for="dept in filteredDepartments" :key="dept.id"
                    @click="selectedDepartments.push(dept.id); departmentInput = ''"
                    class="px-4 py-2 hover:bg-gray-800 dark:hover:bg-gray-200 cursor-pointer">
                    {{ dept.name }}
                  </div>
                </div>
                <p v-if="errors.department_ids" class="text-red-500 text-sm mt-1">{{ errors.department_ids }}</p>
              </div>
              <div class="flex flex-wrap gap-2 mt-2">
                <div v-for="deptId in selectedDepartments" :key="deptId"
                  class="bg-gray-900 dark:bg-gray-300 dark:text-gray-700 text-gray-200 border border-gray-600 px-4 py-1 rounded-full text-sm flex items-center">
                  {{props.userDepartments.find(d => d.id === deptId).name}}
                  <button @click="removeDepartment(deptId)" class="ml-2 mt-1 cursor-pointer focus:outline-none">
                    <box-icon name='x' :color="checkDarkMode() ? '#000000' : '#ffffff'" size="sm"></box-icon>
                  </button>
                </div>
              </div>
            </div>
            <div class="">
              <label for="projectLeader" class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">Project
                Leader</label>
              <SelectWithIcon v-model="projectLeader" icon="user" placeholder="Select project leader" class="w-full"
                :options="[
                  { label: 'Select the project leader', value: '' },
                  ...departmentHead.map(leader => ({ label: leader.label, value: leader.value }))
                ]" />
                <p v-if="errors.project_leader_id" class="text-red-500 text-sm mt-1">{{ errors.project_leader_id }}</p>
            </div>
            <div class="">
              <label for="priority"
                class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">Priority</label>
              <SelectWithIcon v-model="priority" icon="flag" placeholder="Select priority" class="w-full" :options="[
                { label: 'Low', value: 1 },
                { label: 'Medium', value: 2 },
                { label: 'High', value: 3 },
                { label: 'Urgent', value: 4 }
              ]" />
              <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority }}</p>
            </div>


            
            <div>
              <label for="startDate" class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">Start
                Date</label>
              <InputWithIcon v-model="startDate" icon="calendar" placeholder="Select start date" class="w-full"
                type="date" />
                <p v-if="errors.start_date" class="text-red-500 text-sm mt-1">{{ errors.start_date }}</p>
            </div>

            <div>
              <label for="endDate" class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">End
                Date</label>
              <InputWithIcon v-model="endDate" icon="calendar" placeholder="Select end date" class="w-full"
                type="date" />
                <p v-if="errors.end_date" class="text-red-500 text-sm mt-1">{{ errors.end_date }}</p>
            </div>

            <div>
              <label for="assignedHours"
                class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">Assigned Hours</label>
              <InputWithIcon v-model="assignedHours" icon="time" placeholder="Enter assigned hours" class="w-full"
                type="number" />
                <p v-if="errors.assigned_hours" class="text-red-500 text-sm mt-1">{{ errors.assigned_hours }}</p>
            </div>









            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label class="block text-sm font-medium text-gray-400 mb-2 dark:text-gray-600">Description</label>
              <textarea v-model="description"
                class="w-full h-32 bg-gray-900 dark:bg-white p-3 border border-gray-700 rounded-lg focus:outline-none focus:border-gray-300 text-white placeholder-gray-500"
                placeholder="Enter project description"></textarea>
            </div>
            <div class="col-span-1 md:col-span-2 lg:col-span-3">

              <div v-if="attachments.length > 0" class="mt-2">
                <p class="text-sm text-gray-400 dark:text-gray-600">{{ attachments.length }} file(s) selected</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end space-x-4 mt-8">
            <div class="flex items-center justify-center">
              <div class="flex items-center">
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <div class="flex items-center space-x-1">
                    <box-icon name='lock-alt' color="#99A1AF"></box-icon>
                    <span
                      class="text-sm mt-1 text-gray-400 group-hover:text-gray-300 dark:group-hover:text-gray-600 transition-colors duration-200 ease-in-out">
                      Private
                    </span>
                  </div>
                  <div class="relative">
                    <input type="checkbox" v-model="is_private" class="sr-only" />
                    <div
                      class="w-5 h-5 bg-gray-700 dark:bg-white border-2 border-gray-600 rounded-md transition-all duration-200 ease-in-out group-hover:border-gray-500">
                      <svg
                        class="w-3 h-3 text-blue-500 opacity-0 transition-opacity duration-200 ease-in-out absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                        :class="{ 'opacity-100': is_private }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                  </div>

                </label>
              </div>
            </div>
            <StandardButton type="submit">
              Create Project
            </StandardButton>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>
