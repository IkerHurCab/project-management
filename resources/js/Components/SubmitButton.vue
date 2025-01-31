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
                "px-4 py-2 rounded-lg transition duration- hover:cursor-pointer w-full",
                {
                    "bg-black text-white hover:bg-gray-950 border border-gray-600": this.variant === "primary",
                    "bg-red-600 text-white hover:bg-red-700": this.variant === "danger",
                },
            ];
        },
    },
};
</script>

<style scoped></style>