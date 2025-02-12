<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
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
          emit('updateTaskStatus', { taskId, status: toStatus });
          router.visit(`/projects/${projectId}`);
        })
        .catch(err => {
          console.error('Error al actualizar el estado:', err);
        });
    }
  
};


</script>


<template>
  <div
    v-for="(tasks, status) in tasksList"
    :key="status"
    class="bg-gray-950 rounded-lg overflow-hidden border border-gray-700"
  >
    <h3 class="text-white font-medium flex p-4 items-center justify-between">
      {{ status.toUpperCase().replace('_', ' ') }}
      <span class="bg-gray-700 text-xs px-2 py-1 rounded-full">{{ tasks.length }}</span>
    </h3>
    <hr class="border-t border-gray-700 mb-2" />

    <VueDraggableNext
      :list="tasks"
      :group="{ name: 'tasks', pull: true, put: true }"
      item-key="id"
      @end="onMove"
      :data-status="status" 
      class="h-full "
    >
      <div v-for="task in tasks" :key="task.id" class="py-2 px-3 " :id="task.id">
        <TaskCard :task="task" />
      </div>
    </VueDraggableNext>
  </div>
</template>