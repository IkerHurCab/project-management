<script setup>
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import StandardButton from '@/Components/StandardButton.vue';
import EditDepartmentModal from '@/Pages/Users/EditDepartmentModal.vue';
import 'boxicons';
import { ref, onMounted } from 'vue';
import { computed } from 'vue'; 
import { toast } from 'vue3-toastify';
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const auth = usePage().props.auth;


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
const isEditDepartmentModalOpen = ref(false);
const isDarkMode = ref(false);
const isMobile = ref(false);
const openEditDepartmentModal = () => {
    isEditDepartmentModalOpen.value = true;
}

const closeEditDepartmentModal = () => {
    isEditDepartmentModalOpen.value = false;
}
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

const filteredUsers = ref(props.users);
const filteredAvailableUsers = ref(props.filteredAvailableUsers);
const pagination = ref(props.pagination);
const searchQuery = ref('');
const searchAvailableUsersQuery = ref('');
const selectedUsers = ref([]);
const user = computed(() => auth.user);
const isDepartmentHead = computed(() => {
  console.log('User ID:', user.value.id);
  console.log('Department managers:', props.department_managers);
  return props.department_managers.some(manager => manager.id === user.value.id);
});
const showActions = ref(false);
const hoveredUserId = ref(null);

let timeout = null;
const url = '/departments/' + props.department.id;

