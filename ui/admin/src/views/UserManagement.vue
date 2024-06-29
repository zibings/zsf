<template>
	<Card class="card mb-3">
		<template #title> User Management </template>
		<template #content>
			<DataTable
				:value="userStore.users"
				:rows="10"
				:paginator="true"
				:loading="loading"
				striped-rows
				responsive-layout="scroll"
				show-gridlines
				:resizable-columns="true"
				column-resize-mode="fit"
				v-model:filters="filters"
				:global-filter-fields="globalFilters"
				paginator-template="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
				current-page-report-template="Showing {first} to {last} of {totalRecords} entries"
				:rows-per-page-options="[10, 25, 50, 100]"
			>
				<template #header>
					<div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
						<span class="head-refresh">
							<h5 class="m-0">Users</h5>

							<Button type="button" icon="pi pi-refresh" class="p-button-text ml-3" aria-label="Refresh Users Table" v-tooltip="'Refresh Users Table'" @click="refreshUsers()" />
						</span>

						<span class="p-input-icon-left">
							<i class="pi pi-search" />
							<InputText v-model="filters['global'].value" type="search" placeholder="Search" />
						</span>
					</div>
				</template>
				<template #empty> No users found. </template>
				<template #loading> Loading users. Please wait... </template>
				<Column v-for="col of userStore.columns" :field="col.field" :header="col.header" :key="col.field" :sortable="col.sortable" :hidden="!col.display">
					<template #body="slotProps">
						<span v-if="isBoolean(slotProps.data[col.field])">
							<i v-if="slotProps.data[col.field]" class="pi pi-check-circle"></i>
							<i v-else class="pi pi-times-circle"></i>
						</span>
						<span v-else>{{ slotProps.data[col.field] }}</span>
					</template>
				</Column>
				<Column header-style="width: 4rem; text-align: center" body-style="text-align: center; overflow: visible;display: flex;">
					<template #body="slotProps">
						<!-- <div> -->
						<router-link :to="{ name: 'userEdit', params: { id: slotProps.data.id } }" v-slot="{ navigate }">
							<Button type="button" icon="pi pi-user-edit" @click="navigate"></Button>
						</router-link>

						<Button type="button" icon="pi pi-trash"></Button>
						<!-- </div> -->
						<!-- <span>Edit {{ slotProps }}</span> -->
					</template>
				</Column>
			</DataTable>
		</template>
	</Card>
</template>

<script setup>
import { useUserStore } from "@/stores/user-store";
import { FilterMatchMode } from "primevue/api";
import { ref } from "vue";

const userStore = useUserStore();

const loading = ref(true);
// const users = ref([]);
// const columns = ref([]);
const globalFilters = ref([]);
const filters = ref({
	global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const isBoolean = (val) => "boolean" === typeof val;

async function getUserData() {
	// const { data } = await api.get("/users");
	// users = data;
	console.log("fetch users");

	await userStore.fetchColumnsCached();
	await userStore.fetchUsersCached();

	// columns = userStore.columns;
	// users = userStore.users;

	await columnFilter();

	loading.value = false;
}

async function refreshUsers() {
	loading.value = true;
	userStore.columns = [];
	userStore.users = [];

	// await new Promise((r) => setTimeout(r, 2000));

	await userStore.fetchColumns();
	await userStore.fetchUsers();

	await columnFilter();
	loading.value = false;
}

async function columnFilter() {
	const defaultBools = { filter: false, sortable: false, display: true };

	userStore.columns = userStore.columns.map((col) => Object.assign({}, defaultBools, col));

	globalFilters.value = userStore.columns.reduce((a, o) => {
		o.filter && a.push(o.field);
		return a;
	}, []);
}

getUserData();
</script>

<style lang="scss" scoped>
.p-button {
	margin-right: 0.25rem;
}

.head-refresh {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.refresh-btn {
	margin-left: 0.5rem;
}
</style>
