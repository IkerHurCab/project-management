<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { router } from '@inertiajs/vue3'
    import Layout from '@/Layouts/Layout.vue'
    import StandardButton from '@/Components/StandardButton.vue'
    import StatusBadge from '@/Components/StatusBadge.vue'
    import VueApexCharts from 'vue3-apexcharts'
    import CreateProject from '@/Pages/ProjectManagement/Project/CreateProject.vue'
    import 'boxicons'
    
    // Dummy data for the dashboard
    const user = ref({
      id: 1,
      name: 'John Doe',
      role: 'Department Head',
      department: 'Engineering'
    })
    
    const projects = ref([
      {
        id: 1,
        name: 'Website Redesign',
        description: 'Complete overhaul of the company website with new design and features',
        status: 'in_progress',
        priority: 3,
        start_date: '2025-01-15',
        end_date: '2025-04-30',
        total_tasks: 24,
        completed_tasks: 10,
        team_members: 5,
        progress: 42
      },
      {
        id: 2,
        name: 'Mobile App Development',
        description: 'Develop a new mobile application for iOS and Android platforms',
        status: 'to_do',
        priority: 2,
        start_date: '2025-03-01',
        end_date: '2025-07-15',
        total_tasks: 32,
        completed_tasks: 0,
        team_members: 7,
        progress: 0
      },
      {
        id: 3,
        name: 'Database Migration',
        description: 'Migrate existing database to a new cloud-based solution',
        status: 'review',
        priority: 4,
        start_date: '2025-01-05',
        end_date: '2025-02-28',
        total_tasks: 18,
        completed_tasks: 16,
        team_members: 4,
        progress: 89
      },
      {
        id: 4,
        name: 'Security Audit',
        description: 'Comprehensive security audit of all systems and infrastructure',
        status: 'done',
        priority: 3,
        start_date: '2025-01-10',
        end_date: '2025-02-10',
        total_tasks: 15,
        completed_tasks: 15,
        team_members: 3,
        progress: 100
      }
    ])
    
    const tasks = ref([
      {
        id: 1,
        name: 'Design Homepage Mockup',
        description: 'Create mockup designs for the new homepage',
        status: 'in_progress',
        priority: 2,
        project_id: 1,
        project_name: 'Website Redesign',
        assignee: 'Alice Johnson',
        start_date: '2025-01-20',
        end_date: '2025-02-05',
        estimated_hours: 20,
        completed_hours: 12
      },
      {
        id: 2,
        name: 'Setup Database Schema',
        description: 'Design and implement the new database schema',
        status: 'review',
        priority: 3,
        project_id: 3,
        project_name: 'Database Migration',
        assignee: 'Bob Smith',
        start_date: '2025-01-10',
        end_date: '2025-01-25',
        estimated_hours: 30,
        completed_hours: 28
      },
      {
        id: 3,
        name: 'API Integration',
        description: 'Integrate third-party APIs for payment processing',
        status: 'to_do',
        priority: 4,
        project_id: 2,
        project_name: 'Mobile App Development',
        assignee: 'Charlie Davis',
        start_date: '2025-03-10',
        end_date: '2025-03-30',
        estimated_hours: 40,
        completed_hours: 0
      },
      {
        id: 4,
        name: 'Penetration Testing',
        description: 'Conduct penetration testing on all systems',
        status: 'done',
        priority: 3,
        project_id: 4,
        project_name: 'Security Audit',
        assignee: 'Diana Wilson',
        start_date: '2025-01-25',
        end_date: '2025-02-05',
        estimated_hours: 25,
        completed_hours: 25
      },
      {
        id: 5,
        name: 'User Authentication',
        description: 'Implement secure user authentication system',
        status: 'in_progress',
        priority: 3,
        project_id: 2,
        project_name: 'Mobile App Development',
        assignee: 'Eve Martin',
        start_date: '2025-03-05',
        end_date: '2025-03-20',
        estimated_hours: 35,
        completed_hours: 15
      }
    ])
    
    const teamMembers = ref([
      { id: 1, name: 'Alice Johnson', role: 'Senior Developer', tasks_assigned: 4, tasks_completed: 2 },
      { id: 2, name: 'Bob Smith', role: 'Database Administrator', tasks_assigned: 3, tasks_completed: 2 },
      { id: 3, name: 'Charlie Davis', role: 'Frontend Developer', tasks_assigned: 5, tasks_completed: 1 },
      { id: 4, name: 'Diana Wilson', role: 'Security Specialist', tasks_assigned: 3, tasks_completed: 3 },
      { id: 5, name: 'Eve Martin', role: 'Mobile Developer', tasks_assigned: 4, tasks_completed: 2 }
    ])
    
    // Dashboard statistics
    const totalProjects = computed(() => projects.value.length)
    const completedProjects = computed(() => projects.value.filter(p => p.status === 'done').length)
    const inProgressProjects = computed(() => projects.value.filter(p => p.status === 'in_progress').length)
    const totalTasks = computed(() => projects.value.reduce((sum, project) => sum + project.total_tasks, 0))
    const completedTasks = computed(() => projects.value.reduce((sum, project) => sum + project.completed_tasks, 0))
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
        'to_do': projects.value.filter(p => p.status === 'to_do').length,
        'in_progress': projects.value.filter(p => p.status === 'in_progress').length,
        'review': projects.value.filter(p => p.status === 'review').length,
        'done': projects.value.filter(p => p.status === 'done').length
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
        tasks.value.filter(t => t.priority === 1).length,
        tasks.value.filter(t => t.priority === 2).length,
        tasks.value.filter(t => t.priority === 3).length,
        tasks.value.filter(t => t.priority === 4).length
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
        data: projects.value.map(project => ({
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
      return tasks.value
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