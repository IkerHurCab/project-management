<script setup>
import Layout from '@/Layouts/Layout.vue';
import 'boxicons';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    department: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

console.log(props.users);
</script>

<template>

    <Layout :pageTitle="department.name" class="px-8">
        <div class="grid grid-cols-3">
            <div class="col-span-2"></div>

            <div class="rounded-xl bg-secondary p-4">
                <div class="flex gap-4 items-center">
                    <box-icon name='user' color="white" class="w-10 h-10"></box-icon>
                    <h1 class="text-4xl text-center">Members</h1>
                </div>
                <ul class="mt-4">
                    <li v-for="user in users" :key="user.id" class="mt-2">
                        <div class="flex items-center gap-4">
                            <div
                                class="border border-gray-700 rounded-full hover:cursor-pointer relative w-10 h-10 transition duration-300">
                                <box-icon :name="'user'" :color="'gray'"
                                    class="w-6 h-6 absolute top-2 left-2"></box-icon>
                                <div class="border border-gray-700 absolute w-2 h-2 rounded-full top-7 right-0 transition duration-300"
                                    :class="{
                                        'bg-yellow-500': user.status === 'away',
                                        'bg-green-500': user.status === 'online',
                                        'bg-secondary': user.status === 'offline'
                                    }">
                                </div>
                            </div>
                            <span class="text-sm sm:text-base">{{ user.name }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </Layout>
</template>