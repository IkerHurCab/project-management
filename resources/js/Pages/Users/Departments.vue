<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { toast } from 'vue3-toastify';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
const isDarkMode = ref(false);

const errors = ref({});

function checkDarkMode() {
    isDarkMode.value = document.documentElement.classList.contains('dark');
    return isDarkMode.value;
}

const user_departments = ref(usePage().props.user_departments);
const other_departments = ref(usePage().props.other_departments);
const projects_count = ref(usePage().props.projects_count);
const users = ref(usePage().props.users);

//modals
const modalCreateDepartment = ref(false);

//v-models
const newDepartmentName = ref('');
const newDepartmentDescription = ref('');
const departmentHead = ref();

//funciones
function createDepartment() {
    router.post('/departments', {
        name: newDepartmentName.value,
        description: newDepartmentDescription.value,
        department_head: departmentHead.value,
        preserveState: true,
        preserveScroll: true,
    }, {
        onSuccess: (page) => {
            toast.success('Department created successfully');
            user_departments.value = page.props.user_departments;
            modalCreateDepartment.value = false;
            newDepartmentName.value = '';
            newDepartmentDescription.value = '';
        },
        onError: (err) => {
            errors.value = err;
        }
    });
}

let timeout = null;

function searchMyDepartments(event) {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const query = event.target.value.toLowerCase();
        router.get(`/departments?searchMyDepartments=${query}`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {

                user_departments.value = page.props.user_departments;
            }
        });
    }, 300);
}

function searchOtherDepartments(event) {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const query = event.target.value.toLowerCase();
        router.get(`/departments?searchOtherDepartments=${query}`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                other_departments.value = page.props.other_departments;
            }
        });
    }, 300);
}

function singleDepartment(departmentId) {
    window.location.href = `/departments/${departmentId}`;
}

function requestAccess() {
    console.log('Requesting access...');
}

const isPopupVisible = ref(false);

const closeCreateDepartmentModal = () => {
    modalCreateDepartment.value = false;
    newDepartmentName.value = '';
    newDepartmentDescription.value = '';
    departmentHead.value = '';
    errors.value = {};
}

</script>

<template>
    <Layout pageTitle="Departments">
        <h1 class="text-2xl mt-5">Your departments</h1>
        <div class="flex justify-between items-center mt-2">
            <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchMyDepartments"
                class="focus:border-white" />
            <div class="flex gap-2">
                <h1 class="bg-white text-black dark:bg-blue-500 dark:hover:bg-blue-600 dark:text-white py-2 px-4 rounded-lg mb-2 cursor-pointer transition duration-300"
                    @click="modalCreateDepartment = true">Create department</h1>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg mt-4 border dark:border-none dark:shadow-xl">
            <table class="w-full">
                <thead class="bg-gray-800 dark:bg-gray-200 dark:text-gray-700 text-gray-400 font-normal">
                    <tr>
                        <th class="p-4 text-left">Department Name</th>
                        <th class="p-4 text-left">Description</th>
                        <th class="p-4 text-left">Members</th>
                        <th class="p-4 text-left">Active projects</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="user_departments.length === 0" class="border-gray-900 bg-gray-950 dark:bg-white">
                        <td class="p-4 text-center" colspan="4">No departments found.</td>
                    </tr>
                    <tr v-else v-for="department in user_departments" :key="department.id"
                        @click="singleDepartment(department.id)"
                        class="hover:bg-gray-900 dark:bg-white dark:hover:bg-gray-100  transition duration-300 hover:cursor-pointer border-b border-gray-900 dark:border-gray-200 bg-gray-950">
                        <td class="p-4">{{ department.name }}</td>
                        <td class="p-4">{{ department.description }}</td>
                        <td class="p-4">{{ department.users_count }}</td>
                        <td class="p-4">
                            {{ projects_count[department.name] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h1 class="text-2xl mt-4">Other departments</h1>
        <div class="flex justify-between items-center mt-2">
            <InputWithIcon icon="search" placeholder="Search departments" @input.stop="searchOtherDepartments"
                class="focus:border-white focus:ring-white" />
        </div>
        <div class="overflow-x-auto rounded-lg dark:border-none dark:shadow-xl mt-4">
            <table class="w-full">
                <thead class="bg-gray-800 text-gray-400 dark:bg-gray-200 dark:text-gray-700 font-normal">
                    <tr>
                        <th class="p-4 text-left">Department Name</th>
                        <th class="p-4 text-left">Description</th>
                        <th class="p-4 text-left">Members</th>
                        <th class="p-4 text-left">Active projects</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-950 ">
                    <tr v-if="other_departments.length === 0" class="dark:bg-white bg-gray-950">
                        <td class="p-4 text-center" colspan="4">No departments found.</td>
                    </tr>
                    <tr v-else v-for="department in other_departments" :key="department.id"
                        @click="isPopupVisible = true"
                        class="hover:bg-gray-900 dark:bg-white dark:hover:bg-gray-100 transition duration-300 hover:cursor-pointer border-b border-gray-900">
                        <td class="p-4">{{ department.name }}</td>
                        <td class="p-4">{{ department.description }}</td>
                        <td class="p-4">{{ department.users_count }}</td>
                        <td class="p-4">
                            <span class="text-red-500">?</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="isPopupVisible">
            <div class="fixed inset-0 flex items-center justify-center bg-black/50" @click="isPopupVisible = false">
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-1/3 dark:bg-white">
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

        <div v-if="modalCreateDepartment">
            <div v-if="modalCreateDepartment"
                class="fixed inset-0 flex items-center justify-center bg-black/50 transition duration-300"
                @click.stop="closeCreateDepartmentModal" @esc.ctrl="closeCreateDepartmentModal">
                <div class="bg-gray-800 dark:bg-white p-8 rounded-lg shadow-lg w-1/3"
                    @click.stop="modalCreateDepartment = true">
                    <h2 class="text-xl font-bold mb-4">Create Department</h2>
                    <p class="mb-4">Fill in the details to create a new department.</p>
                    <div class="mb-4">
                        <label for="name"
                            class="block text-sm font-medium text-gray-300 dark:text-gray-700">Name</label>
                        <input v-model="newDepartmentName" id="name" type="text" placeholder="Department name"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 border dark:bg-gray-100 dark:text-black bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>
                    <div class="mb-4">
                        <label for="description"
                            class="block text-sm font-medium text-gray-300 dark:text-gray-700">Description</label>
                        <textarea v-model="newDepartmentDescription" id="description" rows="3"
                            placeholder="Department description"
                            class="mt-1 p-1 block w-full rounded-md border border-gray-700 bg-gray-700 dark:bg-gray-100 dark:text-black text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="department_head"
                            class="block text-sm font-medium text-gray-300 dark:text-gray-700">Department
                            Head</label>
                        <select v-model="departmentHead" id="department_head"
                            class="mt-1 p-1 block w-full dark:bg-gray-100 border dark:text-black rounded-md border-gray-700 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option disabled value="">Select a department head</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <p v-if="errors.department_head" class="text-red-500 text-sm mt-1">{{ errors.department_head }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click.stop="closeCreateDepartmentModal"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:cursor-pointer hover:bg-red-600">Close</button>
                        <button @click.stop="createDepartment"
                            class="bg-gray-600 hover:cursor-pointer hover:bg-gray-700 text-white px-4 py-2 rounded">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>