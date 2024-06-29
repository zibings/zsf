import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';

const routes = [
	{
		path: '/',
		component: AppLayout,
		children: [
			{
				path: '/',
				name: 'dashboard',
				exact: true,
				component: () => import('@/views/dashboards/Dashboard.vue'),
				meta: {
					breadcrumb: [{ label: 'Sales Dashboard' }]
				}
			},
			{
				path: '/dashboards/dashboardanalytics',
				name: 'dashboardanalytics',
				exact: true,
				component: () => import('@/views/dashboards/DashboardAnalytics.vue'),
				meta: {
					breadcrumb: [{ label: 'Analytics Dashboard' }]
				}
			},
			{
				path: '/dashboards/dashboardsaas',
				name: 'saas',
				exact: true,
				component: () => import('@/views/dashboards/DashboardSaas.vue'),
				meta: {
					breadcrumb: [{ label: 'SaaS Dashboard' }]
				}
			},
			{
				path: '/apps/blog/list',
				component: () => import('@/views/apps/blog/List.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Blog', item: 'List' }]
				}
			},
			{
				path: '/apps/blog/detail',
				component: () => import('@/views/apps/blog/Detail.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Blog', item: 'Detail' }]
				}
			},
			{
				path: '/apps/blog/edit',
				name: 'blog-edit',
				component: () => import('@/views/apps/blog/Edit.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Blog', item: 'Edit' }]
				}
			},
			{
				path: '/apps/calendar',
				name: 'calendar',
				component: () => import('@/views/apps/Calendar.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Calendar' }]
				}
			},
			{
				path: '/apps/files',
				name: 'files',
				component: () => import('@/views/apps/Files.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Files' }]
				}
			},
			{
				path: '/apps/chat',
				name: 'chat',
				component: () => import('@/views/apps/chat/Index.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Chat' }]
				}
			},
			{
				path: '/apps/tasklist',
				name: 'tasklist',
				component: () => import('@/views/apps/tasklist/Index.vue'),
				meta: {
					breadcrumb: [{ parent: 'Apps', label: 'Task List' }]
				}
			},
			{
				path: '/apps/mail',
				component: () => import('@/views/apps/mail/Index.vue'),
				children: [
					{
						path: '/apps/mail/inbox',
						name: 'mail-inbox',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Inbox' }]
						}
					},
					{
						path: '/apps/mail/compose',
						name: 'mail-compose',
						component: () => import('@/views/apps/mail/ComposeNew.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Compose' }]
						}
					},
					{
						path: '/apps/mail/detail/:id',
						name: 'mail-detail',
						component: () => import('@/views/apps/mail/Detail.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Detail' }]
						}
					},
					{
						path: '/apps/mail/starred',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Starred' }]
						}
					},
					{
						path: '/apps/mail/spam',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Spam' }]
						}
					},
					{
						path: '/apps/mail/important',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Important' }]
						}
					},
					{
						path: '/apps/mail/sent',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Sent' }]
						}
					},
					{
						path: '/apps/mail/archived',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Archived' }]
						}
					},
					{
						path: '/apps/mail/trash',
						component: () => import('@/views/apps/mail/MailTypes.vue'),
						meta: {
							breadcrumb: [{ parent: 'Apps', label: 'Mail', item: 'Trash' }]
						}
					}
				]
			},
			{
				path: '/uikit/formlayout',
				name: 'formlayout',

				component: () => import('@/views/uikit/FormLayout.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Form Layout' }]
				}
			},
			{
				path: '/uikit/invalidstate',
				name: 'invalidstate',
				component: () => import('@/views/uikit/InvalidState.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Invalid State' }]
				}
			},
			{
				path: '/uikit/input',
				name: 'input',
				component: () => import('@/views/uikit/Input.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Input' }]
				}
			},
			{
				path: '/uikit/floatlabel',
				name: 'floatlabel',
				component: () => import('@/views/uikit/FloatLabel.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Float Label' }]
				}
			},
			{
				path: '/uikit/button',
				name: 'button',
				component: () => import('@/views/uikit/Button.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Button' }]
				}
			},
			{
				path: '/uikit/table',
				name: 'table',
				component: () => import('@/views/uikit/Table.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Table' }]
				}
			},
			{
				path: '/uikit/list',
				name: 'list',
				component: () => import('@/views/uikit/List.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'List' }]
				}
			},
			{
				path: '/pages/timeline',
				name: 'timeline',
				component: () => import('@/views/pages/Timeline.vue'),
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'Timeline' }]
				}
			},
			{
				path: '/uikit/tree',
				name: 'tree',
				component: () => import('@/views/uikit/Tree.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Tree' }]
				}
			},
			{
				path: '/uikit/panel',
				name: 'panel',
				component: () => import('@/views/uikit/Panels.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Panel' }]
				}
			},
			{
				path: '/uikit/overlay',
				name: 'overlay',
				component: () => import('@/views/uikit/Overlay.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Overlay' }]
				}
			},
			{
				path: '/uikit/media',
				name: 'media',
				component: () => import('@/views/uikit/Media.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Media' }]
				}
			},
			{
				path: '/uikit/menu/',
				component: () => import('@/views/uikit/Menu.vue'),
				children: [
					{
						path: '',
						component: () => import('@/views/uikit/menu/PersonalDemo.vue'),
						meta: {
							breadcrumb: [{ parent: 'UI Kit', label: 'Menu' }]
						}
					},
					{
						path: '/uikit/menu/seat',
						component: () => import('@/views/uikit/menu/SeatDemo.vue'),
						meta: {
							breadcrumb: [{ parent: 'UI Kit', label: 'Menu' }]
						}
					},
					{
						path: '/uikit/menu/payment',
						component: () => import('@/views/uikit/menu/PaymentDemo.vue'),
						meta: {
							breadcrumb: [{ parent: 'UI Kit', label: 'Menu' }]
						}
					},
					{
						path: '/uikit/menu/confirmation',
						component: () => import('@/views/uikit/menu/ConfirmationDemo.vue'),
						meta: {
							breadcrumb: [{ parent: 'UI Kit', label: 'Menu' }]
						}
					}
				]
			},
			{
				path: '/uikit/message',
				name: 'message',
				component: () => import('@/views/uikit/Messages.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Messages' }]
				}
			},
			{
				path: '/uikit/file',
				name: 'file',
				component: () => import('@/views/uikit/File.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'File' }]
				}
			},
			{
				path: '/uikit/charts',
				name: 'charts',
				component: () => import('@/views/uikit/Chart.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Charts' }]
				}
			},
			{
				path: '/uikit/misc',
				name: 'misc',
				component: () => import('@/views/uikit/Misc.vue'),
				meta: {
					breadcrumb: [{ parent: 'UI Kit', label: 'Misc' }]
				}
			},
			{
				path: '/utilities/icons',
				name: 'icons',
				component: () => import('@/views/utilities/Icons.vue'),
				meta: {
					breadcrumb: [{ parent: 'Utilities', label: 'Icons' }]
				}
			},
			{
				path: '/utilities/colors',
				name: 'colors',
				component: () => import('@/views/utilities/Colors.vue'),
				meta: {
					breadcrumb: [{ parent: 'Utilities', label: 'Colors' }]
				}
			},
			{
				path: '/pages/crud',
				name: 'crud',
				component: () => import('@/views/pages/Crud.vue'),
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'Crud' }]
				}
			},
			{
				path: '/ecommerce/product-overview',
				name: 'product-overview',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Product Overview' }]
				},
				component: () => import('@/views/e-commerce/ProductOverview.vue')
			},
			{
				path: '/ecommerce/product-list',
				name: 'product-list',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Product List' }]
				},
				component: () => import('@/views/e-commerce/ProductList.vue')
			},
			{
				path: '/ecommerce/shopping-cart',
				name: 'shopping-cart',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Shopping Chart' }]
				},
				component: () => import('@/views/e-commerce/ShoppingCart.vue')
			},
			{
				path: '/ecommerce/new-product',
				name: 'new-product',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'New Product' }]
				},
				component: () => import('@/views/e-commerce/NewProduct.vue')
			},
			{
				path: '/ecommerce/order-history',
				name: 'order-history',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Order History' }]
				},
				component: () => import('@/views/e-commerce/OrderHistory.vue')
			},
			{
				path: '/ecommerce/order-summary',
				name: 'order-summary',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Order Summary' }]
				},
				component: () => import('@/views/e-commerce/OrderSummary.vue')
			},
			{
				path: '/ecommerce/checkout-form',
				name: 'checkout-form',
				meta: {
					breadcrumb: [{ parent: 'E-Commerce', label: 'Checkout Form' }]
				},
				component: () => import('@/views/e-commerce/CheckoutForm.vue')
			},
			{
				path: '/profile/list',
				name: 'user-list',
				component: () => import('@/views/user-management/UserList.vue'),
				meta: {
					breadcrumb: [{ parent: 'User Management', label: 'List' }]
				}
			},
			{
				path: '/profile/create',
				name: 'user-create',
				component: () => import('@/views/user-management/UserCreate.vue'),
				meta: {
					breadcrumb: [{ parent: 'User Management', label: 'Create' }]
				}
			},
			{
				path: '/pages/invoice',
				name: 'invoice',
				component: () => import('@/views/pages/Invoice.vue'),
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'Invoice' }]
				}
			},
			{
				path: '/pages/help',
				name: 'help',
				component: () => import('@/views/pages/Help.vue'),
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'Help' }]
				}
			},
			{
				path: '/pages/empty',
				name: 'empty',
				component: () => import('@/views/pages/Empty.vue')
			},
			{
				path: '/pages/aboutus',
				name: 'aboutus',
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'About' }]
				},
				component: () => import('@/views/pages/AboutUs.vue')
			},
			{
				path: '/pages/contact',
				name: 'contact',
				component: () => import('@/views/pages/ContactUs.vue'),
				meta: {
					breadcrumb: [{ parent: 'Pages', label: 'Contact' }]
				}
			},
			{
				path: '/documentation',
				name: 'documentation',
				component: () => import('@/views/utilities/Documentation.vue'),
				meta: {
					breadcrumb: [{ parent: 'Start', label: 'Documentation' }]
				}
			},
			{
				path: '/blocks',
				name: 'blocks',
				component: () => import('@/views/utilities/Blocks.vue'),
				meta: {
					breadcrumb: [{ parent: 'Prime Blocks', label: 'Free Blocks' }]
				}
			}
		]
	},
	{
		path: '/auth/login',
		name: 'login',
		component: () => import('@/views/pages/auth/Login.vue')
	},
	{
		path: '/auth/login2',
		name: 'login2',
		component: () => import('@/views/pages/auth/Login2.vue')
	},
	{
		path: '/auth/access',
		name: 'accessDenied',
		component: () => import('@/views/pages/auth/AccessDenied.vue')
	},
	{
		path: '/auth/access2',
		name: 'accessDenied2',
		component: () => import('@/views/pages/auth/AccessDenied2.vue')
	},
	{
		path: '/auth/error',
		name: 'error',
		component: () => import('@/views/pages/auth/Error.vue')
	},
	{
		path: '/auth/error2',
		name: 'error2',
		component: () => import('@/views/pages/auth/Error2.vue')
	},
	{
		path: '/auth/register',
		name: 'register',
		component: () => import('@/views/pages/auth/Register.vue')
	},
	{
		path: '/auth/forgotpassword',
		name: 'forgotpassword',
		component: () => import('@/views/pages/auth/ForgotPassword.vue')
	},
	{
		path: '/auth/newpassword',
		name: 'newpassword',
		component: () => import('@/views/pages/auth/NewPassword.vue')
	},
	{
		path: '/auth/verification',
		name: 'verification',
		component: () => import('@/views/pages/auth/Verification.vue')
	},
	{
		path: '/auth/lockscreen',
		name: 'lockscreen',
		component: () => import('@/views/pages/auth/LockScreen.vue')
	},
	{
		path: '/pages/notfound',
		name: 'notfound',
		component: () => import('@/views/pages/NotFound.vue')
	},
	{
		path: '/landing',
		name: 'landing',
		component: () => import('@/views/pages/Landing.vue')
	},
	{
		path: '/:pathMatch(.*)*',
		name: 'notfound',
		component: () => import('@/views/pages/NotFound.vue')
	}
];

const router = createRouter({
	history: createWebHistory(),
	routes,
	scrollBehavior() {
		return { left: 0, top: 0 };
	}
});

export default router;
