<template>
  <div class="flex h-screen">
    <aside :class="sidebar ? 'w-16' : 'w-0'" class="transition-all duration-300 ease-in-out">
      <Sidebar v-if="sidebar" />
    </aside>

    <div class="flex flex-col flex-grow">
      <header :class="[header ? 'h-20' : 'h-0', 'transition-all duration-300 ease-in-out z-10 border-b-2 border-gray-800']">
        <Header v-if="header" :page="pageTitle" />
      </header>

      <main class="flex-grow p-4">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import Header from './Header.vue';
import Sidebar from './Sidebar.vue';

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

<script>
export default {
  data() {
    return {
      isPopupVisible: false,
    }
  },
}
</script>