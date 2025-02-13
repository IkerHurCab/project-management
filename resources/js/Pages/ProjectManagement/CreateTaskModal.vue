<script setup>
    import { ref } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import StandardButton from '@/Components/StandardButton.vue';
    import InputWithIcon from '@/Components/InputWithIcon.vue';
    
    const props = defineProps({
      isOpen: Boolean,
      projectId: Number,
      employees: Array,
    });
    
    const emit = defineEmits(['close']);
    
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
    });
    
    const priorities = [
      { value: '1', label: 'Low' },
      { value: '2', label: 'Medium' },
      { value: '3', label: 'High' },
    ];
    
    const statuses = [
      { value: 'to_do', label: 'To Do' },
      { value: 'in_progress', label: 'In Progress' },
      { value: 'review', label: 'Review' },
      { value: 'done', label: 'Done' },
    ];
    
    const submitForm = () => {
      form.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
          emit('close');
          form.reset();
        },
      });
    };
    </script>
    
    <template>
      <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-950 rounded-lg w-full max-w-2xl border border-gray-700">
          <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Create New Task</h2>
            <button @click="emit('close')" class="cursor-pointer text-gray-400 hover:text-white">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
          <form @submit.prevent="submitForm" class="p-6 space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-400 mb-1">Task Name</label>
              <InputWithIcon v-model="form.name" id="name" type="text" icon="task" placeholder="Enter task name" />
            </div>
            <div>
              <label for="description" class="block text-sm font-medium text-gray-400 mb-1">Description</label>
              <textarea v-model="form.description" id="description" rows="3" 
                        class="w-full bg-gray-900 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter task description"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="estimated_hours" class="block text-sm font-medium text-gray-400 mb-1">Estimated Hours</label>
                <InputWithIcon v-model="form.estimated_hours" id="estimated_hours" type="number" icon="time" placeholder="Enter estimated hours" />
              </div>
              <div>
                <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                <select v-model="form.status" id="status"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option v-for="status in statuses" :key="status.value" :value="status.value">
                    {{ status.label }}
                  </option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="priority" class="block text-sm font-medium text-gray-400 mb-1">Priority</label>
                <select v-model="form.priority" id="priority"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option v-for="priority in priorities" :key="priority.value" :value="priority.value">
                    {{ priority.label }}
                  </option>
                </select>
              </div>
              <div>
                <label for="user_id" class="block text-sm font-medium text-gray-400 mb-1">Assign To</label>
                <select v-model="form.user_id" id="user_id"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Select an employee</option>
                  <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                    {{ employee.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="start_date" class="block text-sm font-medium text-gray-400 mb-1">Start Date</label>
                <InputWithIcon v-model="form.start_date" id="start_date" type="date" icon="calendar" />
              </div>
              <div>
                <label for="end_date" class="block text-sm font-medium text-gray-400 mb-1">End Date</label>
                <InputWithIcon v-model="form.end_date" id="end_date" type="date" icon="calendar" />
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-500">Create Task</StandardButton>
            </div>
          </form>
        </div>
      </div>
    </template>
    
    <style scoped>
    /* Add any additional styles here if needed */
    </style>