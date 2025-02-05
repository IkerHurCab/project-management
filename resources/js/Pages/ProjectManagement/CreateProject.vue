<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import SelectWithIcon from '@/Components/SelectWithIcon.vue';
import 'boxicons'
// Props
defineProps({
  departmentHead: Array,
});

// Refs
const projectName = ref('');
const company = ref('');
const projectLeader = ref('');
const startDate = ref('');
const endDate = ref('');
const assignedHours = ref('');
const status = ref('');
const description = ref('');
const priority = ref(1);
const isPublic = ref(false);
const attachments = ref([]);

// Methods
const createProject = () => {
  // Lógica para crear el proyecto
};

const assignTask = () => {
  // Lógica para asignar la tarea
};

const handleFileUpload = (event) => {
  attachments.value = Array.from(event.target.files);
};
</script>

<template>
  <Layout pageTitle="Project Management">
    <div class="flex flex-col bg-black text-gray-300 min-h-screen p-8">
      <h1 class="text-3xl font-bold text-white mb-4">Create Project</h1>
      <p class="text-gray-400 mb-8 max-w-3xl">Create and Manage Projects of Any Scale: From Simple Tasks to Complex
        Collaborative Workflows with Defined Deadlines and Milestones.</p>

      <div class="bg-gray-950 border border-gray-700 rounded-lg overflow-hidden">
        <div class="border-b border-gray-700 p-6">
          <h3 class="text-xl font-semibold text-white">Project details</h3>
        </div>

        <form @submit.prevent="createProject" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="projectName" class="block text-sm font-medium text-gray-400 mb-2">Project Name</label>
              <InputWithIcon v-model="projectName" icon="folder" placeholder="Enter project name" class="w-full" />
            </div>

            <div>
              <label for="company" class="block text-sm font-medium text-gray-400 mb-2">Company</label>
              <InputWithIcon v-model="company" icon="building" placeholder="Enter company name" class="w-full"
                type="text" />
            </div>

            <div>
              <label for="projectLeader" class="block text-sm font-medium text-gray-400 mb-2">Project Leader</label>
              <SelectWithIcon v-model="projectLeader" icon="user" placeholder="Select project leader" class="w-full"
                :options="[
                  { label: 'Select project leader', value: '' },
                  ...departmentHead
                ]"/>
            </div>

            <div>
              <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
              <SelectWithIcon v-model="status" icon="check-square" placeholder="Select status" class="w-full" :options="[
                { label: 'Select status', value: '' },
                { label: 'Inactive', value: 'inactive' },
                { label: 'Pending', value: 'pending' },
                { label: 'Active', value: 'active' }
              ]" />
            </div>

            <!-- Agrupar estos campos en una fila -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 col-span-1 md:col-span-2 lg:col-span-3">
              <div>
                <label for="startDate" class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
                <InputWithIcon v-model="startDate" icon="calendar" placeholder="Select start date" class="w-full"
                  type="date" />
              </div>

              <div>
                <label for="endDate" class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
                <InputWithIcon v-model="endDate" icon="calendar" placeholder="Select end date" class="w-full"
                  type="date" />
              </div>

              <div>
                <label for="assignedHours" class="block text-sm font-medium text-gray-400 mb-2">Assigned Hours</label>
                <InputWithIcon v-model="assignedHours" icon="time" placeholder="Enter assigned hours" class="w-full"
                  type="number" />
              </div>

              <div>
                <label for="priority" class="block text-sm font-medium text-gray-400 mb-2">Priority</label>
                <SelectWithIcon v-model="priority" icon="flag" placeholder="Select priority" class="w-full" :options="[
                  { label: 'Low', value: 1 },
                  { label: 'Medium', value: 2 },
                  { label: 'High', value: 3 },
                  { label: 'Urgent', value: 4 }
                ]" />
              </div>


            </div>



            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
              <textarea v-model="description"
                class="w-full h-32 bg-gray-900 p-3 border border-gray-700 rounded-lg focus:outline-none focus:border-blue-500 text-white placeholder-gray-500"
                placeholder="Enter project description"></textarea>
            </div>
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="attachments" class="block text-sm font-medium text-gray-400 mb-2">Attachments</label>
              <div class="flex items-center justify-center w-full">
                <label for="dropzone-file"
                  class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-900 hover:bg-gray-800">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <box-icon name="upload" class="w-8 h-8 mb-3 text-gray-400"></box-icon>
                    <p class="mb-2 text-sm text-gray-400"><span class="font-semibold">Click to upload</span> or drag and
                      drop</p>
                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                  </div>
                  <input id="dropzone-file" type="file" class="hidden" multiple @change="handleFileUpload" />
                </label>
              </div>
              <div v-if="attachments.length > 0" class="mt-2">
                <p class="text-sm text-gray-400">{{ attachments.length }} file(s) selected</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end space-x-4 mt-8">
            <div class="flex items-center justify-center">
              <div class="flex items-center justify-between w-full">
               
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <div class="relative">
                    <input type="checkbox" class="sr-only" />
                    <div
                      class="w-5 h-5 bg-gray-700 border-2 border-gray-600 rounded-md transition-all duration-200 ease-in-out group-hover:border-gray-500">
                      <svg
                        class="w-3 h-3 text-blue-500 opacity-0 transition-opacity duration-200 ease-in-out absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                        :class="{ 'opacity-100': isPublic }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                  </div>
                  <span
                    class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors duration-200 ease-in-out">Make
                    project Private</span>
                </label>

              </div>
            </div>
            <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-700">
              Create Project
              
            </StandardButton>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>
