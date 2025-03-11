<script setup>
  import { ref, watchEffect, computed, toRaw, watch } from 'vue';
  import { router } from '@inertiajs/vue3'
  import { toast } from 'vue3-toastify';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  import SelectWithIcon from '@/Components/SelectWithIcon.vue';
  import StandardButton from '@/Components/StandardButton.vue';
  
  const emit = defineEmits(['close']);
  const props = defineProps({
    isOpen: {
      type: Boolean,
      required: true
    },
    departmentHead: {
      type: Array,
      required: true
    },
    project: {
      type: Object,
      required: true
    },
    userDepartments: {
      type: Array,
      required: true
    }
  
  });
  
  const isDarkMode = ref(false);
  
  function checkDarkMode() {
    isDarkMode.value = document.documentElement.classList.contains('dark');
    return isDarkMode.value;
  }
  
  const projectName = ref('');
  const departmentInput = ref('');
  const selectedDepartments = ref(props.userDepartments || []);
  
  
  const errors = ref({});
  
  const projectLeader = ref('');
  const priority = ref('');
  const startDate = ref('');
  const endDate = ref('');
  const assignedHours = ref('');
  const description = ref('');
  const attachments = ref([]);
  const isPrivate = ref(false);
  const showDeleteModal = ref(false);
  const status = ref('');
  
  
  watchEffect(() => {
    if (props.project) {
      projectName.value = props.project.name || '';
      selectedDepartments.value = props.project.departments || [];
      projectLeader.value = props.project.project_leader_id || '';
      priority.value = props.project.priority || '';
      startDate.value = props.project.start_date || '';
      endDate.value = props.project.end_date || '';
      assignedHours.value = props.project.assigned_hours || '';
      description.value = props.project.description || '';
      isPrivate.value = !props.project.is_public;
      status.value = props.project.status || '';
    }
  
  
  });
  
  watch(() => props.isOpen, (newValue) => {
    if (newValue) {
      // When modal opens, clear errors
      errors.value = {};
    } else {
      // When modal closes, clear errors
      errors.value = {};
    }
  });
  
  
  
  const handleFileUpload = (event) => {
    attachments.value = Array.from(event.target.files);
  };
  
  const createProject = () => {
    const data = {
      projectName: projectName.value,
      department_ids: selectedDepartments.value.map(dept => dept.id),
      projectLeader: projectLeader.value,
      priority: priority.value,
      startDate: startDate.value,
      endDate: endDate.value,
      assignedHours: assignedHours.value,
      description: description.value,
      attachments: attachments.value,
      isPublic: !isPrivate.value,
      status: status.value
    };
  
    router.put(`/projects/${props.project.id}`, data, {
      onSuccess: () => {
        toast.success('Project updated successfully');
        emit('close');
      },
      onError: (err) => {
        toast.error('An error occurred. Please try again.');
        errors.value = err;
  
  
      }
    });
  
  
  };
  
  
  
  
  
  
  const openDeleteModal = () => {
    showDeleteModal.value = true;
    console.log(showDeleteModal.value)
  };
  
  const closeDeleteModal = () => {
    showDeleteModal.value = false;
  };
  
  const deleteProject = () => {
    router.delete(`/projects/${props.project.id}`);
    closeDeleteModal();
  };
  
  // Department selection functionality
  const addDepartment = () => {
    const selectedDept = props.userDepartments.find(dept =>
      dept.label.toLowerCase() === departmentInput.value.toLowerCase() &&
      !selectedDepartments.value.some(selected => selected.id === dept.value)
    );
  
    if (selectedDept) {
      selectedDepartments.value.push({
        id: selectedDept.value,
        name: selectedDept.label
      });
      departmentInput.value = '';
    }
  };
  
  const removeDepartment = (dept) => {
    selectedDepartments.value = selectedDepartments.value.filter(d => d.id !== dept.id);
  };
  
  const filteredDepartments = computed(() => {
    if (departmentInput.value === '') {
      return [];
    }
  
    // Filtra los departamentos que no han sido seleccionados
    return props.userDepartments.filter(dept => {
      // Asegúrate de que el departamento no esté ya seleccionado
      const isAlreadySelected = selectedDepartments.value.some(selected => selected.id === dept.id);
  
      // Filtra los departamentos que coinciden con el input
      return !isAlreadySelected && dept.name.toLowerCase().includes(departmentInput.value.toLowerCase());
    });
  });
  
  
  
  const handleKeydown = (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      addDepartment();
    }
  };
  
  const closeModal = () => {
    errors.value = {};
    emit('close');
  };
  </script>
  
  <template>
    <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
      <div
        class="bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl rounded-lg w-full max-w-6xl h-auto border border-gray-700 shadow-lg">
        <div
          class="border-b border-gray-700 dark:bg-gray-100 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
          <h2 class="text-2xl font-semibold text-white dark:text-black">Edit Project</h2>
          <div class="flex items-center space-x-4">
            <button @click="openDeleteModal" class="text-red-500 cursor-pointer hover:text-red-400 transition-colors">
              <box-icon name='trash' type='solid' color='currentColor'></box-icon>
            </button>
            <button @click="closeModal"
              class="text-gray-400 dark:hover:text-black dark:text-gray-700 cursor-pointer hover:text-white transition-colors">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
        </div>
        <form @submit.prevent="createProject" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="projectName" class="block text-sm font-medium text-gray-400 mb-2">Project Name</label>
              <InputWithIcon v-model="projectName" icon="folder" placeholder="Enter project name" class="w-full" />
              <p v-if="errors.projectName" class="text-red-500 text-sm mt-1">{{ errors.projectName }}</p>
            </div>
  
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label for="department" class="block text-sm font-medium text-gray-400 mb-2">Departments</label>
              <div class="relative">
                <InputWithIcon v-model="departmentInput" icon="group" placeholder="Select departments" class="w-full"
                  @keydown="handleKeydown" />
                <p v-if="errors.department_ids" class="text-red-500 text-sm mt-1">{{ errors.department_ids }}</p>
                <div v-if="filteredDepartments.length > 0"
                  class="absolute z-10 w-full mt-1 bg-gray-900 border border-gray-700 rounded-lg shadow-lg">
                  <div v-for="dept in filteredDepartments" :key="dept.id"
                    @click="selectedDepartments.push({ id: dept.id, name: dept.name }); departmentInput = ''"
                    class="px-4 py-2 hover:bg-gray-800 cursor-pointer">
                    {{ dept.name }}
                  </div>
                </div>
              </div>
              <div class="flex flex-wrap gap-2 mt-2">
                <div v-for="dept in selectedDepartments" :key="dept.id"
                  class="bg-gray-900 dark:text-black dark:bg-gray-200 dark:border-none dark:shadow-xl text-gray-200 border border-gray-600 px-4 py-1 rounded-full text-sm flex items-center">
                  {{ dept.name }}
                  <button @click="removeDepartment(dept)" class="ml-2 mt-1 cursor-pointer focus:outline-none">
                    <box-icon name='x' :color="checkDarkMode() ? 'black' : 'white'" size="sm"></box-icon>
                  </button>
                </div>
              </div>
  
            </div>
  
            <div>
              <label for="projectLeader" class="block text-sm font-medium text-gray-400 mb-2">Project Leader</label>
              <SelectWithIcon v-model="projectLeader" icon="user" placeholder="Select project leader" class="w-full"
                :options="[
                  { label: 'Select the project leader', value: '' },
                  ...departmentHead.map(leader => ({ label: leader.label, value: leader.value }))
                ]" />
                <p v-if="errors.projectLeader" class="text-red-500 text-sm mt-1">{{ errors.projectLeader }}</p>
            </div>
  
            <div>
              <label for="priority" class="block text-sm font-medium text-gray-400 mb-2">Priority</label>
              <SelectWithIcon v-model="priority" icon="flag" placeholder="Select priority" class="w-full" :options="[
                { label: 'Low', value: 1 },
                { label: 'Medium', value: 2 },
                { label: 'High', value: 3 },
                { label: 'Urgent', value: 4 }
              ]" />
              <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority }}</p>
            </div>
  
            <div>
              <label for="startDate" class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
              <InputWithIcon v-model="startDate" icon="calendar" placeholder="Select start date" class="w-full"
                type="date" />
                <p v-if="errors.startDate" class="text-red-500 text-sm mt-1">{{ errors.startDate }}</p>
            </div>
  
            <div>
              <label for="endDate" class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
              <InputWithIcon v-model="endDate" icon="calendar" placeholder="Select end date" class="w-full" type="date" />
              <p v-if="errors.endDate" class="text-red-500 text-sm mt-1">{{ errors.endDate }}</p>
            </div>
  
            <div>
              <label for="assignedHours" class="block text-sm font-medium text-gray-400 mb-2">Assigned Hours</label>
              <InputWithIcon v-model="assignedHours" icon="time" placeholder="Enter assigned hours" class="w-full"
                type="number" />
                <p v-if="errors.assignedHours" class="text-red-500 text-sm mt-1">{{ errors.assignedHours }}</p>
            </div>
            <div>
              <label for="assignedHours" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
              <SelectWithIcon v-model="status" icon="check-square" placeholder="Select status" class="w-full" :options="[
                { label: 'In Progress', value: 'in_progress' },
                { label: 'Finished', value: 'finished' },
                { label: 'Suspended', value: 'suspended' },
              ]" />
            </div>
  
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
              <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
              <textarea v-model="description"
                class="w-full h-32 bg-gray-900 p-3 border border-gray-700 dark:bg-gray-100 dark:text-black rounded-lg focus:outline-none focus:border-gray-300 text-white placeholder-gray-500"
                placeholder="Enter project description"></textarea>
            </div>
  
          </div>
  
          <div class="flex justify-between items-center mt-8">
            <div class="flex items-center ">
  
              <label class="flex space-x-3 items-center cursor-pointer group">
                <div class="flex items-center space-x-1">
                  <box-icon name='lock-alt' color="#99A1AF"></box-icon>
                  <span
                    class="text-sm mt-1 text-gray-400 dark:group-hover:text-gray-500 group-hover:text-gray-300 transition-colors duration-200 ease-in-out">
                    Private
                  </span>
                </div>
                <div class="relative">
                  <input type="checkbox" v-model="isPrivate" class="sr-only" />
                  <div
                    class="w-5 h-5 bg-gray-700 dark:bg-gray-100 border-2 border-gray-600 rounded-md transition-all duration-200 ease-in-out group-hover:border-gray-500">
                    <svg
                      class="w-3 h-3 text-blue-500 opacity-0 transition-opacity duration-200 ease-in-out absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                      :class="{ 'opacity-100': isPrivate }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
  
              </label>
            </div>
            <div class="flex space-x-3">
              <StandardButton type="button" @click="closeModal"
                class="bg-gray-600 hover:bg-gray-500 transition-colors">
                Cancel
              </StandardButton>
              <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-500 transition-colors">
                Edit Project
              </StandardButton>
            </div>
          </div>
        </form>
      </div>
      <div v-if="showDeleteModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-md p-6 border border-gray-700 shadow-lg">
          <h3 class="text-xl font-semibold text-white dark:text-black mb-4">Are you sure you want to delete this project?</h3>
          <p class="text-gray-400 dark:text-gray-600 mb-6">This action cannot be undone.</p>
          <div class="flex justify-end space-x-4">
            <StandardButton @click="closeDeleteModal" class="bg-gray-600 hover:bg-gray-500 transition-colors">
              Cancel
            </StandardButton>
            <StandardButton @click="deleteProject" class="bg-red-600 hover:bg-red-500 dark:bg-red-600 dark:hover:bg-red-500 transition-colors">
              Delete
            </StandardButton>
          </div>
        </div>
      </div>
    </div>
  </template>
  