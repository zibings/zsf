const routes = [
	{
		path: "/",
		component: () => import("layout/MainLayout.vue"),
		children: [
			{
				path: "/",
				name: "home",
				component: () => import("views/HomeView.vue"),
			},
			{
				path: "/login",
				name: "login",
				meta: { requiresAuth: false },
				component: () => import("views/Login.vue"),
			},
			{
				path: "/register",
				name: "register",
				meta: { requiresAuth: false },
				component: () => import("views/Register.vue"),
			},
			{
				path: "/ConfirmEmail/:token",
				name: "ConfirmEmail",
				meta: { requiresAuth: false },
				component: () => import("views/ConfirmEmail.vue"),
				props: true
			},
			{
				path: "/profile",
				name: "profile",
				meta: { requiresAuth: true },
				component: () => import("views/Profile.vue"),
			},
		]
	},
];

export default routes;
