<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { router } from '@inertiajs/vue3'
    import Layout from '@/Layouts/Layout.vue'
    import StandardButton from '@/Components/StandardButton.vue'
    import StatusBadge from '@/Components/StatusBadge.vue'
    import VueApexCharts from 'vue3-apexcharts'
    import CreateProject from '@/Pages/ProjectManagement/Project/CreateProject.vue'
    import 'boxicons'
    
    const props = defineProps({
      projects: Array,
      tasks: Array,
      user: Object,
    })

    const user = ref({
      id: 1,
      name: 'John Doe',
      role: 'Department Head',
      department: 'Engineering'
    })
    
   
    
    const teamMembers = ref([
      { id: 1, name: 'Alice Johnson', role: 'Senior Developer', tasks_assigned: 4, tasks_completed: 2 },
      { id: 2, name: 'Bob Smith', role: 'Database Administrator', tasks_assigned: 3, tasks_completed: 2 },
      { id: 3, name: 'Charlie Davis', role: 'Frontend Developer', tasks_assigned: 5, tasks_completed: 1 },
      { id: 4, name: 'Diana Wilson', role: 'Security Specialist', tasks_assigned: 3, tasks_completed: 3 },
      { id: 5, name: 'Eve Martin', role: 'Mobile Developer', tasks_assigned: 4, tasks_completed: 2 },
    ]);
    
    // Dashboard statistics
    const totalProjects = computed(() => props.projects.length)
    const completedProjects = computed(() => props.projects.filter(p => p.status === 'done').length)
    const inProgressProjects = computed(() => props.projects.filter(p => p.status === 'in_progress').length)
    const totalTasks = computed(() => props.projects.reduce((sum, project) => sum + project.tasks.length, 0))
    const completedTasks = computed(() => {
  return props.projects.reduce((sum, project) => {
    // Filtrar las tareas que tienen el status 'done' y contar cuántas son
    return sum + project.tasks.filter(task => task.status === 'done').length;
  }, 0);
});

    const overallProgress = computed(() => Math.round((completedTasks.value / totalTasks.value) * 100) || 0)
    
    // Project status chart
    const projectStatusChartOptions = {
      chart: {
        type: 'donut',
        background: 'transparent',
        foreColor: '#E5E7EB'
      },
      colors: ['#155DFC', '#D08700', '#9810FA', '#00A63E'],
      labels: ['To Do', 'In Progress', 'Review', 'Done'],
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: '16px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 600,
                color: '#fff',
                offsetY: -10
              },
              value: {
                show: true,
                fontSize: '14px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 400,
                color: '#fff',
                offsetY: 16
              },
              total: {
                show: true,
                showAlways: false,
                label: 'Total',
                fontSize: '16px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 600,
                color: '#fff'
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
          colors: '#fff'
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
      }]
    }
    
    const projectStatusSeries = computed(() => {
      const statusCounts = {
        'to_do': props.projects.filter(p => p.status === 'to_do').length,
        'in_progress': props.projects.filter(p => p.status === 'in_progress').length,
        'review': props.projects.filter(p => p.status === 'review').length,
        'done': props.projects.filter(p => p.status === 'done').length
      }
      return Object.values(statusCounts)
    })
    
    // Task priority chart
    const taskPriorityChartOptions = {
      chart: {
        type: 'bar',
        background: 'transparent',
        foreColor: '#E5E7EB',
        toolbar: {
          show: false
        }
      },
      colors: ['#4ADE80', '#FACC15', '#FB923C', '#EF4444'],
      plotOptions: {
    bar: {
      horizontal: true,
      distributed: true,
      dataLabels: {
        position: 'top'
      },
      hover: {
        // Cambiar el color de fondo al hacer hover
        brightness: 0.1,  // Puedes ajustar el brillo para cambiar el color al hacer hover
        color: '#000000'  // Color negro de fondo al hacer hover
      }
    }
  },
      dataLabels: {
        enabled: true,
        offsetX: -6,
        style: {
          fontSize: '12px',
          colors: ['#fff']
        }
      },
      xaxis: {
        categories: ['Low', 'Medium', 'High', 'Urgent'],
        labels: {
          style: {
            colors: '#E5E7EB'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: '#E5E7EB'
          }
        }
      },
      grid: {
        borderColor: '#374151'
      },
      legend: {
        show: false
      }
    }
    
    const taskPrioritySeries = computed(() => {
      const priorityCounts = [
        props.tasks.filter(t => t.priority === 1).length,
        props.tasks.filter(t => t.priority === 2).length,
        props.tasks.filter(t => t.priority === 3).length,
        props.tasks.filter(t => t.priority === 4).length
      ]
      return [{
        name: 'Tasks',
        data: priorityCounts
      }]
    })
    
    // Progress chart
    const progressChartOptions = {
      chart: {
        type: 'radialBar',
        background: 'transparent',
        foreColor: '#E5E7EB'
      },
      plotOptions: {
        radialBar: {
          hollow: {
            size: '70%'
          },
          dataLabels: {
            name: {
              show: false
            },
            value: {
              color: '#E5E7EB',
              fontSize: '30px',
              fontWeight: 600
            }
          },
          track: {
            background: '#374151'
          }
        }
      },
      colors: ['#3B82F6'],
      labels: ['Progress']
    }
    
    // Timeline chart
    const timelineChartOptions = {
      chart: {
        height: 350,
        type: 'rangeBar',
        background: 'transparent',
        foreColor: '#E5E7EB',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          distributed: true,
          dataLabels: {
            hideOverflowingLabels: false
          }
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function(val, opts) {
          return opts.w.globals.labels[opts.dataPointIndex]
        },
        style: {
          colors: ['#f3f4f5', '#fff']
        }
      },
      xaxis: {
        type: 'datetime',
        labels: {
          style: {
            colors: '#E5E7EB'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: '#E5E7EB'
          }
        }
      },
      grid: {
        borderColor: '#374151',
        row: {
          colors: ['#1F2937', '#111827'],
          opacity: 0.5
        }
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: 'vertical',
          shadeIntensity: 0.25,
          gradientToColors: undefined,
          inverseColors: true,
          opacityFrom: 1,
          opacityTo: 1,
          stops: [50, 0, 100, 100]
        }
      },
      colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']
    }
    
    const timelineSeries = computed(() => {
      return [{
        data: props.projects.map(project => ({
          x: project.name,
          y: [
            new Date(project.start_date).getTime(),
            new Date(project.end_date).getTime()
          ],
          fillColor: project.status === 'done' ? '#10B981' : 
                    project.status === 'in_progress' ? '#3B82F6' : 
                    project.status === 'review' ? '#F59E0B' : '#EF4444'
        }))
      }]
    })
    
    // Modals
    const isCreateProjectModalOpen = ref(false)
    const openCreateProjectModal = () => isCreateProjectModalOpen.value = true
    const closeCreateProjectModal = () => isCreateProjectModalOpen.value = false
    
    // Utility functions
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
    }
    
    const getPriorityColor = (priority) => {
      const colors = ['bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500']
      return colors[priority - 1] || 'bg-gray-500'
    }
    
    const getPriorityLabel = (priority) => {
      const labels = ['Low', 'Medium', 'High', 'Urgent']
      return labels[priority - 1] || 'Unknown'
    }
    
    const navigateToProject = (projectId) => {
      router.visit(`/projects/${projectId}`)
    }
    
    const navigateToTask = (projectId, taskId) => {
      router.visit(`/projects/${projectId}/task/${taskId}`)
    }
    
    // Get urgent tasks (high priority tasks with upcoming deadlines)
    const urgentTasks = computed(() => {
      return props.tasks
        .filter(task => task.priority >= 3 && task.status !== 'done')
        .sort((a, b) => new Date(a.end_date) - new Date(b.end_date))
        .slice(0, 3)
    })
    
    // Get team performance data
    const teamPerformance = computed(() => {
      return teamMembers.value.map(member => ({
        ...member,
        completion_rate: Math.round((member.tasks_completed / member.tasks_assigned) * 100) || 0
      }))
    })
    </script>
    
    <template>
      <Layout pageTitle="Department Head Dashboard">
        <div class="min-h-screen bg-black text-gray-300">
          
    
          <!-- Main Content -->
          <div class="p-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
              <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-gray-400 text-sm">Total Projects</p>
                    <h3 class="text-3xl font-bold text-white mt-1">{{ totalProjects }}</h3>
                  </div>
                  <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='folder' color='#3B82F6'></box-icon>
                  </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                  <span class="text-green-500 flex items-center">
                    <box-icon name='trending-up' color='#10B981'></box-icon>
                    {{ completedProjects }} completed
                  </span>
                  <span class="mx-2">•</span>
                  <span class="text-blue-500 flex items-center">
                    <box-icon name='time' color='#3B82F6'></box-icon>
                    {{ inProgressProjects }} in progress
                  </span>
                </div>
              </div>
    
              <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-gray-400 text-sm">Total Tasks</p>
                    <h3 class="text-3xl font-bold text-white mt-1">{{ totalTasks }}</h3>
                  </div>
                  <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='task' color='#9810FA'></box-icon>
                  </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                  <span class="text-green-500 flex items-center">
                    <box-icon name='check-circle' color='#10B981'></box-icon>
                    {{ completedTasks }} completed
                  </span>
                  <span class="mx-2">•</span>
                  <span class="text-yellow-500 flex items-center">
                    <box-icon name='loader-circle' color='#F59E0B'></box-icon>
                    {{ totalTasks - completedTasks }} pending
                  </span>
                </div>
              </div>
    
              <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-gray-400 text-sm">Team Members</p>
                    <h3 class="text-3xl font-bold text-white mt-1">{{ teamMembers.length }}</h3>
                  </div>
                  <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='group' color='#10B981'></box-icon>
                  </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                  <span class="text-blue-500 flex items-center">
                    <box-icon name='user-check' color='#3B82F6'></box-icon>
                    {{ teamMembers.length }} active members
                  </span>
                </div>
              </div>
    
              <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-gray-400 text-sm">Overall Progress</p>
                    <h3 class="text-3xl font-bold text-white mt-1">{{ overallProgress }}%</h3>
                  </div>
                  <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='chart' color='#3B82F6'></box-icon>
                  </div>
                </div>
                <div class="mt-4">
                  <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${overallProgress}%` }"></div>
                  </div>
                </div>
              </div>
            </div>
    
            <!-- Charts and Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
              <!-- Project Status Chart -->
              <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4">
                  <h2 class="text-xl font-semibold text-white">Project Status</h2>
                </div>
                <div class="p-6">
                  <VueApexCharts
                    type="donut"
                    height="300"
                    :options="projectStatusChartOptions"
                    :series="projectStatusSeries"
                  />
                </div>
              </div>
    
              <!-- Task Priority Chart -->
              <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4">
                  <h2 class="text-xl font-semibold text-white">Task Priority Distribution</h2>
                </div>
                <div class="p-6">
                  <VueApexCharts
                    type="bar"
                    height="300"
                    :options="taskPriorityChartOptions"
                    :series="taskPrioritySeries"
                  />
                </div>
              </div>
    
              <!-- Urgent Tasks -->
              <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4">
                  <h2 class="text-xl font-semibold text-white">Urgent Tasks</h2>
                </div>
                <div class="p-6">
                  <div v-if="urgentTasks.length > 0" class="space-y-4">
                    <div v-for="task in urgentTasks" :key="task.id" 
                         class="bg-gray-900 p-4 rounded-lg cursor-pointer hover:bg-gray-800 transition-colors"
                         @click="navigateToTask(task.project_id, task.id)">
                      <div class="flex justify-between items-start">
                        <div>
                          <h3 class="text-white font-medium">{{ task.name }}</h3>
                          <p class="text-gray-400 text-sm mt-1">{{ task.project_name }}</p>
                        </div>
                        <StatusBadge :status="task.status" class="ml-2" />
                      </div>
                      <div class="mt-3 flex items-center justify-between">
                        <div class="flex items-center">
                          <div :class="`w-3 h-3 rounded-full ${getPriorityColor(task.priority)} mr-2`"></div>
                          <span class="text-sm text-gray-300">{{ getPriorityLabel(task.priority) }}</span>
                        </div>
                        <div class="text-sm text-gray-400">
                          Due: {{ formatDate(task.end_date) }}
                        </div>
                      </div>
                      <div class="mt-2">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                          <span>Progress</span>
                          <span>{{ Math.round((task.completed_hours / task.estimated_hours) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5">
                          <div class="bg-blue-600 h-1.5 rounded-full" 
                               :style="{ width: `${(task.completed_hours / task.estimated_hours) * 100}%` }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="flex flex-col items-center justify-center h-64">
                    <box-icon name='check-circle' color='#10B981' size="lg"></box-icon>
                    <p class="text-gray-400 mt-4">No urgent tasks at the moment</p>
                  </div>
                </div>
              </div>
            </div>
    
            <!-- Project Timeline -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700 mb-6">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Project Timeline</h2>
              </div>
              <div class="p-6">
                <VueApexCharts
                  type="rangeBar"
                  height="350"
                  :options="timelineChartOptions"
                  :series="timelineSeries"
                />
              </div>
            </div>
    
            <!-- Projects Table -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700 mb-6">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Active Projects</h2>
                <StandardButton @click="router.visit('/projects')" size="sm">View All Projects</StandardButton>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="bg-gray-900 text-left">
                      <th class="p-4 font-semibold text-gray-400">Project Name</th>
                      <th class="p-4 font-semibold text-gray-400">Status</th>
                      <th class="p-4 font-semibold text-gray-400">Priority</th>
                      <th class="p-4 font-semibold text-gray-400">Team Size</th>
                      <th class="p-4 font-semibold text-gray-400">Progress</th>
                      <th class="p-4 font-semibold text-gray-400">Deadline</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="project in projects.filter(p => p.status !== 'done')" :key="project.id"
                        @click="navigateToProject(project.id)"
                        class="border-b border-gray-800 hover:bg-gray-900 cursor-pointer transition-colors">
                      <td class="p-4 text-white font-medium">{{ project.name }}</td>
                      <td class="p-4"><StatusBadge :status="project.status" /></td>
                      <td class="p-4">
                        <div class="flex items-center">
                          <div :class="`w-3 h-3 rounded-full ${getPriorityColor(project.priority)} mr-2`"></div>
                          <span>{{ getPriorityLabel(project.priority) }}</span>
                        </div>
                      </td>
                      <td class="p-4 text-center">{{ project.team_members }}</td>
                      <td class="p-4">
                        <div class="w-full bg-gray-700 rounded-full h-2">
                          <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${project.progress}%` }"></div>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 text-center">{{ project.progress }}%</div>
                      </td>
                      <td class="p-4 text-gray-300">{{ formatDate(project.end_date) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
    
            <!-- Team Performance -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Team Performance</h2>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="bg-gray-900 text-left">
                      <th class="p-4 font-semibold text-gray-400">Team Member</th>
                      <th class="p-4 font-semibold text-gray-400">Role</th>
                      <th class="p-4 font-semibold text-gray-400">Tasks Assigned</th>
                      <th class="p-4 font-semibold text-gray-400">Tasks Completed</th>
                      <th class="p-4 font-semibold text-gray-400">Completion Rate</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="member in teamPerformance" :key="member.id"
                        class="border-b border-gray-800 hover:bg-gray-900 transition-colors">
                      <td class="p-4">
                        <div class="flex items-center space-x-3">
                          <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ member.name.charAt(0) }}
                          </div>
                          <span class="text-white">{{ member.name }}</span>
                        </div>
                      </td>
                      <td class="p-4 text-gray-300">{{ member.role }}</td>
                      <td class="p-4 text-center">{{ member.tasks_assigned }}</td>
                      <td class="p-4 text-center">{{ member.tasks_completed }}</td>
                      <td class="p-4">
                        <div class="w-full bg-gray-700 rounded-full h-2">
                          <div 
                            :class="[
                              'h-2 rounded-full', 
                              member.completion_rate >= 75 ? 'bg-green-500' : 
                              member.completion_rate >= 50 ? 'bg-blue-500' : 
                              member.completion_rate >= 25 ? 'bg-yellow-500' : 'bg-red-500'
                            ]"
                            :style="{ width: `${member.completion_rate}%` }">
                          </div>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 text-center">{{ member.completion_rate }}%</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    
        <!-- Modals -->
      
      </Layout>
    </template>