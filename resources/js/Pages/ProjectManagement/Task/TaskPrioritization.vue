<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  import StandardButton from '@/Components/StandardButton.vue'
  
  const props = defineProps({
    projectId: {
      type: Number,
      required: true
    },
    recommendations: {
      type: String, // Asumimos que es un string de recomendaciones
      default: ''
    }
  });

  // Estado local
  const loading = ref(false);  // Ahora `loading` es solo local al componente
  const recommendations = ref(props.recommendations);  // Inicializa con el valor recibido por props

  const updatePriorities = async () => {
    loading.value = true;

    try {
      const response = await router.post(`/api/projects/${props.projectId}/update-priorities`);
      // Asumiendo que la respuesta actualiza las prioridades, puedes manejar la respuesta si es necesario
    } catch (error) {
      console.error('Error updating priorities:', error);
    } finally {
      loading.value = false;
    }
  };

  const loadPriorityAssistant = async () => {
    loading.value = true; // Muestra el mensaje de "loading" al empezar a cargar

    try {
      // Haces el GET para cargar las recomendaciones
      const response = await router.get(`/projects/${props.projectId}/task-priority`);
      // Aquí esperas que la respuesta te pase un string con las recomendaciones
      recommendations.value = response.data.recommendations || ''; // Asumimos que el servidor pasa recommendations como string
    } catch (error) {
      console.error('Error loading task priority recommendations:', error);
    } finally {
      loading.value = false; // Deja de mostrar el mensaje de "loading" después de recibir las recomendaciones
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
      <div v-if="!recommendations && !loading" class="flex justify-center items-center">
        <StandardButton @click="loadPriorityAssistant">Load task priority recommendations</StandardButton>
      </div>

      <!-- Mostrar el mensaje de loading cuando el estado de carga es verdadero -->
      <div v-if="loading" class="flex justify-center items-center text-white">
        Loading recommendations...
      </div>

      <!-- Mostrar las recomendaciones solo si ya fueron cargadas -->
      <div v-if="recommendations && !loading">
        <p class="text-white">{{ recommendations }}</p>
      </div>
    </div>
  </div>
</template>
