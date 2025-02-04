<script setup>
import { defineProps } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import 'boxicons';
const props = defineProps({
  project: Object
});
</script>

<template>
  <div class="flex flex-row w-full">
    <Sidebar/>
  
  <div class="bg-black text-gray-200 w-full min-h-screen p-8">
    <h1 class="text-3xl font-bold mb-8 text-white">{{ props.project.name }}</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <div class="bg-gray-800 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4 text-white">Project Details</h2>
        <p class="mb-2"><strong class="text-gray-400">Description:</strong> {{ props.project.description }}</p>
        <div class="flex items-center mb-2">
          <box-icon name='time-five' ></box-icon>
          <span><strong class="text-gray-400">Assigned Hours:</strong> {{ props.project.assigned_hours }} h</span>
        </div>
        <div class="flex items-center mb-2">
          <box-icon name='calendar' ></box-icon>
          <span><strong class="text-gray-400">Start Date:</strong> {{ props.project.start_date }}</span>
        </div>
        <div class="flex items-center">
          <box-icon name='calendar' ></box-icon>
          <span><strong class="text-gray-400">End Date:</strong> {{ props.project.end_date }}</span>
        </div>
      </div>
      
      <div class="bg-gray-800 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4 text-white flex items-center">
          <box-icon name='user' ></box-icon>
          Employees
        </h2>
        <ul class="space-y-4">
          <li v-for="user in project.users" :key="user.id" class="flex items-center">
            <div class="bg-gray-700 rounded-full p-2 mr-4">
              {{ user.name.charAt(0) }}
            </div>
            <div>
              <p class="font-medium">{{ user.name }}</p>
              <p class="text-gray-400 text-sm">{{ user.email }}</p>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-lg">
      <h2 class="text-xl font-semibold mb-4 text-white flex items-center">
        <box-icon name='check-square' ></box-icon>
        Tasks
      </h2>
      <ul class="space-y-6">
        <li v-for="task in project.tasks" :key="task.id" class="border-b border-gray-700 pb-4 last:border-b-0">
          <h3 class="font-semibold text-lg mb-2">{{ task.name }}</h3>
          <p class="mb-2 text-gray-400">{{ task.description }}</p>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <strong class="text-gray-400">Assigned Hours:</strong> {{ task.estimated_hours }} h
            </div>
            <div>
              <strong class="text-gray-400">Start Date:</strong> {{ task.start_date }}
            </div>
            <div>
              <strong class="text-gray-400">Assigned Employee:</strong> {{ task.user.name }}
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
</template>

<style scoped>
/* Add any additional styles here */
</style>