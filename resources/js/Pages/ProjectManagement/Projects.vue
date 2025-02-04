<script setup>
import { defineProps } from 'vue';

import StandardButton from '@/Components/StandardButton.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import FilterTag from '@/Components/FilterTag.vue';

import 'boxicons';

defineProps({ projects: Array });
</script>

<template>
  <div class="flex flex-row bg-black text-gray-300 min-h-screen">
    <div class="flex-1 p-8">
      <h1 class="text-3xl font-bold mb-8 text-white">Projects</h1>
  
      <div class="mb-6 flex justify-between items-center">
        <div class="flex flex-row space-x-2">
        <div class="relative">
          
          <InputWithIcon  icon="search"  placeholder="Search projects..." class="h-10 w-full"
          type="text" />
        </div>
        <div>
        <FilterTag />
      </div>
      </div>
<div class="flex-row flex items-center justify-end gap-x-2">
        <button class="flex items-center bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition duration-200">
          <box-icon name='filter' class="mr-2" color='#ffffff' ></box-icon> Filter
        </button>
        <StandardButton >
          New Project
        </StandardButton>
      </div>
      </div>

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
            <tr
              v-for="project in projects"
              :key="project.id"
              class="border-b border-gray-900 bg-gray-950 hover:bg-gray-900 transition duration-200 cursor-pointer"
              @click="$inertia.get(`/projects/${project.id}`)"
            >
              <td class="p-4">
          {{ project.name }}
              </td>
              <td class="p-4 text-gray-400">Company</td>
              <td class="p-4 text-gray-400">Lucas Martínez</td>
              <td class="p-4 text-gray-400">{{ project.start_date }}</td>
              <td class="p-4 text-gray-400">{{ project.end_date }}</td>
              <td class="p-4 text-gray-400">{{ project.assigned_hours }} h</td>
              <td class="p-4 flex ">
                <StatusBadge  :status="project.status" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>


