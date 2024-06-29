import iconData from "../assets/data/icons.json";

interface SubIcon {
	paths: string[];
	attrs: unknown[];
	isMulticolor: boolean;
	isMulticolor2: boolean;
	grid: number;
	tags: string[];
}

interface Icon {
	icon: SubIcon;
	attrs: unknown[];
	properties: Record<string, string | number | boolean>;
	setIdx: number;
	setId: number;
	iconIdx: number;
}

export class IconService {
	private icons: Icon[];
	private selectedIcon: Icon | undefined;

	constructor() {
		this.icons = iconData.icons;
		this.selectedIcon = undefined;

		return;
	}

	getIcons(): Promise<Icon[]> {
		return new Promise((resolve) => {
			resolve(this.icons);
		});
	}

	getIcon(id: string): Icon | undefined {
		if (this.icons) {
			this.selectedIcon = this.icons.find(icon => icon.properties.id === id);

			return this.selectedIcon;
		}

		return undefined;
	}
}
