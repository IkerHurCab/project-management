<template>
  <div class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg relative">
    <!-- Ícono -->
    <box-icon
      :type="fill"
      :name="icon"
      class="w-7 h-7 relative z-10 transition-transform duration-300 group-hover:scale-110"
      :color="'#707070'"
    ></box-icon>

    <!-- Input o Select según `isSelect` -->
    <template v-if="!isSelect">
      <input
        v-bind="$attrs"
        :value="modelValue"
        @input="handleInput"
        :placeholder="placeholder"
        :type="inputType"
        class="p-2 ml-2 w-full outline-none"
      />
      
      <span
        v-if="toggleVisibility"
        @click="toggleVisibilityfn"
        class="material-symbols-outlined right-2 mt-1 top-2 cursor-pointer"
      >
        {{ inputType === 'password' ? 'visibility_off' : 'visibility' }}
      </span>
    </template>

    <template v-else>
      <select
        v-bind="$attrs"
        :value="modelValue"
        @change="handleInput"
        class="p-2 ml-2 w-full outline-none"
      >
        <option v-for="(option, index) in options" :key="index" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </template>
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
    placeholder: String,
    type: {
      type: String,
      default: "text",
    },
    isSelect: {
      type: Boolean,
      default: false, // Si es true, renderiza un select en vez de un input
    },
    options: {
      type: Array,
      default: () => [], // Opciones del select (ejemplo: [{ value: '1', label: 'Opción 1' }])
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
      this.$emit("update:modelValue", event.target.value);
    },
    toggleVisibilityfn() {
      this.inputType = this.inputType === "password" ? "text" : "password";
    },
  },
};
</script>
