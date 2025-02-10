<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StandardButton from '@/Components/StandardButton.vue';
import VueApexCharts from 'vue3-apexcharts';
import 'boxicons';

const props = defineProps({
  project: {
    type: Object,
    required: true
  },
  employees: {
    type: Array,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  }
});

const activeTab = ref('tasks');

const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    background: 'transparent'
  },
  colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'], // Updated colors
  plotOptions: {
    donut: {
      size: '85%',
      background: 'transparent',
      labels: {
        show: true,
        total: {
          show: true,
          label: 'Total Hours',
          color: '#E5E7EB',
          fontSize: '16px',
          fontFamily: 'Inter, sans-serif'
        }
      }
    }
  },
  legend: {
    position: 'bottom',
    labels: {
      colors: '#E5E7EB'
    },
    markers: {
      width: 8,
      height: 8,
      radius: 12
    }
  },
  theme: {
    mode: 'dark'
  }
}));

const series = computed(() => {
  const completed = props.tasks
    .filter(task => task.status === 'completed')
    .reduce((sum, task) => sum + task.estimated_hours, 0);

  const inProgress = props.tasks
    .filter(task => task.status === 'in_progress')
    .reduce((sum, task) => sum + task.estimated_hours, 0);

  return [completed, inProgress];
});

const tasksByStatus = computed(() => {
  if (!Array.isArray(props.tasks)) {
    return {
      todo: [],
      in_progress: [],
      review: [],
      done: []
    };
  }

  return {
    todo: props.tasks.filter(task => task.status === 'pending'),
    in_progress: props.tasks.filter(task => task.status === 'in_progress'),
    review: props.tasks.filter(task => task.status === 'review'),
    done: props.tasks.filter(task => task.status === 'completed')
  };
});
</script>

<template>
  <Layout pageTitle="Project Management">
    <div class="min-h-screen bg-black text-gray-300">
      <div class="border-b border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-8">
            <a @click="$inertia.visit('/projects')"
              class="text-gray-300 hover:text-white cursor-pointer flex items-center space-x-2 transition-colors">
              <box-icon name='arrow-back' color='#E5E7EB'></box-icon>
              <span>Back to Projects</span>
            </a>
            <h1 class="text-xl font-semibold text-white">{{ project.name }}</h1>
          </div>
          <div class="flex items-center space-x-4">
            <StandardButton class="bg-blue-600 hover:bg-blue-700">+ Create Task</StandardButton>
          </div>
        </div>
      </div>

      <div class="bg-gray-950 px-6 py-2 border-b border-gray-700">
        <div class="flex space-x-6">
          <button v-for="tab in ['Overview', 'Tasks', 'Team', 'Analytics']" :key="tab"
            @click="activeTab = tab.toLowerCase()" :class="[
              'px-4 py-2 text-sm cursor-pointer font-medium rounded-md transition-colors',
              activeTab === tab.toLowerCase()
                ? 'bg-blue-600 text-white'
                : 'text-gray-400 hover:text-white'
            ]">
            {{ tab }}
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex h-[calc(100vh-132px)]">
        <!-- Left Panel -->
        <div class="flex-1 p-6 overflow-auto">
          <div v-if="activeTab === 'tasks'" class="grid grid-cols-4 gap-6 h-full">
            <!-- Task Columns -->
            <template v-for="(tasks, status) in tasksByStatus" :key="status">
              <div class="bg-gray-950 rounded-lg p-4 border border-gray-700">
                <h3 class="text-white font-medium mb-4 flex items-center justify-between">
                  {{ status.toUpperCase().replace('_', ' ') }}
                  <span class="bg-gray-800 text-xs px-2 py-1 rounded-full">{{ tasks.length }}</span>
                </h3>
                <div class="space-y-3">
                  <div v-for="task in tasks" :key="task.id"
                    class="bg-gray-900 p-4 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer border border-gray-700">
                    <h4 class="text-white font-medium">{{ task.name }}</h4>
                    <p class="text-gray-400 text-sm mt-1">{{ task.description }}</p>
                    <div class="mt-3 flex items-center justify-between">
                      <StatusBadge :status="task.status" />
                      <span class="text-gray-400 text-sm">{{ task.estimated_hours }}h</span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <div v-if="activeTab === 'overview'" class="grid grid-cols-12 gap-6">
            <!-- Project Details -->
            <div class="col-span-8 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-2xl font-semibold text-white ">Project Details</h2>
              </div>
              <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2 flex flex-col ">
                    <span class="text-sm font-medium text-gray-400">Status</span>
                    <StatusBadge :status="project.status"
                      class="bg-blue-500 text-white w-fit rounded-full py-1 px-4 text-sm" />
                  </div>
                  <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-400">Company</span>
                    <span class="text-lg text-white block font-light">Company</span>
                  </div>
                  <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-400">Start Date</span>
                    <span class="text-lg text-white block font-light">{{ project.start_date }}</span>
                  </div>
                  <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-400">End Date</span>
                    <span class="text-lg text-white block font-light">{{ project.end_date }}</span>
                  </div>
                  <div class="space-y-2 col-span-2">
                    <span class="text-sm font-medium text-gray-400">Description</span>
                    <p class="text-white">SmartTask Manager es una aplicación web diseñada para optimizar la gestión de tareas diarias tanto a nivel personal como profesional. Con una interfaz intuitiva y fácil de usar, permite a los usuarios crear, organizar y hacer seguimiento de sus tareas de manera eficiente. La aplicación ofrece funciones como la asignación de prioridades.</p>
                  </div>
                 
                </div>
                
              </div>
            </div>


            <!-- Hours Chart -->
            <div class="col-span-4 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Hours Overview</h2>
              </div>
              <div class="p-6">
                <VueApexCharts type="donut" height="300" :options="chartOptions" :series="series" />
              </div>
            </div>
          </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-80 bg-gray-950 border-l border-gray-700">
          <div class="p-6 space-y-6">
            <div>
              <h3 class="text-sm font-medium text-gray-400 mb-4">Project Leader</h3>
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                  {{ project.leader?.name.charAt(0) }}
                </div>
                <span class="text-white">{{ project.leader?.name }}</span>
              </div>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-400 mb-4">Team Members</h3>
              <div class="space-y-3">
                <div v-for="employee in employees" :key="employee.id"
                  class="flex items-center justify-between p-2 hover:bg-gray-900 rounded-lg transition-colors">
                  <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-white">
                      {{ employee.name.charAt(0) }}
                    </div>
                    <span class="text-white">{{ employee.name }}</span>
                  </div>
                  <span class="text-gray-400 text-sm">{{ employee.role }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>