<template>
    <Layout pageTitle="Departments" class="px-8">
        <h1 class="text-2xl">Your departments</h1>
        <InputWithIcon icon="search" placeholder="Search departments"  @input.stop="searchMyDepartments"/>
        <table class="w-full mt-4">
            <thead class="bg-gray-800 text-gray-400 font-normal">
                <tr>
                    <th class="p-4 text-left rounded-tl-lg">Department Name</th>
                    <th class="p-4 text-left">Description</th>
                    <th class="p-4 text-left">Members</th>
                    <th class="p-4 text-left rounded-tr-lg">Active projects</th>
                </tr>
            </thead>
            <tbody class="bg-gray-950">
                <tr v-for="department in user_departments" :key="department.id" class="hover:bg-gray-900 transition duration-300 hover:cursor-pointer border-b border-gray-900">
                    <td class="p-4">{{ department.name }}</td>
                    <td class="p-4">{{ department.description }}</td>
                    <td class="p-4">{{ department.users_count }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <h1 class="text-2xl">Other departments</h1>
        <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchOtherDepartments"/>
    </Layout>
</template>
<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const user_departments = ref(usePage().props.user_departments);
console.log(user_departments.value);
function searchMyDepartments() {
    console.log('Searching departments...');
}

function searchOtherDepartments() {
    console.log('Searching other departments...');
}
</script>