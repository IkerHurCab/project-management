<script setup>
import Layout from '@/Layouts/Layout.vue';
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const page = usePage();
const organization = computed(() => usePage().props.auth?.current_organization);
const users = page.props.users;
const isMobile = ref(false);

// Check if device is mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

const newOrganizationName = ref(organization.value?.name || '');
const newOrganizationDescription = ref(organization.value?.description || '');
const organizationHead = ref(organization.value?.organization_head || '');

// Logo handling
const organizationLogo = ref(null);
const previewLogo = ref(
    organization.value?.organization_logo
        ? new URL(`/storage/${organization.value.organization_logo}`, window.location.origin).href
        : null
);

// Banner handling (new)
const organizationBanner = ref(null);
const previewBanner = ref(
    organization.value?.organization_banner
        ? new URL(`/storage/${organization.value.organization_banner}`, window.location.origin).href
        : null
);

// Cropper references
const cropperLogo = ref(null);
const cropperBanner = ref(null);
const imageElement = ref(null);
const showCropperLogoModal = ref(false);
const showCropperBannerModal = ref(false);

const onLogoChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;

            img.onload = () => {
                if (img.width < 250 || img.height < 250) {
                    alert("The image must be at least 250x250 pixels.");
                    return;
                }

                previewLogo.value = e.target.result;
                showCropperLogoModal.value = true;

                setTimeout(() => {
                    if (imageElement.value) {
                        cropperLogo.value = new Cropper(imageElement.value, {
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
        };

        reader.readAsDataURL(file);
    }
};

const onBannerChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;

            img.onload = () => {
                const minWidth = 1000;
                const minHeight = 300;

                if (img.width < minWidth || img.height < minHeight) {
                    alert(`The image must be at least ${minWidth}x${minHeight} pixels.`);
                    return;
                }

                previewBanner.value = e.target.result;
                showCropperBannerModal.value = true;

                setTimeout(() => {
                    if (imageElement.value) {
                        cropperBanner.value = new Cropper(imageElement.value, {
                            aspectRatio: 3,
                            viewMode: 1,
                            autoCropArea: 1,
                            movable: true,
                            zoomable: false,
                            scalable: false,
                            rotatable: true,
                            cropBoxResizable: false,
                            dragMode: "move",
                        });
                    }
                }, 100);
            };
        };

        reader.readAsDataURL(file);
    }
};

const cropLogoImage = () => {
    if (cropperLogo.value) {
        const canvas = cropperLogo.value.getCroppedCanvas();
        canvas.toBlob((blob) => {
            organizationLogo.value = new File([blob], "cropped_logo.png", { type: "image/png" });
            previewLogo.value = URL.createObjectURL(blob);
            showCropperLogoModal.value = false;
            cropperLogo.value.destroy();
        }, "image/png");
    }
};

const cropBannerImage = () => {
    if (cropperBanner.value) {
        const canvas = cropperBanner.value.getCroppedCanvas();
        canvas.toBlob((blob) => {
            organizationBanner.value = new File([blob], "cropped_banner.png", { type: "image/png" });
            previewBanner.value = URL.createObjectURL(blob);
            showCropperBannerModal.value = false;
            cropperBanner.value.destroy();
        }, "image/png");
    }
};

const rotateLogoImage = (angle) => {
    if (cropperLogo.value) {
        cropperLogo.value.rotate(angle);
    }
};

const rotateBannerImage = (angle) => {
    if (cropperBanner.value) {
        cropperBanner.value.rotate(angle);
    }
};

const organizationInitials = computed(() => {
    return newOrganizationName.value
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
});

const updateOrganization = () => {
    if (!newOrganizationName.value.trim() || !newOrganizationDescription.value.trim()) {
        alert("Please fill in all required fields.");
        return;
    }

    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("name", newOrganizationName.value);
    formData.append("description", newOrganizationDescription.value);

    if (organizationLogo.value) {
        formData.append("organization_logo", organizationLogo.value);
    }

    if (organizationBanner.value) {
        formData.append("organization_banner", organizationBanner.value);
    }

    router.post(`/organizations/${organization.value.id}/update`, formData, {
        onSuccess: () => {
            toast.success("Organization updated successfully.");
            router.get('/organization');
        },
        onError: (errors) => {
            console.error("Error updating organization:", errors);
            alert("Failed to update organization.");
        }
    });
};
</script>

