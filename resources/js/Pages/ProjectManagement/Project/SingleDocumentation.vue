<script>
  import Markdown from 'vue3-markdown-it';
  import 'highlight.js/styles/monokai.css';
  
  export default {
    components: {
      Markdown
    },
    data() {
      return {
        source: '# __Hello World!__'
      }
    }
  }
  </script>
<template>
  <div>
    <Markdown class="prose " :source="source" />
    <h1>h1</h1>
    <h2>h2</h2>
  </div>
</template>
<style scoped>
  .markdown-content {
    padding: 1rem;
    line-height: 1.6;
  }
  
  .markdown-content h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #222;
  }
  
  .markdown-content h2 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #444;
  }
  
  .markdown-content ul {
    list-style-type: disc;
    margin-left: 2rem;
  }
  
  .markdown-content code {
    background-color: #f4f4f4;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
  }
  
  .markdown-content a {
    color: blue;
    text-decoration: underline;
  }
  </style>
  