function searchUsers(event, type) {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const query = event?.target?.value?.toLowerCase() || '';
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
    if (members.length === 0) {
        toast.warning('Please select at least one member to add');
        return;
    }

    members.forEach(member => {
        router.post(`${url}/addUser`, { user_id: member.id }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                toast.success('User added successfully');
                closeAddMemberModal();
            },
            onError: (errors) => {
                toast.error('Error adding user');
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

function closeAddMemberModal() {
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

function kickMember(userId) {
    modalKickMember.value = true;
    userToKick.value = filteredUsers.value.find(user => user.id === userId);
}

function confirmKickMember() {
    if (!userToKick.value) return;

    router.post(`${url}/removeUser`, { user_id: userToKick.value.id }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            toast.success('User removed successfully');
            modalKickMember.value = false;
            userToKick.value = null;
            // Refresh the user list
            searchUsers();
        },
        onError: (errors) => {
            toast.error('Error removing user');
        }
    });
}

// Modals
const modalAddMember = ref(false);
const modalKickMember = ref(false);
const userToKick = ref(null);

// Status badge colors
const getStatusColor = (status) => {
    switch (status) {
        case 'online': return 'bg-green-500';
        case 'away': return 'bg-yellow-500';
        case 'offline': return 'bg-gray-500';
        default: return 'bg-gray-500';
    }
};

// Project status badge colors
const getProjectStatusColor = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'in_progress': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

// Format project status for display
const formatProjectStatus = (status) => {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<template>
    <Layout :pageTitle="department.name">
        <div class="p-4 md:p-6 max-w-7xl mx-auto">
            <!-- Header with Department Info and Add Member Button -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white dark:text-gray-800">{{ department.name }}</h1>
                </div>
                <div class="flex gap-1">
                    <button @click="modalAddMember = true"
                        class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-300 text-sm md:text-base flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM18 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                        Add Member
                    </button>
                    <StandardButton v-if="isDepartmentHead" @click="openEditDepartmentModal" size="sm">
                        Edit Department
                    </StandardButton>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Department Info Card -->
                <div
                    class="lg:col-span-2 bg-gray-900 dark:bg-white rounded-xl shadow-lg border border-gray-800 dark:border-none dark:shadow-xl overflow-hidden">
                    <div class="bg-gray-800 dark:bg-gray-100 p-4 border-b border-gray-700 dark:border-gray-200">
                        <h2 class="text-xl font-bold text-white dark:text-gray-800">Department Information</h2>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Description</h3>
                            <p class="text-white dark:text-gray-800 text-lg">{{ department.description }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Department Heads</h3>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="manager in department_managers" :key="manager.id"
                                    class="flex items-center gap-2 bg-gray-800 dark:bg-gray-100 p-2 rounded-lg border border-gray-700 dark:border-gray-300">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                        {{ manager.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="text-white dark:text-gray-800">{{ manager.name }}</span>
                                </div>
                                <div v-if="department_managers.length === 0" class="text-gray-400 dark:text-gray-600">
                                    No department heads assigned
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Members Card -->
                <div
                    class="lg:row-span-2 bg-gray-900 dark:bg-white rounded-xl shadow-lg border border-gray-800 dark:border-none dark:shadow-xl overflow-hidden flex flex-col">
                    <div class="bg-gray-800 dark:bg-gray-100 p-4 border-b border-gray-700 dark:border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-600"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h2 class="text-xl font-bold text-white dark:text-gray-800">Members</h2>
                        </div>
                    </div>
                    <div class="p-4 flex-grow flex flex-col">
                        <InputWithIcon v-model="searchQuery" icon="search" placeholder="Search members"
                            class="h-10 w-full mb-4" @input="searchUsers($event, 'members')" />

                        <div class="flex-grow overflow-hidden">
                            <ul class="overflow-y-auto h-full custom-scrollbar">
                                <li v-if="filteredUsers.length === 0"
                                    class="flex flex-col items-center justify-center h-32 text-gray-400 dark:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p>No members found</p>
                                </li>
                                <li v-else v-for="user in filteredUsers" :key="user.id"
                                    class="mb-2 p-3 bg-gray-800 dark:bg-gray-100 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-200 transition duration-300"
                                    @mouseover="[showActions = true, hoveredUserId = user.id]"
                                    @mouseleave="showActions = false">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold border border-gray-700">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-gray-800 dark:border-gray-100"
                                                    :class="getStatusColor(user.status)">
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-white dark:text-gray-800 font-medium">{{ user.name }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-600">{{ user.email || 'No email available' }}</p>
                                            </div>
                                        </div>
                                        <transition name="fade">
                                            <button v-if="showActions && user.id === hoveredUserId"
                                                @click.stop="kickMember(user.id)"
                                                class="p-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </transition>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Pagination Controls -->
                        <div
                            class="mt-4 border-t border-gray-800 dark:border-gray-200 pt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <span class="text-sm text-gray-400 dark:text-gray-600">
                                Showing {{ filteredUsers.length }} of {{ props.totalUsers }} members
                            </span>
                            <div class="flex items-center gap-2">
                                <button :disabled="pagination.current_page <= 1"
                                    @click="changePage(pagination.current_page - 1)"
                                    class="p-2 rounded-lg transition-colors"
                                    :class="pagination.current_page <= 1
                                        ? 'bg-gray-700 text-gray-500 cursor-not-allowed dark:bg-gray-200 dark:text-gray-400'
                                        : 'bg-gray-700 text-white hover:bg-gray-600 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-gray-300'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-1">
                                    <input type="number" v-model.number="pagination.current_page"
                                        @input="changePage(pagination.current_page)"
                                        class="w-12 h-9 text-center bg-gray-700 text-white rounded-lg border border-gray-600 dark:bg-gray-100 dark:text-gray-800 dark:border-gray-300"
                                        :max="pagination.last_page" :min="1" />
                                    <span class="text-gray-400 dark:text-gray-600">of {{ pagination.last_page }}</span>
                                </div>

                                <button :disabled="pagination.current_page >= pagination.last_page"
                                    @click="changePage(pagination.current_page + 1)"
                                    class="p-2 rounded-lg transition-colors"
                                    :class="pagination.current_page >= pagination.last_page
                                        ? 'bg-gray-700 text-gray-500 cursor-not-allowed dark:bg-gray-200 dark:text-gray-400'
                                        : 'bg-gray-700 text-white hover:bg-gray-600 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-gray-300'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Card -->
                <div
                    class="lg:col-span-2 bg-gray-900 dark:bg-white rounded-xl shadow-lg border border-gray-800 dark:border-none dark:shadow-xl overflow-hidden">
                    <div class="bg-gray-800 dark:bg-gray-100 p-4 border-b border-gray-700 dark:border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-600"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <h2 class="text-xl font-bold text-white dark:text-gray-800">Projects</h2>
                        </div>
                    </div>
                    <div class="p-4 md:p-6">
                        <div v-if="projects.length === 0"
                            class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <p class="text-lg">No active projects in this department</p>
                            <p class="text-sm mt-2">Projects assigned to this department will appear here</p>
                        </div>
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="project in projects" :key="project.id"
                                @click="router.visit(`/projects/${project.id}`)"
                                class="bg-gray-800 dark:bg-gray-100 rounded-lg p-4 border border-gray-700 dark:border-gray-300 hover:bg-gray-700 dark:hover:bg-gray-200 transition-colors cursor-pointer">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-white dark:text-gray-800 text-lg">{{ project.name }}</h3>
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 text-xs rounded-full"
                                            :class="getProjectStatusColor(project.status)">
                                            {{ formatProjectStatus(project.status) }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-gray-400 dark:text-gray-600 text-sm mb-3 line-clamp-2">{{
                                    project.description }}</p>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <div class="flex items-center text-xs text-gray-400 dark:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ project.assigned_hours || 0 }} hours
                                    </div>
                                    <div class="flex items-center text-xs text-gray-400 dark:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor"
                                            :class="project.is_public ? 'text-green-500' : 'text-red-500'">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                :class="project.is_public ? 'hidden' : ''" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"
                                                :class="project.is_public ? '' : 'hidden'" />
                                        </svg>
                                        {{ project.is_public ? 'Public' : 'Private' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div v-if="modalAddMember" class="fixed inset-0 flex items-center justify-center bg-black/70 p-4 z-50"
            @click="closeAddMemberModal">
            <div class="bg-gray-800 dark:bg-white p-6 rounded-xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-100"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white dark:text-gray-800">Add Members to Department</h2>
                    <button @click.stop="closeAddMemberModal"
                        class="text-gray-400 hover:text-white dark:hover:text-gray-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Selected Users -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Selected Members ({{
                        selectedUsers.length }})</h3>
                    <div
                        class="flex flex-wrap gap-2 min-h-12 p-2 bg-gray-700 dark:bg-gray-100 rounded-lg border border-gray-600 dark:border-gray-300">
                        <div v-if="selectedUsers.length === 0"
                            class="text-gray-500 dark:text-gray-400 text-sm flex items-center justify-center w-full">
                            No members selected
                        </div>
                        <div v-for="user in selectedUsers" :key="user.id"
                            class="flex items-center gap-2 bg-gray-600 dark:bg-gray-200 px-2 py-1 rounded-full">
                            <div
                                class="w-6 h-6 rounded-full bg-gray-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <span class="text-white dark:text-gray-800 text-sm">{{ user.name }}</span>
                            <button @click="removeSelectedUser(user)"
                                class="text-gray-300 dark:text-gray-600 hover:text-white dark:hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Available Users -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-400 dark:text-gray-600 mb-2">Available Members</h3>
                    <InputWithIcon v-model="searchAvailableUsersQuery" icon="search"
                        placeholder="Search available members" class="h-10 w-full"
                        @input="searchUsers($event, 'available')" />
                </div>

                <!-- Available Users List -->
                <div
                    class="max-h-60 overflow-y-auto custom-scrollbar mb-6 bg-gray-700 dark:bg-gray-100 rounded-lg border border-gray-600 dark:border-gray-300">
                    <div v-if="filteredAvailableUsers.length === 0"
                        class="flex flex-col items-center justify-center h-32 text-gray-400 dark:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p>No available members found</p>
                    </div>
                    <ul v-else>
                        <li v-for="user in filteredAvailableUsers" :key="user.id" @click="addUser(user)"
                            class="p-3 hover:bg-gray-600 dark:hover:bg-gray-200 transition-colors cursor-pointer border-b border-gray-600 dark:border-gray-300 last:border-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center text-white font-bold">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-white dark:text-gray-800">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-600">{{ user.email || 'No email available' }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click.stop="closeAddMemberModal"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button @click.stop="addMembers(selectedUsers)"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Selected Members
                    </button>
                </div>
            </div>
        </div>

        <!-- Kick Member Modal -->
        <div v-if="modalKickMember" class="fixed inset-0 flex items-center justify-center bg-black/70 p-4 z-50"
            @click="modalKickMember = false">
            <div class="bg-gray-800 dark:bg-white p-6 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100"
                @click.stop>
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-red-100 dark:bg-red-50 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white dark:text-gray-800 text-center mb-2">Remove Member</h2>
                <p class="mb-6 text-gray-300 dark:text-gray-600 text-center">
                    Are you sure you want to remove <span class="font-bold text-white dark:text-gray-800">{{
                        userToKick?.name }}</span> from this department?
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="modalKickMember = false"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button @click="confirmKickMember"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition duration-300 flex-1 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                        </svg>
                        Remove Member
                    </button>
                </div>
            </div>
        </div>
    </Layout>

    <EditDepartmentModal 
        :is-open="isEditDepartmentModalOpen" 
        :departmentHead="department_managers" 
        :department="department" 
        :users="filteredUsers" 
        @close="closeEditDepartmentModal" 
    />

</template>

<style>
ul.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

ul.custom-scrollbar::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 10px;
}

ul.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 10px;
}

ul.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

.dark ul.custom-scrollbar::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.dark ul.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
}

.dark ul.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
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