<script setup>
    import { ref } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import StandardButton from '@/Components/StandardButton.vue';
    
    const props = defineProps({
      isOpen: Boolean,
      taskId: Number,
    });
    
    const emit = defineEmits(['close', 'add-attachment']);
    
    const form = useForm({
      attachments: [],
    });
    
    const fileInput = ref(null);
    
    const handleFileUpload = (event) => {
      form.attachments = [...event.target.files];
    };
    
    const removeAttachment = (index) => {
      form.attachments.splice(index, 1);
    };
    
    const submitForm = () => {
      emit('add-attachment', form);
      form.reset();
      emit('close');
    };
    </script>
    
    <template>
      <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-950 dark:bg-white rounded-lg w-full max-w-lg sm:max-w-xs md:max-w-md lg:max-w-lg h-auto border border-gray-700 shadow-lg 
        max-h-[90vh] overflow-y-auto">
          <div class="border-b border-gray-700 dark:bg-gray-100 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
            <h2 class="text-2xl font-semibold text-white dark:text-black">Add Attachment</h2>
            <button @click="emit('close')" class="text-gray-400 dark:text-gray-700 dark:hover:text-black cursor-pointer hover:text-white transition-colors">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Attachments</label>
              <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-800 dark:bg-gray-100 dark:hover:bg-gray-200 hover:bg-gray-700 transition-all">
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
            <div class="flex justify-end space-x-3 mt-7">
             
              <StandardButton type="submit" class="bg-blue-600 w-full hover:bg-blue-500 transition-colors">Add Attachment</StandardButton>
            </div>
          </form>
        </div>
      </div>
    </template>
    
    