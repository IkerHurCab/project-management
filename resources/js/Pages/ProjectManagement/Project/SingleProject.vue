<script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';

  import Layout from '@/Layouts/Layout.vue';
  import StatusBadge from '@/Components/StatusBadge.vue';
  import StandardButton from '@/Components/StandardButton.vue';
  import VueApexCharts from 'vue3-apexcharts';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  import CreateTaskModal from '@/Pages/ProjectManagement/Task/CreateTaskModal.vue';
  import AddMemberModal from '@/Pages/ProjectManagement/Project/AddMemberModal.vue';



  import TaskList from '@/Pages/ProjectManagement/Task/TaskList.vue';
  import 'boxicons';

  const props = defineProps({
    tasksCompleted: {
      type: Number,
    },
    project: {
      type: Object,
      required: true
    },
    user: {
      type: Object,
      required: true
    },
    employees: {
      type: Array,
      required: true
    },
    allUsers: {
      type: Array,
      required: true
    },
    tasks: {
      type: Array,
      required: true
    },
    searchQuery: {
      type: String,
    },
    personalTasks: {
      type: Array,
    },

  });
 
  const taskCount = computed(() => props.tasks.length);

  console.log(props.tasks)

  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  }

  const projectProgress = computed(() => {
    if (props.project.total_tasks === 0) return 0
    return Math.round((props.project.completed_tasks / props.project.total_tasks) * 100)
  })

 
  const activeTab = ref('overview');
  const chartOptions = computed(() => ({
    chart: {
      type: 'donut',
      background: 'transparent'
    },
    colors: ['#6366F1', '#22D3EE', '#F59E0B', '#EF4444'],
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
        to_do: [],
        in_progress: [],
        review: [],
        done: []
      };
    }
    return {
      to_do: props.tasks.filter(task => task.status === 'to_do'),
      in_progress: props.tasks.filter(task => task.status === 'in_progress'),
      review: props.tasks.filter(task => task.status === 'review'),
      done: props.tasks.filter(task => task.status === 'done')
    };
  });



  const totalTasks = computed(() => props.tasks.length);
  const completedTasks = computed(() => props.tasks.filter(task => task.status === 'done').length);
  const progressPercentage = computed(() => (completedTasks.value / totalTasks.value) * 100 || 0);

  const priorityTasks = computed(() =>
    props.tasks
      .filter(task => task.priority === 3)
      .sort((a, b) => new Date(a.end_date) - new Date(b.end_date))
      .slice(0, 3)
  );
    


  const leader = computed(() => {
    return props.project.users.find(user => user.id === props.project.project_leader_id)
  })
  const searchQuery = ref(usePage().props.searchQuery || '');



  watch(searchQuery, () => {
    applyFilters();
  });

  const getPriorityColor = (priority) => {
    const colors = ['bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500']
    return colors[priority - 1] || 'bg-gray-500'
  }

  const getPriorityLabel = (priority) => {
    const labels = ['Low', 'Medium', 'High', 'Urgent']
    return labels[priority - 1] || 'Unknown'
  }

  const applyFilters = () => {
    const queryParams = new URLSearchParams();

    if (searchQuery.value) {
      queryParams.append('searchMember', searchQuery.value);
    }

    console.log(props.filteredEmployees);

    const url = queryParams.toString()
      ? `${props.project.id}/?${queryParams.toString()}`
      : `${props.project.id}`;

    router.visit(url, {
      method: 'get',
      preserveState: true,
      replace: true,
    });
  };


  const isCreateTaskModalOpen = ref(false);
  const isModalOpen = ref(false);
  const allMembers = ref([]);

  const openCreateTaskModal = () => {
    isCreateTaskModalOpen.value = true;
  };

  const closeCreateTaskModal = () => {
    isCreateTaskModalOpen.value = false;
  };

  const openModal = () => {
    isModalOpen.value = true;
  }
  const closeModal = () => {
    isModalOpen.value = false;
  }


  const handleAddMembers = (newEmployees) => {
    router.post(`/projects/${props.project.id}/new-members`, { users: newEmployees });
  };

</script>

