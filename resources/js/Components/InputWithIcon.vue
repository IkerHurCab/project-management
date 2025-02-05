<template>
  <div
    class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg relative focus-within:border-gray-300"
  >
    <box-icon
      :type="fill"
      :name="icon"
      class="w-7 h-7 relative z-10 transition-transform duration-300 group-hover:scale-110"
      :color="'#707070'"
    ></box-icon>

    <input
      v-bind="$attrs"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :type="inputType"
      class="p-2 ml-2 w-full h-10 outline-none align-middle"
    />

    <span
      v-if="toggleVisibility"
      @click="toggleVisibilityfn"
      class="material-symbols-outlined right-2 mt-1 top-2 cursor-pointer"
    >
      {{ inputType === 'password' ? 'visibility_off' : 'visibility' }}
    </span>
  </div>
</template>

  
  <script>
    import 'boxicons'
  import { ref } from 'vue'
  const isHovered = ref(false)

  export default {
    props: {
      modelValue: {
        type: String,
        required: true,
      },
      icon: String,
      name: String,
      placeholder: String,
      type: {
        type: String,
        default: "text",
      },
      toggleVisibility: {
        type: Boolean,
        default: false,
      },
    },
    data() {
      return {
        inputType: this.type,  
      };
    },
    methods: {
      handleInput(event) {
        this.$emit('update:modelValue', event.target.value);
      },
      toggleVisibilityfn() {
        this.inputType = this.inputType === 'password' ? 'text' : 'password';
      },
    },
  };
  </script>