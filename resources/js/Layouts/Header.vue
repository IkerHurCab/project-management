<script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { usePage, useForm } from '@inertiajs/vue3';
  import NotificationsModal from '@/Components/NotificationsModal.vue';
  import moment from 'moment';
  
  // Recibimos el prop "page" de las vistas que usen este componente
  const props = defineProps({
    page: String,
    notifications: Array,
  });
  const { notifications: initialNotifications } = usePage().props;
  const notifications = ref(initialNotifications);
  const hasNewNotification = ref(false);

  onMounted(() => {
    console.log("Notificationes de Header: ", notifications.value);
  notifications.value.forEach(notification => {
    // Asegúrate de que la fecha sea tratada en UTC
    const createdAt = moment.utc(notification.created_at.replace(' ', 'T'));

    // Convertir a la hora local para mostrarla correctamente
    notification.time = createdAt.local().fromNow();


  });
});



const formattedNotifications = computed(() => {
  return notifications.value.map(notification => {
    // Convierte el formato de fecha de MySQL a ISO 8601 para que moment.js lo pueda interpretar
    const createdAt = moment(notification.created_at.replace(' ', 'T')); // Reemplazamos el espacio con T para ISO 8601
    notification.time = createdAt.isValid() ? createdAt.fromNow() : 'Invalid date'; // Calcula el tiempo desde ahora
    return notification;
  });
});


  // Estado reactivo para mostrar el modal de notificaciones
  const isNotificationsModalOpen = ref(false);
  
  // Estado reactivo para mostrar el popup de estado del usuario
  const isPopupVisible = ref(false);
  
  // Obtener el usuario actual desde las propiedades de Inertia
  const user = usePage().props.auth?.user || null;
  
  // Cambiar el estado de visibilidad del modal de notificaciones
  const changeNotificationsModal = () => {
    // Si el popup de estado está abierto, lo cerramos
    if (isPopupVisible.value) {
      isPopupVisible.value = false;
    }
    isNotificationsModalOpen.value = !isNotificationsModalOpen.value;
  };
  
  // Abrir el modal de notificaciones
  const openNotificationsModal = () => {
    // Si el popup de estado está abierto, lo cerramos
    if (isPopupVisible.value) {
      isPopupVisible.value = false;
    }
    isNotificationsModalOpen.value = true;
  };
  
  // Cerrar el modal de notificaciones
  const closeNotificationsModal = () => {
    isNotificationsModalOpen.value = false;
  };
  
  // Computed para determinar el color de estado del usuario
  const statusColor = computed(() => {
    return {
      'bg-green-500': user?.status === 'online',
      'bg-yellow-500': user?.status === 'away',
      'bg-gray-500': user?.status === 'offline',
    };
  });
  
  // Computed para determinar el color del borde del avatar de usuario
  const borderColor = computed(() => {
    return {
      'hover:border-green-500': user?.status === 'online',
      'hover:border-yellow-500': user?.status === 'away',
      'hover:border-gray-500': user?.status === 'offline',
    };
  });
  
  // Función para cambiar el estado del usuario
  const changeStatus = (status) => {
    if (user) {
      user.status = status;
      isPopupVisible.value = false;
  
      const form = useForm({ status });
      form.post('/update-status', { preserveScroll: true });
    }
  };
  
  // Función para mostrar u ocultar el popup de estado
  const togglePopup = () => {
    // Si el modal de notificaciones está abierto, lo cerramos
    if (isNotificationsModalOpen.value) {
      isNotificationsModalOpen.value = false;
    }
    isPopupVisible.value = !isPopupVisible.value;
  };

  watch(() => usePage().props.notifications, (newNotifications) => {
  notifications.value = newNotifications;  // Actualiza las notificaciones
  // Verificar si hay nuevas notificaciones no leídas
  hasNewNotification.value = newNotifications.some(notification => !notification.read);
}, { immediate: true });
  </script>
  
  <template>
    <header class="flex border-b border-gray-800 items-center justify-between py-3 px-6 bg-black w-full">
      <h1 class="text-2xl px-5 font-bold text-white">{{ props.page }}</h1>
  
      <div class="flex items-center gap-5">
        <!-- Notification Bell -->
        <div class="relative group">
          <button
            @click="changeNotificationsModal"
            class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-800 transition-colors duration-200 cursor-pointer"
          >
            <box-icon name="bell" color="white"></box-icon>
            <div v-if="hasNewNotification" class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></div>
          </button>
          <NotificationsModal :notifications="formattedNotifications" :is-open="isNotificationsModalOpen" @close="closeNotificationsModal" />
        </div>
  
        <!-- User Avatar with Status -->
        <div
          @click="togglePopup"
          class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-800 transition-colors duration-200 cursor-pointer"
        >
          <box-icon name="user" color="white"></box-icon>
          <div
            class="absolute w-3 h-3 rounded-full border-2 border-black right-0 bottom-0"
            :class="statusColor"
          ></div>
        </div>
  
        <div
          v-if="isPopupVisible"
          class="absolute right-4 top-16 mt-2 bg-gray-900 text-white rounded-lg shadow-lg p-2 w-64 z-10 border border-gray-700"
        >
          <h3 class="font-bold p-2 text-sm">Estado</h3>
          <ul class="space-y-1">
            <li
              @click="changeStatus('online')"
              class="flex items-center gap-3 cursor-pointer hover:bg-gray-700 p-2 rounded-lg transition duration-200"
            >
              <div class="bg-green-500 w-3 h-3 rounded-full"></div>
              <span class="text-sm">Conectado</span>
            </li>
            <li
              @click="changeStatus('away')"
              class="flex items-center gap-3 cursor-pointer hover:bg-gray-700 p-2 rounded-lg transition duration-200"
            >
              <div class="bg-yellow-500 w-3 h-3 rounded-full"></div>
              <span class="text-sm">Ausente</span>
            </li>
            <li
              @click="changeStatus('offline')"
              class="flex items-center gap-3 cursor-pointer hover:bg-gray-700 p-2 rounded-lg transition duration-200"
            >
              <div class="bg-gray-500 w-3 h-3 rounded-full"></div>
              <span class="text-sm">Invisible</span>
            </li>
          </ul>
        </div>
      </div>
    </header>
  </template>
  
  <style scoped>
  .dot {
    transition: transform 0.3s ease;
  }
  
  input:checked ~ .dot {
    transform: translateX(100%);
  }
  </style>
  