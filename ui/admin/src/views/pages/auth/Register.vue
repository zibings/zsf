<script setup>
import { ref } from 'vue';
import AppConfig from '@/layout/AppConfig.vue';

const value1 = ref(null);
const value2 = ref(null);

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
                        <i class="pi pi-users text-primary text-4xl"></i>
                    </div>
                    <div class="mt-4">
                        <h1 class="m-0 text-primary font-semibold text-4xl">Join us!</h1>
                        <span class="block text-700 mt-2">Please fill the fields to sign-up Ultima network</span>
                    </div>
                </div>
                <div class="flex flex-column gap-3 mt-6">
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-user"></i>
                        </InputGroupAddon>
                        <InputText placeholder="Name" />
                    </InputGroup>
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-at"></i>
                        </InputGroupAddon>
                        <InputText placeholder="Email" />
                    </InputGroup>
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-key"></i>
                        </InputGroupAddon>
                        <Password id="password" v-model="value1" placeholder="Password" inputClass="w-full" toggleMask />
                    </InputGroup>
                    <InputGroup>
                        <InputGroupAddon>
                            <i class="pi pi-key"></i>
                        </InputGroupAddon>
                        <Password id="password" v-model="value2" placeholder="Password Again" inputClass="w-full" toggleMask />
                    </InputGroup>
                    <div>
                        <Button class="w-full" label="SIGN UP"></Button>
                    </div>
                    <div>
                        <Button class="w-full text-primary-500" text label="BACK TO LOGIN"></Button>
                    </div>
                </div>
            </div>
        </div>
        <div :style="backgroundStyle" class="hidden lg:flex flex-1 align-items-center justify-content-center bg-cover">
            <img src="/layout/images/logo/vector_logo.png" alt="" />
        </div>
    </div>
    <AppConfig simple />
</template>
