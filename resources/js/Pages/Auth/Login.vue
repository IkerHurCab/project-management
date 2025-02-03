<template>
    <div class="min-h-screen w-full flex">
        <div class="relative hidden md:flex w-2/3 min-h-screen text-black items-center justify-center">
            <img class="absolute top-0 left-0 w-full h-full object-cover"
                :src="'https://static.vecteezy.com/system/resources/previews/015/450/233/non_2x/tropical-leaves-black-background-elegant-hand-drawn-natural-botanical-leaves-with-faded-background-design-illustration-for-decoration-wall-decor-wallpaper-cover-banner-poster-card-vector.jpg'">
        </div>
        <div class="w-full md:w-1/3 bg-gray-950 border-l border-gray-600 min-h-screen flex items-center justify-center">
            <form @submit.prevent="login" class="space-y-6 w-full max-w-md flex flex-col items-center p-8">
                <div class="w-24 h-24 border-2 border-gray-700 rounded-full flex items-center justify-center mb-4">
                    <span class="text-gray-400 text-xl font-bold">LOGO</span>
                </div>
                <h1 class="text-2xl font-bold text-white self-start mb-6">Login</h1>
                <InputWithIcon v-model="form.email" icon="envelope" placeholder="Email" class="w-full"
                    type="email" required />
                <InputWithIcon v-model="form.password" icon="lock" placeholder="Password" type="password"
                    class="w-full" :toggleVisibility="true" required />
                <SubmitButton variant="primary" type="submit" class="w-full">Login</SubmitButton>
                <div class="flex items-center justify-between w-full">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            v-model="form.remember" 
                            class="form-checkbox h-4 w-4 text-gray-600 transition duration-150 ease-in-out"
                        />
                        <span class="text-sm text-gray-400">Remember session</span>
                    </label>
                    <a href="#" class="text-gray-400 hover:text-gray-200 text-sm transition duration-150 ease-in-out">
                        Have you forgotten your password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import SubmitButton from "@/Components/SubmitButton.vue";
import InputWithIcon from "@/Components/InputWithIcon.vue";

export default {
    components: {
        SubmitButton,
        InputWithIcon,
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
            remember: false, // Agregar remember a la definición del formulario
        });

        function login() {
            // Asegúrate de pasar 'remember' en los datos del POST
            form.post('/login', {
                data: {
                    email: form.email,
                    password: form.password,
                    remember: form.remember, // Enviar el valor del checkbox
                },
            });
        }

        return {
            form,
            login,
        };
    },
};
</script>
