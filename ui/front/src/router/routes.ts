const routes = [
	{
		path: "/",
		component: () => import("layout/MainLayout.vue"),
		children: [
			{
				path: "",
				name: "home",
				alias: "index.html",
				meta: { static: true },
				component: () => import("views/HomeView.vue"),
			},
			{
				path: "login",
				name: "login",
				alias: "login.html",
				meta: { requiresAuth: false, static: true },
				component: () => import("views/Login.vue"),
			},
			{
				path: "register",
				name: "register",
				alias: "register.html",
				meta: { requiresAuth: false, static: true },
				component: () => import("views/Register.vue"),
			},
			{
				path: "ConfirmEmail/:token",
				name: "ConfirmEmail",
				meta: { requiresAuth: false },
				component: () => import("views/ConfirmEmail.vue"),
				props: true,
			},
			{
				path: "profile",
				name: "profile",
				meta: { requiresAuth: true },
				component: () => import("views/Profile.vue"),
			},
		],
	},
];

export default routes;
