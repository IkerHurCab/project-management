<script setup>
  import { ref } from 'vue';
  import { useForm, router } from '@inertiajs/vue3';
  import { toast } from 'vue3-toastify';
  import StandardButton from '@/Components/StandardButton.vue';
  import SelectWithIcon from '@/Components/SelectWithIcon.vue'
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  
  const props = defineProps({
    isOpen: Boolean,
    task: Object,
    project: Object,
    employees: Array,
  });
  
  const errors = ref({});

  const emit = defineEmits(['close', 'update', 'delete']);
  
  const form = useForm({
    name: props.task?.name || '',
    description: props.task?.description || '',
    estimated_hours: props.task?.estimated_hours || '',
    status: props.task?.status || 'to_do',
    priority: props.task?.priority || '1',
    start_date: props.task?.start_date || '',
    end_date: props.task?.end_date || '',
    user_id: props.task?.user || '',
  });
  
  const priorities = [
    { value: '1', label: 'Low' },
    { value: '2', label: 'Medium' },
    { value: '3', label: 'High' },
    { value: '4', label: 'Urgent' },
  ];
  
  const statuses = [
    { value: 'to_do', label: 'To Do' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'review', label: 'Review' },
    { value: 'done', label: 'Done' },
  ];
  
  const showDeleteModal = ref(false);
  
  const submitForm = () => {
  router.post(`/projects/${props.project.id}/task/${props.task.id}`, form, {
    onSuccess: () => {
      toast.success('Task updated successfully');
      closeModal();
    },
    onError: (err) => {
      errors.value = err;

    }
  });
};

  
  const openDeleteModal = () => {
    showDeleteModal.value = true;
  };
  
  const closeDeleteModal = () => {
    showDeleteModal.value = false;
  };
  
  const deleteTask = () => {
    router.delete(`/projects/${props.project.id}/task/${props.task.id}`)
    closeDeleteModal();
    closeModal();
  };

  const closeModal = () => {
    errors.value = {};
    emit('close');
  };
  </script>
  
  <template>
    <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-5xl h-auto border border-gray-700 shadow-lg 
                  max-h-[90vh] overflow-y-auto sm:max-w-md md:max-w-lg lg:max-w-4xl">
        <div class="border-b border-gray-700 dark:bg-gray-100 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
          <h2 class="text-xl sm:text-lg md:text-xl font-semibold text-white dark:text-black">Edit Task</h2>
          <div class="flex items-center space-x-4">
            <button @click="openDeleteModal" class="text-red-500 cursor-pointer hover:text-red-400 transition-colors">
              <box-icon name="trash" type="solid" color="currentColor"></box-icon>
            </button>
            <button @click="closeModal" class="text-gray-400 dark:text-gray-700 dark:hover:text-black cursor-pointer hover:text-white transition-colors">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
        </div>
  
        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Task Name</label>
              <InputWithIcon v-model="form.name" id="name" type="text" icon="task" placeholder="Enter task name" class="bg-gray-950 dark:bg-gray-100 dark:text-black border-gray-700 text-white" />
              <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
         
            </div>
            <div>
              <label for="description" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" id="description" rows="4"
                        class="w-full bg-gray-800 border dark:bg-gray-100 dark:text-black border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                        placeholder="Enter task description"></textarea>
            </div>
          </div>
  
          <!-- Responsive Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label for="estimated_hours" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Estimated Hours</label>
              <InputWithIcon v-model="form.estimated_hours" id="estimated_hours" type="number" icon="time" placeholder="Enter estimated hours" class="border-gray-700 w-full text-white" />
              <p v-if="errors.estimated_hours" class="text-red-500 text-sm mt-1">{{ errors.estimated_hours }}</p>
            </div>
            <div>
              <label for="status" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Status</label>
              <SelectWithIcon v-model="form.status" icon="check-square" placeholder="Select status" class="w-full" :options="statuses" />
              <p v-if="errors.status" class="text-red-500 text-sm mt-1">{{ errors.status }}</p>
            </div>
            <div>
              <label for="priority" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-2">Priority</label>
              <SelectWithIcon v-model="form.priority" icon="flag" placeholder="Select priority" class="w-full" :options="priorities" />
              <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority }}</p>
            </div>
            <div>
              <label for="user_id" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Assign To</label>
              <SelectWithIcon v-model="form.user_id" icon="user" placeholder="Select employee" class="w-full" :options="[
                { label: 'Assign the employee', value: '' },
                ...employees.map(employee => ({ label: employee['name'], value: employee['id'] }))
              ]" />
              <p v-if="errors.user_id" class="text-red-500 text-sm mt-1">{{ errors.user_id }}</p>
            </div>
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Start Date</label>
              <InputWithIcon v-model="form.start_date" id="start_date" type="date" icon="calendar" class="border-gray-700 w-full text-white" />
              <p v-if="errors.start_date" class="text-red-500 text-sm mt-1">{{ errors.start_date }}</p>
            </div>
            <div>
              <label for="end_date" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">End Date</label>
              <InputWithIcon v-model="form.end_date" id="end_date" type="date" icon="calendar" class="w-full border-gray-700 text-white" />
              <p v-if="errors.end_date" class="text-red-500 text-sm mt-1">{{ errors.end_date }}</p>
            </div>
          </div>
  
          <div class="flex justify-end space-x-3 mt-6">
            <StandardButton type="button" @click="closeModal()" class="bg-gray-600 hover:bg-gray-500 dark:bg-gray-600 dark:hover:bg-gray-500 transition-colors">Cancel</StandardButton>
            <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-500 transition-colors">Update Task</StandardButton>
          </div>
        </form>
      </div>
    </div>
  
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-md p-6 border border-gray-700 shadow-lg">
        <h3 class="text-xl font-semibold text-white mb-4 dark:text-black">Are you sure you want to delete this task?</h3>
        <p class="text-gray-400 mb-6 dark:text-gray-700">This action cannot be undone.</p>
        <div class="flex justify-end space-x-4">
          <StandardButton @click="closeDeleteModal" class="bg-gray-600 hover:bg-gray-500  dark:bg-gray-600 dark:hover:bg-gray-500 transition-colors">
            Cancel
          </StandardButton>
          <StandardButton @click="deleteTask" class="bg-red-600 hover:bg-red-500 dark:bg-red-600 dark:hover:bg-red-500 transition-colors">
            Delete
          </StandardButton>
        </div>
      </div>
    </div>
  </template>
  