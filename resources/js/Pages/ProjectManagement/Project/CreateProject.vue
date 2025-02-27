  <script setup>
  import { ref } from 'vue';
  import Layout from '@/Layouts/Layout.vue';
  import StandardButton from '@/Components/StandardButton.vue';
  import InputWithIcon from '@/Components/InputWithIcon.vue';
  import SelectWithIcon from '@/Components/SelectWithIcon.vue';
  import { router } from '@inertiajs/vue3'

  import 'boxicons';
  // Props
  defineProps({
    departmentHead: Array,
  });

  const createProject = () => {
    // Datos a enviar al servidor
    const projectData = {
      name: projectName.value,
      company: company.value,
      project_leader_id: projectLeader.value,
      start_date: startDate.value,
      end_date: endDate.value,
      assigned_hours: assignedHours.value,
      status: 'in_progress',
      description: description.value,
      priority: priority.value,
      attachments: attachments.value, // Asegúrate de enviar los archivos correctamente (o manejarlos en el servidor)
    };

    // Enviar el formulario usando Inertia
    router.post('/projects', projectData, {
      onSuccess: () => {
        // Aquí puedes redirigir a otra página o mostrar un mensaje de éxito
        console.log('Proyecto creado con éxito');
      },
      onError: (errors) => {
        // Manejar los errores aquí (por ejemplo, mostrar mensajes de error)
        console.error('Hubo un error:', errors);
      }
    });
  };


  const projectName = ref('');
  const company = ref('');
  const projectLeader = ref('');
  const startDate = ref(getCurrentDate());
  const endDate = ref('');
  const assignedHours = ref('');
  const status = ref('');
  const description = ref('');
  const priority = ref(1);
  const attachments = ref([]);

  function getCurrentDate() {

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Meses en JS empiezan desde 0, sumamos 1
    const day = String(today.getDate()).padStart(2, '0'); // Aseguramos que el día tenga 2 dígitos
    return `${year}-${month}-${day}`;

  }

  const handleFileUpload = (event) => {
    attachments.value = Array.from(event.target.files);
  };
</script>

  <template>
    <Layout pageTitle="Project Management">
   
      <div class="flex flex-col bg-black text-gray-300 min-h-screen px-6 py-4">
        <div class="flex flex-row  gap-x-4 ">
          <div class="cursor-pointer flex items-center " @click="$inertia.visit('/projects')">

          
      <box-icon name='arrow-back' color='#fffdfd' ></box-icon>
    </div>
        <h1 class="text-3xl font-bold text-white ">Create Project</h1>
      </div>
        <div class="bg-gray-950 border border-gray-700 mt-5 rounded-lg overflow-hidden">
          <div class="border-b border-gray-700 p-6">
            <h3 class="text-xl font-semibold text-white">Project Details</h3>
          </div>

          <form @submit.prevent="createProject" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="col-span-1 md:col-span-2 lg:col-span-3">
                <label for="projectName" class="block text-sm font-medium text-gray-400 mb-2">Project Name</label>
                <InputWithIcon v-model="projectName" icon="folder" placeholder="Enter project name" class="w-full" />
              </div>

              <div>
                <label for="company" class="block text-sm font-medium text-gray-400 mb-2">Company</label>
                <InputWithIcon v-model="company" icon="building" placeholder="Enter company name" class="w-full"
                  type="text" />
              </div>

              <div>
                <label for="projectLeader" class="block text-sm font-medium text-gray-400 mb-2">Project Leader</label>
                <SelectWithIcon v-model="projectLeader" icon="user" placeholder="Select project leader" class="w-full"
                :options="[
    { label: 'Select the project leader', value: '' },
    ...departmentHead.map(leader => ({ label: leader.label, value: leader.value }))
  ]" />
              </div>
              <div>
                <label for="priority" class="block text-sm font-medium text-gray-400 mb-2">Priority</label>
                <SelectWithIcon v-model="priority" icon="flag" placeholder="Select priority" class="w-full" :options="[
                  { label: 'Low', value: 1 },
                  { label: 'Medium', value: 2 },
                  { label: 'High', value: 3 },
                  { label: 'Urgent', value: 4 }
                ]" />
              </div>
              

              <!-- Agrupar estos campos en una fila -->
              <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-6 col-span-1 md:col-span-1 lg:col-span-3">
                <div>
                  <label for="startDate" class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
                  <InputWithIcon v-model="startDate" icon="calendar" placeholder="Select start date" class="w-full"
                    type="date" />
                </div>

                <div>
                  <label for="endDate" class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
                  <InputWithIcon v-model="endDate" icon="calendar" placeholder="Select end date" class="w-full"
                    type="date" />
                </div>

                <div>
                  <label for="assignedHours" class="block text-sm font-medium text-gray-400 mb-2">Assigned Hours</label>
                  <InputWithIcon v-model="assignedHours" icon="time" placeholder="Enter assigned hours" class="w-full"
                    type="number" />
                </div>

                


              </div>



              <div class="col-span-1 md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                <textarea v-model="description"
                  class="w-full h-32 bg-gray-900 p-3 border border-gray-700 rounded-lg focus:outline-none focus:border-gray-300 text-white placeholder-gray-500"
                  placeholder="Enter project description"></textarea>
              </div>
              <div class="col-span-1 md:col-span-2 lg:col-span-3">
                
                <div v-if="attachments.length > 0" class="mt-2">
                  <p class="text-sm text-gray-400">{{ attachments.length }} file(s) selected</p>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
              
              <StandardButton type="submit" class="bg-blue-600 hover:bg-blue-700">
                Create Project

              </StandardButton>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  </template>
