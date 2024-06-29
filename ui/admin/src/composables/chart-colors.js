import { ref, watch } from "vue";
import { useGeneralStore } from "@/stores/general-store";

const generalStore = useGeneralStore();

// const lineOptions = ref(null);
const barOptions = ref(null);
const pieOptions = ref(null);
const polarOptions = ref(null);
const radarOptions = ref(null);

let documentStyle = getComputedStyle(document.documentElement);
let textColor = documentStyle.getPropertyValue("--text-color").trim();
let textColorSecondary = documentStyle.getPropertyValue("--text-color-secondary").trim();
let surfaceBorder = documentStyle.getPropertyValue("--surface-border").trim();

function setColorOptions() {
	documentStyle = getComputedStyle(document.documentElement);
	textColor = documentStyle.getPropertyValue("--text-color").trim();
	textColorSecondary = documentStyle.getPropertyValue("--text-color-secondary").trim();
	surfaceBorder = documentStyle.getPropertyValue("--surface-border").trim();
}

export function lineChart() {
	const lineOptions = ref(null);

	watch(
		() => generalStore.themeUpdated,
		() => {
			setColorOptions();
			applyTheme();
		},
		{ immediate: true }
	);

	function applyTheme() {
		lineOptions.value = {
			plugins: {
				legend: {
					labels: {
						color: textColor,
					},
				},
			},
			scales: {
				x: {
					ticks: {
						color: textColorSecondary,
					},
					grid: {
						color: surfaceBorder,
						drawBorder: true,
					},
					border: {
						display: true,
					},
				},
				y: {
					ticks: {
						color: textColorSecondary,
					},
					grid: {
						color: surfaceBorder,
						drawBorder: false,
					},
					border: {
						display: false,
					},
				},
			},
		};
	}

	return lineOptions;
}

export function barChart() {
	watch(
		() => generalStore.themeUpdated,
		() => {
			setColorOptions();
			applyTheme();
		},
		{ immediate: true }
	);

	function applyTheme() {
		barOptions.value = {
			plugins: {
				legend: {
					labels: {
						color: textColor,
					},
				},
			},
			scales: {
				x: {
					ticks: {
						color: textColor,
						font: {
							weight: 500,
						},
					},
					grid: {
						display: false,
						drawBorder: false,
					},
					border: {
						display: false,
					},
				},
				y: {
					ticks: {
						color: textColor,
					},
					grid: {
						color: surfaceBorder,
						drawBorder: false,
					},
					border: {
						display: false,
					},
				},
			},
		};
	}

	return barOptions;
}

export function doughnutChart() {
	return pieChart();
}

export function pieChart() {
	watch(
		() => generalStore.themeUpdated,
		() => {
			setColorOptions();
			applyTheme();
		},
		{ immediate: true }
	);

	function applyTheme() {
		pieOptions.value = {
			plugins: {
				legend: {
					labels: {
						color: textColor,
						usePointStyle: true,
					},
				},
			},
		};
	}

	return pieOptions;
}

export function polarChart() {
	watch(
		() => generalStore.themeUpdated,
		() => {
			setColorOptions();
			applyTheme();
		},
		{ immediate: true }
	);

	function applyTheme() {
		polarOptions.value = {
			plugins: {
				legend: {
					labels: {
						color: textColor,
					},
				},
			},
			scales: {
				r: {
					grid: {
						color: surfaceBorder,
					},
				},
			},
		};
	}

	return polarOptions;
}

export function radarChart() {
	watch(
		() => generalStore.themeUpdated,
		() => {
			setColorOptions();
			applyTheme();
		},
		{ immediate: true }
	);

	function applyTheme() {
		radarOptions.value = {
			plugins: {
				legend: {
					labels: {
						color: textColor,
					},
				},
			},
			scales: {
				r: {
					grid: {
						color: textColorSecondary,
					},
					pointLabels: {
						color: textColor,
					},
				},
			},
		};
	}

	return radarOptions;
}
