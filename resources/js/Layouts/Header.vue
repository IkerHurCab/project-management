<template>
    <header class="flex border-b border-gray-800 items-center justify-between py-3 px-6 bg-black w-full ">
      <h1 class="text-2xl font-bold text-white">{{ page }}</h1>
      
      <div class="flex items-center gap-5 ">
       
  
        <!-- Notification Bell -->
        <div class="relative group">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-800 transition-colors duration-200 cursor-pointer">
            <box-icon name="bell" color="white"></box-icon>
          </div>
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
  
        <!-- Status Popup -->
        <div 
          v-if="isPopupVisible"
          class="absolute right-4 top-16 mt-2 bg-gray-800 text-white rounded-lg shadow-lg p-2 w-64 z-10 border border-gray-700"
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
  
  <script>
  import 'boxicons';
  import { usePage, useForm } from '@inertiajs/vue3';
  
  export default {
    props: {
      page: String
    },
  
    data() {
      return {
        isPopupVisible: false,
      }
    },
  
    computed: {
      user() {
        return usePage().props.auth?.user || null;
      },
  
      statusColor() {
        return {
          'bg-green-500': this.user.status === 'online',
          'bg-yellow-500': this.user.status === 'away',
          'bg-gray-500': this.user.status === 'offline'
        };
      }
    },
  
    methods: {
      togglePopup() {
        this.isPopupVisible = !this.isPopupVisible;
      },
      
      changeStatus(status) {
        this.user.status = status;
        this.isPopupVisible = false;
  
        const form = useForm({
          status: status
        });
  
        form.post('/update-status', {
          status,
          preserveScroll: true,
        });
      }
    }
  }
  </script>
  
  <style scoped>
  .dot {
    transition: transform 0.3s ease;
  }
  input:checked ~ .dot {
    transform: translateX(100%);
  }
  </style>