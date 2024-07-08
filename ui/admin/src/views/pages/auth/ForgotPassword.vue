<template>
	<div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
		<div class="flex flex-column align-items-center justify-content-center">
			<!-- <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" /> -->
			<div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
				<div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
					<div class="text-center mb-5">
						<div class="text-900 text-3xl font-medium mb-3">Reset Your Password</div>
						<span class="text-600 font-medium">Fill in your email to continue</span>
					</div>

					<div>
						<form @submit.prevent="resetPwd">
							<label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
							<InputText id="email1" type="email" placeholder="Email address" class="w-full mb-5" style="padding: 1rem" v-model="email" required />
							<!-- md:w-30rem -->

							<Button type="submit" label="Reset Password" class="w-full p-3 text-xl"></Button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { useLayout } from "@/layout/composables/layout";
import { ref, computed } from "vue";

const { layoutConfig } = useLayout();
const email = ref("");

const logoUrl = computed(() => {
	return `/layout/images/${layoutConfig.darkTheme.value ? "logo-white" : "logo-dark"}.svg`;
});
</script>

<style scoped lang="scss"></style>
