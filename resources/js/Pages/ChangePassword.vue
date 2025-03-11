<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import Layout from '@/Layouts/Layout.vue';
import InputWithIcon from '../Components/InputWithIcon.vue';

const user = computed(() => usePage().props.auth?.user);
const isMobile = ref(false);

// Check if device is mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');

const isSubmitting = ref(false);
const errors = ref({});
const successMessage = ref('');

const passwordStrength = computed(() => {
  if (!newPassword.value) return 0;
  
  let strength = 0;
  if (newPassword.value.length >= 8) strength += 1;
  if (/\d/.test(newPassword.value)) strength += 1;
  if (/[a-z]/.test(newPassword.value)) strength += 1;
  if (/[A-Z]/.test(newPassword.value)) strength += 1;
  if (/[^A-Za-z0-9]/.test(newPassword.value)) strength += 1;
  
  return strength;
});

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value;
  if (strength === 0) return '';
  if (strength < 3) return 'Weak';
  if (strength < 5) return 'Medium';
  return 'Strong';
});

const passwordStrengthColor = computed(() => {
  const strength = passwordStrength.value;
  if (strength === 0) return 'bg-gray-300';
  if (strength < 3) return 'bg-red-500';
  if (strength < 5) return 'bg-yellow-500';
  return 'bg-green-500';
});

const validateForm = () => {
  errors.value = {};
  
  if (!currentPassword.value) {
    errors.value.currentPassword = 'Current password is required';
  }
  
  if (!newPassword.value) {
    errors.value.newPassword = 'New password is required';
  } else if (newPassword.value.length < 8) {
    errors.value.newPassword = 'Password must be at least 8 characters';
  } else if (passwordStrength.value < 3) {
    errors.value.newPassword = 'Password is too weak';
  }
  
  if (!confirmPassword.value) {
    errors.value.confirmPassword = 'Please confirm your new password';
  } else if (newPassword.value !== confirmPassword.value) {
    errors.value.confirmPassword = 'Passwords do not match';
  }
  
  return Object.keys(errors.value).length === 0;
};

const updatePassword = async () => {
  if (!validateForm()) return;
  
  isSubmitting.value = true;
  successMessage.value = '';
  errors.value = {};
  
  try {
    const response = await axios.put('/user/password/update', {
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value
    });
    
    if (response.status === 200) {
      currentPassword.value = '';
      newPassword.value = '';
      confirmPassword.value = '';
      
      successMessage.value = response.data.message || 'Password updated successfully!';
    }
  } catch (error) {
    console.error('Password update error:', error);
    successMessage.value = '';
    
    if (error.response?.status === 422) {
      if (error.response.data.errors) {
        const serverErrors = error.response.data.errors;
        
        if (serverErrors.current_password) {
          errors.value.currentPassword = Array.isArray(serverErrors.current_password) 
            ? serverErrors.current_password[0] 
            : serverErrors.current_password;
        }
        
        if (serverErrors.password) {
          errors.value.newPassword = Array.isArray(serverErrors.password)
            ? serverErrors.password[0]
            : serverErrors.password;
        }
        
        if (serverErrors.password_confirmation) {
          errors.value.confirmPassword = Array.isArray(serverErrors.password_confirmation)
            ? serverErrors.password_confirmation[0]
            : serverErrors.password_confirmation;
        }
      } else {
        errors.value.general = 'Validation failed. Please check your input.';
      }
    } else {
      errors.value.general = 'An error occurred. Please try again.';
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Layout pageTitle="Change Password">
    <div class="bg-gray-950 dark:bg-gray-200 rounded-lg h-fit border border-gray-500 mx-2 md:mx-6">
      <h1 class="border-b border-gray-500 p-2 rounded-t-lg dark:text-gray-500">CHANGE PASSWORD</h1>
      <p class="px-4 mt-4">Update your password to keep your account secure.</p>
      
      <form @submit.prevent="updatePassword" class="p-4">
        <div class="w-full max-w-md space-y-6">
          <div v-if="successMessage" class="bg-green-100 dark:bg-green-200 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ successMessage }}
          </div>
          
          <div v-if="errors.general" class="bg-red-100 dark:bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ errors.general }}
          </div>
          
          <div>
            <label for="current-password" class="block mb-1">Current Password</label>
            <InputWithIcon 
              icon="lock" 
              id="current-password"
              v-model="currentPassword" 
              type="password" 
              class="w-full" 
              required 
            />
            <p v-if="errors.currentPassword" class="text-red-500 text-sm mt-1">{{ errors.currentPassword }}</p>
          </div>
          
          <div>
            <label for="new-password" class="block mb-1">New Password</label>
            <InputWithIcon 
              icon="lock" 
              id="new-password"
              v-model="newPassword" 
              type="password" 
              class="w-full" 
              required 
            />
            <p v-if="errors.newPassword" class="text-red-500 text-sm mt-1">{{ errors.newPassword }}</p>
            
            <div v-if="newPassword" class="mt-2">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm">Password Strength:</span>
                <span class="text-sm" :class="{
                  'text-red-500': passwordStrengthText === 'Weak',
                  'text-yellow-500': passwordStrengthText === 'Medium',
                  'text-green-500': passwordStrengthText === 'Strong'
                }">{{ passwordStrengthText }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all duration-300" 
                  :class="passwordStrengthColor"
                  :style="{ width: `${(passwordStrength / 5) * 100}%` }"></div>
              </div>
              <ul class="text-xs text-gray-400 dark:text-gray-600 mt-2 space-y-1">
                <li>• At least 8 characters</li>
                <li>• Include numbers</li>
                <li>• Include lowercase letters</li>
                <li>• Include uppercase letters</li>
                <li>• Include special characters</li>
              </ul>
            </div>
          </div>
          
          <div>
            <label for="confirm-password" class="block mb-1">Confirm New Password</label>
            <InputWithIcon 
              icon="lock" 
              id="confirm-password"
              v-model="confirmPassword" 
              type="password" 
              class="w-full" 
              required 
            />
            <p v-if="errors.confirmPassword" class="text-red-500 text-sm mt-1">{{ errors.confirmPassword }}</p>
          </div>
          
          <div>
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg cursor-pointer transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSubmitting ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </Layout>
</template>