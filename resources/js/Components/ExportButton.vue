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
    const exportStatus = ref('idle'); // idle, processing, completed

    // Check for flash messages from the server
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


        router.get(`/projects/${props.projectId}/export`, {}, {
            preserveState: true,
            onError: (errors) => {
                console.error('Export error:', errors);
                toast.error('An error occurred during export. Please try again.');
                isExporting.value = false;
                exportStatus.value = 'idle';
            }
        });


        checkExportStatus();
    };

    const checkExportStatus = async () => {
        try {
            const response = await fetch(`/projects/${props.projectId}/export-status`);
            let data = '';
            if (response.ok) {
                data = await response.json();
                console.log("data", data.status);
            } else {
                console.error('Error: ', response.statusText);
            }

            if (data.status === 'completed') {
                exportStatus.value = 'completed';
                isExporting.value = false;
                toast.success('Export completed! Your file is ready for download.');

                console.log(data.download_url);
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
</script>

<template>
    <StandardButton @click="exportProject" :disabled="isExporting" class="flex items-center space-x-2">
        <box-icon name='download'></box-icon>
        <span v-if="exportStatus === 'idle'">Export to Excel</span>
        <span v-else-if="exportStatus === 'processing'">Processing...</span>
        <span v-else>Download</span>
    </StandardButton>
</template>