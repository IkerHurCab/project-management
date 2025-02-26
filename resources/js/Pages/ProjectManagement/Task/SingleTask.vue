<script setup>
  import { ref, computed, onMounted, watch } from 'vue'
  import { useForm, router  } from '@inertiajs/vue3'
  import Layout from '@/Layouts/Layout.vue'
  import Button from '@/Components/StandardButton.vue'
  import StatusBadge from '@/Components/StatusBadge.vue'
  import EditTaskModal from '@/Pages/ProjectManagement/Task/EditTaskModal.vue'
  import LogTimeModal from '@/Pages/ProjectManagement/Task/LogTimeModal.vue'
  import AddAttachmentModal from '@/Pages/ProjectManagement/Task/AddAttachmentModal.vue'
  import VueApexCharts from 'vue3-apexcharts'
  import FloatingVue from 'floating-vue'
  
  const props = defineProps({
    task: Object,
    user: Object,
    project: Object,
    comments: Array,
    relatedTasks: Array,
    taskLogs: Array,
  })
  const attachmentsTask = ref(false);

  onMounted(() => {
    updateAttachmentsTask();
  });

  watch(() => props.task.attachments, () => {
    updateAttachmentsTask();
  }, { deep: true });

  function updateAttachmentsTask() {
  try {
    const parsedAttachments = JSON.parse(props.task.attachments || "[]");
    attachmentsTask.value = Array.isArray(parsedAttachments) && parsedAttachments.length > 0;
  } catch (error) {
    console.error("Error parsing attachments:", error);
    attachmentsTask.value = false;
  }
}



  const isEditTaskModalOpen = ref(false)
  const isLogTimeModalOpen = ref(false)
  const isAddAttachmentModalOpen = ref(false)
  
  const openEditTaskModal = () => isEditTaskModalOpen.value = true
  const closeEditTaskModal = () => isEditTaskModalOpen.value = false
  const openLogTimeModal = () => isLogTimeModalOpen.value = true
  const closeLogTimeModal = () => isLogTimeModalOpen.value = false
  const openAddAttachmentModal = () => isAddAttachmentModalOpen.value = true
  const closeAddAttachmentModal = () => isAddAttachmentModalOpen.value = false
  const redirectToRelatedTask =  (relatedTask) =>  router.get(`/projects/${props.project.id}/task/${relatedTask.id}`);
  
  const progressPercentage = computed(() => {
    return Math.round((props.task.completed_hours / props.task.estimated_hours) * 100)
  })
  
  const chartOptions = {
    chart: {
      type: 'radialBar',
      foreColor: '#E5E7EB',
    },
    plotOptions: {
      radialBar: {
        hollow: {
          size: '70%',
        },
        dataLabels: {
          name: {
            show: false,
          },
          value: {
            color: '#E5E7EB',
            fontSize: '30px',
            fontWeight: 600,
          },
        },
      },
    },
    labels: ['Progress'],
    colors: ['#3B82F6'],
  }
  
  const getPriorityLabel = (priority) => {
    const labels = ['Low', 'Medium', 'High', 'Urgent']
    return labels[priority - 1] || 'Unknown'
  }
  
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  }
  
  const downloadAttachment = (attachment) => {
    const link = document.createElement("a")
    link.href = attachment;
    link.download = attachment.split('/').pop()
    console.log(link.href)
    document.body.appendChild(link);
    link.click()
    document.body.removeChild(link);
  }
  
  const updateTask = (updatedTask) => {
    // Implement task update logic here
    console.log('Updating task:', updatedTask)
  }
  
  const logTime = (timeEntry) => {
 
    console.log('Logging time:', timeEntry)
  }
  
  const addAttachment = async (attachment) => {
  try {
    const formData = new FormData();

  
    attachment.attachments.forEach((file, index) => {
      if (file instanceof File) {
        formData.append(`attachments[${index}]`, file);
      }
    });

    if (formData.has('attachments[0]')) {
      await router.post(`/projects/${props.project.id}/task/${props.task.id}`, formData, {
        _method: "PUT"
      });
      console.log('Attachments added successfully');
    } else {
      console.error('No valid files found in attachments');
    }
  } catch (error) {
    console.error('Error adding attachments:', error);
  }
};
const getPriorityColor = (priority) => {
  const colors = ['bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500']
  return colors[priority - 1] || 'bg-gray-500'
}

