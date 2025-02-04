<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
defineProps({ projects: Array });


</script>

<template>
  <div class="flex flex-row">
    <Sidebar />
  <div>
    
    <h1 class="text-2xl font-bold mb-4">Lista de Proyectos</h1>
    <ul>
      <li v-for="project in projects" :key="project.id">
        <Link :href="`/projects/${project.id}`" class="text-blue-500 hover:underline">
          {{ project.name }}
        </Link>
      </li>
    </ul>
  </div>
</div>
</template>
