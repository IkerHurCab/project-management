<template>
    <header class="flex items-center justify-between py-2  px-4 bg-black w-full">
        <h1 class="text-3xl font-bold text-white mb-4 px-8 py-1">{{ page }}</h1>
        <div class="flex gap-4 items-center">
            <div class="flex items-center min-h-12">
                <label for="theme-switch" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input id="theme-switch" type="checkbox" class="sr-only" />
                        <div class="block bg-gray-800 w-24 min-h-12 rounded-full"></div>
                        <div class="absolute text-white flex items-center gap-4 top-3 left-4">
                            <box-icon name="moon" color="#FFFFFF"></box-icon>
                            <box-icon name="sun" color="#FFFFFF"></box-icon>
                        </div>
                    </div>
                </label>
            </div>

            <div class="p-4 hover:cursor-pointer transition duration-300 relative min-w-12 min-h-12">
                <box-icon :name="'bell'" :color="'gray'" class="w-7 h-7 absolute top-2.5 left-2.5"></box-icon>
            </div>
            <div @click="togglePopup"
                class="border-2 border-gray-800 rounded-full  hover:cursor-pointer relative min-w-15 min-h-15 transition duration-300"
                :class="borderColor">
                <box-icon :name="'user'" :color="'gray'" class="w-8 h-8 absolute top-3 left-3"></box-icon>
                <div class="absolute min-h-4 min-w-4 rounded-full top-10 right-0 border-2 border-gray-500 transition duration-300"
                    :class="statusColor">
                </div>
            </div>

            <div v-if="isPopupVisible"
                class="border-2 border-gray-800 absolute right-0 top-17 mt-2 bg-black text-white rounded-b-lg shadow-lg p-4 w-96">
                <h3 class="font-bold p-2">Estado</h3>
                <ul>
                    <li @click="changeStatus('online')"
                        class="flex items-center gap-2 cursor-pointer hover:bg-gray-800 p-2 transition duration-300">
                        <div class="bg-green-500 w-3 h-3 rounded-full"></div>
                        <h4>Conectado</h4>
                    </li>
                    <li @click="changeStatus('away')"
                        class="flex items-center gap-2 cursor-pointer hover:bg-gray-800 p-2 transition duration-300">
                        <div class="bg-yellow-500 w-3 h-3 rounded-full"></div>Ausente
                    </li>
                    <li @click="changeStatus('offline')"
                        class="flex items-center gap-2 cursor-pointer hover:bg-gray-800 p-2 transition duration-300">
                        <div class="bg-gray-500 w-3 h-3 rounded-full"></div>Invisible
                    </li>
                </ul>
            </div>
        </div>
    </header>

</template>

<script>
import 'boxicons';
import { usePage, useForm } from '@inertiajs/vue3';

export default {
    props: {
        page: String
    },

    data() {
        return {
            isPopupVisible: false,
        }
    },

    computed: {
        user() {
            return usePage().props.user;
        },
        statusColor() {
            return {
                'bg-green-500': this.user.status === 'online',
                'bg-yellow-500': this.user.status === 'away',
                'bg-black': this.user.status === 'offline'
            };
        },
        borderColor() {
            return {
                'hover:border-green-500': this.user.status === 'online',
                'hover:border-yellow-500': this.user.status === 'away',
                'hover:border-gray-500': this.user.status === 'offline'
            };
        }
    },

    methods: {
        togglePopup() {
            this.isPopupVisible = !this.isPopupVisible;
        },
        changeStatus(status) {
            this.user.status = status;
            this.isPopupVisible = false;

            const form = useForm({
                status: status
            });

            form.post('/update-status', {
                status,
                preserveScroll: true,
            });
        },
    }

}
</script>
