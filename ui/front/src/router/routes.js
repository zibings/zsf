const routes = [
	{
		path: "/",
		component: () => import("../layout/MainLayout.vue"),
		children: [
			{
				path: "/",
				name: "home",
				component: () => import("../views/HomeView.vue"),
			},
			{
				path: "/login",
				name: "login",
				component: () => import("../views/Login.vue"),
			},
			{
				path: "/register",
				name: "register",
				component: () => import("../views/Register.vue"),
			},
			{
				path: "/profile",
				name: "profile",
				meta: { requiresAuth: true },
				component: () => import("../views/Profile.vue"),
			},
		]
	},
];

export default routes;
