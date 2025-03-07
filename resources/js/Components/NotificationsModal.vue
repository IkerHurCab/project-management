<script setup>
  import { ref, watch, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    isOpen: Boolean,
    notifications: Array,
  });
  
 
  
  // Marcar todas como leídas
  const markAllAsRead = () => {
    router.put('/notifications/mark-all-as-read', {}, {
      preserveState: true,
      onSuccess: () => {
        props.notifications.forEach(notification => {
       
        });
      },
      onError: (errors) => {
        console.error('Mark all as read error:', errors);
      }
    });
  };
  const markAsRead = (notification) => {
    router.put(`/notifications/${notification.id}/mark-as-read`, {}, {
      preserveState: true,
      onSuccess: () => {
        
      },
      onError: (errors) => {
        console.error('Mark as read error:', errors);
      }
    });
  };
  
  // Ver todas las notificaciones
  const viewAllNotifications = () => {
    router.get('/notifications', {}, {
      preserveState: true,
      onSuccess: () => {
        // Cerrar el modal de notificaciones
        props.isOpen = false;
      },
      onError: (errors) => {
        console.error('View all notifications error:', errors);
      }
    });
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
          <button @click="markAllAsRead" class="text-xs cursor-pointer text-gray-400 hover:text-white transition-colors duration-200">
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
               :class="{ 'bg-gray-700 bg-opacity-40': !notification.is_read }"
               @click="markAsRead(notification)"
               >
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 mt-1">
                <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center">
                  <box-icon :name="notification.icon" color="white"></box-icon>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <p class="font-medium text-sm"
                     :class="{ 'text-white': !notification.is_read, 'text-gray-300': notification.is_read }">
                    {{ notification.title }}
                  </p>
                  <span class="text-xs text-gray-400">{{ notification.time }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ notification.message }}</p>
              </div>
              <div v-if="!notification.is_read" class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1"></div>
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
  