<template>
  <Layout pageTitle="Project Management">
    <div class="min-h-screen bg-black text-gray-300">
      <div class="border-b border-gray-800 px-6 py-4">
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
            <StandardButton @click="openCreateTaskModal">+ Create Task</StandardButton>
          </div>
        </div>
      </div>

      <div class="bg-gray-950 px-6 py-2 border-b border-gray-700">
        <div class="flex space-x-6">
          <button v-for="tab in ['Overview', 'Tasks', 'Documentation', 'Analytics', 'Files']" :key="tab"
            @click="activeTab = tab.toLowerCase()" :class="[
              'px-4 py-2 text-sm cursor-pointer font-medium rounded-md transition-colors',
              activeTab === tab.toLowerCase()
                ? 'bg-blue-500 text-white'
                : 'text-gray-400 hover:text-white'
            ]">
            {{ tab }}
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex min-h-[80vh]">
        <!-- Left Panel -->
        <div class="flex-1 p-6 overflow-auto">
          <div v-if="activeTab === 'tasks'" class="grid grid-cols-4 gap-6 ">
            <!-- Task Columns -->
            <TaskList ref="taskContainer" :projectId="project.id" :tasksByStatus="tasksByStatus" />
          </div>

          <div v-if="activeTab === 'overview'" class="grid grid-cols-3  gap-6">
            <!-- Project Details -->
            <div class="col-span-2 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white">Project Details</h2>
                <StandardButton @click="openEditProjectModal" size="sm" class="">Edit Project</StandardButton>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-gray-900 rounded-lg p-4 flex flex-col justify-between">
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-gray-400">Status</span>
                      <StatusBadge :status="project.status" class="mt-2  w-fit" />
                    </div>
                    <div class="mt-4">
                      <span class="text-sm font-medium text-gray-400">Priority</span>
                      <div class="flex items-center mt-1">
                        <div :class="`w-3 h-3 rounded-full ${getPriorityColor(project.priority)} mr-2`"></div>
                        <span class="text-lg text-white font-light">{{ getPriorityLabel(project.priority) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-900 rounded-lg p-4 flex flex-col justify-between">
                    <div>
                      <span class="text-sm font-medium text-gray-400">Start Date</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='calendar' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ formatDate(project.start_date) }}</span>
                      </div>
                    </div>
                    <div class="mt-4">
                      <span class="text-sm font-medium text-gray-400">End Date</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='calendar-check' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ formatDate(project.end_date) }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-gray-900 rounded-lg p-4 col-span-2 flex justify-between">
                    <div>
                      <span class="text-sm font-medium text-gray-400">Total Tasks</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='task' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ totalTasks }}</span>
                      </div>
                    </div>
                    <div>
                      <span class="text-sm font-medium text-gray-400">Completed Tasks</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='check-circle' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ tasksCompleted }}</span>
                      </div>
                    </div>
                    <div>
                      <span class="text-sm font-medium text-gray-400">Team Members</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='group' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ employees.length }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-6 bg-gray-900 rounded-lg p-4">
                  <span class="text-sm font-medium text-gray-400">Description</span>
                  <p class="text-white text-lg leading-relaxed mt-2 break-words">
                    {{ project.description }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Hours Chart -->
            <div class="col-span-1 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Hours Overview</h2>
              </div>
              <div class="p-6">
                <VueApexCharts type="donut" height="300" :options="chartOptions" :series="series" />
              </div>
            </div>


            <div class="col-span-3 bg-gray-950 rounded-lg overflow-hidden border border-gray-700 ">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-2xl font-semibold text-white ">Your Tasks</h2>
              </div>
              <div class="">
                <div class="bg-gray-900  overflow-hidden">
                  <table class="w-full">
                    <thead>
                      <tr class="bg-gray-800 text-left">
                        <th class="p-4 font-semibold text-gray-400">Task Name</th>
                        <th class="p-4 font-semibold text-gray-400">Priority</th>
                        <th class="p-4 font-semibold text-gray-400">Start Date</th>
                        <th class="p-4 font-semibold text-gray-400">End Date</th>
                        <th class="p-4 font-semibold text-gray-400">Completed Hours</th>
                        <th class="p-4 font-semibold text-gray-400">Status</th>
                      </tr>
                    </thead>
                    <tbody>

                      <!-- Si no hay tareas pendientes -->
                      <tr v-if="personalTasks.length === 0" class="border-b border-gray-900 bg-gray-950">
                        <td colspan="7" class="p-4 text-center text-gray-400">There are no pending tasks</td>
                      </tr>


                      <tr v-for="task in personalTasks" :key="task.id"
                        @click="router.get(`/projects/${project.id}/task/${task.id}`)"
                        class="bg-gray-950 border-b border-gray-700 text-left cursor-pointer hover:bg-gray-900">
                        <td class="p-4 text-gray-400">{{ task.name }}</td>
                        <td class="p-4 text-gray-400">{{ task.description }}</td>
                        <td class="p-4 text-gray-400">{{ task.start_date }}</td>
                        <td class="p-4 text-gray-400">{{ task.end_date }}</td>
                        <td class="p-4  text-gray-400">{{ task.completed_hours || 0 }} h</td>
                        <td class="p-4 text-gray-400">
                          <StatusBadge :status="task.status" />
                        </td>
                      </tr>




                    </tbody>
                  </table>
                </div>
              </div>


            </div>
          </div>

          <div v-if="activeTab === 'documentation'">
            <ProjectDocumentation :project /> 
          </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-96 bg-gray-950  right-0 border-l border-gray-700 overflow-y-auto">
          <div class="p-6 space-y-8">
            <div>
              <h3 class="text-lg font-semibold text-white mb-4">Project Progress</h3>
              <div class="bg-gray-700 h-2 rounded-full">
                <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${progressPercentage}%` }"></div>
              </div>
              <div class="mt-2 text-sm text-gray-400">
                {{ completedTasks }} of {{ totalTasks }} tasks completed
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold text-white mb-4">Project Leader</h3>
              <div class="flex items-center space-x-3">
                <div
                  class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                  {{ project.leader?.name.charAt(0) }}
                </div>
                <div>
                  <span class="text-white block">{{ project.leader?.name }}</span>
                  <span class="text-sm text-gray-400">Project Manager</span>
                </div>
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white mb-4">Team Members</h3>
                <StandardButton @click="openModal" class="h-7 w-7 mb-4 flex items-center justify-center">
                  +
                </StandardButton>
              </div>

              <div class="space-y-3">
                <div>
                  <InputWithIcon icon="search" v-model="searchQuery" placeholder="Search members..." class="h-10 w-full"
                    type="text" />
                </div>
                <div class="overflow-y-auto max-h-70 scrollbar">
                  <div v-for="employee in employees" :key="employee.id"
                    class="flex cursor-pointer items-center justify-between p-2 hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                      <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white">
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
    </div>
    <AddMemberModal :is-open="isModalOpen" :all-members="allUsers" @close="closeModal"
      @add-members="handleAddMembers" />
    <CreateTaskModal :is-open="isCreateTaskModalOpen" :project-id="project.id" :employees="employees"
      @close="closeCreateTaskModal" />

  </Layout>
</template>