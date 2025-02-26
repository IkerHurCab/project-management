<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import 'boxicons';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    department: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        required: true
    },
    filteredAvailableUsers: {
        type: Array,
        required: true
    },
    department_managers: {
        type: Array,
        required: true
    },
    projects: {
        type: Array,
        required: true
    },
    pagination: Object,
    totalUsers: Number
});

const filteredUsers = ref(props.users);
const filteredAvailableUsers = ref(props.filteredAvailableUsers);
const pagination = ref(props.pagination);
const searchQuery = ref('');
const searchAvailableUsersQuery = ref('');
const selectedUsers = ref([]);

const showActions = ref(false);

let timeout = null;
const url = '/departments/' + props.department.id;

function searchUsers(event, type) {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const search = type === 'members' ? query : searchQuery.value;
        const searchAvailableUsers = type === 'available' ? query : searchAvailableUsersQuery.value;

        router.get(`${url}?search=${search}&searchAvailableUsers=${searchAvailableUsers}&page=1`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                filteredUsers.value = page.props.users;
                pagination.value.current_page = 1;
                filteredAvailableUsers.value = page.props.filteredAvailableUsers;
                filteredAvailableUsers.value = filteredAvailableUsers.value.filter(user => !selectedUsers.value.some(selectedUser => selectedUser.id === user.id));
            }
        });
    }, 300);

}


function addUser(user) {
    selectedUsers.value.push(user);
    const index = filteredAvailableUsers.value.findIndex(u => u.id === user.id);
    if (index !== -1) {
        filteredAvailableUsers.value.splice(index, 1);
    }
}

function addMembers(members) {
        members.forEach(member => {
            router.post(`${url}/addUser`, { user_id: member.id }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                closeAddMemberModal();
            }
        });
        });
    
}

function removeSelectedUser(user) {
    const index = selectedUsers.value.findIndex(u => u.id === user.id);
    if (index !== -1) {
        selectedUsers.value.splice(index, 1);
        filteredAvailableUsers.value.push(user);
    }
}

function closeAddMemberModal(){
    selectedUsers.value.forEach(user => {
        filteredAvailableUsers.value.push(user);
    });
    selectedUsers.value = [];
    modalAddMember.value = false;
    searchAvailableUsersQuery.value = '';

    searchUsers();
}



function changePage(page) {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        if (page > pagination.value.last_page) {
            page = pagination.value.last_page;
        }

        const query = new URLSearchParams(window.location.search).get('search') || '';

        router.get(`${url}?search=${query}&page=${page}`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                pagination.value = page.props.pagination;
                filteredUsers.value = page.props.users;
            }
        });

    }, 300);
}



//modals

const modalAddMember = ref(false);
const modalKickMember = ref(false);




</script>

