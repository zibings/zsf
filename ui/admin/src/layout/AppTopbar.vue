<template>
	<div class="layout-topbar">
		<router-link to="/" class="layout-topbar-logo">
			<!-- <img :src="logoUrl" alt="logo" /> -->
			<span>{{ generalStore.displayNames.header }} ({{ generalStore.currentUser.userId }})</span>
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
			<button @click="toggleProfileMenu" class="p-link layout-topbar-button" type="button" aria-haspopup="true" aria-controls="profile-menu">
				<i class="pi pi-user"></i>
				<span>Profile</span>
			</button>
			<Menu ref="profileMenu" id="profile-menu" :model="profileMenuItems" :popup="true">
				<template #item="{ item, props }">
					<router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
						<a v-ripple :href="href" v-bind="props.action" @click="navigate">
							<span :class="item.icon" />
							<span class="ml-2">{{ item.label }}</span>
						</a>
					</router-link>
					<a v-else v-ripple :href="item.url" :target="item.target" v-bind="props.action">
						<span :class="item.icon" />
						<span class="ml-2">{{ item.label }}</span>
					</a>
				</template>
			</Menu>
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
import { toggleDarkMode } from "@/composables/toggleDarkMode";
import { useGeneralStore } from "@/stores/general-store";
import ThemeToggle from "@/components/switch/ThemeToggle.vue";
import { useAuthStore } from '@/stores/auth-store.js';
import { useRouter } from 'vue-router';

const router = useRouter();
const $toggleDark = inject("$toggleDark");
const authStore = useAuthStore();
const generalStore = useGeneralStore();
const { layoutConfig, onMenuToggle } = useLayout();

watchEffect(async () => {
	await toggleDarkMode(generalStore.darkMode);
	$toggleDark(generalStore.darkMode);
});

const outsideClickListener = ref(null);
const topbarMenuActive = ref(false);
const profileMenu = ref();
const profileMenuItems = ref([
	{
		label: "Profile Settings",
		icon: 'pi pi-cog',
		route: '/users/1'
	},
	{
		label: 'Logout',
		icon: 'pi pi-sign-out',
		command: () => {
			authStore.logOut();

			router.push('/auth/login');

			return;
		}
	}
]);
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
const toggleProfileMenu = event => {
	profileMenu.value.toggle(event);

	return;
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
