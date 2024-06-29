<script setup>
import { ref } from 'vue';
import AppConfig from '@/layout/AppConfig.vue';

const backgroundImage = ref('url(/demo/images/pages/accessDenied-bg.jpg)');
const backgroundStyle = ref({
    background: backgroundImage.value
});
</script>

<template>
    <div class="h-screen flex w-full surface-ground">
        <div class="flex flex-1 flex-column surface-ground align-items-center justify-content-center">
            <div class="w-11 sm:w-30rem">
                <div class="flex flex-column">
                    <div style="height: 56px; width: 56px" class="bg-primary-50 border-circle flex align-items-center justify-content-center">
                        <i class="pi pi-lock text-primary text-4xl"></i>
                    </div>
                    <div class="mt-4">
                        <h1 class="m-0 text-primary font-semibold text-4xl">Lock Screen</h1>
                        <span class="block text-700 mt-2">Enter your password</span>
                    </div>
                </div>
                <div class="flex flex-column gap-3 mt-6">
                    <div class="flex justify-content-between align-items-center">
                        <div class="flex gap-3 align-items-center">
                            <Avatar image="/demo/images/avatar/annafali.png" shape="circle" size="large"> </Avatar>
                            <div class="flex flex-column gap-1">
                                <span class="text-color font-semibold text-lg">Amy Elsner</span>
                                <span class="text-color-secondary text-sm">UX Designer</span>
                            </div>
                        </div>
                        <Button class="text-primary-500" text icon="pi pi-sign-out" label="Log out"></Button>
                    </div>
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-key"></i>
                        </InputGroupAddon>
                        <Password id="password" placeholder="Password" inputClass="w-full"></Password>
                    </InputGroup>
                    <InputGroup>
                        <Button class="w-full" label="UNLOCK"></Button>
                    </InputGroup>
                    <InputGroup>
                        <Button class="w-full text-primary-500" text label="SWITCH ACCOUNT"></Button>
                    </InputGroup>
                </div>
            </div>
        </div>
        <div :style="backgroundStyle" class="hidden lg:flex flex-1 align-items-center justify-content-center bg-cover">
            <img src="/layout/images/logo/vector_logo.png" alt="" />
        </div>
    </div>

    <AppConfig simple />
</template>
