import { defineStore } from "pinia";
import { useApi } from "@/composables/useApi.js";
import { useGeneralStore } from "./general-store";

const generalStore = useGeneralStore();

const columns = [
	{ field: "id", header: "ID", filter: false, sortable: true },
	{ field: "name", header: "Name", filter: true, sortable: false },
	{ field: "emailConfirmed", header: "Email Confirmed", filter: false, sortable: false, display: true },
];

const users = [
	{ id: 1, name: "John", emailConfirmed: true },
	{ id: 2, name: "Jane", emailConfirmed: false },
	{ id: 3, name: "Mary" },
	{ id: 4, name: "Peter" },
	{ id: 5, name: "Barbara" },
	{ id: 6, name: "Joseph" },
	{ id: 7, name: "Glenn" },
	{ id: 8, name: "Lucy" },
	{ id: 9, name: "Brenda" },
	{ id: 10, name: "Mark" },
	{ id: 11, name: "Jenny" },
	{ id: 12, name: "George" },
	{ id: 13, name: "Susan" },
	{ id: 14, name: "Laura" },
	{ id: 15, name: "Robert" },
	{ id: 16, name: "Linda" },
	{ id: 17, name: "Donna" },
	{ id: 18, name: "Edward" },
	{ id: 19, name: "Richard" },
	{ id: 20, name: "Carol" },
	{ id: 21, name: "Karen" },
	{ id: 22, name: "Henry" },
	{ id: 23, name: "Debra" },
	{ id: 24, name: "Ronald" },
	{ id: 25, name: "Paul" },
	{ id: 26, name: "Janet" },
	{ id: 27, name: "Ruth" },
	{ id: 28, name: "Sharon" },
	{ id: 29, name: "Michelle" },
	{ id: 30, name: "Sarah" },
	{ id: 31, name: "Karen" },
	{ id: 32, name: "Nancy" },
	{ id: 33, name: "Betty" },
	{ id: 34, name: "Dorothy" },
	{ id: 35, name: "Lisa" },
	{ id: 36, name: "Sandra" },
	{ id: 37, name: "Ashley" },
	{ id: 38, name: "Kimberly" },
	{ id: 39, name: "Donna" },
	{ id: 40, name: "Emily" },
	{ id: 41, name: "Carol" },
	{ id: 42, name: "Amanda" },
	{ id: 43, name: "Melissa" },
	{ id: 44, name: "Deborah" },
	{ id: 45, name: "Stephanie" },
	{ id: 46, name: "Rebecca" },
	{ id: 47, name: "Laura" },
	{ id: 48, name: "Helen" },
	{ id: 49, name: "Sharon" },
	{ id: 50, name: "Cynthia" },
	{ id: 51, name: "Kathleen" },
	{ id: 52, name: "Amy" },
	{ id: 53, name: "Shirley" },
	{ id: 54, name: "Angela" },
	{ id: 55, name: "Anna" },
	{ id: 56, name: "Ruth" },
	{ id: 57, name: "Brenda" },
	{ id: 58, name: "Pamela" },
	{ id: 59, name: "Nicole" },
	{ id: 60, name: "Katherine" },
	{ id: 61, name: "Samantha" },
	{ id: 62, name: "Christine" },
	{ id: 63, name: "Catherine" },
	{ id: 64, name: "Virginia" },
	{ id: 65, name: "Debra" },
	{ id: 66, name: "Rachel" },
	{ id: 67, name: "Janet" },
	{ id: 68, name: "Emma" },
	{ id: 69, name: "Carolyn" },
	{ id: 70, name: "Maria" },
	{ id: 71, name: "Heather" },
	{ id: 72, name: "Diane" },
	{ id: 73, name: "Julie" },
	{ id: 74, name: "Joyce" },
	{ id: 75, name: "Victoria" },
	{ id: 76, name: "Olivia" },
	{ id: 77, name: "Kelly" },
	{ id: 78, name: "Christina" },
	{ id: 79, name: "Joan" },
	{ id: 80, name: "Evelyn" },
	{ id: 81, name: "Lauren" },
	{ id: 82, name: "Judith" },
	{ id: 83, name: "Cheryl" },
	{ id: 84, name: "Megan" },
	{ id: 85, name: "Andrea" },
	{ id: 86, name: "Martha" },
	{ id: 87, name: "Frances" },
	{ id: 88, name: "Hannah" },
	{ id: 89, name: "Jacqueline" },
	{ id: 90, name: "Ann" },
	{ id: 91, name: "Gloria" },
	{ id: 92, name: "Jean" },
	{ id: 93, name: "Kathryn" },
	{ id: 94, name: "Alice" },
	{ id: 95, name: "Teresa" },
	{ id: 96, name: "Sara" },
	{ id: 97, name: "Janice" },
	{ id: 98, name: "Doris" },
	{ id: 99, name: "Madison" },
	{ id: 100, name: "Julia" },
];

