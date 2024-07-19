<template>
	<nav class="navbar-container">
		<RouterLink to="/" class="home-link">
			<div class="navbar-logo"></div>
			ZSF Website
		</RouterLink>

		<Menubar :model="items">
			<template #item="{ item, props, hasSubmenu }">
				<router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
					<a v-ripple :href="href" v-bind="props.action" @click="navigate" :class="{ 'active-route': checkActiveRoute(item) }">
						<span :class="item.icon" />
						<span class="ml-2">{{ item.label }}</span>
					</a>
				</router-link>
				<a v-else v-ripple :href="item.url" :target="item.target" v-bind="props.action" :class="{ 'active-route': checkActiveRoute(item) }">
					<span :class="item.icon" />
					<span class="ml-2">{{ item.label }}</span>
					<span v-if="hasSubmenu" class="pi pi-fw pi-angle-down ml-2" />
				</a>
			</template>
		</Menubar>
	</nav>
</template>

<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useUserStore } from "stores/user";

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();

const items = computed(() => {
	if (userStore.loggedIn) {
		return authedItems;
	}

	return publicItems;
});

const publicItems = [
	{
		label: "Home",
		route: { name: "home" },
	},
	{
		label: "Login",
		route: { name: "login" },
	},
	{
		label: "Register",
		route: { name: "register" },
	},
];

const authedItems = [
	{
		label: "About",
		route: { name: "home" },
	},
	{
		label: "Features",
		route: { name: "home" },
	},
	{
		label: "Profile",
		route: { name: "profile" },
	},
	{
		label: "Contact",
		url: "https://vuejs.org",
		target: "_blank",
	},
	{
		label: "Log Out",
		command: () => {
			userStore.logOut();
			router.push({ name: "login" });
		},
	},
];

const checkActiveRoute = (item) => {
	return route.path === item.route || route.name === item.route?.name;
};
</script>

<style scoped>
nav.navbar-container {
	padding: 10px;
	display: flex;
	justify-content: space-between;
}

.active-route {
	color: var(--link-color);
}
</style>
