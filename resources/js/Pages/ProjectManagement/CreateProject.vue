<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import SelectWithIcon from '@/Components/SelectWithIcon.vue';

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

// Methods
const createProject = () => {
  // Lógica para crear el proyecto
};

const assignTask = () => {
  // Lógica para asignar la tarea
};
</script>

<template>
  <Layout pageTitle="Create Project">
    <div class="flex flex-col bg-black text-gray-300 min-h-screen px-8 py-3">
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
                :options="[{ label: 'Select project leader', value: '' },
                ...departmentHead
                ]" />
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

            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
              <textarea v-model="description"
                class="w-full h-32 bg-gray-900 p-3 border border-gray-700 rounded-lg focus:outline-none focus:border-gray-300 text-white placeholder-gray-500"
                placeholder="Enter project description"></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-4 mt-8">

            <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-700">
              Create Project
            </StandardButton>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>