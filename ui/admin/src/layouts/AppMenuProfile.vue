<script setup>
import { computed } from 'vue';
import { useLayout } from '@/layouts/composables/layout';
import { useRouter } from 'vue-router';

const router = useRouter();
const { layoutState, layoutConfig, onMenuProfileToggle, isHorizontal, isSlim } = useLayout();

const menuClass = computed(() => (isHorizontal.value ? 'overlay' : null));

const toggleMenu = () => {
    const menu = document.querySelector('.menu-transition');
    if (layoutState.menuProfileActive.value) {
        menu.style.maxHeight = '0';
        menu.style.opacity = '0';
        if (isHorizontal.value) {
            menu.style.transform = 'scaleY(0.8)';
        }
    } else {
        menu.style.maxHeight = menu.scrollHeight + 'px';
        menu.style.opacity = '1';
        if (isHorizontal.value) {
            menu.style.transform = 'scaleY(1)';
        }
    }
    onMenuProfileToggle();
};

const iconClass = computed(() => {
    return {
        'pi-angle-up':
            (layoutState.menuProfileActive.value && (layoutConfig.menuProfilePosition.value === 'start' || isHorizontal.value)) || (!layoutState.menuProfileActive.value && layoutConfig.menuProfilePosition.value === 'end' && !isHorizontal.value),
        'pi-angle-down': (!layoutState.menuProfileActive.value && layoutConfig.menuProfilePosition.value === 'start') || (layoutState.menuProfileActive.value && layoutConfig.menuProfilePosition.value === 'end') || isHorizontal.value
    };
});

const tooltipValue = (tooltipText) => {
    return isSlim.value ? tooltipText : null;
};
</script>

<template>
    <div class="layout-menu-profile">
        <button v-tooltip="{ value: tooltipValue('Profile') }" class="p-link" @click="toggleMenu()">
            <img src="/layout/images/avatar/amyelsner.png" alt="avatar" style="width: 32px; height: 32px" />
            <span>
                <strong>Amy Elsner</strong>
                <small>Webmaster</small>
            </span>
            <i class="layout-menu-profile-toggler pi pi-fw" :class="iconClass"></i>
        </button>

        <ul v-if="layoutState.menuProfileActive" :class="['menu-transition', menuClass]" style="overflow: hidden; max-height: 0; opacity: 0">
            <li v-tooltip="{ value: tooltipValue('Settings') }">
                <button class="p-link" @click="router.push('/documentation')">
                    <i class="pi pi-cog pi-fw"></i>
                    <span>Settings</span>
                </button>
            </li>

            <li v-tooltip="{ value: tooltipValue('Profile') }">
                <button class="p-link" @click="router.push('/documentation')">
                    <i class="pi pi-file-o pi-fw"></i>
                    <span>Profile</span>
                </button>
            </li>
            <li v-tooltip="{ value: tooltipValue('Support') }">
                <button class="p-link" @click="router.push('/documentation')">
                    <i class="pi pi-compass pi-fw"></i>
                    <span>Support</span>
                </button>
            </li>
            <li v-tooltip="{ value: tooltipValue('Logout') }">
                <button class="p-link" @click="router.push('/auth/login2')">
                    <i class="pi pi-power-off pi-fw"></i>
                    <span>Logout</span>
                </button>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.menu-transition {
    transition:
        max-height 400ms cubic-bezier(0.86, 0, 0.07, 1),
        opacity 400ms cubic-bezier(0.86, 0, 0.07, 1);
}
.menu-transition.overlay {
    transition:
        opacity 100ms linear,
        transform 120ms cubic-bezier(0, 0, 0.2, 1);
}
</style>
