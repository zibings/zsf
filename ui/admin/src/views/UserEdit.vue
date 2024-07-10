<template>
	<form name="userEditForm">
		<div class="grid">
			<div class="col-12">
				<div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
					<span class="text-2xl">
						<router-link :to="{ name: 'users' }">
							<span>User Management </span>
						</router-link>

						<span class="ml-2">&rarr; Edit Name</span>
					</span>

					<span>
						<Button label="Save" class="p-button-outlined p-button-success" icon="pi pi-save" icon-pos="right" @click="userStore.saveUser(userStore.currentUser)" />
						<Button label="Delete" class="p-button-outlined p-button-danger" icon="pi pi-trash" icon-pos="right" />
					</span>
				</div>
			</div>

			<div class="col-12 lg:col-4 xl:col-4">
				<Card class="card">
					<template #title>
						<h5>Account Info</h5>
					</template>

					<template #content>
						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="currentemail">Email Address</label>
								<InputText id="currentemail" type="email" autocomplete="false" v-model="userStore.currentUser.account.email" />
								<small id="currentemail-help">User's email address</small>
							</div>

							<div class="field col-12 mb-1">
								<label for="newpassword">Password</label>
								<PasswordInput input-id="newpassword" :toggle-mask="true" v-model="userStore.currentUser.password" />
								<small id="newpassword-help">Set/change user's password</small>
							</div>
						</div>

						<h6>Settings</h6>

						<div class="p-fluid grid mt-3">
							<div class="field-checkbox col-12 mb-1">
								<Checkbox v-model="userStore.currentUser.settings.htmlEmail" :binary="true" input-id="htmlEmailsBox" />
								<label for="htmlEmailsBox">Receive HTML emails</label>
							</div>

							<div class="field-checkbox col-12 mb-1">
								<Checkbox v-model="userStore.currentUser.settings.playSounds" :binary="true" input-id="playSoundsBox" />
								<label for="playSoundsBox">Play sounds in browser</label>
							</div>
						</div>

						<h6><label for="roles">Roles</label></h6>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<AutoComplete
									input-id="roles"
									v-model="userStore.currentUser.roles"
									:suggestions="filteredUserRoles"
									@complete="searchUserRoles($event)"
									option-label="name"
									option-value="id"
									placeholder="Select User Role"
									aria-label="Select User Role"
									dropdown
									multiple
									force-selection
									:delay="250"
								/>
							</div>
						</div>
					</template>
				</Card>
			</div>

			<div class="col-12 lg:col-4 xl:col-4">
				<Card class="card">
					<template #title>
						<h5>Profile Info</h5>
					</template>

					<template #content>
						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="username">Username</label>
								<InputText id="username" type="text" v-model="userStore.currentUser.profile.displayName" />
								<small id="username-help">Their display name for the site</small>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="realName">Real Name</label>
								<InputText id="realName" type="text" v-model="userStore.currentUser.profile.realName" />
								<small id="realName-help">Their real name</small>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="description">About Me</label>
								<Textarea id="description" v-model="userStore.currentUser.profile.description" />
								<small id="description-help">A little about them</small>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="gender">Gender</label>
								<Dropdown
									id="gender"
									v-model="userStore.currentUser.profile.gender"
									:selection-limit="1"
									:options="genders"
									option-label="label"
									option-value="value"
									placeholder="Preferred Gender"
								/>
								<small id="gender-help">Their preferred gender identification</small>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="birthday">Birthday</label>
								<span class="p-input-icon-right">
									<Calendar input-id="birthday" ref="birthdayCal" :show-icon="true" v-model="userStore.currentUser.profile.birthday" />
								</span>
							</div>
						</div>
					</template>
				</Card>
			</div>

			<div class="col-12 lg:col-4 xl:col-4">
				<Card class="card">
					<template #title>
						<h5>Visibility</h5>
					</template>

					<template #content>
						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visBirthday">Show Birthday To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visBirthday"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visDescription">Show 'About Me' To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visDescription"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visEmail">Show Email Address To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visEmail"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visGender">Show Gender To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visGender"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visProfile">Show Profile To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visProfile"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visRealName">Show Real Name To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visRealName"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>

						<div class="p-fluid grid mt-3">
							<div class="field col-12 mb-1">
								<label for="visSearches">Show In Searches To..</label>
								<Dropdown
									v-model="userStore.currentUser.settings.visSearches"
									:selection-limit="1"
									:options="visibilityOpts"
									option-label="label"
									option-value="value"
									placeholder="Select Visibility"
								/>
							</div>
						</div>
					</template>
				</Card>
			</div>
		</div>
	</form>
</template>

<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import { useApi } from "@/composables/useApi.js";
import { useUserStore } from "@/stores/user-store";
import PasswordInput from "@/components/custom/PasswordInput.vue";

const route = useRoute();
const userStore = useUserStore();

const id = Number(route.params.id);

await userStore.fetchCurrentUser(id);

const filteredUserRoles = ref(null);
const birthdayCal = ref(null);

const userRoles = ref([]);

useApi().get("/1.1/Roles").then((res) => {
	if (res.status === 200) {
		userRoles.value = res.data;
	}

	return;
});

const searchUserRoles = (event) => {
	if (!event.query.trim().length) {
		filteredUserRoles.value = userRoles.value.filter((role) => !userStore.currentUser?.userRoles?.some((userRole) => userRole.value === role.value));
	} else {
		filteredUserRoles.value = userRoles.value.filter((role) => {
			if (userStore.currentUser?.userRoles?.some((userRole) => userRole.value === role.value)) {
				return;
			}

			return role.label?.toLowerCase().startsWith(event.query.toLowerCase());
		});
	}
};

const visibilityOpts = [
	{ label: "Only Them", value: 0 },
	{ label: "Friends", value: 1 },
	{ label: "Any Authenticated Users", value: 2 },
	{ label: "Anyone", value: 3 },
];

const genders = [
	{ label: "None", value: 0 },
	{ label: "Female", value: 1 },
	{ label: "Male", value: 2 },
	{ label: "Other", value: 3 },
];
</script>

<style lang="scss" scoped>
.p-button {
	margin-right: 0.5rem;
}
</style>
