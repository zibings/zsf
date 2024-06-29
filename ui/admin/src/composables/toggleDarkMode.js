// import { inject, ref, watchEffect } from "vue";
import { useGeneralStore } from "@/stores/general-store";

// console.log("useGeneralStore", useGeneralStore);

// console.log(generalStore);

export async function toggleDarkMode(dark) {
	const generalStore = useGeneralStore();

	console.log("dark:", generalStore.darkMode);
	let newTheme = generalStore.theme;
	if (dark) {
		newTheme = generalStore.darkTheme;
	} else {
		newTheme = generalStore.lightTheme;
	}

	const elementId = "theme-css";
	const linkElements = [...document.querySelectorAll("#" + elementId)];

	if (linkElements.length > 1) {
		linkElements.slice(1).forEach((linkEl) => {
			linkEl.remove();
		});
	}

	const linkElement = document.getElementById(elementId);
	const cloneLinkElement = linkElement.cloneNode(true);

	if (linkElement.getAttribute("href").search(newTheme)) {
		if (dark) {
			generalStore.theme = generalStore.lightTheme;
		} else {
			generalStore.theme = generalStore.darkTheme;
		}
	}

	const newThemeUrl = linkElement.getAttribute("href").replace(generalStore.theme, newTheme);

	cloneLinkElement.setAttribute("id", elementId + "-clone");
	cloneLinkElement.setAttribute("href", newThemeUrl);
	cloneLinkElement.addEventListener("load", () => {
		linkElement.remove();
		cloneLinkElement.setAttribute("id", elementId);

		generalStore.themeUpdated = !generalStore.themeUpdated;
	});
	linkElement.parentNode.insertBefore(cloneLinkElement, linkElement.nextSibling);

	generalStore.theme = newTheme;
}
