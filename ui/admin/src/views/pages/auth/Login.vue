<template>
	<div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
		<div class="flex flex-column align-items-center justify-content-center">
			<!-- <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" /> -->
			<div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
				<div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
					<div class="text-center mb-5">
						<!-- <img src="/demo/images/login/avatar.png" alt="Image" height="50" class="mb-3" /> -->
						<div class="text-900 text-3xl font-medium mb-3">Welcome!</div>
						<span class="text-600 font-medium">Sign in to continue</span>
					</div>

					<div>
						<form @submit.prevent="login">
							<p class="error-message" v-if="errorMessage.length > 0">{{ errorMessage }}</p>

							<label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
							<InputText id="email1" type="email" placeholder="Email address" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="email" required />

							<label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
							<Password
								id="password1"
								v-model="password"
								placeholder="Password"
								class="w-full mb-3"
								input-class="w-full"
								:input-style="{ padding: '1rem' }"
								:toggle-mask="true"
								:feedback="false"
								required
							/>

							<div class="flex align-items-center justify-content-between mb-5 gap-5">
								<router-link :to="{ name: 'forgotpass' }" class="text-primary font-medium"> Forgot password? </router-link>
							</div>

							<Button type="submit" label="Sign In" class="w-full p-3 text-xl" @click="doLogIn"></Button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from 'vue-router';
import { useApi } from '@/composables/useApi.js';
import { useAuthStore } from '@/stores/auth-store.js';
import { useGeneralStore } from '@/stores/general-store.js';

const router = useRouter();
const authStore = useAuthStore();
const generalStore = useGeneralStore();

const email = ref("");
const password = ref("");
const errorMessage = ref("");

const doLogIn = async () => {
	const api = useApi();

	try {
		const res = await api.post('/1.1/Account/Login', {
			email: email.value,
			key: password.value,
			provider: 1
		});

		if (res.status === 200) {
			const axsRes = await api.post('/1.1/Roles/UserInRole', {
				name: 'Administrator'
			});

			if (axsRes.status === 200 && axsRes.data) {
				generalStore.currentUser.userId = res.data.userId;

				router.push({ name: 'dashboard' });
			} else {
				authStore.logOut();
				errorMessage.value = "Account cannot access this section";
			}
		} else {
			errorMessage.value = res.data;
		}
	} catch (error) {
		console.log(error);
		authStore.logOut();
		errorMessage.value = error.response.data;
	}

	return;
};
</script>

<style scoped>
.pi-eye {
	transform: scale(1.6);
	margin-right: 1rem;
}

.pi-eye-slash {
	transform: scale(1.6);
	margin-right: 1rem;
}

.error-message {
	color: red;
	font-size: 1.2rem;
	margin-bottom: 1rem;
	font-style: italic;
}
</style>
