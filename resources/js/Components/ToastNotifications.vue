<script setup>
import { BellAlertIcon, BellSlashIcon } from '@heroicons/vue/24/outline';
import { useToasts } from '@/Composables/ToastNotifications';

const { toasts, toastAgain, reToasted } = useToasts();


; (() => {
  // Check for dark mode preference in localStorage
  const theme = localStorage.getItem("theme")

  // Apply dark mode if saved preference is 'dark'
  if (theme === "dark") {
    document.documentElement.classList.add("dark")
  } else {
    document.documentElement.classList.remove("dark")
  }
})()

</script>
<template>
  <div>
    <button v-if="toasts" v-tooltip="$t('Notifications')"
      class="flex h-10 w-10 items-center justify-center rounded-full dark:bg-gray-100 dark:hover:bg-gray-300 bg-gray-700 text-gray-300 "
      :class="{ 'animate-pulse': !reToasted }" @click="toastAgain()">
      <component :is="reToasted ? 'BellSlashIcon' : 'BellAlertIcon'" class="h-5 w-5" />
    </button>
  </div>
</template>