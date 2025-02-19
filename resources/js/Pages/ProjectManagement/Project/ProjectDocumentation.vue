<script setup>
    import { ref, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import Layout from '@/Layouts/Layout.vue';
    import StandardButton from '@/Components/StandardButton.vue';
    import InputWithIcon from '@/Components/InputWithIcon.vue';
    import SingleDocumentation from '@/Pages/ProjectManagement/Project/SingleDocumentation.vue';
    
    import 'boxicons';
  
    // Definición de los props
    const props = defineProps({
      project: {
        type: Object,
        required: true
      },
      documentations: {
        type: Array,
        required: true
      }
    });
  
   
    const project = ref({
      id: 1,
      name: 'Project Dummy',
    });
  
    const documentations = ref([
      {
        id: 1,
        title: 'Architecture Overview',
        content: 'This document provides an overview of the project architecture. It covers all components and their interactions.',
        is_public: true,
        created_at: '2025-01-01T12:00:00Z',
      },
      {
        id: 2,
        title: 'Task Management Strategy',
        content: 'Details the strategy for task assignment, workflow, and management tools to be used.',
        is_public: false,
        created_at: '2025-02-01T12:00:00Z',
      },
      {
        id: 3,
        title: 'Testing Procedures',
        content: 'Explains the testing process, including unit tests, integration tests, and user acceptance testing.',
        is_public: true,
        created_at: '2025-03-01T12:00:00Z',
      }
    ]);
  
    const searchQuery = ref('');
    const showAddForm = ref(false);
    const openSingleDoc = ref(false);
  
    const form = useForm({
      title: '',
      content: '',
      is_public: false
    });
  
    const filteredDocumentations = ref([]);
  
    onMounted(() => {
      filteredDocumentations.value = documentations.value;
    });
  
    const filterDocumentations = () => {
      filteredDocumentations.value = documentations.value.filter(doc => 
        doc.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        doc.content.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    };
  
    const submitForm = () => {
      form.post(`/projects/${project.value.id}/documentation`, {
        preserveScroll: true,
        onSuccess: () => {
          form.reset();
          showAddForm.value = false;
        },
      });
    };
  
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    };

   
  </script>
  
  <template>
    <div class="max-w-7xl">
        <div v-if="!openSingleDoc">        
        <div class="mb-6">
            <InputWithIcon icon="search" v-model="searchQuery" @input="filterDocumentations" placeholder="Search documentation..." class="w-full" type="text" />
          </div>
          <div class="flex flex-row gap-6">
            <div v-for="doc in filteredDocumentations" :key="doc.id" @click="openSingleDoc = true" class="bg-gray-950 cursor-pointer rounded-lg p-6 border border-gray-700 w-full hover:bg-gray-900 group">
              <h3 class="text-lg font-semibold text-white mb-2 group-hover:underline">{{ doc.title }}</h3>
              <p class="text-gray-400 mb-4 line-clamp-3">{{ doc.content }}</p>
              <div class="flex justify-between items-center text-sm text-gray-500">
                <span>Created: {{ formatDate(doc.created_at) }}</span>
                <span v-if="doc.is_public" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">Public</span>
              </div>
            </div>
          </div>
        </div>
        <div v-else>
            <SingleDocumentation />
        </div>

          
          
  
    </div>
  </template>
  
  
  
  
  