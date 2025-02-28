<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Layout from '../Layouts/Layout.vue';

const user = computed(() => usePage().props.auth?.user);
const organization_departments = computed(() => usePage().props.organization_departments);
const user_departments = computed(() => usePage().props.user_departments);

const currentOrganization = computed(() => usePage().props.auth?.current_organization);
const organizationInitials = computed(() => {
    if (!currentOrganization.value?.name) return "";
    return currentOrganization.value.name
        .split(" ")
        .map(word => word.charAt(0).toUpperCase())
        .join("");
});
const isOrganizationHead = computed(() => user.value?.id === currentOrganization.value?.organization_head);

// Función para comprobar si el usuario pertenece a un departamento
const isUserInDepartment = (departmentId) => {
    return user_departments.value.some(dep => dep.id === departmentId);
};

const handleDepartmentClick = (departmentId) => {
    if (isUserInDepartment(departmentId)) {
        router.get(`/departments/${departmentId}`);
    }
};

//modals
const logoPreviewModal = ref(false);
</script>

<template>
    <Layout pageTitle="Home">
        <template v-if="currentOrganization">
            <div class="bg-gray-900 rounded-2xl max-w-[1000px] mx-auto pb-4">
                <!-- Banner container con aspect ratio fijo -->
                <div class="relative w-full" style="aspect-ratio: 1000/300;">
                    <img v-if="currentOrganization.organization_banner"
                        :src="`/storage/${currentOrganization.organization_banner}`" alt="Banner"
                        class="absolute inset-0 w-full h-full object-cover rounded-t-2xl" />
                    <div v-else class="absolute inset-0 w-full h-full bg-gray-400 rounded-t-2xl"></div>
                    
                    <!-- Logo container -->
                    <div class="absolute bottom-0 left-0 transform translate-y-1/2 ml-4 sm:ml-8">
                        <div class="group cursor-pointer" @click="logoPreviewModal = true">
                            <img v-if="currentOrganization.organization_logo"
                                :src="`/storage/${currentOrganization.organization_logo}`" alt="Org"
                                class="rounded border-2 h-20 w-20 sm:h-32 sm:w-32 object-cover bg-gray-200" />
                            <div v-else
                                class="h-20 w-20 sm:h-32 sm:w-32 bg-gray-700 text-white flex items-center justify-center border-2 text-3xl sm:text-5xl font-bold rounded">
                                {{ organizationInitials }}
                            </div>
                            <div
                                class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded">
                            </div>
                        </div>
                    </div>

                    <!-- Edit button -->
                    <div v-if="isOrganizationHead"
                        @click="router.get(`/organizations/${currentOrganization.id}/edit`)"
                        class="absolute top-2 right-2 bg-gray-200 p-5 rounded-full shadow hover:bg-gray-300 transition cursor-pointer">
                        <div class="relative">
                            <box-icon type='solid' name='pencil'
                                class="absolute translate-y-full translate-x-full bottom-3 right-3"></box-icon>
                        </div>
                    </div>
                </div>

                <!-- Organization details -->
                <div class="mt-16 sm:mt-24 px-4 sm:px-8">
                    <h1 class="font-bold text-2xl sm:text-4xl">{{ currentOrganization.name }}</h1>
                    <p class="text-base sm:text-lg mt-3">{{ currentOrganization.description }}</p>

                    <!-- Departments section -->
                    <div class="bg-gray-800 rounded-lg p-2 mt-4">
                        <h2 class="text-2xl sm:text-2xl font-bold border-b-2 border-gray-600 p-2">Organization departments</h2>
                        <div v-if="organization_departments.length > 0">
                            <div v-for="department in organization_departments" :key="department.id"
                                :class="isUserInDepartment(department.id) ? 'bg-gray-900 hover:bg-gray-700 cursor-pointer' : 'bg-gray-700 opacity-50 cursor-not-allowed'"
                                class="mt-2 p-2 transition duration-300" @click="handleDepartmentClick(department.id)">
                                <h3 class="text-lg sm:text-xl">{{ department.name }}</h3>
                            </div>
                        </div>
                        <div v-else class="flex flex-wrap gap-2 items-center justify-center mt-2">
                            <box-icon name='error-circle' color="gray"></box-icon>
                            <h3 style="color: gray">There are no available departments in this organization.</h3>
                            <h3 class="text-blue-500 cursor-pointer hover:text-blue-700"
                                @click="router.get('/departments')">Go to departments</h3>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Layout>

    <transition name="fade">
        <div v-if="logoPreviewModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
            @click.self="logoPreviewModal = false">
            <div class="relative bg-gray-900 p-6 rounded-lg shadow-lg max-w-3xl">
                <h1 class="font-bold text-xl">{{ currentOrganization.name }}</h1>

                <button @click="logoPreviewModal = false"
                    class="absolute text-xl top-2 right-2 text-white hover:text-gray-300 transition duration-300 cursor-pointer">
                    &times;
                </button>
                <img v-if="currentOrganization.organization_logo"
                    :src="`/storage/${currentOrganization.organization_logo}`" alt="Org Logo"
                    class="mt-3 mx-auto rounded-lg" />

                <div v-else class="mt-3 text-7xl rounded-lg bg-gray-800 flex items-center justify-center min-w-[250px] min-h-[250px]">
                    {{ organizationInitials }}
                </div>
            </div>
        </div>
    </transition>
</template>


<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
