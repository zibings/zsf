<script setup>
import { ref, watch, computed } from 'vue';
import { usePrimeVue } from 'primevue/config';
import { useLayout } from '@/layouts/composables/layout';

defineProps({
    simple: {
        type: Boolean,
        default: false
    }
});

const $primevue = usePrimeVue();
const rippleActive = computed(() => $primevue.config.ripple);
const inputStyle = computed(() => $primevue.config.inputStyle || 'outlined');

const { setScale, layoutConfig, layoutState, onConfigSidebarToggle, replaceLink, changeColorScheme } = useLayout();

const componentThemes = ref([
    { name: 'indigo', color: '#3F51B5' },
    { name: 'pink', color: '#E91E63' },
    { name: 'purple', color: '#9C27B0' },
    { name: 'deeppurple', color: '#673AB7' },
    { name: 'blue', color: '#2196F3' },
    { name: 'lightblue', color: '#03A9F4' },
    { name: 'cyan', color: '#00BCD4' },
    { name: 'teal', color: '#009688' },
    { name: 'green', color: '#4CAF50' },
    { name: 'lightgreen', color: '#8BC34A' },
    { name: 'lime', color: '#CDDC39' },
    { name: 'yellow', color: '#FFEB3B' },
    { name: 'amber', color: '#FFC107' },
    { name: 'orange', color: '#FF9800' },
    { name: 'deeporange', color: '#FF5722' },
    { name: 'brown', color: '#795548' },
    { name: 'bluegrey', color: '#607D8B' }
]);
const menuThemes = ref([
    { name: 'light', color: '#FDFEFF' },
    { name: 'dark', color: '#434B54' },
    { name: 'indigo', color: '#1A237E' },
    { name: 'bluegrey', color: '#37474F' },
    { name: 'brown', color: '#4E342E' },
    { name: 'cyan', color: '#006064' },
    { name: 'green', color: '#2E7D32' },
    { name: 'deeppurple', color: '#4527A0' },
    { name: 'deeporange', color: '#BF360C' },
    { name: 'pink', color: '#880E4F' },
    { name: 'purple', color: '#6A1B9A' },
    { name: 'teal', color: '#00695C' }
]);
const topbarThemes = ref([
    { name: 'lightblue', color: '#2E88FF' },
    { name: 'dark', color: '#363636' },
    { name: 'white', color: '#FDFEFF' },
    { name: 'blue', color: '#1565C0' },
    { name: 'deeppurple', color: '#4527A0' },
    { name: 'purple', color: '#6A1B9A' },
    { name: 'pink', color: '#AD1457' },
    { name: 'cyan', color: '#0097A7' },
    { name: 'teal', color: '#00796B' },
    { name: 'green', color: '#43A047' },
    { name: 'lightgreen', color: '#689F38' },
    { name: 'lime', color: '#AFB42B' },
    { name: 'yellow', color: '#FBC02D' },
    { name: 'amber', color: '#FFA000' },
    { name: 'orange', color: '#FB8C00' },
    { name: 'deeporange', color: '#D84315' },
    { name: 'brown', color: '#5D4037' },
    { name: 'grey', color: '#616161' },
    { name: 'bluegrey', color: '#546E7A' },
    { name: 'indigo', color: '#3F51B5' }
]);

const scales = ref([12, 13, 14, 15, 16]);

watch(layoutConfig.menuMode, (newVal) => {
    if (newVal === 'static') {
        layoutState.staticMenuDesktopInactive.value = false;
    }
});

const onConfigButtonClick = () => {
    onConfigSidebarToggle();
};

const colorScheme = ref(layoutConfig.colorScheme.value);

const changeTheme = (theme) => {
    const themeLink = document.getElementById('theme-link');
    const themeHref = themeLink.getAttribute('href');
    const newHref = themeHref.replace(layoutConfig.componentTheme.value, theme);

    replaceLink(themeLink, newHref, () => {
        layoutConfig.componentTheme.value = theme;
    });
};

const changeMenuTheme = (theme) => {
    layoutConfig.menuTheme.value = theme;
};
const changeTopbarTheme = (theme) => {
    layoutConfig.topbarTheme.value = theme;
};

const decrementScale = () => {
    setScale(layoutConfig.scale.value - 1);
    applyScale();
};
const incrementScale = () => {
    setScale(layoutConfig.scale.value + 1);
    applyScale();
};
const applyScale = () => {
    document.documentElement.style.fontSize = layoutConfig.scale.value + 'px';
};
const onInputStyleChange = (value) => {
    $primevue.config.inputStyle = value;
};
const onRippleChange = (value) => {
    $primevue.config.ripple = value;
};
</script>

