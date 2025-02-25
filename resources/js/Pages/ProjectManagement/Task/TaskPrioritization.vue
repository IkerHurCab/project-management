<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  import StandardButton from '@/Components/StandardButton.vue';

  const props = defineProps({
    projectId: {
      type: Number,
      required: true
    },
    recommendations: {
      type: Array, // Asumimos que es un array de recomendaciones
      default: () => []
    }
  });

  const loading = ref(false);

  const loadPriorityAssistant = async () => {
    loading.value = true;

    try {
      // Haces el GET para cargar las recomendaciones
      const response = await router.get(`/projects/${props.projectId}/task-priority`);
      recommendations.value = response.data.recommendations || [];
    } catch (error) {
      console.error('Error loading task priority recommendations:', error);
    } finally {
      loading.value = false;
    }
  };

  const recommendations = ref(props.recommendations);
</script>

<template>
  <div class="mt-8 bg-gray-950 rounded-lg overflow-hidden border w-full border-gray-700">
    <div class="bg-gray-800 p-4 flex items-center justify-between">
      <h3 class="text-white font-medium">Task Prioritization Assistant</h3>
      <StandardButton
        @click="updatePriorities"
        class="bg-indigo-600 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
      >
        Update Priorities
      </StandardButton>
    </div>
    <div class="p-8">
      <!-- Mostrar el botón solo si no hay recomendaciones y no se está cargando -->
      <div v-if="!loading" class="flex justify-center items-center">
        <StandardButton @click="loadPriorityAssistant">Load task priority recommendations</StandardButton>
      </div>

      <!-- Mostrar el mensaje de loading cuando el estado de carga es verdadero -->
      <div v-if="loading" class="flex justify-center items-center text-white">
        Loading recommendations...
      </div>

      <!-- Mostrar las recomendaciones solo si ya fueron cargadas -->
      <div v-if=" !loading">
        <ul class="text-white">
          <li v-for="(task, index) in recommendations" :key="index">
            {{ task }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
