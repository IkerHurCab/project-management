<template>
    <Layout pageTitle="Departments" class="px-8">
        <h1 class="text-2xl">Your departments</h1>
        <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchMyDepartments" />
        <table class="w-full mt-4">
            <thead class="bg-gray-800 text-gray-400 font-normal">
                <tr>
                    <th class="p-4 text-left rounded-tl-lg">Department Name</th>
                    <th class="p-4 text-left">Description</th>
                    <th class="p-4 text-left">Members</th>
                    <th class="p-4 text-left rounded-tr-lg">Active projects</th>
                </tr>
            </thead>
            <tbody class="bg-gray-950 ">
                <tr v-for="department in user_departments" :key="department.id" @click="singleDepartment(department.id)"
                    class="hover:bg-gray-900 transition duration-300 hover:cursor-pointer border-b border-gray-900 rounded-b-lg">
                    <td class="p-4 rounded-bl-lg">{{ department.name }}</td>
                    <td class="p-4">{{ department.description }}</td>
                    <td class="p-4">{{ department.users_count }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <h1 class="text-2xl mt-4">Other departments</h1>
        <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchOtherDepartments" />
        <table class="w-full mt-4">
            <thead class="bg-gray-800 text-gray-400 font-normal">
                <tr>
                    <th class="p-4 text-left rounded-tl-lg">Department Name</th>
                    <th class="p-4 text-left">Description</th>
                    <th class="p-4 text-left">Members</th>
                    <th class="p-4 text-left rounded-tr-lg">Active projects</th>
                </tr>
            </thead>
            <tbody class="bg-gray-950 ">
                <tr v-for="department in other_departments" :key="department.id" @click="isPopupVisible = true"
                    class="hover:bg-gray-900 transition duration-300 hover:cursor-pointer border-b border-gray-900 rounded-b-lg">
                    <td class="p-4 rounded-bl-lg">{{ department.name }}</td>
                    <td class="p-4">{{ department.description }}</td>
                    <td class="p-4">{{ department.users_count }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div v-if="isPopupVisible">
            <div class="fixed inset-0 flex items-center justify-center bg-black/50" @click="isPopupVisible = false">
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-1/3">
                    <h2 class="text-xl font-bold mb-4">Access Denied</h2>
                    <p class="mb-4">You do not have access to this department.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="isPopupVisible = false"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:cursor-pointer hover:bg-red-600">Close</button>
                        <button @click.stop="requestAccess"
                            class="bg-gray-600 hover:cursor-pointer hover:bg-gray-700 text-white px-4 py-2 rounded">Request
                            Access</button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const user_departments = ref(usePage().props.user_departments);
const other_departments = ref(usePage().props.other_departments);

function searchMyDepartments() {
    console.log('Searching departments...');
}

function searchOtherDepartments() {
    console.log('Searching other departments...');
}

function singleDepartment(departmentId) {
    window.location.href = `/departments/${departmentId}`;
}

function requestAccess() {
    console.log('Requesting access...');
}

const isPopupVisible = ref(false);

</script>
