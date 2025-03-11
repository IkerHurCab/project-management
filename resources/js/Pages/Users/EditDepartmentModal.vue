<script setup>
import { ref, watchEffect, computed } from 'vue';
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import SelectWithIcon from '@/Components/SelectWithIcon.vue';
import StandardButton from '@/Components/StandardButton.vue';
import { usePage } from '@inertiajs/vue3';

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
  department: {
    type: Object,
    required: false,
    default: () => ({})
  },
  users: {
    type: Array,
    required: false,
    default: () => []
  }
});

const isDarkMode = ref(false);

function checkDarkMode() {
  isDarkMode.value = document.documentElement.classList.contains('dark');
  return isDarkMode.value;
}

const departmentName = ref('');
const description = ref('');
const selectedDepartmentHeads = ref([]);
const showDeleteModal = ref(false);

watchEffect(() => {
  if (props.department) {
    departmentName.value = props.department.name || '';
    description.value = props.department.description || '';
    selectedDepartmentHeads.value = props.departmentHead || [];
  }
});

const updateDepartment = () => {
  const data = {
    name: departmentName.value,
    description: description.value,
    department_head_ids: selectedDepartmentHeads.value.map(head => head.id || head.value)
  };

  router.put(`/departments/${props.department.id}`, data, {
    onSuccess: () => {
      toast.success('Department updated successfully');
      emit('close');
    },
    onError: (errors) => {
      toast.error('An error occurred. Please try again.');
    }
  });
};

const openDeleteModal = () => {
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
};

const deleteDepartment = () => {
  router.delete(`/departments/${props.department.id}`, {
    onSuccess: () => {
      toast.success('Department deleted successfully');
      emit('close');
    },
    onError: () => {
      toast.error('Failed to delete department');
    }
  });
  closeDeleteModal();
};

// Department head selection functionality
const departmentHeadInput = ref('');

// Use all department users as potential department heads
const availableDepartmentHeads = computed(() => {
  if (departmentHeadInput.value === '') {
    return [];
  }
  
  // Use the users prop (all department members) instead of just departmentHead
  return props.users.filter(user => {
    // Check if user is already selected as a department head
    const isAlreadySelected = selectedDepartmentHeads.value.some(
      selected => (selected.id || selected.value) === user.id
    );
    
    // Filter by name matching the input
    return !isAlreadySelected && 
           user.name.toLowerCase().includes(departmentHeadInput.value.toLowerCase());
  });
});

const addDepartmentHead = () => {
  const selectedUser = props.users.find(user => {
    return user.name.toLowerCase() === departmentHeadInput.value.toLowerCase() &&
      !selectedDepartmentHeads.value.some(selected => 
        (selected.id || selected.value) === user.id
      );
  });

  if (selectedUser) {
    selectedDepartmentHeads.value.push({
      id: selectedUser.id,
      name: selectedUser.name
    });
    departmentHeadInput.value = '';
  }
};

const removeDepartmentHead = (head) => {
  selectedDepartmentHeads.value = selectedDepartmentHeads.value.filter(
    h => (h.id || h.value) !== (head.id || head.value)
  );
};

const handleKeydown = (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    addDepartmentHead();
  }
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div
      class="bg-gray-950 max-h-[90vh] overflow-y-auto dark:bg-white dark:border-none dark:shadow-xl rounded-lg w-full max-w-2xl h-auto border border-gray-700 shadow-lg">
      <div
        class="border-b border-gray-700 dark:bg-gray-100 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
        <h2 class="text-2xl font-semibold text-white dark:text-black">Edit Department</h2>
        <div class="flex items-center space-x-4">
          <button @click="openDeleteModal" class="text-red-500 cursor-pointer hover:text-red-400 transition-colors">
            <box-icon name='trash' type='solid' color='currentColor'></box-icon>
          </button>
          <button @click="emit('close')"
            class="text-gray-400 dark:hover:text-black dark:text-gray-700 cursor-pointer hover:text-white transition-colors">
            <box-icon name='x' color='currentColor'></box-icon>
          </button>
        </div>
      </div>
      <form @submit.prevent="updateDepartment" class="p-6 space-y-6">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label for="departmentName" class="block text-sm font-medium text-gray-400 mb-2">Department Name</label>
            <InputWithIcon v-model="departmentName" icon="building" placeholder="Enter department name" class="w-full" />
          </div>

          <div>
            <label for="departmentHeads" class="block text-sm font-medium text-gray-400 mb-2">Department Heads</label>
            <div class="relative">
              <InputWithIcon v-model="departmentHeadInput" icon="user" placeholder="Select department heads" class="w-full"
                @keydown="handleKeydown" />
              <div v-if="availableDepartmentHeads.length > 0"
                class="absolute z-10 w-full mt-1 bg-gray-900 dark:bg-gray-100 border border-gray-700 rounded-lg shadow-lg">
                <div v-for="user in availableDepartmentHeads" :key="user.id"
                  @click="selectedDepartmentHeads.push({ id: user.id, name: user.name }); departmentHeadInput = ''"
                  class="px-4 py-2 hover:bg-gray-800 dark:hover:bg-gray-200 cursor-pointer">
                  {{ user.name }}
                </div>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-2">
              <div v-for="head in selectedDepartmentHeads" :key="head.id || head.value"
                class="bg-gray-900 dark:text-black dark:bg-gray-200 dark:border-none dark:shadow-xl text-gray-200 border border-gray-600 px-4 py-1 rounded-full text-sm flex items-center">
                {{ head.name || head.label }}
                <button @click="removeDepartmentHead(head)" class="ml-2 mt-1 cursor-pointer focus:outline-none">
                  <box-icon name='x' :color="checkDarkMode() ? 'black' : 'white'" size="sm"></box-icon>
                </button>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
            <textarea v-model="description"
              class="w-full h-32 bg-gray-900 p-3 border border-gray-700 dark:bg-gray-100 dark:text-black rounded-lg focus:outline-none focus:border-gray-300 text-white placeholder-gray-500"
              placeholder="Enter department description"></textarea>
          </div>
        </div>

        <div class="flex justify-end items-center mt-8 space-x-3">
          <StandardButton type="button" @click="emit('close')"
            class="bg-gray-600 hover:bg-gray-500 transition-colors">
            Cancel
          </StandardButton>
          <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-500 transition-colors">
            Update Department
          </StandardButton>
        </div>
      </form>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
      <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-md p-6 border border-gray-700 shadow-lg">
        <h3 class="text-xl font-semibold text-white dark:text-black mb-4">Are you sure you want to delete this department?</h3>
        <p class="text-gray-400 dark:text-gray-600 mb-6">This action cannot be undone. All members will be removed from this department.</p>
        <div class="flex justify-end space-x-4">
          <StandardButton @click="closeDeleteModal" class="bg-gray-600 hover:bg-gray-500 transition-colors">
            Cancel
          </StandardButton>
          <StandardButton @click="deleteDepartment" class="bg-red-600 hover:bg-red-500 dark:bg-red-600 dark:hover:bg-red-500 transition-colors">
            Delete
          </StandardButton>
        </div>
      </div>
    </div>
  </div>
</template>