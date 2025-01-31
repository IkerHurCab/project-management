<template>
    <div class="min-h-screen w-full flex">
        <div class="relative hidden md:flex w-2/3 min-h-screen  text-black items-center justify-center">
            <img 
            class="absolute top-0 left-0 w-full h-full object-cover"
            :src="'https://static.vecteezy.com/system/resources/previews/015/450/233/non_2x/tropical-leaves-black-background-elegant-hand-drawn-natural-botanical-leaves-with-faded-background-design-illustration-for-decoration-wall-decor-wallpaper-cover-banner-poster-card-vector.jpg'">

        </div>
        <div class="w-full md:w-1/3 bg-gray-950 min-h-screen flex items-center justify-center">
            <form @submit.prevent="login"
                class="space-y-4 w-full flex flex-col items-center p-6 min-h-screen justify-center">
                <div class="w-1/2 h-24 bg-gray-300 flex items-center justify-center hidden md:flex">
                    <span class="text-gray-500">LOGO AQUÍ</span>
                </div>
                <h1 class="text-2xl font-bold self-start">INICIAR SESIÓN EN PROJECT MANAGEMENT</h1>
                <InputWithIcon v-model="email" icon="alternate_email" placeholder="Correo electrónico" class="w-full"
                    type="email" required/>
                <InputWithIcon v-model="password" icon="lock" placeholder="Contraseña" type="password" class="w-full"
                    :toggleVisibility="true" required/>
                <SubmitButton variant="primary" type="submit">Iniciar sesión</SubmitButton>
                <a href="#" class="text-blue-500 self-end">¿Has olvidado la contraseña?</a>
            </form>
        </div>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import SubmitButton from "@/Components/SubmitButton.vue";
import InputWithIcon from "@/Components/InputWithIcon.vue";

export default {
    components: {
        SubmitButton,
        InputWithIcon,
    },
    data() {
        return {
            email: '',
            password: ''
        };
    },
    methods: {
        login() {
            console.log("Login");
        },
    },

    setup() {
        const form = useForm({
            email: '',
            password: '',
        });
        const error = ref('');
        const showPassword = ref(false);

        function login() {
            form.post('/login', {
                onError: (errors) => {
                    error.value = errors.email || errors.password || 'Login failed';
                }
            });
        }

        function togglePasswordVisibility() {
            showPassword.value = !showPassword.value;
        }

        return {
            form,
            error,
            login,
            showPassword,
            togglePasswordVisibility,
        };
    },
};
</script>