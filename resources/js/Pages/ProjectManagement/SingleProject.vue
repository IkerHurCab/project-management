<script setup>
import { defineProps } from 'vue';

defineProps({ project: Object });
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">{{ project.name }}</h1>
    <p><strong>Descripción:</strong> {{ project.description }}</p>
    <p><strong>Horas asignadas:</strong> {{ project.assigned_hours }}</p>
  </div>
</template>