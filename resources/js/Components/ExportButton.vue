<script setup>
  import { ref, onMounted } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  import { toast } from 'vue3-toastify';
  import StandardButton from '@/Components/StandardButton.vue';
  
  const props = defineProps({
    projectId: {
      type: Number,
      required: true
    }
  });
  
  const page = usePage();
  const isExporting = ref(false);
  const exportStatus = ref('idle'); 
  const downloadUrl = ref(null);
  
  onMounted(() => {
    if (page.props.flash && page.props.flash.message) {
      toast.info(page.props.flash.message);
      
      if (page.props.flash.exportStatus === 'processing') {
        exportStatus.value = 'processing';
        isExporting.value = true;
        checkExportStatus();
      }
    }
  });
  
  const exportProject = () => {
    isExporting.value = true;
    exportStatus.value = 'processing';
    
    // Use Inertia router to trigger the export
    router.get(`/projects/${props.projectId}/export`, {}, {
      preserveState: true,
      onError: (errors) => {
        console.error('Export error:', errors);
        toast.error('An error occurred during export. Please try again.');
        isExporting.value = false;
        exportStatus.value = 'idle';
      }
    });
    
    // Start polling for status
    checkExportStatus();
  };
  
  const checkExportStatus = async () => {
    try {
      const response = await fetch(`/projects/${props.projectId}/export-status`);
      const data = await response.json();
      
      if (data.status === 'completed') {
        exportStatus.value = 'completed';
        isExporting.value = false;
        downloadUrl.value = data.download_url;
        toast.success('PDF export completed! Your file is ready for download.');
        
        // Automatically trigger download
        window.location.href = data.download_url;
      } else {
        // Continue polling
        setTimeout(checkExportStatus, 3000);
      }
    } catch (error) {
      console.error('Status check error:', error);
      toast.error('Error checking export status.');
      isExporting.value = false;
      exportStatus.value = 'idle';
    }
  };
  
  const downloadReport = () => {
    if (downloadUrl.value) {
      window.location.href = downloadUrl.value;
    }
  };
  </script>
  
  <template>
    <StandardButton 
      @click="exportStatus === 'completed' ? downloadReport() : exportProject()" 
      :disabled="isExporting"
      class="flex items-center space-x-2"
    >
      <box-icon name='download' color='#ffffff'></box-icon>
      <span v-if="exportStatus === 'idle'">Export PDF Report</span>
      <span v-else-if="exportStatus === 'processing'">Generating PDF...</span>
      <span v-else>Download Report</span>
    </StandardButton>
  </template>
  
  