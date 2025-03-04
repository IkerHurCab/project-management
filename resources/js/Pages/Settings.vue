<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '../Components/InputWithIcon.vue'

const user = computed(() => usePage().props.auth?.user);
const username = ref(user.name);

const isTwoFactorEnabled = ref(!!user.value?.two_factor_secret);
const qrCode = ref("");

const getQRCode = async () => {
    try {
        const response = await axios.get('/user/two-factor-qr-code');
        qrCode.value = response.data.svg;
        qrModal.value = true;
    } catch (error) {
        console.error("Error fetching QR Code:", error);
    }
};


const enable2FA = async () => {
    try {
        await router.post('/user/two-factor-authentication');
        isTwoFactorEnabled.value = true;
        getQRCode();
    } catch (error) {
        console.error('Error enabling 2FA', error);
    }
};

const disable2FA = async () => {
    try {
        await router.delete('/user/two-factor-authentication');
        isTwoFactorEnabled.value = false;
        qrModal.value = false
    } catch (error) {
        console.error('Error disabling 2FA', error);
    }
};

//modals
const qrModal = ref(false);
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
                    <h3 @click="isTwoFactorEnabled ? disable2FA() : enable2FA()"
                        class="w-1/2 bg-blue-500 text-white font-semibold p-3 rounded-lg hover:bg-blue-600 cursor-pointer text-center mt-4 transition duration-300">
                        {{ isTwoFactorEnabled ? "DISABLE 2FA" : "ENABLE 2FA" }}
                    </h3>
                </div>
            </div>
        </div>
        <div v-if="qrCode && qrModal" class="fixed inset-0 flex items-center justify-center bg-black/50">
            <div class="bg-gray-900 p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-semibold mb-4">Scan this QR Code</h2>
                <div v-html="qrCode" class="p-4 bg-gray-100 rounded-lg inline-block"></div>
                <p class="text text-gray-300 mt-2">Use your authenticator app to scan the code and enable 2FA</p>
                <div class="fle gap-2">
                <button @click="disable2FA()"
                    class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 cursor-pointer transition duration-300">Cancel</button>
                <button @click="qrModal = false"
                    class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-red-600 cursor-pointer transition duration-300 ml-2">Confirm</button>
                </div>
            </div>
        </div>

    </Layout>
</template>