function getRandomColor(index) {
  const colors = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
    '#F06292', '#AED581', '#FFD54F', '#4DB6AC', '#7986CB'
  ];
  return colors[index % colors.length];
}

  

  const newComment = ref('')
  const addComment = async () => {
  const commentData = {
    content: newComment.value,
  }
  try {
    await router.post(`/projects/${props.project.id}/task/${props.task.id}/comment`, commentData)
  
    newComment.value = ''
  } catch (error) {
    console.error('Error adding comment:', error)
  }

 

}

  console.log(JSON.parse(props.task.attachments))
 
  
  
  

  

  </script>
  
<template>
  <Layout pageTitle="Task">
    <div class="min-h-screen bg-black text-gray-300">
      <!-- Header -->
      <div class="border-b border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-8">
            <a @click="$inertia.visit('/projects/' + task.project_id)"
              class="text-gray-300 hover:text-white cursor-pointer flex items-center space-x-2 transition-colors">
              <box-icon name='arrow-back' color='#E5E7EB'></box-icon>
              <span>Back to Project</span>
            </a>
            <h1 class="text-xl font-semibold text-white">{{ task.name }}</h1>
          </div>
          
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex min-h-[80vh]">
        <!-- Left Panel -->
        <div class="flex-1 p-6 overflow-auto">
          <div class="grid grid-cols-3 gap-6">
            <!-- Task Details -->
            <div class="col-span-2 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white">Task Details</h2>
                <Button @click="openEditTaskModal" size="sm" class="bg-blue-600 hover:bg-blue-700">Edit Task</Button>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-gray-900 rounded-lg p-4 flex flex-col justify-between">
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-gray-400">Status</span>
                      <StatusBadge :status="task.status" class="mt-1 w-fit" />
                    </div>
                    <div class="mt-4">
                      <span class="text-sm font-medium text-gray-400">Priority</span>
                      <div class="flex items-center mt-1">
                        <div :class="`w-3 h-3 rounded-full ${getPriorityColor(task.priority)} mr-2`"></div>
                        <span class="text-lg text-white font-light">{{ getPriorityLabel(task.priority) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-900 rounded-lg p-4 flex flex-col justify-between">
                    <div>
                      <span class="text-sm font-medium text-gray-400">Start Date</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='calendar' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ formatDate(task.start_date) }}</span>
                      </div>
                    </div>
                    <div class="mt-4">
                      <span class="text-sm font-medium text-gray-400">End Date</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='calendar-check' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ formatDate(task.end_date) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-900 rounded-lg p-4 flex items-center justify-between">
                    <div>
                      <span class="text-sm font-medium text-gray-400">Estimated Hours</span>
                      <div class="flex items-center mt-1">
                        <box-icon name='time' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ task.estimated_hours }} h</span>
                      </div>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center">
                      <span class="text-white font-bold">{{ Math.round((task.completed_hours / task.estimated_hours) * 100) }}%</span>
                    </div>
                  </div>
                  <div class="bg-gray-900 rounded-lg p-4">
                    <span class="text-sm font-medium text-gray-400">Completed Hours</span>
                    <div class="flex items-center justify-between mt-1">
                      <div class="flex items-center">
                        <box-icon name='check-circle' color='#9CA3AF' class="mr-2"></box-icon>
                        <span class="text-lg text-white font-light">{{ task.completed_hours }} h</span>
                      </div>
                      <div class="w-1/2 bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" :style="{ width: `${(task.completed_hours / task.estimated_hours) * 100}%` }"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-6 bg-gray-900 rounded-lg p-4">
                  <span class="text-sm font-medium text-gray-400">Description</span>
                  <p class="text-white text-lg leading-relaxed mt-2 break-words">
                    {{ task.description }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Progress Chart -->
            <div class="col-span-1 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Progress Overview</h2>
              </div>
              <div class="p-6">
                <VueApexCharts type="radialBar" height="300" :options="chartOptions" :series="[progressPercentage]" />
              </div>
            </div>

            <!-- Time Tracking -->
            <div class="col-span-2 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white">Time Tracking</h2>
                <Button @click="openLogTimeModal" size="sm">Log Time</Button>
              </div>
              <div class="p-6">
                <div class="mb-4">
                  <div class="flex justify-between text-sm text-gray-400 mb-2">
                    <span>Progress</span>
                    <span>{{ progressPercentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-700 rounded-full h-5 overflow-hidden">
                    <div 
                      v-for="(log, index) in taskLogs" 
                      :key="index"
                      class="h-full inline-block"
                      :style="{
                        width: `${(log.log_time / task.estimated_hours) * 100}%`,
                        backgroundColor: getRandomColor(index)
                      }"
                      v-tooltip="`${log.description}`"
                    ></div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div>
                    <p class="text-gray-400 text-sm">Estimated</p>
                    <p class="text-white text-lg font-semibold">{{ task.estimated_hours ?? 0 }} hours</p>
                  </div>
                  <div>
                    <p class="text-gray-400 text-sm">Logged</p>
                    <p class="text-white text-lg font-semibold">{{ task.completed_hours ?? '' }}  hours</p>
                  </div>
                </div>
              </div>
            </div>
            

            <!-- Related Tasks -->
            <div class="col-span-1 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Related Tasks</h2>
              </div>
              <div class="p-6">
                <ul class="space-y-2">
                  <p v-if="relatedTasks.length === 0" class="text-gray-500 flex h-full items-center justify-center">
                    There are no related Tasks in this Project
                  </p>
                  <li v-for="relatedTask in relatedTasks" :key="relatedTask.id" class="flex items-center justify-between">
                    <!-- Tarea completada -->
                    <a v-if="relatedTask.status === 'done'" @click="redirectToRelatedTask(relatedTask)" class="text-blue-400 hover:text-blue-300 cursor-pointer">
                      {{ relatedTask.name }} - <span class="text-green-500">Done</span>
                    </a>
                    <a v-if="relatedTask.status === 'review'" @click="redirectToRelatedTask(relatedTask)" class="text-blue-400 hover:text-blue-300 cursor-pointer">
                      {{ relatedTask.name }} - <span class="text-purple-500">Review</span>
                    </a>
                
                    <!-- Tarea en progreso -->
                    <a v-else-if="relatedTask.status === 'in_progress'" @click="redirectToRelatedTask(relatedTask)" class="text-blue-400 hover:text-blue-300 cursor-pointer">
                      {{ relatedTask.name }} - <span class="text-yellow-500">In Progress</span>
                    </a>
                
                    <a v-else-if="relatedTask.status === 'to_do'" @click="redirectToRelatedTask(relatedTask)" class="text-blue-400 hover:text-blue-300 cursor-pointer">
                      {{ relatedTask.name }} - <span class="text-blue-500">To Do</span>
                    </a>
                
                   
                  </li>
                </ul>
              </div>
            </div>

            <!-- Attachments -->
            <div class="col-span-3 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white">Attachments</h2>
                <Button @click="openAddAttachmentModal" size="sm">Add Attachment</Button>
              </div>
              <div class="p-6">
                <div v-if="attachmentsTask" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div v-for="(attachment, index) in JSON.parse(task.attachments)" :key="index" class="bg-gray-900 p-4 rounded-lg flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                          <box-icon name='file-blank' color='#ffffff'></box-icon>
                          <span class="text-white truncate">{{ attachment.split('/').pop() }}</span>
                      </div>
                      <Button @click="downloadAttachment(attachment)" size="sm">Download</Button>
                  </div>
              </div>
              <div v-else class="text-gray-400 text-center py-4">
                  No attachments available
              </div>
              
              </div>
            </div>

            <!-- Comments Section -->
            <div class="col-span-3 bg-gray-950 rounded-lg overflow-hidden border border-gray-700">
              <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-2xl font-semibold text-white">Comments</h2>
              </div>
              <div class="p-6">
                <div class="space-y-4 mb-6">
                  <div v-for="comment in comments" :key="comment.id" class="bg-gray-900 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                          {{ comment.user.name.charAt(0) || 0 }}
                        </div>
                        <span class="text-white font-medium">{{ comment.user.name }}</span>
                      </div>
                      <span class="text-sm text-gray-400">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <p class="text-gray-300">{{ comment.content }}</p>
                  </div>
                </div>
                <div class="flex items-start space-x-4">
                  <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    {{ user.name.charAt(0) }}
                  </div>
                  <div class="flex-grow">
                    <textarea v-model="newComment" rows="3" class="w-full bg-gray-800 text-white rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add a comment..."></textarea>
                    <Button @click="addComment"  class="mt-2">Post Comment</Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Sidebar -->
        
      </div>
    </div>
    <EditTaskModal 
      :is-open="isEditTaskModalOpen"
      :task="task"
      :project="project"
      @close="closeEditTaskModal"
      @update="updateTask"
    />
    <LogTimeModal
      :is-open="isLogTimeModalOpen"
      :task="task"
      :user="user"
      :project="project"
      @close="closeLogTimeModal"
      @log-time="logTime"
    />
    <AddAttachmentModal
      :is-open="isAddAttachmentModalOpen"
      :task-id="task.id"
      @close="closeAddAttachmentModal"
      @add-attachment="addAttachment"
    />
  </Layout>
</template>


