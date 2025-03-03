<script setup>
import { ref, computed } from 'vue';
import NavButton from '../Components/NavButton.vue';
import { usePage, router } from '@inertiajs/vue3';



const user = computed(() => usePage().props.auth?.user);
const currentOrganization = computed(() => usePage().props.auth?.current_organization);
console.log(currentOrganization);
const userOrganizations = computed(() => usePage().props.auth?.user_organizations);

const organizationInitials = computed(() => {
  const name = currentOrganization.value?.name || "Org";
  return name.split(" ").map(word => word[0]).join("").toUpperCase();
});


const isPopupOpen = ref(false);


// Función para redirigir a la página de proyectos
const projects = () => {
  window.location.href = '/projects';
};

const departments = () => {
  window.location.href = '/departments';
};

const dashboard = () => {
  window.location.href = '/dashboard';
};  


// Función para cerrar sesión
const logout = () => {
  window.location.href = '/logout';
};

const settings = () => {
  window.location.href = '/settings';
};


const organization = () => {
  window.location.href = '/organization';
}

const addOrg = () => {
  window.location.href = '/organizations/create';
}


const changeOrganization = (id) => {
    router.post('/change-organization', { organization_id: id }, {
        onSuccess: () => {
            isPopupOpen.value = false;
        },
        onError: (errors) => {
            console.error('Error al cambiar de organización:', errors);
        }
    });
};

// Botones de navegación
const buttons = [
 
  { name: 'dashboard', type: 'solid', action: dashboard },
  { name: 'folder', action: projects }, // Aseguramos que el botón de "Projects" tenga acción
  //{ name: 'message-square' },
 // { name: 'calendar' },
  { name: 'group', action: departments },
  { name: 'building-house', action: organization},
 // { name: 'time-five' }
];

const lowButtons = [{ name: 'cog' }];

// Estado para manejar la línea activa
const activeTopIndex = ref(null);
const activeBottomIndex = ref(null);

const createLineStyle = (activeIndex) => computed(() => {
  return activeIndex.value === null
    ? { opacity: 0, transform: 'translateY(0)' }
    : { opacity: 1, transform: `translateY(${activeIndex.value * 56}px)`, height: '48px' };
});

const topLineStyle = createLineStyle(activeTopIndex);
const bottomLineStyle = createLineStyle(activeBottomIndex);
</script>

<template>
  <div class="fixed top-0 left-0 h-full w-16 flex flex-col justify-between py-4 bg-black text-white">
    <!-- Logo de la organización -->
    <div class="flex justify-center cursor-pointer" @click="isPopupOpen = true">
      <template v-if="currentOrganization?.organization_logo">
        <img :src="`/storage/${currentOrganization.organization_logo}`" alt="Org"
          class="w-12 h-12 rounded object-cover" />
      </template>
      <template v-else>
        <div class="w-12 h-12 flex items-center justify-center rounded bg-gray-700 text-white font-bold">
          {{ organizationInitials }}
        </div>
      </template>
    </div>

    <!-- Sección superior -->
    <div class="relative flex flex-col space-y-2" @mouseleave="activeTopIndex = null">
      <div class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out" :style="topLineStyle"></div>
      <div class="flex flex-col items-center">
        <NavButton v-for="(button, index) in buttons" :key="index" v-bind="button" @mouseenter="activeTopIndex = index"
          :isActive="activeTopIndex === index" @click="button.action ? button.action() : null" />
      </div>
    </div>

    <!-- Sección inferior -->
    <div class="flex flex-col gap-y-4" @mouseleave="activeBottomIndex = null">
      <div class="absolute left-0 w-1 bg-white transition-all duration-300 ease-in-out" :style="bottomLineStyle"></div>
      <div class="flex flex-col items-center">
        <NavButton v-for="(button, index) in lowButtons" :key="index" v-bind="button"
          @mouseenter="activeBottomIndex = index" :isActive="activeBottomIndex === index" 
          @click = "settings"/>
      </div>
      <div class="flex items-center justify-center">
        <div class="w-3/4 h-px bg-gray-600"></div>
      </div>
      <NavButton name="exit" @mouseenter="activeBottomIndex = null" :isActive="false" :noLine="true" class="mt-2"
        @click="logout" />
    </div>
  </div>

  <div v-if="isPopupOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-10">
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-80">
      <h2 class="text-lg font-semibold mb-4 text-center">Select an organization</h2>
      <div class="space-y-2">
        <div class="max-h-96 overflow-y-auto">
          <div v-for="org in userOrganizations" :key="org.id"
            class="flex items-center p-2 rounded-lg cursor-pointer hover:bg-gray-600 transition"
            @click="changeOrganization(org.id)">
            <img v-if="org.organization_logo" :src="`/storage/${org.organization_logo}`" alt="Org Logo"
              class="w-10 h-10 rounded-lg object-cover">


            <div v-else class="w-10 h-10 flex items-center justify-center rounded bg-gray-700 text-white font-bold">
              {{org.name.split(' ').map(word => word[0]).join('').toUpperCase()}}
            </div>
            <span class="ml-3">{{ org.name }}</span>
          </div>
        </div>
        <hr>
        <div class="flex items-center justify-center gap-2 hover:bg-gray-700 p-2 rounded-full cursor-pointer"
          @click="addOrg">
          <box-icon name='plus' color="white"></box-icon>
          <h1>Add new organization</h1>
        </div>
      </div>
      <button @click="isPopupOpen = false" class="mt-4 w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">
        Close
      </button>
    </div>
  </div>
</template>
