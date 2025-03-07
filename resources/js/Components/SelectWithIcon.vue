<template>
    <div class="w-1/2 flex items-center p-1 px-3 border border-gray-600 focus-within:border-gray-300 rounded-lg relative">
      <box-icon
        :type="fill"
        :name="icon"
        class="w-7 h-7 relative z-10 transition-transform duration-300 group-hover:scale-110"
        :color="'#707070'"
      ></box-icon>
  
      <select
        v-bind="$attrs"
        :value="modelValue"
        @change="handleInput"
    
        class="p-2 ml-2 h-10 w-full bg-gray-950 dark:bg-white  outline-none"
      >
        <option v-for="(option, index) in options" :key="index" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>
  </template>
  
  <script>
  import 'boxicons'
  
  export default {
    props: {
      modelValue: {
        type: String,
        required: true,
      },
      icon: String,
      name: String,
      options: {
        type: Array,
        default: () => [],
      },
    },
    methods: {
      handleInput(event) {
        this.$emit("update:modelValue", event.target.value);
      },
    },
  };
  </script>