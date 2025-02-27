<script setup>
  import { ref, watch,  defineProps, computed, onMounted } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Layouts/Layout.vue'
  import StandardButton from '@/Components/StandardButton.vue'
  import StatusBadge from '@/Components/StatusBadge.vue'
  import VueApexCharts from 'vue3-apexcharts'
  import LogTimeModal from '@/Pages/ProjectManagement/Task/LogTimeModal.vue'
  import 'boxicons'

  const props = defineProps({
    user: Object,
    myTasks: Array,
    myProjects: Array,
  });

  const viewMode = ref('all')

  watch(() => viewMode.value, (mode) => {
    console.log('View mode changed:', mode)
  })

  const filteredTasks = computed(() => {
    if (viewMode.value === 'active') {
      return props.MyTasks.filter(task => task.status !== 'done')
    } else {
      return props.MyTasks
    }

    console.log(filteredTasks.value)
  })

  const user = ref({
    id: 1,
    name: 'Alex Johnson',
    role: 'Frontend Developer',
    department: 'Engineering',
    avatar: 'A'
  })

  const myTasks = ref([
    {
      id: 1,
      name: 'Design Homepage Mockup',
      description: 'Create mockup designs for the new homepage',
      status: 'in_progress',
      priority: 2,
      project_id: 1,
      project_name: 'Website Redesign',
      start_date: '2025-01-20',
      end_date: '2025-02-05',
      estimated_hours: 20,
      completed_hours: 12
    },
    {
      id: 2,
      name: 'Implement User Authentication',
      description: 'Develop the authentication system for the application',
      status: 'to_do',
      priority: 3,
      project_id: 1,
      project_name: 'Website Redesign',
      start_date: '2025-02-06',
      end_date: '2025-02-20',
      estimated_hours: 30,
      completed_hours: 0
    },
    {
      id: 3,
      name: 'Create Responsive Components',
      description: 'Build reusable UI components that work on all devices',
      status: 'review',
      priority: 2,
      project_id: 1,
      project_name: 'Website Redesign',
      start_date: '2025-01-15',
      end_date: '2025-01-30',
      estimated_hours: 25,
      completed_hours: 23
    },
    {
      id: 4,
      name: 'Optimize Image Loading',
      description: 'Implement lazy loading and optimize image delivery',
      status: 'done',
      priority: 1,
      project_id: 1,
      project_name: 'Website Redesign',
      start_date: '2025-01-10',
      end_date: '2025-01-18',
      estimated_hours: 15,
      completed_hours: 15
    },
    {
      id: 5,
      name: 'Develop Mobile Navigation',
      description: 'Create a responsive navigation menu for mobile devices',
      status: 'to_do',
      priority: 4,
      project_id: 2,
      project_name: 'Mobile App Development',
      start_date: '2025-03-05',
      end_date: '2025-03-15',
      estimated_hours: 18,
      completed_hours: 0
    }
  ])

  const myProjects = ref([
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
      my_tasks: 4,
      my_completed_tasks: 1,
      progress: 42,
      role: 'Frontend Developer'
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
      my_tasks: 1,
      my_completed_tasks: 0,
      progress: 0,
      role: 'UI Developer'
    }
  ])

  const timeEntries = ref([
    {
      id: 1,
      task_id: 1,
      task_name: 'Design Homepage Mockup',
      project_name: 'Website Redesign',
      date: '2025-01-25',
      hours: 4,
      description: 'Created initial wireframes and design concepts'
    },
    {
      id: 2,
      task_id: 1,
      task_name: 'Design Homepage Mockup',
      project_name: 'Website Redesign',
      date: '2025-01-26',
      hours: 5,
      description: 'Refined designs based on feedback'
    },
    {
      id: 3,
      task_id: 3,
      task_name: 'Create Responsive Components',
      project_name: 'Website Redesign',
      date: '2025-01-20',
      hours: 6,
      description: 'Built button, card, and form components'
    },
    {
      id: 4,
      task_id: 3,
      task_name: 'Create Responsive Components',
      project_name: 'Website Redesign',
      date: '2025-01-21',
      hours: 7,
      description: 'Implemented responsive behavior for all components'
    },
    {
      id: 5,
      task_id: 4,
      task_name: 'Optimize Image Loading',
      project_name: 'Website Redesign',
      date: '2025-01-15',
      hours: 8,
      description: 'Researched and implemented lazy loading techniques'
    }
  ])

  const notifications = ref([
    {
      id: 1,
      type: 'task_assigned',
      message: 'You have been assigned a new task: Develop Mobile Navigation',
      time: '2 hours ago',
      read: false
    },
    {
      id: 2,
      type: 'comment',
      message: 'Sarah commented on your task: Design Homepage Mockup',
      time: '1 day ago',
      read: false
    },
    {
      id: 3,
      type: 'deadline',
      message: 'Task deadline approaching: Create Responsive Components (2 days left)',
      time: '2 days ago',
      read: true
    },
    {
      id: 4,
      type: 'review',
      message: 'Your task Create Responsive Components has been moved to review',
      time: '3 days ago',
      read: true
    }
  ])

  // Dashboard statistics
  const totalAssignedTasks = computed(() => props.myTasks.length)
  const completedTasks = computed(() => props.myTasks.filter(t => t.status === 'done').length)
  const inProgressTasks = computed(() => myTasks.value.filter(t => t.status === 'in_progress').length)
  const toDoTasks = computed(() => myTasks.value.filter(t => t.status === 'to_do').length)
  const reviewTasks = computed(() => myTasks.value.filter(t => t.status === 'review').length)

  const totalEstimatedHours = computed(() =>
    props.myTasks.reduce((sum, task) => sum + Number(task.estimated_hours), 0)
  )
  const totalCompletedHours = computed(() =>
    props.myTasks.reduce((sum, task) => sum + Number(task.completed_hours), 0)
  )

  const completionRate = computed(() =>
    Math.round((completedTasks.value / totalAssignedTasks.value) * 100) || 0
  )

  // Weekly time tracking
  const weeklyTimeData = [
    { day: 'Monday', hours: 7.5 },
    { day: 'Tuesday', hours: 8 },
    { day: 'Wednesday', hours: 6.5 },
    { day: 'Thursday', hours: 8.5 },
    { day: 'Friday', hours: 7 },
    { day: 'Saturday', hours: 2 },
    { day: 'Sunday', hours: 0 }
  ]

  const weeklyTimeChartOptions = {
    chart: {
      type: 'bar',
      background: 'transparent',
      foreColor: '#E5E7EB',
      toolbar: {
        show: false
      }
    },
    colors: ['#3B82F6'],
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '60%'
      }
    },
    dataLabels: {
      enabled: false
    },
    xaxis: {
      categories: weeklyTimeData.map(d => d.day),
      labels: {
        style: {
          colors: '#E5E7EB'
        }
      }
    },
    yaxis: {
      title: {
        text: 'Hours',
        style: {
          color: '#E5E7EB'
        }
      },
      labels: {
        style: {
          colors: '#E5E7EB'
        }
      }
    },
    grid: {
      borderColor: '#374151'
    },
    tooltip: {
      theme: 'dark'
    }
  }

  const weeklyTimeSeries = [{
    name: 'Hours Logged',
    data: weeklyTimeData.map(d => d.hours)
  }]

  // Task status chart
  const taskStatusChartOptions = {
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

  const taskStatusSeries = computed(() => [
    toDoTasks.value,
    inProgressTasks.value,
    reviewTasks.value,
    completedTasks.value
  ])

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
    labels: ['Completion']
  }

  // Modals
  const isLogTimeModalOpen = ref(false)
  const selectedTask = ref(null)

  const openLogTimeModal = (task) => {
    selectedTask.value = task
    isLogTimeModalOpen.value = true
  }

  const closeLogTimeModal = () => {
    isLogTimeModalOpen.value = false
  }

  const logTime = (timeEntry) => {
    console.log('Logging time:', timeEntry)
    // Here you would normally send this to your API
  }

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

  // Get upcoming deadlines (tasks sorted by end date)
  const upcomingDeadlines = computed(() => {
    return myTasks.value
      .filter(task => task.status !== 'done')
      .sort((a, b) => new Date(a.end_date) - new Date(b.end_date))
      .slice(0, 3)
  })

  // Mark notification as read
  const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
      notification.read = true
    }
  }

  // Mark all notifications as read
  const markAllAsRead = () => {
    notifications.value.forEach(notification => {
      notification.read = true
    })
  }

  // Get unread notifications count
  const unreadNotificationsCount = computed(() => {
    return notifications.value.filter(n => !n.read).length
  })
