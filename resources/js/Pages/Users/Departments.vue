<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { toast } from 'vue3-toastify';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const isDarkMode = ref(false);
const isMobile = ref(false);

function checkDarkMode() {
    isDarkMode.value = document.documentElement.classList.contains('dark');
    return isDarkMode.value;
}

// Check if device is mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

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
    toast.info('Access request sent');
    isPopupVisible.value = false;
}

const isPopupVisible = ref(false);
</script>

<template>
    <Layout pageTitle="Departments">
        <div class="p-4 md:p-6 max-w-7xl mx-auto">
            <!-- Your Departments Section -->
            <div class="bg-gray-950 dark:bg-white rounded-xl shadow-lg border border-gray-800 dark:border-none dark:shadow-xl overflow-hidden mb-8">
                <div class="bg-gray-900 dark:bg-white p-4 md:p-6 border-b border-gray-800 dark:border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <h1 class="text-xl md:text-2xl font-bold text-white dark:text-gray-800">Your Departments</h1>
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <InputWithIcon 
                                icon="search" 
                                placeholder="Search departments" 
                                @input.stop="searchMyDepartments"
                                class="focus:border-blue-500 focus:ring-blue-500 w-full md:w-64" 
                            />
                            <button 
                                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-300 text-sm md:text-base flex items-center justify-center"
                                @click="modalCreateDepartment = true"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Create Department
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table for medium screens and up -->
                <div class="hidden md:block">
                    <table class="w-full">
                        <thead class="bg-gray-800 dark:bg-gray-200 dark:text-gray-700 text-gray-300 font-medium">
                            <tr>
                                <th class="p-4 text-left">Department Name</th>
                                <th class="p-4 text-left">Description</th>
                                <th class="p-4 text-left text-center">Members</th>
                                <th class="p-4 text-left text-center">Active Projects</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="user_departments.length === 0" class="border-gray-900 bg-gray-950 dark:bg-white">
                                <td class="p-6 text-center text-gray-400 dark:text-gray-500" colspan="4">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p>No departments found</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else v-for="department in user_departments" :key="department.id"
                                @click="singleDepartment(department.id)"
                                class="hover:bg-gray-900 dark:bg-white dark:hover:bg-gray-100 transition duration-300 hover:cursor-pointer border-b border-gray-800 dark:border-gray-200 bg-gray-950">
                                <td class="p-4 text-white dark:text-gray-800 font-medium">{{ department.name }}</td>
                                <td class="p-4 text-gray-400 dark:text-gray-600">{{ department.description }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        {{ department.users_count }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                        {{ projects_count[department.name] }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Card view for small screens -->
                <div class="md:hidden p-4">
                    <div v-if="user_departments.length === 0" class="p-6 text-center bg-gray-900 dark:bg-gray-100 rounded-lg">
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-400 dark:text-gray-600">No departments found</p>
                        </div>
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="department in user_departments" :key="department.id"
                            @click="singleDepartment(department.id)"
                            class="p-4 bg-gray-900 dark:bg-gray-100 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition duration-300 hover:cursor-pointer border border-gray-800 dark:border-gray-300 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-white dark:text-gray-800 text-lg">{{ department.name }}</h3>
                                    <p class="text-sm text-gray-400 dark:text-gray-600 mt-1 line-clamp-2">{{ department.description }}</p>
                                </div>
                                <div class="flex flex-col items-end space-y-1">
                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        {{ department.users_count }} Members
                                    </span>
                                    <span class="inline-flex items-center justify-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                        {{ projects_count[department.name] }} Projects
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Departments Section -->
            <div class="bg-gray-950 dark:bg-white rounded-xl shadow-lg border border-gray-800 dark:border-none dark:shadow-xl overflow-hidden">
                <div class="bg-gray-900 dark:bg-white p-4 md:p-6 border-b border-gray-800 dark:border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <h1 class="text-xl md:text-2xl font-bold text-white dark:text-gray-800">Other Departments</h1>
                        <InputWithIcon 
                            icon="search" 
                            placeholder="Search departments" 
                            @input.stop="searchOtherDepartments"
                            class="focus:border-blue-500 focus:ring-blue-500 w-full md:w-64" 
                        />
                    </div>
                </div>

                <!-- Table for medium screens and up -->
                <div class="hidden md:block">
                    <table class="w-full">
                        <thead class="bg-gray-800 dark:bg-gray-200 dark:text-gray-700 text-gray-300 font-medium">
                            <tr>
                                <th class="p-4 text-left">Department Name</th>
                                <th class="p-4 text-left">Description</th>
                                <th class="p-4 text-left text-center">Members</th>
                                <th class="p-4 text-left text-center">Active Projects</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="other_departments.length === 0" class="border-gray-900 bg-gray-950 dark:bg-white">
                                <td class="p-6 text-center text-gray-400 dark:text-gray-500" colspan="4">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p>No departments found</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else v-for="department in other_departments" :key="department.id"
                                @click="isPopupVisible = true"
                                class="hover:bg-gray-900 dark:bg-white dark:hover:bg-gray-100 transition duration-300 hover:cursor-pointer border-b border-gray-800 dark:border-gray-200 bg-gray-950">
                                <td class="p-4 text-white dark:text-gray-800 font-medium">{{ department.name }}</td>
                                <td class="p-4 text-gray-400 dark:text-gray-600">{{ department.description }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        {{ department.users_count }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Unknown
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Card view for small screens -->
                <div class="md:hidden p-4">
                    <div v-if="other_departments.length === 0" class="p-6 text-center bg-gray-900 dark:bg-gray-100 rounded-lg">
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-400 dark:text-gray-600">No departments found</p>
                        </div>
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="department in other_departments" :key="department.id"
                            @click="isPopupVisible = true"
                            class="p-4 bg-gray-900 dark:bg-gray-100 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition duration-300 hover:cursor-pointer border border-gray-800 dark:border-gray-300 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-white dark:text-gray-800 text-lg">{{ department.name }}</h3>
                                    <p class="text-sm text-gray-400 dark:text-gray-600 mt-1 line-clamp-2">{{ department.description }}</p>
                                </div>
                                <div class="flex flex-col items-end space-y-1">
                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        {{ department.users_count }} Members
                                    </span>
                                    <span class="inline-flex items-center justify-center bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Unknown
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Denied Popup - Responsive -->
        <div v-if="isPopupVisible" class="fixed inset-0 flex items-center justify-center bg-black/70 p-4 z-50" @click="isPopupVisible = false">
            <div class="bg-gray-800 dark:bg-white p-6 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100" 
                @click.stop>
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-red-100 dark:bg-red-50 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H9m3-4V8m0 0V6m0 0h2m-2 0H9" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white dark:text-gray-800 text-center mb-2">Access Denied</h2>
                <p class="mb-6 text-gray-300 dark:text-gray-600 text-center">You do not have access to this department. Would you like to request access?</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="isPopupVisible = false"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button @click.stop="requestAccess"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        Request Access
                    </button>
                </div>
            </div>
        </div>

        <!-- Create Department Modal - Responsive -->
        <div v-if="modalCreateDepartment" class="fixed inset-0 flex items-center justify-center bg-black/70 p-4 z-50"
            @click.stop="modalCreateDepartment = false">
            <div class="bg-gray-800 dark:bg-white p-6 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white dark:text-gray-800">Create Department</h2>
                    <button @click.stop="modalCreateDepartment = false" class="text-gray-400 hover:text-white dark:hover:text-gray-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Department Name</label>
                        <input 
                            v-model="newDepartmentName" 
                            id="name" 
                            type="text" 
                            placeholder="Enter department name" 
                            class="w-full p-3 bg-gray-700 dark:bg-gray-100 border border-gray-600 dark:border-gray-300 rounded-lg text-white dark:text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                        />
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Description</label>
                        <textarea 
                            v-model="newDepartmentDescription" 
                            id="description" 
                            rows="3" 
                            placeholder="Enter department description" 
                            class="w-full p-3 bg-gray-700 dark:bg-gray-100 border border-gray-600 dark:border-gray-300 rounded-lg text-white dark:text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        ></textarea>
                    </div>
                    <div>
                        <label for="department_head" class="block text-sm font-medium text-gray-300 dark:text-gray-700 mb-1">Department Head</label>
                        <select 
                            v-model="departmentHead" 
                            id="department_head" 
                            class="w-full p-3 bg-gray-700 dark:bg-gray-100 border border-gray-600 dark:border-gray-300 rounded-lg text-white dark:text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        >
                            <option disabled value="">Select a department head</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button @click.stop="modalCreateDepartment = false"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                        <button @click.stop="createDepartment"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>