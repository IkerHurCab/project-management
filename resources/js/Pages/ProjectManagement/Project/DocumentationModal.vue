<script setup>
  import { ref, watch, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  import { toast } from 'vue3-toastify';
  import StandardButton from '@/Components/StandardButton.vue';

  const props = defineProps({
    isEdit: {
      type: Boolean,
      required: true,
    },
    project: {
      type: Object,
      required: true,
    },
    documentation: {
      type: Object
    }
  });

  const emit = defineEmits(['updateIsEdit']);

  const form = ref({
    title: '',
    summary: '',
    content: '',
    is_public: false,
  });

  // Actualizar los campos del formulario si estamos en modo de edición
  watch(() => props.isEdit, (newVal) => {
    if (newVal && props.documentation) {
      form.value.title = props.documentation.title || '';
      form.value.summary = props.documentation.summary || '';
      form.value.content = props.documentation.content || '';
      form.value.is_public = props.documentation.is_public || false;
    } else {
      form.value = {
        title: '',
        summary: '',
        content: '',
        is_public: false,
      };
    }
  }, { immediate: true });

  const submitForm = async () => {
  try {
    if (props.isEdit) {
      await router.put(`/projects/${props.project.id}/documentation/${props.documentation.id}`, form.value, {
        onSuccess: () => {
          toast.success('Document updated successfully');
        }
      });
    } else {
      await router.post(`/projects/${props.project.id}/documentation`, form.value, {
        onSuccess: () => {
          toast.success('Document created successfully');
        }
      });
    }

  } catch (error) {
    console.error('Error al enviar el formulario:', error);
  }
};


  const deleteDocument = async (documentId) => {
    if (confirm("¿Seguro que quieres eliminar este documento?")) {
      await router.delete(`/projects/${props.project.id}/documentation/${props.documentation.id}`), {
        onSuccess: () => {
          toast.success('Document deleted successfully');
          emit('updateIsEdit', false);
        }
      }
    }
  };

  const resetForm = () => {
    form.value = {
      title: '',
      summary: '',
      content: '',
      is_public: false,
    };
    emit('updateIsEdit', false);  // Emitir evento para cambiar el valor de isEdit en el componente padre
  };
</script>

<template>
  <div>
    <form @submit.prevent="submitForm" class="space-y-4">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-400 mb-1">Title</label>
        <input v-model="form.title" id="title" type="text"
          class="w-full bg-gray-900 text-white border border-gray-700 rounded-md p-2" required />
      </div>

      <!-- Summary -->
      <div>
        <label for="summary" class="block text-sm font-medium text-gray-400 mb-1">Summary</label>
        <input v-model="form.summary" id="summary" type="text"
          class="w-full bg-gray-900 text-white border border-gray-700 rounded-md p-2" required />
      </div>

      <!-- Content input (Markdown) -->
      <div>
        <label for="content" class="block text-sm font-medium text-gray-400 mb-1">Content (Markdown)</label>
        <textarea v-model="form.content" id="content" rows="12"
          class="w-full bg-gray-900 text-white border border-gray-700 rounded-md p-2" required></textarea>
      </div>


      <div class="flex items-center">
        <input v-model="form.is_public" id="is_public" type="checkbox" class="mr-2">
        <label for="is_public" class="text-sm font-medium text-gray-400">Make public</label>
      </div>
      <div :class="['flex', props.isEdit ? 'justify-between' : 'justify-end']">
        <button v-if="props.isEdit" @click="deleteDocument" type="button"
          class="hover:border borderflex items-center flex text-red-500 hover:border rounded-lg p-2 hover:text-red-700 transition-colors duration-200 space-x-2">
          <box-icon name='trash' color='#fd0303'></box-icon>
          <span>Delete</span>
        </button>
        <div class="flex justify-end gap-x-4">
          <StandardButton type="submit">{{ props.isEdit ? 'Update' : 'Create' }}</StandardButton>
        </div>
      </div>
      
    </form>
  </div>
</template>