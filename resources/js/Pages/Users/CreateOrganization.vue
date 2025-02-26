<script setup>
import Layout from '@/Layouts/Layout.vue';
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const page = usePage();
const users = page.props.users;

const newOrganizationName = ref('');
const newOrganizationDescription = ref('');
const organizationHead = ref('');
const organizationLogo = ref(null);
const previewLogo = ref(null);

const cropper = ref(null);
const imageElement = ref(null);
const showCropperModal = ref(false);

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewLogo.value = e.target.result;
            showCropperModal.value = true;
            setTimeout(() => {
                if (imageElement.value) {
                    cropper.value = new Cropper(imageElement.value, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        movable: false,
                        zoomable: false,
                        scalable: false,
                        rotatable: true,
                    });
                }
            }, 100);
        };
        reader.readAsDataURL(file);
    }
};

const cropImage = () => {
    if (cropper.value) {
        const canvas = cropper.value.getCroppedCanvas();
        canvas.toBlob((blob) => {
            organizationLogo.value = new File([blob], "cropped_logo.png", { type: "image/png" });
            previewLogo.value = URL.createObjectURL(blob);
            showCropperModal.value = false;
            cropper.value.destroy();
        }, "image/png");
    }
};

const rotateImage = (angle) => {
    if (cropper.value) {
        cropper.value.rotate(angle);
    }
};

const organizationInitials = computed(() => {
    return newOrganizationName.value
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
});

const createOrganization = () => {
    if (!newOrganizationName.value.trim() || !newOrganizationDescription.value.trim() || !organizationHead.value) {
        alert("Please fill in all required fields.");
        return;
    }

    const formData = new FormData();
    formData.append("name", newOrganizationName.value);
    formData.append("description", newOrganizationDescription.value);
    formData.append("organization_head", organizationHead.value);

    if (organizationLogo.value) {
        formData.append("organization_logo", organizationLogo.value);
    }

    router.post('/organizations/create', formData, {
        onSuccess: () => {
            alert("Organization created successfully!");
        },
        onError: (errors) => {
            console.error("Error creating organization:", errors);
            alert("Failed to create organization.");
        }
    });
};
</script>

<template>
    <Layout pageTitle="Create Organization">
        <div class="w-2/3 mx-auto bg-gray-800 p-6 rounded-lg shadow-lg space-x-6">
            <div class="flex justify-center items-center gap-8">
                <div class="w-2/3">
                    <p class="mb-4">Fill in the details to create a new organization.</p>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                        <input v-model="newOrganizationName" id="name" type="text" placeholder="Organization name"
                            maxlength="20"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                        <textarea v-model="newOrganizationDescription" id="description" rows="3" maxlength="100"
                            placeholder="Organization description"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="organization_head" class="block text-sm font-medium text-gray-300">Organization
                            Head</label>
                        <select v-model="organizationHead" id="organization_head"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option disabled value="">Select an organization head</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="organization_logo" class="block text-sm font-medium text-gray-300">Upload
                            Logo</label>
                        <input type="file" id="organization_logo" accept="image/*" @change="onFileChange"
                            class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition duration-300">
                    </div>
                </div>

                <div class="w-1/3 p-6 bg-gray-900 rounded-lg shadow-md text-center">
                    <h2 class="font-bold text-lg text-white mb-4">Preview</h2>
                    <div class="flex justify-center cursor-pointer mb-4">
                        <template v-if="previewLogo">
                            <img :src="previewLogo" alt="Org Logo" class="w-20 h-20 rounded object-cover shadow-md">
                        </template>
                        <template v-else>
                            <div
                                class="w-20 h-20 flex items-center justify-center rounded bg-gray-700 text-white font-bold text-xl overflow-hidden">
                                {{ organizationInitials }}
                            </div>
                        </template>
                    </div>

                    <h3 class="text-white text-lg font-semibold">{{ newOrganizationName || 'Organization Name' }}</h3>
                    <div>
                        <p class="text-gray-400 text-sm mt-2 overflow-x-auto">{{ newOrganizationDescription ||
                            'Organization description...'
                            }}</p>
                    </div>
                </div>
            </div>

            <h2 class="text-center bg-white text-black rounded-lg p-2 cursor-pointer hover:bg-gray-300 transition duration-300"
                @click.stop="createOrganization">
                Create
            </h2>
        </div>

        <!-- Popup de Cropper -->
        <div v-if="showCropperModal" class="fixed inset-0 flex items-center justify-center bg-black/60">
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-300 max-h-200">
                <h2 class="text-lg font-bold mb-2">Crop Image</h2>
                <div class="mb-4">
                    <img ref="imageElement" :src="previewLogo" class="max-w-full">
                </div>
                <div class="flex justify-between">
                    <button @click="showCropperModal = false"
                        class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    <div class="flex justify-between mb-4">
                        <button @click="rotateImage(-90)" class="bg-gray-700 text-white px-4 py-2 rounded">↩ Rotate
                            Left</button>
                        <button @click="rotateImage(90)" class="bg-gray-700 text-white px-4 py-2 rounded">↪ Rotate
                            Right</button>
                    </div>
                    <button @click="cropImage" class="bg-green-500 text-white px-4 py-2 rounded">Crop</button>
                </div>
            </div>
        </div>
    </Layout>
</template>
