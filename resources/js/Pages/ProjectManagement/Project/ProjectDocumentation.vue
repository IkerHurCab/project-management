<script setup>
  import { ref, onMounted } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import Layout from '@/Layouts/Layout.vue';
  import StandardButton from '@/Components/StandardButton.vue';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  import SingleDocumentation from '@/Pages/ProjectManagement/Project/SingleDocumentation.vue';
  import DocumentationModal from '@/Pages/ProjectManagement/Project/DocumentationModal.vue';
  
  import 'boxicons';

  
  const props = defineProps({
    project: {
      type: Object,
      required: true
    },
    documentations: {
      type: Array,
      required: true
    },
    openSingleDoc: {
      type: Boolean
    },
    createDoc: {
      type: Boolean
    }
  });




  const isEdit = ref(false);
  const createDoc = ref(false);
  const selectedDocumentation = ref(null);

  const selectDocumentation = (doc) => {
    selectedDocumentation.value = doc;
    openSingleDoc.value = true;

  }

 
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
    filteredDocumentations.value = props.documentations;
  });

  const filterDocumentations = () => {
    filteredDocumentations.value = props.documentations.filter(doc =>
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
      <div class="mb-6 flex w-full gap-x-4">
        <InputWithIcon 
          icon="search" 
          v-model="searchQuery" 
          @input="filterDocumentations"
          placeholder="Search documentation..." 
          class="flex-grow"
          type="text" 
        />
        <StandardButton 
          @click="createDoc = true; openSingleDoc = true" 
          
          class="py-2">
          + Add Documentation
        </StandardButton>
      </div>
      
      
      <div class="flex flex-row gap-6">
        <div v-for="doc in filteredDocumentations" :key="doc.id" @click="selectDocumentation(doc)"
          class="bg-gray-950 cursor-pointer rounded-lg p-6 border border-gray-700 lg:w-1/3 md:1/2 sm:w-full hover:bg-gray-900 group">
          <h3 class="text-lg font-semibold text-white mb-2 group-hover:underline">{{ doc.title }}</h3>
          <p class="text-gray-400 mb-4 line-clamp-3">{{ doc.summary }}</p>
          <div class="flex justify-between items-center text-sm text-gray-500">
            <span>Created: {{ formatDate(doc.created_at) }}</span>
            <span v-if="doc.is_public" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">Public</span>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="p-6 w-full">
      <div class="flex justify-between">
        <div @click="openSingleDoc = false; isEdit = false" class="flex hover:font-semibold flex-row gap-x-4 cursor-pointer">
          <box-icon name='arrow-back' color='#ffffff'></box-icon>
          <h2>Back to Documents</h2>
        </div>
        <div v-if="!isEdit && !createDoc ">
          <StandardButton @click="isEdit = true">Edit Documentation</StandardButton>
        </div>
      </div>
      <div class="mt-5">
        <div v-if="!isEdit && selectedDocumentation && !createDoc">
          <SingleDocumentation :selectedDocumentation="selectedDocumentation"/>
        </div>
        
        <div v-if="isEdit">
          <DocumentationModal :documentation="selectedDocumentation" :isEdit="true" :project="project" @updateIsEdit="isEdit = $event" />
        </div>
        <div v-if="createDoc && !isEdit">
          <DocumentationModal :isEdit="false" :project="project"  />
        </div>
   
      </div>
    </div>




  </div>
</template>