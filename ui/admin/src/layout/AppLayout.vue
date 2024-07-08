<template>
	<div class="layout-wrapper" :class="containerClass">
		<AppTopbar />
		<div class="layout-sidebar">
			<AppSidebar />
		</div>
		<div class="layout-main-container">
			<div class="layout-main">
				<router-view />
			</div>
			<AppFooter />
		</div>

		<div class="layout-mask"></div>
	</div>
</template>

<script setup>
import { useGeneralStore } from "@/stores/general-store";
import { useHead } from "@vueuse/head";
import { computed, watch, ref } from "vue";

import AppTopbar from "./AppTopbar.vue";
import AppFooter from "./AppFooter.vue";
import AppSidebar from "./AppSidebar.vue";
import { useLayout } from "@/layout/composables/layout";

const generalStore = useGeneralStore();

const { layoutConfig, layoutState, isSidebarActive } = useLayout();

const outsideClickListener = ref(null);

useHead({
	title: generalStore.displayNames.title,
});

watch(isSidebarActive, (newVal) => {
	if (newVal) {
		bindOutsideClickListener();
	} else {
		unbindOutsideClickListener();
	}
});

const containerClass = computed(() => {
	return {
		"layout-overlay": layoutConfig.menuMode.value === "overlay",
		"layout-static": layoutConfig.menuMode.value === "static",
		"layout-static-inactive": layoutState.staticMenuDesktopInactive.value && layoutConfig.menuMode.value === "static",
		"layout-overlay-active": layoutState.overlayMenuActive.value,
		"layout-mobile-active": layoutState.staticMenuMobileActive.value,
		"p-input-filled": layoutConfig.inputStyle.value === "filled",
		"p-ripple-disabled": !layoutConfig.ripple.value,
	};
});
const bindOutsideClickListener = () => {
	if (!outsideClickListener.value) {
		outsideClickListener.value = (event) => {
			if (isOutsideClicked(event)) {
				layoutState.overlayMenuActive.value = false;
				layoutState.staticMenuMobileActive.value = false;
				layoutState.menuHoverActive.value = false;
			}
		};
		document.addEventListener("click", outsideClickListener.value);
	}
};
const unbindOutsideClickListener = () => {
	if (outsideClickListener.value) {
		document.removeEventListener("click", outsideClickListener);
		outsideClickListener.value = null;
	}
};
const isOutsideClicked = (event) => {
	const sidebarEl = document.querySelector(".layout-sidebar");
	const topbarEl = document.querySelector(".layout-menu-button");

	return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
};
</script>

<style lang="scss" scoped></style>
