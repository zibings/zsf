<script setup>
import AppConfig from '@/layout/AppConfig.vue';
import { ref } from 'vue';

const value1 = ref(null);
const value2 = ref(null);
const value3 = ref(null);
const value4 = ref(null);

const backgroundImage = ref('url(/demo/images/pages/accessDenied-bg.jpg)');
const backgroundStyle = ref({
    background: backgroundImage.value
});

const focus = (event) => {
    const regexNum = /^\d+$/;
    const isValid = regexNum.test(event.key);
    const nextElementInputRef = event.currentTarget.nextElementSibling.children[0];

    isValid && nextElementInputRef.focus();
};
</script>

<template>
    <div class="h-screen flex w-full surface-ground">
        <div class="flex flex-1 flex-column surface-ground align-items-center justify-content-center">
            <div class="w-11 sm:w-30rem">
                <div class="flex flex-column">
                    <div style="height: 56px; width: 56px" class="bg-primary-50 border-circle flex align-items-center justify-content-center">
                        <i class="pi pi-check-circle text-primary text-4xl"></i>
                    </div>
                    <div class="mt-4">
                        <h1 class="m-0 text-primary font-semibold text-4xl">Authentication?</h1>
                        <span class="block text-700 mt-2">Verify your code</span>
                    </div>
                </div>
                <div class="flex gap-3 align-items-center mt-6 p-fluid">
                    <InputNumber inputId="val1" v-model="value1" inputClass="w-3rem text-center" :max="9" @keyup="focus($event)"></InputNumber>
                    <InputNumber inputId="val2" v-model="value2" inputClass="w-3rem text-center" :max="9" @keyup="focus($event)"></InputNumber>
                    <InputNumber inputId="val3" v-model="value3" inputClass="w-3rem text-center" :max="9" @keyup="focus($event)"></InputNumber>
                    <InputNumber inputId="val4" v-model="value4" inputClass="w-3rem text-center" :max="9"></InputNumber>
                </div>
                <div class="mt-3">
                    <Button class="w-full" label="VERIFY NOW"></Button>
                </div>
                <div class="mt-3">
                    <Button class="w-full text-primary-500" text label="SEND AGAIN"></Button>
                </div>
            </div>
        </div>
        <div :style="backgroundStyle" class="hidden lg:flex flex-1 align-items-center justify-content-center bg-cover">
            <img src="/layout/images/logo/vector_logo.png" alt="" />
        </div>
    </div>

    <AppConfig simple />
</template>