<template>
    <button class="layout-config-button config-link" type="button" @click="onConfigButtonClick()">
        <i class="pi pi-cog"></i>
    </button>
    <Sidebar v-model:visible="layoutState.configSidebarVisible.value" position="right" class="layout-config-sidebar w-20rem p-3">
        <h5>Layout/Theme Scale</h5>
        <div class="flex align-items-center">
            <Button icon="pi pi-minus" type="button" @click="decrementScale()" class="w-2rem h-2rem mr-2" text rounded :disabled="layoutConfig.scale.value === scales[0]"></Button>
            <div class="flex gap-2 align-items-center">
                <i class="pi pi-circle-fill text-300" v-for="s in scales" :key="s" :class="{ 'text-primary-500': s === layoutConfig.scale.value }"></i>
            </div>
            <Button icon="pi pi-plus" type="button" @click="incrementScale()" class="w-2rem h-2rem ml-2" text rounded :disabled="layoutConfig.scale.value === scales[scales.length - 1]"></Button>
        </div>
        <h5>Color Scheme</h5>
        <div class="flex gap-4">
            <div class="field-radiobutton flex-1">
                <RadioButton name="colorScheme" value="light" v-model="colorScheme" id="theme3" @change="changeColorScheme('light')"></RadioButton>
                <label for="theme3">Light</label>
            </div>
            <div class="field-radiobutton flex-1">
                <RadioButton name="colorScheme" value="dark" v-model="colorScheme" id="theme1" @change="changeColorScheme('dark')"></RadioButton>
                <label for="theme1">Dark</label>
            </div>
        </div>

        <template v-if="!simple">
            <h5>Menu Mode</h5>
            <div class="flex flex-wrap row-gap-3">
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="static" v-model="layoutConfig.menuMode.value" inputId="mode1"></RadioButton>
                    <label for="mode1">Static</label>
                </div>

                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="overlay" v-model="layoutConfig.menuMode.value" inputId="mode2"></RadioButton>
                    <label for="mode6">Overlay</label>
                </div>
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="slim" v-model="layoutConfig.menuMode.value" inputId="mode3"></RadioButton>
                    <label for="mode2">Slim</label>
                </div>
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="slim-plus" v-model="layoutConfig.menuMode.value" inputId="mode4"></RadioButton>
                    <label for="mode3">Slim +</label>
                </div>
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="reveal" v-model="layoutConfig.menuMode.value" inputId="mode5"></RadioButton>
                    <label for="mode4">Reveal</label>
                </div>
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="drawer" v-model="layoutConfig.menuMode.value" inputId="mode6"></RadioButton>
                    <label for="mode5">Drawer</label>
                </div>
                <div class="flex align-items-center gap-2 w-6">
                    <RadioButton name="menuMode" value="horizontal" v-model="layoutConfig.menuMode.value" inputId="mode7"></RadioButton>
                    <label for="mode2">Horizontal</label>
                </div>
            </div>
        </template>

        <template v-if="!simple">
            <h5>Menu Profile Position</h5>
            <div class="flex gap-4">
                <div class="field-radiobutton flex-1">
                    <RadioButton name="position" value="start" v-model="layoutConfig.menuProfilePosition.value" inputId="start"></RadioButton>
                    <label for="star">Start</label>
                </div>
                <div class="field-radiobutton flex-1">
                    <RadioButton name="position" value="end" v-model="layoutConfig.menuProfilePosition.value" inputId="end"></RadioButton>
                    <label for="end">End</label>
                </div>
            </div>
        </template>

        <template v-if="!simple">
            <h5>Input Style</h5>
            <div class="flex">
                <div class="field-radiobutton flex-1">
                    <RadioButton :modelValue="inputStyle" name="inputStyle" value="outlined" inputId="outlined_input" @update:modelValue="onInputStyleChange"></RadioButton>
                    <label for="outlined_input">Outlined</label>
                </div>
                <div class="field-radiobutton flex-1">
                    <RadioButton :modelValue="inputStyle" name="inputStyle" value="filled" inputId="filled_input" @update:modelValue="onInputStyleChange"></RadioButton>
                    <label for="filled_input">Filled</label>
                </div>
            </div>
        </template>
        <h5>Ripple Effect</h5>
        <InputSwitch :modelValue="rippleActive" @update:modelValue="onRippleChange"></InputSwitch>

        <template v-if="!simple">
            <h5>Menu Themes</h5>
            <template v-if="layoutConfig.colorScheme.value !== 'dark'">
                <div class="flex flex-wrap row-gap-3">
                    <div v-for="(theme, i) in menuThemes" :key="i" class="w-3">
                        <button
                            class="cursor-pointer p-link w-2rem h-2rem border-round shadow-2 flex-shrink-0 flex justify-content-center align-items-center border-circle"
                            @click="() => changeMenuTheme(theme.name)"
                            :style="{ 'background-color': theme.color }"
                        >
                            <i v-if="theme.name === layoutConfig.menuTheme.value" :class="['pi pi-check', theme.name === layoutConfig.menuTheme.value && layoutConfig.menuTheme.value !== 'light' ? 'text-white' : 'text-dark']"></i>
                        </button>
                    </div>
                </div>
            </template>
            <template v-else>
                <p>Menu themes are only available in light mode by design as large surfaces can emit too much brightness in dark mode.</p>
            </template>
        </template>

        <template v-if="!simple">
            <h5>Topbar Themes</h5>
            <div class="flex flex-wrap row-gap-3">
                <div v-for="(theme, i) in topbarThemes" :key="i" class="w-3">
                    <button
                        class="cursor-pointer p-link w-2rem h-2rem border-round shadow-2 flex-shrink-0 flex justify-content-center align-items-center border-circle"
                        @click="() => changeTopbarTheme(theme.name)"
                        :style="{ 'background-color': theme.color }"
                    >
                        <i v-if="theme.name === layoutConfig.topbarTheme.value" :class="['pi pi-check', theme.name === layoutConfig.topbarTheme.value && layoutConfig.topbarTheme.value !== 'white' ? 'text-white' : 'text-dark']"></i>
                    </button>
                </div>
            </div>
        </template>
        <h5>Component Themes</h5>
        <div class="flex flex-wrap row-gap-3">
            <div v-for="(theme, i) in componentThemes" :key="i" class="w-3">
                <button class="cursor-pointer p-link w-2rem h-2rem border-circle flex-shrink-0 flex align-items-center justify-content-center" @click="() => changeTheme(theme.name)" :style="{ 'background-color': theme.color }">
                    <i v-if="theme.name === layoutConfig.componentTheme.value" class="pi pi-check text-white"></i>
                </button>
            </div>
        </div>
    </Sidebar>
</template>

<style lang="scss" scoped></style>
