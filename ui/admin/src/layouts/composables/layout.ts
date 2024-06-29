import {toRefs, reactive, computed} from 'vue';

export interface LayoutConfig {
	menuMode: string;
	colorScheme: string;
	componentTheme: string;
	scale: number;
	menuTheme: string;
	topbarTheme: string;
	menuProfilePosition: string;
}

const layoutConfig = reactive<LayoutConfig>({
	menuMode: 'slim',
	colorScheme: 'light',
	componentTheme: 'indigo',
	scale: 14,
	menuTheme: 'light',
	topbarTheme: 'indigo',
	menuProfilePosition: 'end'
});

export interface LayoutState {
	staticMenuDesktopInactive: boolean;
	overlayMenuActive: boolean;
	configSidebarVisible: boolean;
	staticMenuMobileActive: boolean;
	menuHoverActive: boolean;
	rightMenuActive: boolean;
	topbarMenuActive: boolean;
	sidebarActive: boolean;
	anchored: boolean;
	activeMenuItem: string;
	overlaySubmenuActive: boolean;
	menuProfileActive: boolean;
}

const layoutState = reactive<LayoutState>({
	staticMenuDesktopInactive: false,
	overlayMenuActive: false,
	configSidebarVisible: false,
	staticMenuMobileActive: false,
	menuHoverActive: false,
	rightMenuActive: false,
	topbarMenuActive: false,
	sidebarActive: false,
	anchored: false,
	activeMenuItem: '',
	overlaySubmenuActive: false,
	menuProfileActive: false
});

export function useLayout() {
	const onMenuProfileToggle = () => {
		layoutState.menuProfileActive = !layoutState.menuProfileActive;

		return;
	};

	const replaceLink = (linkElement: HTMLElement, newHref: string, callback: () => void) => {
		if (!linkElement || !newHref) {
			return;
		}

		const id = linkElement.getAttribute('id');

		if (!id) {
			return;
		}

		const cloneLinkElement = linkElement.cloneNode(true) as HTMLElement;

		cloneLinkElement.setAttribute('href', newHref);
		cloneLinkElement.setAttribute('id', id + '-clone');

		linkElement.parentNode?.insertBefore(cloneLinkElement, linkElement.nextSibling);

		cloneLinkElement.addEventListener('load', () => {
			linkElement.remove();

			const element = document.getElementById(id); // re-check
			element && element.remove();

			cloneLinkElement.setAttribute('id', id);
			callback && callback();

			return;
		});

		return;
	};

	const changeColorScheme = (colorScheme: string) => {
		const themeLink = document.getElementById('theme-link');
		const themeLinkHref = themeLink?.getAttribute('href');
		const currentColorScheme = `theme-${layoutConfig.colorScheme}`;
		const newColorScheme = `theme-${colorScheme}`;
		const newHref = themeLinkHref?.replace(currentColorScheme, newColorScheme);

		if (themeLink && newHref) {
			replaceLink(themeLink, newHref, () => {
				layoutConfig.colorScheme = colorScheme;
				layoutConfig.menuTheme = colorScheme;

				return;
			});
		}

		return;
	};

	const setScale = (scale: number) => {
		layoutConfig.scale = scale;

		return;
	};

	const setActiveMenuItem = (menuItem: string) => {
		layoutState.activeMenuItem = menuItem;

		return;
	};

	const onMenuToggle = () => {
		if (layoutConfig.menuMode === 'overlay') {
			layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
		}

		if (window.innerWidth > 991) {
			layoutState.staticMenuDesktopInactive = !layoutState.staticMenuDesktopInactive;
		} else {
			layoutState.staticMenuMobileActive = !layoutState.staticMenuMobileActive;
		}

		return;
	};

	const onTopbarMenuToggle = () => {
		layoutState.topbarMenuActive = !layoutState.topbarMenuActive;

		return;
	};

	const openRightSidebar = () => {
		layoutState.rightMenuActive = true;

		return;
	};

	const onProfileSidebarToggle = () => {
		layoutState.rightMenuActive = !layoutState.rightMenuActive;

		return;
	};

	const onConfigSidebarToggle = () => {
		layoutState.configSidebarVisible = !layoutState.configSidebarVisible;

		return;
	};

	const isSidebarActive = computed(() => (
		layoutState.overlayMenuActive ||
		layoutState.staticMenuMobileActive ||
		layoutState.overlaySubmenuActive));

	const isDarkTheme = computed(() => layoutConfig.colorScheme === 'dark');

	const isDesktop = computed(() => window.innerWidth > 991);

	const isSlim = computed(() => layoutConfig.menuMode === 'slim');

	const isSlimPlus = computed(() => layoutConfig.menuMode === 'slim-plus');

	const isHorizontal = computed(() => layoutConfig.menuMode === 'horizontal');

	const isOverlay = computed(() => layoutConfig.menuMode === 'overlay');

	return {
		layoutConfig: toRefs(layoutConfig),
		layoutState: toRefs(layoutState),
		setScale,
		onMenuToggle,
		isSidebarActive,
		isDarkTheme,
		setActiveMenuItem,
		onProfileSidebarToggle,
		onConfigSidebarToggle,
		isSlim,
		isSlimPlus,
		isHorizontal,
		isDesktop,
		openRightSidebar,
		onTopbarMenuToggle,
		changeColorScheme,
		replaceLink,
		onMenuProfileToggle,
		isOverlay
	};
};
