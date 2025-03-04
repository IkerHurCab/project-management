<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '../Components/InputWithIcon.vue'

const user = computed(() => usePage().props.auth?.user);
const username = ref(user.name);
</script>
<template>
    <Layout pageTitle="Settings">
        <div class="bg-gray-950 rounded-lg h-full border border-gray-500">

            <h1 class="border-b border-gray-500 p-2 rounded-t-lg">ACCOUNT</h1>
            <p class="px-4 mt-4">Manage your account information.</p>
            <div class="p-4 grid grid-cols-2 gap-4">
                <div>
                    <label for="username">Username</label>
                    <InputWithIcon icon="user" :placeholder="user.name" :value="user.name" class="w-full" type="text"
                        required v-model="username" />
                </div>
                <div>
                    <label for="email">Email</label>
                    <InputWithIcon icon="envelope" :placeholder="user.email" class="w-full" type="email" required />
                </div>
                <div class="grid grid-cols-2 gap-2 w-2/3">
                    <h3 class="bg-blue-500 p-2 rounded-lg hover:bg-blue-600 cursor-pointer text-center">Update acount
                        information</h3>
                    <h3 class="bg-blue-500 p-2 rounded-lg hover:bg-blue-600 cursor-pointer text-center">Change password
                    </h3>
                </div>
            </div>

            <h1 class="border-y border-gray-500 p-2">SOCIAL</h1>
            <div class="p-4">

            </div>

            <h1 class="border-y border-gray-500 p-2">SECURITY</h1>
            <p class="px-4 mt-4">Add an extra layer of security to your account.</p>
            <div class="p-4 grid grid-cols-2 gap-4">
                <div class="bg-gray-900 p-6 rounded-lg">
                    <p class="text-xl font-bold">Two-Factor Authentication (2FA)</p>
                    <p class="mt-2 text-justify">
                        Two-Factor Authentication adds an extra layer of security to your account.
                        By requiring a second form of verification, it helps prevent unauthorized access,
                        even if someone knows your password.
                    </p>
                    <p class="mt-2 text-justify">
                        Once enabled, you'll be asked for a verification code sent to your device each time
                        you log in. You can use an authenticator app or receive codes via SMS.
                    </p>
                    <h3
                        class="w-1/2 bg-blue-500 text-white font-semibold p-3 rounded-lg hover:bg-blue-600 cursor-pointer text-center mt-4 transition duration-300">
                        ENABLE 2FA
                    </h3>
                </div>
            </div>
        </div>


    </Layout>
</template>