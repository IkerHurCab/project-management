<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const isIconRotated = ref(false);
const isIconRotated2 = ref(false);

const user_departments = ref(usePage().props.user_departments);
const other_departments = ref(usePage().props.other_departments);

function searchMyDepartments() {

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

<template>
    <Layout pageTitle="Departments" class="px-8">
        <h1 class="text-2xl">Your departments</h1>
        <div class="flex justify-between items-center mt-2">
            <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchMyDepartments"
                class="focus:border-white" />
            <div class="flex gap-2">
                <h1 class="bg-gray-900 py-2 px-4 rounded-lg mb-2 cursor-pointer flex items-center gap-2 hover:bg-gray-700 transition duration-300"
                    @mouseover="isIconRotated = true" @mouseleave="isIconRotated = false">
                    <box-icon :class="{'rotate-180': isIconRotated, 'transition duration-300': true}" name='filter' color="white"></box-icon> Filter
                </h1>
                <h1 class="bg-white text-black py-2 px-4 rounded-lg mb-2 cursor-pointer transition duration-300">Add department</h1>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg mt-4">
            <table class="w-full">
                <thead class="bg-gray-800 text-gray-400 font-normal">
                    <tr>
                        <th class="p-4 text-left">Department Name</th>
                        <th class="p-4 text-left">Description</th>
                        <th class="p-4 text-left">Members</th>
                        <th class="p-4 text-left">Active projects</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="department in user_departments" :key="department.id"
                        @click="singleDepartment(department.id)"
                        class="hover:bg-gray-900 transition duration-300 hover:cursor-pointer border-b border-gray-900 bg-gray-950">
                        <td class="p-4">{{ department.name }}</td>
                        <td class="p-4">{{ department.description }}</td>
                        <td class="p-4">{{ department.users_count }}</td>
                        <td class="p-4"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h1 class="text-2xl mt-4">Other departments</h1>
        <div class="flex justify-between items-center mt-2">
            <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchOtherDepartments"
                class="focus:border-white focus:ring-white" />
            <div class="flex items-center gap-2">
                <h1 class="bg-gray-900  py-2 px-4 rounded-lg mb-2 cursor-pointer flex items-center gap-2 hover:bg-gray-700 transtion duration-300"
                @mouseover="isIconRotated2 = true" @mouseleave="isIconRotated2 = false">
                    <box-icon :class="{'rotate-180': isIconRotated2, 'transition duration-300': true}" name='filter' color="white"></box-icon> Filter
                </h1>
                <h1 class="bg-white text-black py-2 px-4 rounded-lg mb-2 cursor-pointer transition duration-300 hover:bg-gray-300">Add department</h1>
            </div>
        </div>
        <div class="overflow-x-auto rounded-lg mt-4">
            <table class="w-full">
                <thead class="bg-gray-800 text-gray-400 font-normal">
                    <tr>
                        <th class="p-4 text-left">Department Name</th>
                        <th class="p-4 text-left">Description</th>
                        <th class="p-4 text-left">Members</th>
                        <th class="p-4 text-left">Active projects</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-950 ">
                    <tr v-for="department in other_departments" :key="department.id" @click="isPopupVisible = true"
                        class="hover:bg-gray-900 transition duration-300 hover:cursor-pointer border-b border-gray-900">
                        <td class="p-4">{{ department.name }}</td>
                        <td class="p-4">{{ department.description }}</td>
                        <td class="p-4">{{ department.users_count }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

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
