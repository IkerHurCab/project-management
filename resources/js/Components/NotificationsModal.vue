<script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    isOpen: Boolean,
    notifications: Array,
  });
  
  // Mapeo de tipos a iconos
  const getIconByType = (type) => {
    switch (type) {
      case 'assigned_to_project':
        return 'briefcase';
      case 'assigned_to_task':
        return 'task';
      case 'task_status_changed':
        return 'clipboard';
      case 'new_documentation':
        return 'file';
      default:
        return 'bell';
    }
  };
  
  // Marcar todas como leídas
  const markAllAsRead = () => {
    props.notifications.forEach(notification => {
      notification.read = true;
    });
  };
  
  // Ver todas las notificaciones
  const viewAllNotifications = () => {
    alert('Ver todas las notificaciones');
  };
  
  // Reaccionar a los cambios en las notificaciones
  watch(() => props.notifications, (newNotifications) => {
    console.log('Notifications in modal: ', newNotifications);
  }, { immediate: true });
  </script>
  
  <template>
    <div v-if="isOpen" class="absolute right-0 top-12 mt-2 bg-gray-900 text-white rounded-lg shadow-lg w-80 z-20 border border-gray-700 overflow-hidden">
      <div class="flex items-center justify-between p-3 border-b border-gray-700">
        <h3 class="font-bold text-sm">Notifications</h3>
        <div v-if="props.notifications.length > 0" class="flex items-center gap-2">
          <button @click="markAllAsRead" class="text-xs text-gray-400 hover:text-white transition-colors duration-200">
            Mark all as read
          </button>
        </div>
      </div>
  
      <div class="max-h-96 overflow-y-auto custom-scrollbar">
        <div v-if="props.notifications.length === 0" class="p-4 text-center text-sm text-gray-400">
          There are no notifications
        </div>
  
        <div v-else>
          <div v-for="(notification, index) in props.notifications" :key="index"
               class="p-3 border-b border-gray-600 hover:bg-gray-800 transition-colors duration-200 cursor-pointer"
               :class="{ 'bg-gray-700 bg-opacity-40': !notification.read }">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 mt-1">
                <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center">
                  <box-icon :name="getIconByType(notification.type)" color="white" size="sm"></box-icon>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <p class="font-medium text-sm"
                     :class="{ 'text-white': !notification.read, 'text-gray-300': notification.read }">
                    {{ notification.title }}
                  </p>
                  <span class="text-xs text-gray-400">{{ notification.time }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ notification.message }}</p>
              </div>
              <div v-if="!notification.read" class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1"></div>
            </div>
          </div>
        </div>
      </div>
  
      <div v-if="props.notifications.length > 0" class="p-3 border-t border-gray-700">
        <button class="w-full py-2 text-sm cursor-pointer text-center text-gray-300 hover:text-white transition-colors duration-200 rounded-lg hover:bg-gray-800" @click="viewAllNotifications">
          View all notifications
        </button>
      </div>
    </div>
  </template>
  