</script>

<template>
  <Layout pageTitle="Employee Dashboard">
    <div class="min-h-screen bg-black text-gray-300">


      <!-- Main Content -->
      <div class="p-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">My Tasks</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ totalAssignedTasks }}</h3>
              </div>
              <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <box-icon name='task' color='#3B82F6'></box-icon>
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-green-500 flex items-center">
                <box-icon name='check-circle' color='#10B981'></box-icon>
                {{ completedTasks }} completed
              </span>
              <span class="mx-2">•</span>
              <span class="text-yellow-500 flex items-center">
                <box-icon name='time' color='#F59E0B'></box-icon>
                {{ inProgressTasks }} in progress
              </span>
            </div>
          </div>

          <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">My Projects</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ myProjects.length }}</h3>
              </div>
              <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                <box-icon name='folder' color='#9810FA'></box-icon>
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-blue-500 flex items-center">
                <box-icon name='folder-open' color='#3B82F6'></box-icon>
                {{ myProjects.filter(p => p.status === 'in_progress').length }} active projects
              </span>
            </div>
          </div>

          <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Hours Logged</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ totalCompletedHours }}</h3>
              </div>
              <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                <box-icon name='time-five' color='#10B981'></box-icon>
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-blue-500 flex items-center">
                <box-icon name='calendar' color='#3B82F6'></box-icon>
                {{ totalEstimatedHours }} hours estimated
              </span>
            </div>
          </div>

          <div class="bg-gray-950 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-400 text-sm">Completion Rate</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ completionRate }}%</h3>
              </div>
              <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <box-icon name='chart' color='#3B82F6'></box-icon>
              </div>
            </div>
            <div class="mt-4">
              <div class="w-full bg-gray-700 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${completionRate}%` }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column -->
          <div class="lg:col-span-2 space-y-6">
            <!-- My Tasks -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">My Tasks</h2>
                <div class="flex space-x-2">
                  <button
                    class="px-3 py-1 text-sm rounded-md bg-gray-800 text-gray-300 hover:bg-gray-700"
                    :class="{ 'bg-gray-700': viewMode === 'all' }"
                    @click="viewMode = 'all'">
                    All
                  </button>
                  <button
                    class="px-3 py-1 text-sm rounded-md bg-blue-600 text-white"
                    :class="{ 'bg-blue-700': viewMode === 'active' }"
                    @click="viewMode = 'active'">
                    Active
                  </button>
                </div>
              </div>
        
              <div class="p-4">
                <div class="space-y-3">
                  <!-- Mostrar tareas basadas en la vista seleccionada -->
                  <div
                    v-for="task in filteredTasks"
                    :key="task.id"
                    class="bg-gray-900 p-4 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                    @click="navigateToTask(task.project_id, task.id)">
                    <div class="flex justify-between items-start">
                      <div>
                        <h3 class="text-white font-medium">{{ task.name }}</h3>
                        <p class="text-gray-400 text-sm mt-1">{{ task.project_name }}</p>
                      </div>
                      <StatusBadge :status="task.status" class="ml-2" />
                    </div>
                    <p class="text-gray-400 text-sm mt-2 line-clamp-2">{{ task.description }}</p>
                    <div class="mt-3 flex items-center justify-between">
                      <div class="flex items-center">
                        <div :class="`w-3 h-3 rounded-full ${getPriorityColor(task.priority)} mr-2`"></div>
                        <span class="text-sm text-gray-300">{{ getPriorityLabel(task.priority) }}</span>
                      </div>
                      <div class="text-sm text-gray-400">
                        Due: {{ formatDate(task.end_date) }}
                      </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                      <div class="w-3/4">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                          <span>Progress</span>
                          <span>{{ Math.round((task.completed_hours / task.estimated_hours) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5">
                          <div class="bg-blue-600 h-1.5 rounded-full"
                            :style="{ width: `${(task.completed_hours / task.estimated_hours) * 100}%` }"></div>
                        </div>
                      </div>
                      <button @click.stop="openLogTimeModal(task)"
                        class="text-sm px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Log Time
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Weekly Time Tracking -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Weekly Time Tracking</h2>
              </div>
              <div class="p-6">
                <VueApexCharts type="bar" height="300" :options="weeklyTimeChartOptions" :series="weeklyTimeSeries" />
              </div>
            </div>

            <!-- Recent Time Entries -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Recent Time Entries</h2>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="bg-gray-900 text-left">
                      <th class="p-4 font-semibold text-gray-400">Task</th>
                      <th class="p-4 font-semibold text-gray-400">Project</th>
                      <th class="p-4 font-semibold text-gray-400">Date</th>
                      <th class="p-4 font-semibold text-gray-400">Hours</th>
                      <th class="p-4 font-semibold text-gray-400">Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="entry in timeEntries" :key="entry.id"
                      class="border-b border-gray-800 hover:bg-gray-900 transition-colors">
                      <td class="p-4 text-white">{{ entry.task_name }}</td>
                      <td class="p-4 text-gray-300">{{ entry.project_name }}</td>
                      <td class="p-4 text-gray-300">{{ entry.date }}</td>
                      <td class="p-4 text-gray-300">{{ entry.hours }}</td>
                      <td class="p-4 text-gray-300 truncate max-w-xs">{{ entry.description }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Task Status Chart -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Task Status</h2>
              </div>
              <div class="p-6">
                <VueApexCharts type="donut" height="250" :options="taskStatusChartOptions" :series="taskStatusSeries" />
              </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Upcoming Deadlines</h2>
              </div>
              <div class="p-4">
                <div class="space-y-3">
                  <div v-for="task in upcomingDeadlines" :key="task.id"
                    class="bg-gray-900 p-3 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                    @click="navigateToTask(task.project_id, task.id)">
                    <div class="flex justify-between items-start">
                      <h3 class="text-white font-medium">{{ task.name }}</h3>
                      <StatusBadge :status="task.status" class="ml-2" />
                    </div>
                    <p class="text-gray-400 text-xs mt-1">{{ task.project_name }}</p>
                    <div class="mt-2 flex items-center justify-between">
                      <div class="flex items-center">
                        <div :class="`w-2 h-2 rounded-full ${getPriorityColor(task.priority)} mr-2`"></div>
                        <span class="text-xs text-gray-300">{{ getPriorityLabel(task.priority) }}</span>
                      </div>
                      <div class="text-xs text-gray-400">
                        Due: {{ formatDate(task.end_date) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- My Projects -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">My Projects</h2>
              </div>
              <div class="p-4">
                <div class="space-y-3">
                  <div v-for="project in myProjects" :key="project.id"
                    class="bg-gray-900 p-4 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                    @click="navigateToProject(project.id)">
                    <div class="flex justify-between items-start">
                      <h3 class="text-white font-medium">{{ project.name }}</h3>
                      <StatusBadge :status="project.status" class="ml-2" />
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Role: {{ project.role }}</p>
                    <div class="mt-3">
                      <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>My Tasks: {{ project.my_completed_tasks }}/{{ project.my_tasks }}</span>
                        <span>{{ project.progress }}%</span>
                      </div>
                      <div class="w-full bg-gray-700 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full" :style="{ width: `${project.progress}%` }"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notifications -->
            <div class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Notifications</h2>
                <button @click="markAllAsRead" class="text-sm text-blue-400 hover:text-blue-300">
                  Mark all as read
                </button>
              </div>
              <div class="p-4 max-h-80 overflow-y-auto">
                <div class="space-y-3">
                  <div v-for="notification in notifications" :key="notification.id" :class="[
                             'bg-gray-900 p-3 rounded-lg transition-colors relative',
                             notification.read ? 'opacity-70' : 'border-l-2 border-blue-500'
                           ]">
                    <div class="flex items-start">
                      <div class="mr-3 mt-1">
                        <box-icon v-if="notification.type === 'task_assigned'" name='task' color='#3B82F6'></box-icon>
                        <box-icon v-else-if="notification.type === 'comment'" name='message-square-detail'
                          color='#10B981'></box-icon>
                        <box-icon v-else-if="notification.type === 'deadline'" name='alarm-exclamation'
                          color='#F59E0B'></box-icon>
                        <box-icon v-else-if="notification.type === 'review'" name='check-circle'
                          color='#9810FA'></box-icon>
                      </div>
                      <div class="flex-1">
                        <p class="text-gray-300">{{ notification.message }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ notification.time }}</p>
                      </div>
                      <button v-if="!notification.read" @click.stop="markAsRead(notification.id)"
                        class="text-gray-400 hover:text-white">
                        <box-icon name='x' color='currentColor' size="sm"></box-icon>
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

    <!-- Modals -->
    <LogTimeModal :is-open="isLogTimeModalOpen" :task="selectedTask" :user="user"
      :project="{ id: selectedTask?.project_id }" @close="closeLogTimeModal" @log-time="logTime" />
  </Layout>
</template>