<script setup>
    import { ref, onMounted } from 'vue';
    import { useForm, router } from '@inertiajs/vue3';
    import { toast } from 'vue3-toastify';
    import StandardButton from '@/Components/StandardButton.vue';
    import SelectWithIcon from '@/Components/SelectWithIcon.vue';
    import InputWithIcon from '@/Components/InputWithIcon.vue';
    
    const props = defineProps({
      isOpen: Boolean,
      projectId: Number,
      employees: Array,
    });
    
    const emit = defineEmits(['close']);
    onMounted(() => {
      form.start_date = getCurrentDate();
    })
    const form = useForm({
      name: '',
      description: '',
      estimated_hours: '',
      status: 'to_do',
      priority: '1',
      start_date: '',
      end_date: '',
      project_id: props.projectId,
      user_id: '',
      attachments: [],
    });
    
    const priorities = [
      { value: '1', label: 'Low', color: 'bg-green-500' },
      { value: '2', label: 'Medium', color: 'bg-yellow-500' },
      { value: '3', label: 'High', color: 'bg-red-500' },
    ];
    
    const statuses = [
      { value: 'to_do', label: 'To Do', color: 'bg-gray-500' },
      { value: 'in_progress', label: 'In Progress', color: 'bg-blue-500' },
      { value: 'review', label: 'Review', color: 'bg-purple-500' },
      { value: 'done', label: 'Done', color: 'bg-green-500' },
    ];
    
    const fileInput = ref(null);
    
    const handleFileUpload = (event) => {
      form.attachments = [...event.target.files];
    };
    
    const removeAttachment = (index) => {
      form.attachments.splice(index, 1);
    };
    
    const submitForm = () => {
      router.post(`/projects/${props.projectId}/tasks`, form, {
        onSuccess: () => {
          router.visit(`/projects/${props.projectId}`);
          toast.success('Task created successfully');
        },
        onError: (errors) => {
          toast.error('An error occurred while creating the task.');
        }
      });
    };
    
    function getCurrentDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0'); 
      const day = String(today.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
      <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-4xl sm:max-w-2xl lg:max-w-5xl h-auto border border-gray-700 shadow-lg max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-700 dark:bg-gray-100 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
          <h2 class="text-2xl font-semibold text-white dark:text-black">Create New Task</h2>
          <button @click="emit('close')" class="text-gray-400 dark:hover:text-black dark:text-gray-700 cursor-pointer hover:text-white transition-colors">
            <box-icon name='x' color='currentColor'></box-icon>
          </button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Task Name</label>
              <InputWithIcon v-model="form.name" id="name" type="text" icon="task" placeholder="Enter task name" class="bg-gray-950 dark:bg-gray-100 dark:text-black border-gray-700 text-white" />
            </div>
            <div>
              <label for="description" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Description</label>
              <textarea v-model="form.description" id="description" rows="4"
                        class="w-full dark:bg-gray-100 dark:text-black bg-gray-800 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                        placeholder="Enter task description"></textarea>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label for="estimated_hours" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Estimated Hours</label>
              <InputWithIcon v-model="form.estimated_hours" id="estimated_hours" type="number" icon="time" placeholder="Enter estimated hours" class="border-gray-700 w-full text-white" />
            </div>
            <div>
              <label for="status" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Status</label>
              <SelectWithIcon v-model="form.status" icon="check-square" placeholder="Select status" class="w-full" :options="[ { label: 'To Do', value: 'to_do' }, { label: 'In Progress', value: 'in_progress' }, { label: 'In Review', value: 'in_review' }]" />
            </div>
            <div>
              <label for="priority" class="block text-sm font-medium  text-gray-400 mb-2 dark:text-gray-700">Priority</label>
              <SelectWithIcon v-model="form.priority" icon="flag" placeholder="Select priority" class="w-full" :options="[ { label: 'Low', value: 1 }, { label: 'Medium', value: 2 }, { label: 'High', value: 3 }, { label: 'Urgent', value: 4 }]" />
            </div>
            <div>
              <label for="user_id" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Assign To</label>
              <SelectWithIcon v-model="form.user_id" icon="user" placeholder="Select employee" class="w-full" :options="[ { label: 'Assign the employee', value: '' }, ...employees.map(employee => ({ label: employee['name'], value: employee['id'] })) ]" />
            </div>
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Start Date</label>
              <InputWithIcon v-model="form.start_date" id="start_date" type="date" icon="calendar" class=" border-gray-700 w-full text-white" />
            </div>
            <div>
              <label for="end_date" class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">End Date</label>
              <InputWithIcon v-model="form.end_date" id="end_date" type="date" icon="calendar" class="w-full border-gray-700 text-white" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1 dark:text-gray-700">Attachments</label>
            <div class="flex items-center justify-center w-full">
              <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 dark:bg-gray-100 dark:hover:bg-gray-200 dark:text-black border-dashed rounded-lg cursor-pointer bg-gray-800 hover:bg-gray-700 transition-all">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                  <box-icon name='cloud-upload' color='#9CA3AF'></box-icon>
                  <p class="mb-2 text-sm text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                  <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                </div>
                <input id="dropzone-file" type="file" class="hidden" multiple @change="handleFileUpload" ref="fileInput" />
              </label>
            </div>
            <div v-if="form.attachments.length > 0" class="mt-4 space-y-2">
              <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between bg-gray-800 p-2 rounded-md">
                <span class="text-sm text-gray-300">{{ file.name }}</span>
                <button @click="removeAttachment(index)" type="button" class="cursor-pointer text-red-500 hover:text-red-600">
                  <box-icon name='x' color='currentColor'></box-icon>
                </button>
              </div>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-500 transition-colors">Create Task</StandardButton>
          </div>
        </form>
      </div>
    </div>
</template>

<style scoped>
/* Additional Styles */
@media (max-width: 768px) {
  .sm\:max-w-2xl {
    max-width: 100%;
  }
}
</style>