export const useUserStore = defineStore("user", {
	state: () => ({
		users: [],
		columns: [],
		currentUser: {
			account: {},
			profile: {},
			settings: {},
			roles: [],
		},
	}),
	actions: {
		async fetchColumns() {
			if (!generalStore.api.useApi) {
				this.columns = columns;

				return;
			}

			await useApi()
				.get(generalStore.api.fetchColumns)
				.then((response) => {
					this.columns = response.data;
				});
		},
		async fetchUsers() {
			if (!generalStore.api.useApi) {
				this.users = users;

				return;
			}

			await useApi()
				.get(generalStore.api.fetchUsers)
				.then((response) => {
					this.users = response.data;
				});
		},
		async fetchUsersCached() {
			if (this.users.length <= 0) {
				await this.fetchUsers();
			}
		},
		async fetchColumnsCached() {
			if (this.columns.length <= 0) {
				await this.fetchColumns();
			}
		},
		async fetchCurrentUser(id) {
			const api = useApi();

			try {
				let res = await api.get(`/1.1/Account?userId=${id}&r=${Math.random()}`);

				if (res.status !== 200) {
					console.error("Error fetching user");
					console.log(res);

					return;
				}

				this.currentUser.account = res.data;

				res = await api.get(`/1.1/Profile?userId=${id}&r=${Math.random()}`);

				if (res.status === 200) {
					this.currentUser.profile = res.data;
				}

				res = await api.get(`/1.1/Settings?userId=${id}&r=${Math.random()}`);

				if (res.status === 200) {
					this.currentUser.settings = res.data;
				}

				res = await api.get(`/1.1/Roles/UserRoles/${id}?r=${Math.random()}`);

				if (res.status === 200) {
					this.currentUser.roles = [];

					for (const role of Object.values(res.data)) {
						if (this.currentUser.roles.filter((r) => r.id === role.id).length <= 0) {
							this.currentUser.roles.push(role);
						}
					}
				}
			} catch (error) {
				console.error("Exception fetching user");
				console.log(error);
			}

			return;
		},
		async saveUser(user) {
			const api = useApi();
			let endpoint = "/1.1/Account/Create";

			if (user.account.id > 0) {
				endpoint = `/1.1/Account/Update`;
			}

			const data = {
				id: user.account.id,
				email: user.account.email,
				confirmEmail: user.account.email,
				key: user.account.password,
				confirmKey: user.account.password,
				provider: 1,
				profile: {
					birthday: user.profile.birthday,
					description: user.profile.description,
					displayName: user.profile.displayName,
					gender: user.profile.gender,
					realName: user.profile.realName,
				},
				settings: {
					htmlEmails: user.settings.htmlEmails,
					playSounds: user.settings.playSounds,
				},
				visibilities: {
					birthday: user.settings.visBirthday,
					description: user.settings.visDescription,
					email: user.settings.visEmail,
					gender: user.settings.visGender,
					profile: user.settings.visProfile,
					realName: user.settings.visRealName,
					searches: user.settings.visSearches,
				},
			};

			if (user.password) {
				data.key = user.password;
				data.confirmKey = user.password;
			}

			const res = await api.post(endpoint, data);

			if (res.status !== 200) {
				return `Error saving user: ${res.data}`;
			}

			if (user.roles.length > 0) {
				api.post(`/1.1/Roles/SyncUserRoles?r=${Math.random()}`, {
					userId: user.account.id,
					roles: user.roles.map((r) => r.name),
				});
			}

			return "User saved successsfully";
		},
	},
});
