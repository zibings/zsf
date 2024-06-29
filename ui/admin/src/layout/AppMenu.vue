<template>
	<ul class="layout-menu">
		<template v-for="(item, i) in generalStore.menu" :key="item">
			<app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
			<li v-if="item.separator" class="menu-separator"></li>
		</template>
	</ul>
</template>

<script setup>
import { useGeneralStore } from "@/stores/general-store";
import AppMenuItem from "./AppMenuItem.vue";

const generalStore = useGeneralStore();
</script>

<style lang="scss" scoped></style>
