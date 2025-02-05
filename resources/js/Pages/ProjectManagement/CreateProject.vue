<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import SelectWithIcon from '@/Components/SelectWithIcon.vue';

const projectName = ref('');
const company = ref('');
const projectLeader = ref('');
const startDate = ref('');
const endDate = ref('');
const assignedHours = ref('');
const status = ref('');

const createProject = () => {
  
};

const assignTask = () => {
 
};
    
defineProps({
  departmentHead: Array,
});
</script>

<template>
  <Layout pageTitle="Create Project">
    <div class="flex flex-row bg-black text-gray-300 min-h-screen">
      <div class="flex-1 p-8">

        <div class="bg-gray-900 rounded-lg overflow-hidden p-6">
          <form @submit.prevent="createProject" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label for="projectName" class="block text-sm font-medium text-gray-400 mb-2">Project Name</label>
                <InputWithIcon
                  v-model="projectName"
                  icon="folder"
                  placeholder="Enter project name"
                  class="w-full"
                  

                />
              </div>
              <div>
                <label for="company" class="block text-sm font-medium text-gray-400 mb-2">Company</label>
                <InputWithIcon
                  v-model="company"
                  icon="building"
                  placeholder="Enter company name"
                  class="w-full"
                  type="text"

                />
              </div>
              <div>
                <label for="projectLeader" class="block text-sm font-medium text-gray-400 mb-2">Project Leader</label>
                <SelectWithIcon
                  v-model="projectLeader"
                  icon="user"
                  placeholder="Select project leader"
                  class="w-full"
                  :options="departmentHead" />
              </div>
              <div>
                <label for="startDate" class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
                <InputWithIcon
                  v-model="startDate"
                  icon="calendar"
                  placeholder="Select start date"
                  class="w-full"
                  type="date"
                />
              </div>
              <div>
                <label for="endDate" class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
                <InputWithIcon
                  v-model="endDate"
                  icon="calendar"
                  placeholder="Select end date"
                  class="w-full"
                  type="date"
                />
              </div>
              <div>
                <label for="assignedHours" class="block text-sm font-medium text-gray-400 mb-2">Assigned Hours</label>
                <InputWithIcon
                  v-model="assignedHours"
                  icon="time"
                  placeholder="Enter assigned hours"
                  class="w-full"
                  type="number"
                />
              </div>
              <div>
                <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                <select
                  v-model="status"
                  class="w-full bg-gray-800 border border-gray-700 rounded-lg py-2 px-4 text-white focus:outline-none focus:border-blue-500"
                >
                  <option value="" disabled>Select status</option>
                  <option value="active">Active</option>
                  <option value="pending">Pending</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end space-x-4">
              <StandardButton type="button" @click="assignTask" >
                Assign Task
              </StandardButton>
              <StandardButton type="submit">Create Project</StandardButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Layout>
</template>