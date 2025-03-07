<script setup>
    import { ref, computed, onMounted, watch } from 'vue'
    import { router } from '@inertiajs/vue3'
    import Layout from '@/Layouts/Layout.vue'
    import 'boxicons'
    
    const props = defineProps({
      notifications: Array
    })
    
    const notifications = ref(props.notifications)
    
    // Filter states
    const filterType = ref('all')
    const filterRead = ref('all')
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = ref(10)
    
    // Available notification types for filtering based on your actual types
    const notificationTypes = [
      { value: 'all', label: 'All Notifications' },
      { value: 'assigned_to_project', label: 'Project Assignments' },
      { value: 'assigned_to_task', label: 'Task Assignments' },
      { value: 'task_status_changed', label: 'Status Updates' },
      { value: 'new_documentation', label: 'New Documentation' }
    ]
    
    // Filter notifications based on selected filters and search query
    const filteredNotifications = computed(() => {
      return notifications.value.filter(notification => {
        // Filter by type
        if (filterType.value !== 'all' && notification.type !== filterType.value) {
          return false
        }
        
        // Filter by read status
        if (filterRead.value === 'read' && !notification.is_read) {
          return false
        }
        if (filterRead.value === 'unread' && notification.is_read) {
          return false
        }
        
        // Filter by search query
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase()
          return (
            notification.title.toLowerCase().includes(query) ||
            notification.message.toLowerCase().includes(query)
          )
        }
        
        return true
      })
    })
    
    // Pagination
    const totalPages = computed(() => Math.ceil(filteredNotifications.value.length / itemsPerPage.value))
    const paginatedNotifications = computed(() => {
      const startIndex = (currentPage.value - 1) * itemsPerPage.value
      const endIndex = startIndex + itemsPerPage.value
      return filteredNotifications.value.slice(startIndex, endIndex)
    })
    
    // Get page numbers for pagination
    const getPageNumbers = (currentPage, totalPages, maxVisiblePages = 5) => {
      const pages = []
      
      if (totalPages <= maxVisiblePages) {
        for (let i = 1; i <= totalPages; i++) {
          pages.push(i)
        }
      } else {
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2))
        let endPage = startPage + maxVisiblePages - 1
        
        if (endPage > totalPages) {
          endPage = totalPages
          startPage = Math.max(1, endPage - maxVisiblePages + 1)
        }
        
        for (let i = startPage; i <= endPage; i++) {
          pages.push(i)
        }
      }
      
      return pages
    }
    
    // Pagination navigation
    const goToPage = (page) => {
      currentPage.value = page
    }
    
    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
      }
    }
    
    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
      }
    }
    
    // Reset filters
    const resetFilters = () => {
      filterType.value = 'all'
      filterRead.value = 'all'
      searchQuery.value = ''
      currentPage.value = 1
    }
    
    // Mark notification as read
    const markAsRead = (notification) => {
      router.put(`/notifications/${notification.id}/mark-as-read`, {}, {
        preserveState: true,
        onSuccess: () => {
          const index = notifications.value.findIndex(n => n.id === notification.id)
          if (index !== -1) {
            notifications.value[index].is_read = true
          }
        },
        onError: (errors) => {
          console.error('Mark as read error:', errors)
        }
      })
    }
    
    // Mark all as read
    const markAllAsRead = () => {
      router.put('/notifications/mark-all-as-read', {}, {
        preserveState: true,
        onSuccess: () => {
          notifications.value.forEach(notification => {
            notification.is_read = true
          })
        },
        onError: (errors) => {
          console.error('Mark all as read error:', errors)
        }
      })
    }
    
    // Delete notification
    const deleteNotification = (notification) => {
      router.delete(`/notifications/${notification.id}`, {
        preserveState: true,
        onSuccess: () => {
          const index = notifications.value.findIndex(n => n.id === notification.id)
          if (index !== -1) {
            notifications.value.splice(index, 1)
          }
        },
        onError: (errors) => {
          console.error('Delete notification error:', errors)
        }
      })
    }
    
    // Get icon background color based on notification type
    const getIconBgColor = (type) => {
      const colors = {
        assigned_to_project: 'bg-blue-600/20',
        assigned_to_task: 'bg-purple-600/20',
        task_status_changed: 'bg-orange-600/20',
        new_documentation: 'bg-green-600/20'
      }
      return colors[type] || 'bg-gray-600/20'
    }
    
    // Get icon color based on notification type
    const getIconColor = (type) => {
      const colors = {
        assigned_to_project: '#3B82F6',
        assigned_to_task: '#9810FA',
        task_status_changed: '#F97316',
        new_documentation: '#10B981'
      }
      return colors[type] || '#9CA3AF'
    }
    
    // Navigate to related entity
    const navigateToEntity = (notification) => {
      if (notification.notifiable_type === 'App\\Models\\Project') {
        router.visit(`/projects/${notification.notifiable_id}`)
      } else if (notification.notifiable_type === 'App\\Models\\Task') {
        // Assuming tasks are accessed via /tasks/{id} or similar
        router.visit(`/tasks/${notification.notifiable_id}`)
      } else if (notification.notifiable_type === 'App\\Models\\Documentation') {
        // Assuming documentation is accessed via /documentation/{id} or similar
        router.visit(`/documentation/${notification.notifiable_id}`)
      }
    }
    
    // Format date for display
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      const now = new Date()
      const diffInSeconds = Math.floor((now - date) / 1000)
      
      if (diffInSeconds < 60) {
        return 'Just now'
      } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60)
        return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`
      } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600)
        return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`
      } else if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400)
        return `${days} ${days === 1 ? 'day' : 'days'} ago`
      } else {
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      }
    }
    
    // Stats
    const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length)
    const totalCount = computed(() => notifications.value.length)
    const todayCount = computed(() => {
      const today = new Date().toISOString().split('T')[0]
      return notifications.value.filter(n => n.created_at.startsWith(today)).length
    })
    
    // Reset to page 1 when filters change
    const resetPage = () => {
      currentPage.value = 1
    }
    
    // Watch for filter changes to reset pagination
    watch([filterType, filterRead, searchQuery], resetPage)
    </script>
    
    <template>
      <Layout pageTitle="All Notifications">
        <div class="min-h-screen bg-black text-gray-300">
          <!-- Main Content -->
          <div class="p-6">
            <!-- Header with stats -->
            <div class="mb-6">
              <h1 class="text-2xl font-bold text-white mb-2">Notifications</h1>
              <div class="flex justify-between w-full gap-4">
                <div class="bg-gray-950 rounded-lg w-full  p-4 border border-gray-700 flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='bell' color='#3B82F6'></box-icon>
                  </div>
                  <div>
                    <p class="text-gray-400 text-xs">Total</p>
                    <p class="text-xl font-bold text-white">{{ totalCount }}</p>
                  </div>
                </div>
                
                <div class="bg-gray-950 rounded-lg w-full p-4 border border-gray-700 flex items-center gap-3">
                  <div class="w-10 h-10 bg-red-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='envelope' color='#EF4444'></box-icon>
                  </div>
                  <div>
                    <p class="text-gray-400 text-xs">Unread</p>
                    <p class="text-xl font-bold text-white">{{ unreadCount }}</p>
                  </div>
                </div>
                
                <div class="bg-gray-950 rounded-lg p-4 w-full border border-gray-700 flex items-center gap-3">
                  <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <box-icon name='time' color='#10B981'></box-icon>
                  </div>
                  <div>
                    <p class="text-gray-400 text-xs">Today</p>
                    <p class="text-xl font-bold text-white">{{ todayCount }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Filters -->
            <div class="bg-gray-950 rounded-lg border border-gray-700 mb-6">
              <div class="p-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white">Filters</h2>
              </div>
              <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Search -->
                  <div>
                    <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Search</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <box-icon name='search' color='#9CA3AF' size="sm"></box-icon>
                      </div>
                      <input
                        id="search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search notifications..."
                        class="bg-gray-900 border border-gray-700 text-white rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                    </div>
                  </div>
                  
                  <!-- Type filter -->
                  <div>
                    <label for="type-filter" class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                    <select
                      id="type-filter"
                      v-model="filterType"
                      class="bg-gray-900 border border-gray-700 text-white rounded-md px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option v-for="type in notificationTypes" :key="type.value" :value="type.value">
                        {{ type.label }}
                      </option>
                    </select>
                  </div>
                  
                  <!-- Read status filter -->
                  <div>
                    <label for="read-filter" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                    <select
                      id="read-filter"
                      v-model="filterRead"
                      class="bg-gray-900 border border-gray-700 text-white rounded-md px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="all">All</option>
                      <option value="read">Read</option>
                      <option value="unread">Unread</option>
                    </select>
                  </div>
                </div>
                
                <!-- Filter actions -->
                <div class="flex justify-between mt-4">
                  <button
                    @click="resetFilters"
                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors cursor-pointer flex items-center gap-2"
                  >
                    <box-icon name='reset' color='#ffffff' size="sm"></box-icon>
                    Reset Filters
                  </button>
                  
                  <button
                    @click="markAllAsRead"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors cursor-pointer flex items-center gap-2"
                  >
                    <box-icon name='check-double' color='#ffffff' size="sm"></box-icon>
                    Mark All as Read
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Notifications list -->
            <div class="bg-gray-950 rounded-lg border border-gray-700 mb-6">
              <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-white">
                  {{ filteredNotifications.length }} Notifications
                </h2>
                <div class="flex items-center space-x-2">
                  <select
                    v-model="itemsPerPage"
                    class="bg-gray-900 text-white border border-gray-700 rounded px-2 py-1 text-sm"
                  >
                    <option :value="5">5 per page</option>
                    <option :value="10">10 per page</option>
                    <option :value="15">15 per page</option>
                    <option :value="20">20 per page</option>
                  </select>
                </div>
              </div>
              
              <!-- Empty state -->
              <div v-if="filteredNotifications.length === 0" class="p-12 flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                  <box-icon name='bell-off' color='#9CA3AF' size="md"></box-icon>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">No notifications found</h3>
                <p class="text-gray-400 text-center max-w-md">
                  There are no notifications matching your current filters. Try changing your filter settings or check back later.
                </p>
                <button
                  @click="resetFilters"
                  class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition-colors"
                >
                  Reset Filters
                </button>
              </div>
              
              <!-- Notifications -->
              <div v-else class="divide-y divide-gray-800">
                <div
                  v-for="notification in paginatedNotifications"
                  :key="notification.id"
                  class="p-6 hover:bg-gray-900 transition-colors"
                  :class="{ 'bg-gray-900 bg-opacity-40': !notification.is_read }"
                >
                  <div class="flex gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                      <div
                        :class="`w-12 h-12 rounded-lg ${getIconBgColor(notification.type)} flex items-center justify-center`"
                      >
                        <box-icon :name="notification.icon" :color="getIconColor(notification.type)"></box-icon>
                      </div>
                    </div>
              
                    <!-- Content -->
                    <div class="flex-1 w-full">
                      <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between">
                          <h3
                            class="font-medium text-base"
                            :class="{ 'text-white': !notification.is_read, 'text-gray-300': notification.is_read }"
                          >
                            {{ notification.title }}
                          </h3>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ formatDate(notification.created_at) }}</span>
                            <div v-if="!notification.is_read" class="w-2 h-2 rounded-full bg-blue-500"></div>
                          </div>
                            </div>
              
                          <!-- Message and Delete button in the same row -->
                          <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-400">{{ notification.message }}</p>
                            <div class="flex gap-2">
                            <button
                              @click="deleteNotification(notification)"
                              class="px-2 py-1 bg-gray-800 cursor-pointer text-xs text-gray-300 rounded hover:bg-red-700 hover:text-white transition-colors flex items-center gap-1"
                            >
                              <box-icon name='trash' color='currentColor' class="mb-2" size="xs"></box-icon>
                              Delete
                            </button>
                             <!-- Mark as read button -->
                          <div class="flex gap-2">
                            <button
                              v-if="!notification.is_read"
                              @click="markAsRead(notification)"
                              class="px-2 py-1 bg-gray-800 text-xs text-gray-300 rounded hover:bg-blue-700 cursor-pointer transition-colors flex items-center gap-1"
                            >
                              <box-icon name='check' color='currentColor' class="mb-2" size="xs"></box-icon>
                              Mark as read
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
              
              
              <!-- Pagination -->
              <div v-if="filteredNotifications.length > 0" class="px-6 py-4 bg-gray-900 border-t border-gray-800 flex items-center justify-between">
                <div class="text-sm text-gray-400">
                  Showing {{ paginatedNotifications.length }} of {{ filteredNotifications.length }} notifications
                </div>
                <div class="flex items-center space-x-1">
                  <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    class="px-3 py-1 cursor-pointer hover:bg-gray-700 rounded bg-gray-800 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <box-icon name='chevron-left' color='#ffffff' size="sm"></box-icon>
                  </button>
                  
                  <button
                    v-for="page in getPageNumbers(currentPage, totalPages)"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      'px-3 py-1 cursor-pointer rounded text-sm',
                      currentPage === page
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-800 text-white hover:bg-gray-700'
                    ]"
                  >
                    {{ page }}
                  </button>
                  
                  <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 cursor-pointer hover:bg-gray-700 rounded bg-gray-800 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <box-icon name='chevron-right' color='#ffffff' size="sm"></box-icon>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Layout>
    </template>