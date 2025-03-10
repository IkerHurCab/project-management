<template>
  <div class="flex h-screen overflow-hidden">
    <aside :class="sidebar ? 'w-16' : 'w-0'" class="transition-all duration-300 ease-in-out">
      <Sidebar v-if="sidebar" />
    </aside>

    <div class="flex flex-col flex-grow overflow-x-auto">
      <header :class="[header ? 'h-16' : 'h-0', 'transition-all duration-300 ease-in-out ml-6 sm:ml-0']">
        <Header v-if="header" :page="pageTitle" />
      </header>

      <main class="flex-grow p-4 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import Header from './Header.vue';
import Sidebar from './Sidebar.vue';

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


defineProps({
  pageTitle: String,
  header: {
    type: Boolean,
    default: true
  },
  sidebar: {
    type: Boolean,
    default: true
  }
});
</script>