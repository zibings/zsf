<script setup>
import { ref } from 'vue';
import AppConfig from '@/layout/AppConfig.vue';

const backgroundImage = ref('url(/demo/images/pages/login-bg.jpg)');
const backgroundStyle = ref({
    background: backgroundImage.value
});
</script>

<template>
    <div class="h-screen flex flex-column bg-cover" :style="backgroundStyle">
        <div class="shadow-2 bg-indigo-500 z-5 p-3 flex justify-content-between flex-row align-items-center">
            <div class="ml-3 flex" @click="$router.push('/')">
                <div>
                    <img class="h-2rem" src="/layout/images/logo/logo2x.png" alt="" />
                </div>
            </div>
            <div class="mr-3 flex">
                <Button @click="$router.push('/')" class="text-white" text plain>DASHBOARD</Button>
            </div>
        </div>

        <div class="align-self-center mt-auto mb-auto">
            <div class="text-center z-5 flex flex-column border-1 border-round-md surface-border surface-card px-3">
                <div class="-mt-5 text-white bg-cyan-700 border-round-md mx-auto px-3 py-1 border-1 surface-border">
                    <h2 class="m-0">LOGIN</h2>
                </div>

                <h4>Welcome</h4>

                <div class="text-color-secondary mb-6 px-6">Please use the form to sign-in Ultima network</div>

                <div class="w-full flex flex-column gap-3 px-3 pb-6">
                    <IconField iconPosition="left">
                        <InputIcon class="pi pi-envelope" />
                        <InputText class="w-full" placeholder="E-mail" />
                    </IconField>

                    <IconField iconPosition="left">
                        <InputIcon class="pi pi-key" />
                        <InputText type="password" class="w-full" placeholder="Password" />
                    </IconField>
                    <Button @click="$router.push('/')" class="w-full my-3 px-3" label="LOGIN"></Button>
                </div>
            </div>
        </div>
    </div>
    <AppConfig simple />
</template>
