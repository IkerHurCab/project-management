<script setup>
import { defineProps, ref, watchEffect } from 'vue';
import Markdown from 'vue3-markdown-it';
import hljs from 'highlight.js';

import 'highlight.js/styles/github.css';

const props = defineProps({
  selectedDocumentation: {
    type: Object,
    required: true,
  }
});

const isDarkMode = ref(false);

watchEffect(() => {
  isDarkMode.value = document.documentElement.classList.contains('dark');
});

const source = props.selectedDocumentation.content;

const options = {
  highlight: (str, lang) => {
    if (lang && hljs.getLanguage(lang)) {
      try {
        return hljs.highlight(str, { language: lang }).value;
      } catch (err) {
        console.error(err);
      }
    }
    return '';
  }
};
</script>

<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <Markdown :source="source" :options="options" class="prose max-w-5xl" />
  </div>
</template>

<style scoped>

.prose * {
  color: #ffffff;
}

.dark-mode .prose * {
  color: #000000; 
}
</style>
