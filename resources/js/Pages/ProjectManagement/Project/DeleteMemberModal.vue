<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import StandardButton from '@/Components/StandardButton.vue';
    import { toast } from 'vue3-toastify';
    
    const props = defineProps({
      isOpen: Boolean,
      memberToDelete: Object,
      project: Object,
    });
    
    const emit = defineEmits(['close', 'confirm-delete']);
    
    const confirmDelete = () => {
      console.log(`/projects/${props.project.id}/members/${props.memberToDelete.id}`);
        router.delete(`/projects/${props.project.id}/members/${props.memberToDelete.id}`, {
            onSuccess: () => {
               toast.success('Member deleted successfully');
               emit('confirm-delete');
               emit('close');
            }
        })
        
    };
    </script>
    
    <template>
      <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl rounded-lg w-full max-w-sm sm:max-w-lg h-auto border border-gray-700 shadow-lg">
          <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center bg-gray-950 dark:bg-gray-100 rounded-t-lg">
            <h2 class="text-xl sm:text-2xl font-semibold text-white dark:text-black">Delete Member</h2>
            <button @click="emit('close')" class="text-gray-400 cursor-pointer hover:text-white transition-colors">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
          <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div class="text-white dark:text-black">
              <p class="mb-2">Are you sure you want to delete this member from the project?</p>
              <div class="flex items-center justify-center">
                <div class="flex items-center justify-center mt-1 bg-gray-700 dark:bg-gray-200 dark:shadow-xl rounded-lg transition-colors p-2 space-x-3">
                  <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white">
                    {{ memberToDelete.name.charAt(0) }}
                  </div>
                  <span class="text-white dark:text-black">{{ memberToDelete.name }}</span>
                </div>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-6">
              <StandardButton @click="confirmDelete" class="bg-red-600 hover:bg-red-500 dark:bg-red-500 dark:hover:bg-red-600 transition-colors">
                Delete
              </StandardButton>
            </div>
          </div>
        </div>
      </div>
    </template>
    