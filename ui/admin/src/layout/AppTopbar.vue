<template>
	<div class="layout-topbar">
		<router-link to="/" class="layout-topbar-logo">
			<!-- <img :src="logoUrl" alt="logo" /> -->
			<span>{{ generalStore.displayNames.header }}</span>
		</router-link>

		<button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
			<i class="pi pi-bars"></i>
		</button>

		<button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
			<i class="pi pi-ellipsis-v"></i>
		</button>

		<div class="layout-topbar-menu" :class="topbarMenuClasses">
			<span class="dark-toggle">
				<ThemeToggle v-model="generalStore.darkMode" aria-label="Toggle dark mode" />
			</span>
			<button @click="onTopBarMenuButton()" class="p-link layout-topbar-button">
				<i class="pi pi-calendar"></i>
				<span>Calendar</span>
			</button>
			<button @click="onTopBarMenuButton()" class="p-link layout-topbar-button">
				<i class="pi pi-user"></i>
				<span>Profile</span>
			</button>
			<button @click="onSettingsClick()" class="p-link layout-topbar-button">
				<i class="pi pi-cog"></i>
				<span>Settings</span>
			</button>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watchEffect, inject } from "vue";
import { useLayout } from "@/layout/composables/layout";
// import { useRouter } from "vue-router";
import { toggleDarkMode } from "@/composables/toggleDarkMode";
import { useGeneralStore } from "@/stores/general-store";
import ThemeToggle from "@/components/switch/ThemeToggle.vue";

const $toggleDark = inject("$toggleDark");
const generalStore = useGeneralStore();
const { layoutConfig, onMenuToggle } = useLayout();

watchEffect(async () => {
	await toggleDarkMode(generalStore.darkMode);
	$toggleDark(generalStore.darkMode);
});

const outsideClickListener = ref(null);
const topbarMenuActive = ref(false);
// const router = useRouter();

onMounted(() => {
	bindOutsideClickListener();
});

onBeforeUnmount(() => {
	unbindOutsideClickListener();
});

const logoUrl = computed(() => {
	return `layout/images/${layoutConfig.darkTheme.value ? "logo-white" : "logo-dark"}.svg`;
});

const onTopBarMenuButton = () => {
	topbarMenuActive.value = !topbarMenuActive.value;
};
const onSettingsClick = () => {
	topbarMenuActive.value = false;
	// router.push("/documentation");
};
const topbarMenuClasses = computed(() => {
	return {
		"layout-topbar-menu-mobile-active": topbarMenuActive.value,
	};
});

const bindOutsideClickListener = () => {
	if (!outsideClickListener.value) {
		outsideClickListener.value = (event) => {
			if (isOutsideClicked(event)) {
				topbarMenuActive.value = false;
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
	if (!topbarMenuActive.value) return;

	const sidebarEl = document.querySelector(".layout-topbar-menu");
	const topbarEl = document.querySelector(".layout-topbar-menu-button");

	return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
};
</script>

<style lang="scss">
.layout-topbar .layout-topbar-menu {
	span.dark-toggle {
		display: inline-flex;
		justify-content: center;
		align-items: center;
		position: relative;
	}

	.p-inputswitch {
		height: 1.5rem;
		margin-left: 1rem;

		&.p-inputswitch.p-inputswitch-checked .p-inputswitch-slider {
			background-color: #383838;
		}
	}
}
</style>
