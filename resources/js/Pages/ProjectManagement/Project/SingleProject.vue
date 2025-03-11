<script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  import { toast } from 'vue3-toastify';
  
  import Layout from '@/Layouts/Layout.vue';
  import StatusBadge from '@/Components/StatusBadge.vue';
  import StandardButton from '@/Components/StandardButton.vue';
  import VueApexCharts from 'vue3-apexcharts';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  import CreateTaskModal from '@/Pages/ProjectManagement/Task/CreateTaskModal.vue';
  import AddMemberModal from '@/Pages/ProjectManagement/Project/AddMemberModal.vue';
  import ProjectDocumentation from '@/Pages/ProjectManagement/Project/ProjectDocumentation.vue';
  import EditProjectModal from '@/Pages/ProjectManagement/Project/EditProjectModal.vue';
  import DeleteMemberModal from '@/Pages/ProjectManagement/Project/DeleteMemberModal.vue';
  import TaskList from '@/Pages/ProjectManagement/Task/TaskList.vue';
  
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
    documentations: {
      type: Array,
    },
    activeTab: {
      type: String,
    },
    openSingleDoc: {
      type: Boolean
    },
    isProjectLeader: {
      type: Boolean
    },
    isUserInProject: {
      type: Boolean
    },
    departmentHead: {
      type: Array
    },
    userDepartments: {
      type: Array
    },
  
  });
  
  
  const isDarkMode = ref(false);
  
  function checkDarkMode() {
    isDarkMode.value = document.documentElement.classList.contains('dark');
    return isDarkMode.value;
  }
  
  
  
  
  const taskCount = computed(() => props.tasks.length);
  
  
  const formatDate = (date) => {
    if (!date) return 'No end date'; // Retorna vacío si no hay fecha
  
    const parsedDate = new Date(date);
    if (isNaN(parsedDate)) return 'No end date'; // Verifica si la fecha es válida
  
    return parsedDate.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  };
  
  
  const projectProgress = computed(() => {
    if (props.project.total_tasks === 0) return 0
    return Math.round((props.project.completed_tasks / props.project.total_tasks) * 100)
  })
  
  
  const activeTab = ref(props.activeTab || 'overview');
  const memberToDelete = ref(null);
  
  
  
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
  const isEditProjectModalOpen = ref(false);
  const isDeleteMemberModalOpen = ref(false);
  const isModalOpen = ref(false);
  const allMembers = ref([]);
  
  const openDeleteMemberModal = (member) => {
    memberToDelete.value = member;
    isDeleteMemberModalOpen.value = true;
  };
  
  
  const closeDeleteMemberModal = () => {
    isDeleteMemberModalOpen.value = false;
  }
  
  
  const openEditProjectModal = () => {
    isEditProjectModalOpen.value = true;
  }
  const closeEditProjectModal = () => {
    isEditProjectModalOpen.value = false;
  
  }
  
  const openCreateTaskModal = () => {
    isCreateTaskModalOpen.value = true;
  };
  
  const closeCreateTaskModal = () => {
    isCreateTaskModalOpen.value = false;
  };
  
  const openModal = () => {
    isModalOpen.value = true;
  };
  const closeModal = () => {
    isModalOpen.value = false;
  };
  
  
  const handleAddMembers = (newEmployees) => {
    router.post(`/projects/${props.project.id}/new-members`, { users: newEmployees }, {
      onSuccess: () => {
        toast.success("Members successfully added to the project!");
      },
      onError: (errors) => {
        toast.error("An error occurred while adding members to the project.");
      },
    });
  };
  
  const chartData = computed(() => {
    const statusCounts = {
      'to_do': 0,
      'in_progress': 0,
      'review': 0,
      'done': 0
    };
  
    props.tasks.forEach(task => {
      if (statusCounts.hasOwnProperty(task.status)) {
        statusCounts[task.status]++;
      }
    });
  
    return Object.entries(statusCounts).map(([status, count]) => ({
      status,
      count
    }));
  });
  const statusColors = {
    'to_do': '#155DFC',
    'in_progress': '#D08700',
    'review': '#9810FA',
    'done': '#00A63E'
  };
  
  const chartOptions = computed(() => ({
    chart: {
      type: 'donut',
      background: 'transparent'
    },
    colors: Object.values(statusColors),
    labels: Object.keys(statusColors),
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '22px',
              fontFamily: 'Helvetica, Arial, sans-serif',
              fontWeight: 600,
              color: checkDarkMode() ? '#000000' : '#fff',
              offsetY: -10
            },
            value: {
              show: true,
              fontSize: '16px',
              fontFamily: 'Helvetica, Arial, sans-serif',
              fontWeight: 400,
              color: checkDarkMode() ? '#000000' : '#fff',
              offsetY: 16
            },
            total: {
              show: true,
              showAlways: false,
              label: 'Total Tasks',
              fontSize: '22px',
              fontFamily: 'Helvetica, Arial, sans-serif',
              fontWeight: 600,
              color: checkDarkMode() ? '#000000' : '#fff'
            }
          }
        }
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: true,
      position: 'bottom',
      horizontalAlign: 'center',
      floating: false,
      fontSize: '14px',
      fontFamily: 'Helvetica, Arial, sans-serif',
      fontWeight: 400,
      labels: {
        colors: checkDarkMode() ? '#000000' : '#fff'
      },
      markers: {
        width: 12,
        height: 12,
        strokeWidth: 0,
        strokeColor: checkDarkMode() ? '#000000' : '#fff',
        fillColors: Object.values(statusColors)
      },
      itemMargin: {
        horizontal: 5,
        vertical: 5
      }
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }],
    tooltip: {
      enabled: true,
      theme: 'dark',
      y: {
        formatter: function (value) {
          return value + " tasks";
        }
      }
    }
  }));
  
  const series = computed(() => chartData.value.map(item => item.count));
  
  
  
  </script>
  
  <template>
    <Layout pageTitle="Project Management">
      <div class="min-h-screen bg-black dark:bg-gray-100 text-gray-300 dark:text-gray-700 ">
        <div class="border-b border-gray-800 px-6 py-4 dark:border-gray-400">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
              <a @click="$inertia.visit('/projects')"
                class="hover:text-white dark:hover:text-black cursor-pointer flex items-center space-x-2 transition-colors">
                <box-icon name='arrow-back' :color="checkDarkMode() ? '#000000' : '#E5E7EB'"></box-icon>
                <span>Back to Projects</span>
              </a>
              <h1 class="text-xl font-semibold text-white dark:text-black">{{ project.name }}</h1>
            </div>
            <div class="flex items-center space-x-4">
              <StandardButton v-if="isProjectLeader && isUserInProject" @click="openCreateTaskModal">+ Create Task
              </StandardButton>
            </div>
          </div>
        </div>
  
        <div class="bg-gray-950 dark:bg-white dark:bg-none dark:border-gray-400 px-6 py-2 border-b border-gray-700">
          <div class="flex space-x-6">
            <button v-for="tab in ['Overview', 'Tasks', 'Documentation']" :key="tab"
              v-show="tab !== 'Tasks' || isUserInProject" @click="activeTab = tab.toLowerCase()" :class="[
                'px-4 py-2 text-sm cursor-pointer font-medium rounded-md transition-colors',
                activeTab === tab.toLowerCase()
                  ? 'bg-blue-500 text-white'
                  : 'text-gray-400 hover:text-white dark:text-gray-700 dark:hover:text-black'
              ]">
              {{ tab }}
            </button>
  
          </div>
        </div>
  
        <!-- Main Content -->
        <div class="flex min-h-[80vh]">
          <!-- Left Panel -->
          <div class="flex-1 p-6 overflow-auto">
            <div v-if="activeTab === 'tasks' && isUserInProject" class="grid grid-cols-4 gap-6 ">
              <!-- Task Columns -->
              <TaskList ref="taskContainer" :projectId="project.id" :tasksByStatus="tasksByStatus" />
            </div>
  
            <div v-if="activeTab === 'overview'" class="grid grid-cols-3  gap-6">
              <!-- Project Details -->
              <div
                class="col-span-2 bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl rounded-lg overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                  <h2 class="text-2xl font-semibold text-white dark:text-black">Project Details</h2>
                  <StandardButton v-if="isProjectLeader && isUserInProject" @click="openEditProjectModal" size="sm"
                    class="">Edit Project</StandardButton>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-900 dark:bg-gray-100 dark:shadow-xl rounded-lg p-4 flex flex-col justify-between">
                      <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Status</span>
                        <StatusBadge :status="project.status" class="mt-2  w-fit" />
                      </div>
                      <div class="mt-4">
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Priority</span>
                        <div class="flex items-center mt-1">
                          <div :class="`w-3 h-3 rounded-full ${getPriorityColor(project.priority)} mr-2`"></div>
                          <span class="text-lg text-white dark:text-black font-light">{{
                            getPriorityLabel(project.priority) }}</span>
                        </div>
                      </div>
                    </div>
                    <div
                      class="bg-gray-900 dark:bg-gray-100 dark:border-none dark:shadow-xl rounded-lg p-4 flex flex-col justify-between">
                      <div>
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Start Date</span>
                        <div class="flex items-center mt-1">
                          <box-icon name='calendar' :color="checkDarkMode() ? '#000000' : '#9CA3AF'"
                            class="mr-2"></box-icon>
                          <span class="text-lg text-white font-light dark:text-black">{{ formatDate(project.start_date)
                            }}</span>
                        </div>
                      </div>
                      <div class="mt-4">
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">End Date</span>
                        <div class="flex items-center mt-1">
                          <box-icon name='calendar-check' :color="checkDarkMode() ? '#000000' : '#9CA3AF'"
                            class="mr-2"></box-icon>
                          <span class="text-lg text-white font-light dark:text-black">{{ formatDate(project.end_date)
                            }}</span>
                        </div>
                      </div>
                    </div>
  
                    <div
                      class="bg-gray-900 dark:bg-gray-100 dark:border-none dark:shadow-xl rounded-lg p-4 col-span-2 flex justify-between">
                      <div>
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Total Tasks</span>
                        <div class="flex items-center mt-1">
                          <box-icon name='task' :color="checkDarkMode() ? '#000000' : '#9CA3AF'" class="mr-2"></box-icon>
                          <span class="text-lg text-white dark:text-black font-light">{{ totalTasks }}</span>
                        </div>
                      </div>
                      <div>
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Completed Tasks</span>
                        <div class="flex items-center mt-1">
                          <box-icon name='check-circle' :color="checkDarkMode() ? '#000000' : '#9CA3AF'"
                            class="mr-2"></box-icon>
                          <span class="text-lg text-white dark:text-black font-light">{{ tasksCompleted }}</span>
                        </div>
                      </div>
                      <div>
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-700">Team Members</span>
                        <div class="flex items-center mt-1">
                          <box-icon name='group' :color="checkDarkMode() ? '#000000' : '#9CA3AF'" class="mr-2"></box-icon>
                          <span class="text-lg text-white dark:text-black  font-light">{{ employees.length }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="mt-6 bg-gray-900 dark:bg-gray-100 dark:shadow-xl rounded-lg p-4">
                    <span class="text-sm font-medium text-gray-400 dark:text-gray-700 ">Description</span>
                    <p class="text-white dark:text-black text-lg leading-relaxed mt-2 break-words">
                      {{ project.description }}
                    </p>
                  </div>
                </div>
              </div>
  
              <!-- Hours Chart -->
              <div
                class="col-span-1 bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl rounded-lg overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4">
                  <h2 class="text-xl font-bold text-white dark:text-black">Tasks Overview</h2>
                </div>
                <div class="p-6">
                  <VueApexCharts type="donut" height="350" :options="chartOptions" :series="series" />
                </div>
              </div>
  
  
              <div v-if="isUserInProject"
                class="col-span-3 bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl rounded-lg overflow-hidden border border-gray-700 ">
                <div class="border-b border-gray-700 px-6 py-4">
                  <h2 class="text-2xl font-semibold text-white dark:text-black">Your Tasks</h2>
                </div>
                <div class="">
                  <div class="bg-gray-900 dark:bg-gray-200 overflow-hidden">
                    <table class="w-full">
                      <thead>
                        <tr class="bg-gray-800 dark:bg-gray-200 text-left">
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600">Task Name</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600">Description</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600">Priority</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600 w-30">Start Date</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600 w-30">End Date</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600">Completed Hours</th>
                          <th class="p-4 font-semibold text-gray-400 dark:text-gray-600 w-30">Status</th>
                        </tr>
                      </thead>
                      <tbody>
  
                        <!-- Si no hay tareas pendientes -->
                        <tr v-if="tasks.length === 0" class="bg-gray-950 dark:bg-white">
                          <td colspan="7" class="p-4 text-center text-gray-400">There are no pending tasks</td>
                        </tr>
  
  
                        <tr v-for="(task, index) in tasks" :key="task.id"
                          @click="router.get(`/projects/${project.id}/task/${task.id}`)" :class="{
                            'bg-gray-950 hover:bg-gray-900 dark:bg-white dark:hover:bg-gray-100 transition duration-300 cursor-pointer': true,
                            'border-b border-gray-700': index !== tasks.length - 1
                          }">
                          <td class="p-4 text-white dark:text-black">{{ task.name }}</td>
                          <td class="p-4 text-gray-400 dark:text-gray-500">{{ task.description }}</td>
  
                          <!-- Aquí envuelves el contenido en un <td> -->
                          <td class="p-4">
                            <div class="flex justify-center items-center">
                              <div :class="`w-3 h-3 flex flex-row rounded-full ${getPriorityColor(task.priority)} mr-2`">
                              </div>
                              <span class="text-lg text-white dark:text-black font-light">{{ getPriorityLabel(task.priority) }}</span>
                            </div>
                          </td>
  
                          <td class="p-4 text-gray-400 dark:text-gray-500">{{ task.start_date }}</td>
                          <td class="p-4 text-gray-400 dark:text-gray-500">{{ task.end_date }}</td>
                          <td class="p-4 text-gray-400 dark:text-gray-500">{{ task.completed_hours || 0 }} h</td>
                          <td class="p-4 text-gray-400 dark:text-gray-500">
                            <StatusBadge :status="task.status || {}" />
                          </td>
  
                        </tr>
  
  
  
  
  
                      </tbody>
                    </table>
                  </div>
                </div>
  
  
              </div>
            </div>
  
            <div v-if="activeTab === 'documentation'"
              class="flex justify-center w-full flex-col rounded-lg overflow-auto">
              <ProjectDocumentation :isUserInProject="isUserInProject" :project="project" :createDoc="createDoc"
                :openSingleDoc="openSingleDoc" :documentations="documentations" />
            </div>
          </div>
  
          <!-- Right Sidebar -->
          <div class="w-[20%] bg-gray-950 dark:bg-white dark:border-gray-500 right-0 border-l border-gray-700 overflow-y-auto">
            <div class="p-6 space-y-8">
              <div>
                <h3 class="text-lg font-semibold text-white dark:text-black mb-4">Project Progress</h3>
                <div class="bg-gray-700 dark:bg-gray-300 h-2 rounded-full">
                  <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${progressPercentage}%` }"></div>
                </div>
                <div class="mt-2 text-sm text-gray-400 dark:text-gray-600">
                  {{ completedTasks }} of {{ totalTasks }} tasks completed
                </div>
              </div>
  
              <div>
                <h3 class="text-lg font-semibold text-white dark:text-black mb-4">Project Leader</h3>
                <div class="flex items-center space-x-3">
                  <div
                    class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                    {{ project.leader?.name.charAt(0) }}
                  </div>
                  <div>
                    <span class="text-white block dark:text-black">{{ project.leader?.name }}</span>
                    <span class="text-sm text-gray-400 dark:text-gray-600">Project Manager</span>
                  </div>
                </div>
              </div>
  
              <div>
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-white dark:text-black mb-4">Team Members</h3>
                  <StandardButton v-if="isProjectLeader && isUserInProject" @click="openModal"
                    class="h-7 w-7 mb-4 flex items-center justify-center">
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
                      class="flex cursor-pointer items-center justify-between p-2 hover:bg-gray-700 dark:hover:bg-gray-100 rounded-lg transition-colors group">
  
                      <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white ">
                          {{ employee.name.charAt(0) }}
                        </div>
                        <span class="text-white dark:text-black">{{ employee.name }}</span>
                      </div>
  
                      <div class="flex items-center space-x-3">
  
  
                        <button @click="openDeleteMemberModal(employee)"
                          class=" text-red-500 cursor-pointer opacity-0 group-hover:opacity-100 mt-1 transition-opacity duration-300">
                          <box-icon name='x' color='#ff0000' class="hover:text-red-700"></box-icon>
                        </button>
                      </div>
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
      <EditProjectModal :is-open="isEditProjectModalOpen" :userDepartments="userDepartments" :project="project"
        :departmentHead="departmentHead" @close="closeEditProjectModal" />
      <DeleteMemberModal :is-open="isDeleteMemberModalOpen" :memberToDelete="memberToDelete" :project="project"
        @close="closeDeleteMemberModal" />
  
    </Layout>
  </template>
  
  
