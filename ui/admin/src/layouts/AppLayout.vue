<script setup>
import { computed, watch, ref, onBeforeUnmount } from 'vue';
import { usePrimeVue } from 'primevue/config';
import AppTopbar from './AppTopbar.vue';
import AppSidebar from './AppSidebar.vue';
import AppConfig from './AppConfig.vue';
import AppRightMenu from './AppRightMenu.vue';
import AppBreadCrumb from './AppBreadcrumb.vue';
import AppFooter from './AppFooter.vue';
import { useLayout } from '@/layout/composables/layout';

const $primevue = usePrimeVue();
const { layoutConfig, layoutState, isSidebarActive } = useLayout();
const outsideClickListener = ref(null);
const sidebarRef = ref(null);
const topbarRef = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

onBeforeUnmount(() => {
    unbindOutsideClickListener();
});

const containerClass = computed(() => {
    let styleClass = {
        'layout-overlay': layoutConfig.menuMode.value === 'overlay',
        'layout-static': layoutConfig.menuMode.value === 'static',
        'layout-slim': layoutConfig.menuMode.value === 'slim',
        'layout-slim-plus': layoutConfig.menuMode.value === 'slim-plus',
        'layout-horizontal': layoutConfig.menuMode.value === 'horizontal',
        'layout-reveal': layoutConfig.menuMode.value === 'reveal',
        'layout-drawer': layoutConfig.menuMode.value === 'drawer',
        'layout-sidebar-dark': layoutConfig.colorScheme === 'dark',
        'p-ripple-disabled': $primevue.config.ripple === false,
        'layout-static-inactive': layoutState.staticMenuDesktopInactive.value && layoutConfig.menuMode.value === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive.value,
        'layout-mobile-active': layoutState.staticMenuMobileActive.value,
        'layout-topbar-menu-active': layoutState.topbarMenuActive.value,
        'layout-menu-profile-active': layoutState.rightMenuActive.value,
        'layout-sidebar-active': layoutState.sidebarActive.value,
        'layout-sidebar-anchored': layoutState.anchored.value
    };

    styleClass['layout-topbar-' + layoutConfig.topbarTheme.value] = true;
    styleClass['layout-menu-' + layoutConfig.menuTheme.value] = true;
    styleClass['layout-menu-profile-' + layoutConfig.menuProfilePosition.value] = true;

    return styleClass;
});

const bindOutsideClickListener = () => {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                layoutState.overlayMenuActive.value = false;
                layoutState.overlaySubmenuActive.value = false;
                layoutState.staticMenuMobileActive.value = false;
                layoutState.menuHoverActive.value = false;
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
};
const unbindOutsideClickListener = () => {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener);
        outsideClickListener.value = null;
    }
};
const isOutsideClicked = (event) => {
    if (!sidebarRef.value) return;

    const sidebarEl = sidebarRef?.value.$el;
    const topbarEl = topbarRef?.value.$el.querySelector('.layout-menu-button');

    return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
};
</script>

<template>
    <div :class="['layout-container', { ...containerClass }]">
        <AppTopbar ref="topbarRef" />
        <AppRightMenu />
        <AppSidebar ref="sidebarRef" />

        <div class="layout-content-wrapper">
            <AppBreadCrumb></AppBreadCrumb>
            <div class="layout-content">
                <router-view></router-view>
            </div>
            <AppFooter></AppFooter>
        </div>

        <AppConfig />

        <Toast></Toast>
        <div class="layout-mask"></div>
    </div>
</template>
