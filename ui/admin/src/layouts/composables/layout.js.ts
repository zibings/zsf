import {toRefs, ref, computed} from 'vue';

export interface LayoutConfig {
	menuMode: string;
	colorScheme: string;
	componentTheme: string;
	scale: number;
	menuTheme: string;
	topbarTheme: string;
	menuProfilePosition: string;
}

const layoutConfig = ref<LayoutConfig>({
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

const layoutState = ref<LayoutState>({
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
		layoutState.value.menuProfileActive = !layoutState.value.menuProfileActive;

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
		const currentColorScheme = `theme-${layoutConfig.value.colorScheme}`;
		const newColorScheme = `theme-${colorScheme}`;
		const newHref = themeLinkHref?.replace(currentColorScheme, newColorScheme);

		if (themeLink && newHref) {
			replaceLink(themeLink, newHref, () => {
				layoutConfig.value.colorScheme = colorScheme;
				layoutConfig.value.menuTheme = colorScheme;

				return;
			});
		}

		return;
	};

	const setScale = (scale: number) => {
		layoutConfig.value.scale = scale;

		return;
	};

	const setActiveMenuItem = (menuItem: string) => {
		layoutState.value.activeMenuItem = menuItem;

		return;
	};

	const onMenuToggle = () => {
		if (layoutConfig.value.menuMode === 'overlay') {
			layoutState.value.overlayMenuActive = !layoutState.value.overlayMenuActive;
		}

		if (window.innerWidth > 991) {
			layoutState.value.staticMenuDesktopInactive = !layoutState.value.staticMenuDesktopInactive;
		} else {
			layoutState.value.staticMenuMobileActive = !layoutState.value.staticMenuMobileActive;
		}

		return;
	};

	const onTopbarMenuToggle = () => {
		layoutState.value.topbarMenuActive = !layoutState.value.topbarMenuActive;

		return;
	};

	const openRightSidebar = () => {
		layoutState.value.rightMenuActive = true;

		return;
	};

	const onProfileSidebarToggle = () => {
		layoutState.value.rightMenuActive = !layoutState.value.rightMenuActive;

		return;
	};

	const onConfigSidebarToggle = () => {
		layoutState.value.configSidebarVisible = !layoutState.value.configSidebarVisible;

		return;
	};

	const isSidebarActive = computed(() => (
		layoutState.value.overlayMenuActive ||
		layoutState.value.staticMenuMobileActive ||
		layoutState.value.overlaySubmenuActive));

	const isDarkTheme = computed(() => layoutConfig.value.colorScheme === 'dark');

	const isDesktop = computed(() => window.innerWidth > 991);

	const isSlim = computed(() => layoutConfig.value.menuMode === 'slim');

	const isSlimPlus = computed(() => layoutConfig.value.menuMode === 'slim-plus');

	const isHorizontal = computed(() => layoutConfig.value.menuMode === 'horizontal');

	const isOverlay = computed(() => layoutConfig.value.menuMode === 'overlay');

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
