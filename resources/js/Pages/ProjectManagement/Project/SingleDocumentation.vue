<script setup>
  import { defineProps } from 'vue';
  import Markdown from 'vue3-markdown-it';  
  import hljs from 'highlight.js';  

  import 'highlight.js/styles/github.css'; 


  const props = defineProps({
    selectedDocumentation: {
      type: Object,
      required: true,
    }
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
  <div>
    <Markdown :source="source" :options="options" class="prose max-w-5xl" />
  </div>
</template>

<style scoped>
 
  .prose * {
    color: #ffffff;
  }


  pre {
    background-color: #282c34;  
    padding: 16px;
    border-radius: 8px;
    overflow-x: auto;
  }

  
  code {
    font-family: 'Courier New', Courier, monospace;
  }
</style>
