<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AppConfig from '@/layout/AppConfig.vue';

const email = ref('');
const password = ref('');
const router = useRouter();

const navigateToDashboard = () => {
    router.push('/');
};
</script>

<template>
    <div class="h-screen flex w-full surface-ground">
        <div class="flex flex-1 flex-column surface-ground align-items-center justify-content-center">
            <div class="w-11 sm:w-30rem">
                <div class="flex flex-column">
                    <div style="height: 56px; width: 56px" class="bg-primary-50 border-circle flex align-items-center justify-content-center">
                        <i class="pi pi-sign-in text-primary text-4xl"></i>
                    </div>
                    <div class="mt-4">
                        <h1 class="m-0 text-primary font-semibold text-4xl">Welcome back!</h1>
                        <span class="block text-700 mt-2">Please fill the fields to sign-up Ultima network</span>
                    </div>
                </div>
                <div class="flex flex-column gap-3 mt-6">
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-user"></i>
                        </InputGroupAddon>
                        <InputText type="text" v-model="email" placeholder="Email" />
                    </InputGroup>
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-key"></i>
                        </InputGroupAddon>
                        <InputText type="password" v-model="password" placeholder="Password" />
                    </InputGroup>
                    <div>
                        <Button @click="navigateToDashboard" class="w-full" label="LOGIN"></Button>
                    </div>
                    <div>
                        <Button class="w-full text-primary-500" text label="FORGOT PASSWORD?"></Button>
                    </div>
                </div>
            </div>
        </div>
        <div :style="{ backgroundImage: 'url(/demo/images/pages/accessDenied-bg.jpg)' }" class="hidden lg:flex flex-1 align-items-center justify-content-center bg-cover">
            <img src="/layout/images/logo/vector_logo.png" alt="" />
        </div>
    </div>
    <AppConfig simple />
</template>
