<script>
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SubmitButton from "@/Components/SubmitButton.vue";
import InputWithIcon from "@/Components/InputWithIcon.vue";
export default {
    props: {
        errors: [String, Object],
    },
    components: {
        SubmitButton,
        InputWithIcon,
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        function login() {
            // Asegúrate de pasar 'remember' en los datos del POST
            form.post('/login');
        }

        return {
            form,
            login,
        };
    },
};



</script>

<template>
    <div class="min-h-screen w-full flex">
        <div class="relative hidden md:flex w-2/3 min-h-screen text-black items-center justify-center">
            <img class="absolute top-0 left-0 w-full h-full object-cover"
                :src="'/storage/images/fondo.jpg'">
        </div>
        <div class="w-full md:w-1/3 bg-gray-950 border-l border-gray-600 min-h-screen flex items-center justify-center">
            <form @submit.prevent="login" class="space-y-6 w-full max-w-md flex flex-col items-center p-8">
                <div class="w-24 h-24 border-2 border-gray-700 rounded-full flex items-center justify-center mb-4">
                    <span class="text-gray-400 text-xl font-bold">LOGO</span>
                </div>
                <h1 class="text-2xl font-bold text-white self-start mb-6">Login</h1>
                <div v-if="typeof errors === 'string'"
                    class="w-full bg-red-500/10 border border-red-500/50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-2">
                        <p class="text-red-500 text-sm font-medium">{{ errors }}</p>
                    </div>
                </div>
                <div v-if="typeof errors === 'object' && Object.keys(errors).length > 0"
                    class="w-full bg-red-500/10 border border-red-500/50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-2">
                        <p class="text-red-500 text-sm font-medium">{{ errors }}</p>
                    </div>
                </div>

                <InputWithIcon v-model="form.email" icon="envelope" placeholder="Email" class="w-full" type="email"
                    required />
                <InputWithIcon v-model="form.password" icon="lock" placeholder="Password" type="password" class="w-full"
                    :toggleVisibility="true" required />
                <SubmitButton variant="primary" type="submit" class="w-full">Login</SubmitButton>
                <div class="flex items-center justify-between w-full">
                    <div class="text-gray-400 text-sm flex items-center gap-2">
                        <input type="checkbox" v-model="form.remember" /> Remember Me
                    </div>

                    <a href="#"
                        class="text-gray-400 hover:text-gray-200 hover:underline text-sm transition duration-150 ease-in-out">
                        Forgot password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>