<template>
    <Layout :pageTitle="department.name" class="px-8">
        <div class="flex justify-end items-center mt-2">
            <div class="flex gap-2">
                <h1 class="bg-white text-black py-2 px-4 rounded-lg mb-2 cursor-pointer transition duration-300 hover:bg-gray-300"
                    @click="modalAddMember = true">Add member</h1>
            </div>
        </div>
        <div class="grid grid-cols-3 grid-rows-2 gap-4">
            <div class="col-span-2 rounded-2xl bg-gray-900 p-4 row-span-1">
                <h1 class="text-lg">{{ department.description }}</h1>
                <h2 class="mt-4">Department heads:
                    <span v-for="(manager, index) in department_managers" :key="manager.id">
                        {{ manager.name }}<span v-if="index < department_managers.length - 1">, </span>
                    </span>
                </h2>
            </div>

            <div class="rounded-2xl bg-gray-900 p-4 row-span-2  max-h-186 flex flex-col">
                <div class="flex gap-4 items-center justify-center mb-4">
                    <box-icon name='user' color="white" class="w-5 h-5"></box-icon>
                    <h1 class="text-xl text-center">Members</h1>
                </div>
                <InputWithIcon v-model="searchQuery" icon="search" placeholder="Search members" class="h-10 w-full"
                    @keyup.stop="searchUsers" />
                <ul class="mt-4 max-h-152 overflow-y-auto custom-scrollbar">
                    <li v-if="filteredUsers.length === 0"
                        class="text-gray-400 flex items-center justify-center gap-2 overflow-hidden">
                        <box-icon name='error-circle' color="#9CA3AF"></box-icon>
                        No members found.
                    </li>
                    <div v-else>
                        <li v-for="user in filteredUsers" :key="user.id"
                            class="mt-2 hover:bg-gray-800 p-1 transition duration-300 hover:cursor-pointer"
                            @mouseover.stop="[showActions = true, hoveredUserId = user.id]"
                            @mouseleave.stop="showActions = false">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="border border-gray-700 rounded-full relative w-10 h-10 flex items-center justify-center bg-gray-600">
                                        <span class="text-white text-lg font-bold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                        <div class="border border-gray-700 absolute w-2 h-2 rounded-full top-7 right-0"
                                            :class="{
                                                'bg-yellow-500': user.status === 'away',
                                                'bg-green-500': user.status === 'online',
                                                'bg-gray-900': user.status === 'offline'
                                            }">
                                        </div>
                                    </div>
                                    <span class="text-sm sm:text-base">{{ user.name }}</span>
                                </div>

                                <transition name="fade">
                                    <div v-if="showActions && user.id === hoveredUserId" class="flex gap-2">
                                        <box-icon name="door-open" color="red"
                                            @click="modalKickMember = true"></box-icon>
                                    </div>
                                </transition>
                            </div>
                        </li>
                    </div>
                </ul>
                <div class="flex justify-between items-center mt-4 align-self-end mt-auto">
                    <span class="text-gray-400">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <div class="flex gap-2 items-center">
                        <input type="number" v-model.number="pagination.current_page"
                            @input="changePage(pagination.current_page)"
                            class="w-16 text-center bg-gray-800 text-white rounded" :max="pagination.last_page"
                            :min="1" />
                        <div class="grid grid-cols-2 gap-2">
                            <button :disabled="pagination.current_page <= 1"
                                @click="changePage(pagination.current_page - 1)"
                                :class="['px-4 py-2 rounded transition duration-300',
                                    pagination.current_page <= 1 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-800 text-white hover:bg-gray-700 cursor-pointer']">
                                Previous
                            </button>
                            <button :disabled="pagination.current_page >= pagination.last_page"
                                @click="changePage(pagination.current_page + 1)"
                                :class="['px-4 py-2 rounded transition duration-300 ',
                                    pagination.current_page >= pagination.last_page ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-800 text-white hover:bg-gray-700 cursor-pointer']">
                                Next
                            </button>
                        </div>
                        <span class="text-gray-400">Showing {{ filteredUsers.length }} of {{ props.totalUsers }}</span>
                    </div>
                </div>
            </div>
            <div class="col-span-2 rounded-2xl pb-40">
                <div class="flex items-center gap-4 border-b pb-2">
                    <box-icon name='folder' color="white" class="w-5 h-5"></box-icon>
                    <h1 class="text-2xl">Projects</h1>
                </div>
                <ul class="mt-4">
                    <li v-if="projects.length === 0" class="text-gray-400">
                        There are no active projects in this department.
                    </li>
                    <li v-else v-for="project in projects" :key="project.id"
                        class="mt-2 hover:bg-secondary-light p-1 rounded transition duration-300 hover:cursor-pointer"
                        @click="router.visit(`/projects/${project.id}`)">

                        <div class="flex items-center gap-4 p-2 bg-gray-900 rounded">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm sm:text-base">{{ project.name }}</p>
                                    <box-icon :name="project.is_public ? 'lock-open' : 'lock'"
                                        :color="project.is_public ? 'limegreen' : 'red'" class="w-4 h-4"></box-icon>
                                </div>
                                <p class="text-xs text-gray-400">{{ project.description }}</p>
                                <p class="text-xs text-gray-400">Assigned Hours: {{ project.assigned_hours }}</p>

                                <!-- <p class="text-xs text-gray-400">
                                    Project Leader: 
                                    <span v-for="user in users" v-if="user.id === project.project_leader_id" :key="user.id">
                                        {{ user.name }}
                                    </span>
                                </p> -->
                            </div>

                            <div class="ml-auto">
                                <span :class="{
                                    'text-yellow-500': project.status === 'pending',
                                    'text-green-500': project.status !== 'pending'
                                }">
                                    {{ project.status }}
                                </span>
                            </div>
                        </div>

                    </li>
                </ul>
            </div>
        </div>

        <!--modals-->
        <div v-if="modalAddMember">
            <div class="fixed inset-0 flex items-center justify-center bg-black/50" @click="closeAddMemberModal">
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-2/3" @click.stop>
                    <h2 class="text-xl font-bold mb-4">Add Member</h2>
                    <p class="mb-4">Select the members you want to add</p>

                    <div class="flex gap-4 flex-wrap items-center mb-4 ">
                        <div v-for="user in selectedUsers" :key="user.id"
                            class="flex  items-center gap-1 bg-gray-700 p-1 rounded-full min-w-fit">

                            <div
                                class="border border-gray-700 rounded-full h-7 w-7 flex items-center justify-center bg-gray-600">
                                <span class="text-white text-sm font-bold">{{ user.name.charAt(0).toUpperCase()
                                    }}</span>
                            </div>
                            <span class="text-xs sm:text-sm">{{ user.name }}</span>
                            <box-icon name="x" color="white" size="sm" class="cursor-pointer"
                                @click="removeSelectedUser(user)"></box-icon>

                        </div>
                    </div>

                    <InputWithIcon v-model="searchAvailableUsersQuery" icon="search"
                        placeholder="Search available members" class="h-10 w-full" @keyup.stop="searchUsers" />

                    <ul class="my-4 max-h-52 overflow-y-auto custom-scrollbar">
                        <li v-if="filteredAvailableUsers.length === 0"
                            class="text-gray-400 flex items-center justify-center gap-2 overflow-hidden">
                            <box-icon name='error-circle' color="#9CA3AF"></box-icon>
                            No members found.
                        </li>
                        <div v-else>
                            <li v-for="user in filteredAvailableUsers" :key="user.id"
                                class="mt-2 hover:bg-gray-700 p-1 transition duration-300 hover:cursor-pointer"
                                @click="addUser(user)">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="border border-gray-700 rounded-full relative w-10 h-10 flex items-center justify-center bg-gray-600">
                                        <span class="text-white text-lg font-bold">{{ user.name.charAt(0).toUpperCase()
                                            }}</span>
                                    </div>
                                    <span class="text-sm sm:text-base">{{ user.name }}</span>
                                </div>
                            </li>
                        </div>
                    </ul>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="closeAddMemberModal"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:cursor-pointer hover:bg-red-600">Close</button>
                        <button @click="addMembers(selectedUsers)"
                            class="bg-gray-600 hover:cursor-pointer hover:bg-gray-700 text-white px-4 py-2 rounded">Add</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="modalKickMember">

        </div>

    </Layout>
</template>

<style>
ul.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

ul.custom-scrollbar::-webkit-scrollbar-track {
    background: #0c0a0a;
    border-radius: 10px;
}

ul.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #3D3D3D;
    border-radius: 10px;
}

ul.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>