<template>
    <Layout pageTitle="Edit Organization">
        <div class="w-full md:w-11/12 lg:w-4/5 xl:w-2/3 mx-auto bg-gray-950 dark:bg-white dark:border-none dark:shadow-xl p-4 md:p-6 rounded-lg shadow-lg border border-gray-600">
            <h2 class="text-xl font-bold hover:text-gray-300 dark:hover:text-gray-500 cursor-pointer max-w-fit mb-4"
                @click="router.get('/organization')">↩ Discard changes</h2>
                
            <div class="flex flex-col md:flex-row md:justify-center md:items-start gap-6 md:gap-8">
                <!-- Form section -->
                <div class="w-full md:w-2/3">
                    <p class="mb-4">Edit the details of your organization.</p>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300 dark:text-black">Name</label>
                        <input v-model="newOrganizationName" id="name" type="text" maxlength="20"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-900 dark:bg-gray-200 text-white dark:text-black shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-300 dark:text-black">Description</label>
                        <textarea v-model="newOrganizationDescription" id="description" rows="3" maxlength="1000"
                            class="mt-1 p-1 block w-full min-h-[100px] md:min-h-[135px] max-h-[200px] md:max-h-[135px] rounded-md border-gray-700 dark:bg-gray-200 bg-gray-900 dark:text-black text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row mb-4 gap-4 sm:justify-between">
                        <div class="w-full sm:w-1/2">
                            <label for="organization_logo" class="block text-sm font-medium text-gray-300 dark:text-black">Upload Logo</label>
                            <input type="file" id="organization_logo" accept="image/*" @change="onLogoChange"
                                class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition duration-300">
                        </div>

                        <div class="w-full sm:w-1/2">
                            <label for="organization_banner" class="block text-sm font-medium text-gray-300 dark:text-black">Upload Banner</label>
                            <input type="file" id="organization_banner" accept="image/*" @change="onBannerChange"
                                class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition duration-300">
                        </div>
                    </div>
                </div>

                <!-- Preview section -->
                <div class="w-full md:w-1/3 p-4 md:p-6 bg-gray-900 dark:bg-gray-200 rounded-lg shadow-xl relative">
                    <h2 class="font-bold text-lg text-white dark:text-black mb-4">Preview</h2>
                    <template v-if="previewBanner">
                        <img :src="previewBanner" alt="Org Banner" class="w-full h-20 rounded object-cover shadow-md">
                    </template>
                    <template v-else>
                        <div class="w-full h-20 flex items-center justify-center rounded bg-gray-500 text-white font-bold text-xl overflow-hidden">
                        </div>
                    </template>
                    <div class="flex justify-center cursor-pointer mb-4">
                        <template v-if="previewLogo">
                            <img :src="previewLogo" alt="Org Logo" class="border absolute top-30 left-10 w-10 h-10 rounded object-cover shadow-md">
                        </template>
                        <template v-else>
                            <div class="border absolute top-30 left-10 w-10 h-10 flex items-center justify-center rounded bg-gray-700 text-white font-bold text-xl overflow-hidden">
                                {{ organizationInitials }}
                            </div>
                        </template>
                    </div>

                    <h3 class="text-white dark:text-black font-semibold">{{ newOrganizationName || 'Organization Name' }}</h3>
                    <div>
                        <p class="text-gray-400 dark:text-gray-600 text-justify text-sm mt-2 overflow-x-auto">
                            {{ newOrganizationDescription || 'Organization description...' }}
                        </p>
                    </div>
                </div>
            </div>

            <button 
                class="w-full md:w-auto md:min-w-[200px] mx-auto block text-center bg-white text-black dark:text-white dark:bg-blue-700 dark:hover:bg-blue-900 rounded-lg p-2 cursor-pointer hover:bg-gray-300 transition duration-300 mt-6"
                @click="updateOrganization">
                Save Changes
            </button>
        </div>

        <!-- Popup de Cropper (logo) - Responsive -->
        <div v-if="showCropperLogoModal" class="fixed inset-0 flex items-center justify-center bg-black/60 p-4 z-50">
            <div class="bg-gray-800 dark:bg-white p-4 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-lg font-bold mb-2">Crop Image</h2>
                <div class="mb-4 max-h-[50vh] overflow-auto">
                    <img ref="imageElement" :src="previewLogo" class="max-w-full">
                </div>
                <div class="flex flex-col sm:flex-row justify-between gap-2">
                    <button @click="showCropperLogoModal = false"
                        class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    <div class="flex justify-center gap-2 my-2 sm:my-0">
                        <button @click="rotateLogoImage(-90)" class="bg-gray-700 text-white px-4 py-2 rounded">↩ Rotate Left</button>
                        <button @click="rotateLogoImage(90)" class="bg-gray-700 text-white px-4 py-2 rounded">↪ Rotate Right</button>
                    </div>
                    <button @click="cropLogoImage" class="bg-green-500 text-white px-4 py-2 rounded">Crop</button>
                </div>
            </div>
        </div>

        <!-- Popup de Cropper (banner) - Responsive -->
        <div v-if="showCropperBannerModal" class="fixed inset-0 flex items-center justify-center bg-black/60 p-4 z-50">
            <div class="bg-gray-800 dark:bg-white p-4 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-lg font-bold mb-2">Crop Image</h2>
                <div class="mb-4 max-h-[50vh] overflow-auto">
                    <img ref="imageElement" :src="previewBanner" class="max-w-full">
                </div>
                <div class="flex flex-col sm:flex-row justify-between gap-2">
                    <button @click="showCropperBannerModal = false"
                        class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    <div class="flex justify-center my-2 sm:my-0">
                        <button @click="rotateBannerImage(180)" class="bg-gray-700 text-white px-4 py-2 rounded">↩ Rotate Banner</button>
                    </div>
                    <button @click="cropBannerImage" class="bg-green-500 text-white px-4 py-2 rounded">Crop</button>
                </div>
            </div>
        </div>
    </Layout>
</template>