<script setup>
    import { ref, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import Layout from '@/Layouts/Layout.vue';
    import StandardButton from '@/Components/StandardButton.vue';
    import InputWithIcon from '@/Components/InputWithIcon.vue';
    import 'boxicons';
    
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
    
    const searchQuery = ref('');
    const showAddForm = ref(false);
    
    const form = useForm({
      title: '',
      content: '',
      is_public: false
    });
    
    const filteredDocumentations = ref([]);
    
    onMounted(() => {
      filteredDocumentations.value = props.documentations;
    });
    
    const filterDocumentations = () => {
      filteredDocumentations.value = props.documentations.filter(doc => 
        doc.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        doc.content.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    };
    
    const submitForm = () => {
      form.post(`/projects/${props.project.id}/documentation`, {
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
      <Layout pageTitle="Project Documentation">
        <div class="min-h-screen bg-black text-gray-300">
          <div class="border-b border-gray-800 px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-8">
                <a @click="$inertia.visit(`/projects/${project.id}`)"
                  class="text-gray-300 hover:text-white cursor-pointer flex items-center space-x-2 transition-colors">
                  <box-icon name='arrow-back' color='#E5E7EB'></box-icon>
                  <span>Back to Project</span>
                </a>
                <h1 class="text-xl font-semibold text-white">{{ project.name }} - Documentation</h1>
              </div>
              <div class="flex items-center space-x-4">
                <StandardButton @click="showAddForm = !showAddForm">
                  {{ showAddForm ? 'Cancel' : '+ Add Documentation' }}
                </StandardButton>
              </div>
            </div>
          </div>
    
          <div class="p-6">
            <div v-if="showAddForm" class="bg-gray-950 rounded-lg p-6 mb-6 border border-gray-700">
              <h2 class="text-xl font-semibold text-white mb-4">Add New Documentation</h2>
              <form @submit.prevent="submitForm">
                <div class="mb-4">
                  <label for="title" class="block text-sm font-medium text-gray-400 mb-1">Title</label>
                  <input v-model="form.title" id="title" type="text" class="w-full bg-gray-900 text-white border border-gray-700 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                  <label for="content" class="block text-sm font-medium text-gray-400 mb-1">Content (Markdown)</label>
                  <textarea v-model="form.content" id="content" rows="6" class="w-full bg-gray-900 text-white border border-gray-700 rounded-md p-2" required></textarea>
                </div>
                <div class="mb-4 flex items-center">
                  <input v-model="form.is_public" id="is_public" type="checkbox" class="mr-2">
                  <label for="is_public" class="text-sm font-medium text-gray-400">Make public</label>
                </div>
                <StandardButton type="submit" :disabled="form.processing">Save Documentation</StandardButton>
              </form>
            </div>
    
            <div class="mb-6">
              <InputWithIcon icon="search" v-model="searchQuery" @input="filterDocumentations" placeholder="Search documentation..." class="w-full" type="text" />
            </div>
    
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="doc in filteredDocumentations" :key="doc.id" class="bg-gray-950 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-2">{{ doc.title }}</h3>
                <p class="text-gray-400 mb-4 line-clamp-3">{{ doc.content }}</p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                  <span>Created: {{ formatDate(doc.created_at) }}</span>
                  <span v-if="doc.is_public" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">Public</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Layout>
    </template>
    
    <style scoped>
    /* Add any component-specific styles here */
    </style>