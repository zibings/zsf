<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const backgroundImage = ref('url(/demo/images/pages/error-bg.jpg)');
const backgroundStyle = ref({
    background: backgroundImage.value
});

const navigateToDashboard = () => {
    router.push('/');
};
</script>

<template>
    <div class="h-screen flex flex-column bg-cover" :style="backgroundStyle">
        <div class="shadow-3 z-3 bg-indigo-600 p-3 flex justify-content-between flex-row align-items-center">
            <router-link class="ml-3 flex" to="/">
                <div>
                    <img class="h-2rem" src="/layout/images/logo/logo2x.png" alt="" />
                </div>
            </router-link>
            <div class="mr-3 flex">
                <Button label="DASHBOARD" @click="navigateToDashboard" class="text-white" text></Button>
            </div>
        </div>
        <div class="align-self-center mt-auto mb-auto">
            <div class="text-center z-4 border-round-lg border-1 surface-border bg-white p-3 shadow-3 flex flex-column">
                <div class="border-round-md mx-auto border-1 surface-border text-white bg-pink-500 -mt-6 px-3 py-1">
                    <h2 class="m-0" style="color: #212121">ERROR</h2>
                </div>
                <div class="surface-200 p-3 mb-5 shadow-2 border-round-md mt-3 px-6">
                    <img src="/demo/images/pages/error.png" class="w-full" alt="" />
                </div>
                <div class="text-color-secondary pb-6">Requested resource is not available.</div>
                <Button label="GO BACK TO DASHBOARD" @click="navigateToDashboard" text></Button>
            </div>
        </div>
    </div>
</template>
