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

const organizationBanner = ref(null);
const previewBanner = ref(null);

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
                            rotatable: true, // ✅ Ahora se activa correctamente
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
                const aspectRatioMin = 2.5; // Relación mínima aceptada (ej: 1000x400)
                const aspectRatioMax = 4;   // Relación máxima aceptada (ej: 1600x400)

                const imageAspectRatio = img.width / img.height;

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
            organizationBanner.value = new File([blob], "cropped_logo.png", { type: "image/png" });
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

    if (organizationBanner.value) {
        formData.append("organization_banner", organizationBanner.value);
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
        <div class="w-2/3 mx-auto bg-gray-950 p-6 rounded-lg shadow-lg space-x-6 border border-gray-600">
            <div class="flex justify-center items-center gap-8">
                <div class="w-2/3">
                    <p class="mb-4">Fill in the details to create a new organization.</p>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                        <input v-model="newOrganizationName" id="name" type="text" placeholder="Organization name"
                            maxlength="20"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                        <textarea v-model="newOrganizationDescription" id="description" rows="3" maxlength="1000"
                            placeholder="Organization description"
                            class="mt-1 p-1 block min-h-135 max-h-135 w-full rounded-md border-gray-700 bg-gray-900 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="organization_head" class="block text-sm font-medium text-gray-300">Organization
                            Head</label>
                        <select v-model="organizationHead" id="organization_head"
                            class="mt-1 p-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option disabled value="">Select an organization head</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>

                    <div class="flex mb-4 justify-between">
                        <div>
                            <label for="organization_logo" class="block text-sm font-medium text-gray-300">Upload
                                Logo</label>
                            <input type="file" id="organization_logo" accept="image/*" @change="onLogoChange"
                                class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition duration-300">
                        </div>

                        <div>
                            <label for="organization_banner" class="block text-sm font-medium text-gray-300">Upload
                                Banner</label>
                            <input type="file" id="organization_banner" accept="image/*" @change="onBannerChange"
                                class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition duration-300">
                        </div>
                    </div>
                </div>

                <div class="w-1/3 p-6 bg-gray-900 rounded-lg shadow-md relative">
                    <h2 class="font-bold text-lg text-white mb-4">Preview</h2>
                    <template v-if="previewBanner">
                        <img :src="previewBanner" alt="Org Banner" class="w-full h-20 rounded object-cover shadow-md">
                    </template>
                    <template v-else>
                            <div
                                class="w-full h-20 flex items-center justify-center rounded bg-gray-500 text-white font-bold text-xl overflow-hidden">
                            </div>
                        </template>
                    <div class="flex justify-center cursor-pointer mb-4">
                        <template v-if="previewLogo">
                            <img :src="previewLogo" alt="Org Logo" class="border absolute top-30 left-10 w-10 h-10 rounded object-cover shadow-md">
                        </template>
                        <template v-else>
                            <div
                                class="border absolute top-30 left-10 w-10 h-10 flex items-center justify-center rounded bg-gray-700 text-white font-bold text-xl overflow-hidden">
                                {{ organizationInitials }}
                            </div>
                        </template>
                    </div>

                    <h3 class="text-white font-semibold">{{ newOrganizationName || 'Organization Name' }}</h3>
                    <div>
                        <p class="text-gray-400 text-justify text-sm mt-2 overflow-x-auto">{{ newOrganizationDescription
                            ||
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

        <!-- Popup de Cropper (logo) -->
        <div v-if="showCropperLogoModal" class="fixed inset-0 flex items-center justify-center bg-black/60">
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-300 max-h-200">
                <h2 class="text-lg font-bold mb-2">Crop Image</h2>
                <div class="mb-4">
                    <img ref="imageElement" :src="previewLogo" class="max-w-full">
                </div>
                <div class="flex justify-between">
                    <button @click="showCropperLogoModal = false"
                        class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    <div class="flex justify-between mb-4 px-2 gap-2">
                        <button @click="rotateLogoImage(-90)" class="bg-gray-700 text-white px-4 py-2 rounded">↩ Rotate
                            Left</button>
                        <button @click="rotateLogoImage(90)" class="bg-gray-700 text-white px-4 py-2 rounded">↪ Rotate
                            Right</button>
                    </div>
                    <button @click="cropLogoImage" class="bg-green-500 text-white px-4 py-2 rounded">Crop</button>
                </div>
            </div>
        </div>

        <!-- Popup de Cropper (banner) -->
        <div v-if="showCropperBannerModal" class="fixed inset-0 flex items-center justify-center bg-black/60">
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-300 max-h-200">
                <h2 class="text-lg font-bold mb-2">Crop Image</h2>
                <div class="mb-4">
                    <img ref="imageElement" :src="previewBanner" class="max-w-full">
                </div>
                <div class="flex justify-between">
                    <button @click="showCropperBannerModal = false"
                        class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    <div class="flex justify-between mb-4 px-2 gap-2">
                        <button @click="rotateBannerImage(180)" class="bg-gray-700 text-white px-4 py-2 rounded">↩
                            Rotate
                            Banner</button>
                    </div>
                    <button @click="cropBannerImage" class="bg-green-500 text-white px-4 py-2 rounded">Crop</button>
                </div>
            </div>
        </div>
    </Layout>
</template>
