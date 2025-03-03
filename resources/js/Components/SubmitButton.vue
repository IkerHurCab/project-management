<template>
    <button :class="computedClasses" :type="type" :disabled="disabled" @click="submit">
      <slot />
    </button>
  </template>
  
  <script>
  export default {
    name: "SubmitButton",
    props: {
      type: {
        type: String,
        default: "button",
      },
      variant: {
        type: String,
        default: "primary",
      },
      size: {
        type: String,
        default: "md",
      },
      disabled: {
        type: Boolean,
        default: false,
      },
    },
    computed: {
      computedClasses() {
        return [
  "px-4 py-2 rounded-lg transition duration-150 ease-in-out hover:cursor-pointer w-full font-medium",
  {
    "bg-neutral-800 text-white hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-neutral-800": this.variant === "primary",
    "bg-red-700 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-neutral-800": this.variant === "danger",
  },
];

      },
    },
  };
  </script>