<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import { toast } from 'vue3-toastify';
import TaskCard from './TaskCard.vue';
import { VueDraggableNext } from 'vue-draggable-next';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  tasksByStatus: {
    type: Object,
    required: true
  },
  projectId: {
    type: Number,
    required: true
  }
});



const emit = defineEmits(['updateTaskStatus']);

const tasksList = ref({ 
  to_do: props.tasksByStatus.to_do || [],
  in_progress: props.tasksByStatus.in_progress || [],
  review: props.tasksByStatus.review || [],
  done: props.tasksByStatus.done || []
});



const onMove = (e) => {

  const fromStatus = e.from.getAttribute('data-status');
  const toStatus = e.to.getAttribute('data-status');
  const taskId = e.item.id;
  const projectId = props.projectId;


  if (fromStatus && toStatus && taskId) {
  router.post(`/projects/${projectId}/tasks/${taskId}/update-status`, { status: toStatus })
    .then(() => {
      // Acción en caso de éxito
      toast.success("Task status updated successfully!", {
        position: "top-right",
        autoClose: 3000,
        theme: 'dark'
      });
      
      emit('updateTaskStatus', { taskId, status: toStatus });
      router.visit(`/projects/${projectId}`);
    })
    .catch(err => {
      // Acción en caso de error
      toast.error("Error updating task status", {
        position: "top-right",
        autoClose: 3000,
        theme: 'dark'
      });
      console.error('Error al actualizar el estado:', err);
    });
}

  
};
const getStatusColor = (status) => {
  const colors = {
    to_do: 'bg-blue-600',
    in_progress: 'bg-yellow-600',
    review: 'bg-purple-600',
    done: 'bg-green-600'
  };
  return colors[status] || 'bg-gray-600';
};


</script>


<template>

  <div
    v-for="(tasks, status) in tasksList"
    :key="status"
    class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700"
  >
  <div class="bg-gray-800 p-4">
    <h3 class="text-white font-medium flex items-center justify-between">
      <span class="flex items-center">
        <div :class="[getStatusColor(status), 'w-3 h-3 rounded-full mr-2']"></div>
        {{ status.toUpperCase().replace('_', ' ') }}
      </span>
      <span class="bg-gray-700 text-xs px-2 py-1 rounded-full">{{ tasks.length }}</span>
    </h3>
  </div>

    <VueDraggableNext
      :list="tasks"
      :group="{ name: 'tasks', pull: true, put: true }"
      item-key="id"
      @end="onMove"
      :data-status="status" 
      class="h-full overflow-y-auto "
    >
      <div v-for="task in tasks" :key="task.id" class="py-2 px-3  " :id="task.id">
        <TaskCard :task="task" />
      </div>
    </VueDraggableNext>
  </div>

</template>