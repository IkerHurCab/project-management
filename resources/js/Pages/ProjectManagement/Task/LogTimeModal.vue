<script setup>
  import { ref } from 'vue';
  import { useForm, router } from '@inertiajs/vue3';
  import StandardButton from '@/Components/StandardButton.vue';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  
  const props = defineProps({
    isOpen: Boolean,
    task: Object,
    user: Object,
    project: Object,
  });
  
  const emit = defineEmits(['close', 'log-time']);
  
  const form = useForm({
    hours: '',
    description: '',
    date: new Date().toISOString().substr(0, 10), 
  });
  
  const submitForm = () => {
    form.task_id = props.task.task_id
    form.user_id = props.user.id


    router.post(`/projects/${props.project.id}/task/${props.task.id}/task-log`, form, {
      onSuccess: () => {
      form.reset();
      emit('close'); // Cierra el modal
      emit('log-time', { // Emite el evento log-time al componente padre
        task_id: form.task_id,
        user_id: form.user_id,
        log_time: form.hours,
        log_date: form.date,
        description: form.description,
      });
    },
    })

  };
</script>

<template>
<div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
  <div class="bg-gray-950 rounded-lg w-full max-w-lg h-auto border border-gray-700 shadow-lg">
    <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
      <h2 class="text-2xl font-semibold text-white">Log Time</h2>
      <button @click="emit('close')" class="text-gray-400 cursor-pointer hover:text-white transition-colors">
        <box-icon name='x' color='currentColor'></box-icon>
      </button>
    </div>
    <form @submit.prevent="submitForm" class="p-6 space-y-6">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col">
          <label for="hours" class="block text-sm font-medium text-gray-300 mb-1">Hours Worked</label>
          <InputWithIcon v-model="form.hours" id="hours" type="number" icon="time" placeholder="Hours worked..." class="bg-gray-950 border-gray-700 text-white w-full" />
        </div>
        <div class="flex flex-col">
          <label for="date" class="block text-sm font-medium text-gray-300 mb-1">Date</label>
          <InputWithIcon v-model="form.date" id="date" type="date" icon="calendar" class="bg-gray-950 border-gray-700 text-white w-full" />
        </div>
      </div>
      <div>
        <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
        <textarea v-model="form.description" id="description" rows="4" 
                  class="w-full bg-gray-800 border border-gray-700 rounded-md text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                  placeholder="What did you do today?"></textarea>
      </div>
      
      
      <div class="flex justify-center py-2 mt-6">
        <StandardButton type="submit" class="bg-blue-600 w-full hover:bg-blue-500 transition-colors">Log Time</StandardButton>
      </div>
    </form>
  </div>
</div>
</template>
