<template>
	<div class="grid p-fluid">
		<div class="col-12 xl:col-6">
			<Card class="card">
				<template #title><h5>Linear Chart</h5></template>
				<template #content>
					<Chart type="line" :data="lineData" :options="lineOptions"></Chart>
				</template>
			</Card>
		</div>
		<div class="col-12 xl:col-6">
			<Card class="card">
				<template #title><h5>Bar Chart</h5></template>
				<template #content>
					<Chart type="bar" :data="barData" :options="barOptions"></Chart>
				</template>
			</Card>
		</div>
		<div class="col-12 xl:col-6">
			<Card class="card flex flex-column align-items-center">
				<template #title><h5 class="text-left w-full">Pie Chart</h5></template>
				<template #content>
					<Chart type="pie" :data="pieData" :options="pieOptions"></Chart>
				</template>
			</Card>
		</div>
		<div class="col-12 xl:col-6">
			<Card class="card flex flex-column align-items-center">
				<template #title><h5 class="text-left w-full">Doughnut Chart</h5></template>
				<template #content>
					<Chart type="doughnut" :data="pieData" :options="pieOptions"></Chart>
				</template>
			</Card>
		</div>
		<div class="col-12 xl:col-6">
			<Card class="card flex flex-column align-items-center">
				<template #title><h5 class="text-left w-full">Polar Area Chart</h5></template>
				<template #content>
					<Chart type="polarArea" :data="polarData" :options="polarOptions"></Chart>
				</template>
			</Card>
		</div>
		<div class="col-12 xl:col-6">
			<Card class="card flex flex-column align-items-center">
				<template #title><h5 class="text-left w-full">Radar Chart</h5></template>
				<template #content>
					<Chart type="radar" :data="radarData" :options="radarOptions"></Chart>
				</template>
			</Card>
		</div>
	</div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useLayout } from "@/layout/composables/layout";
import { barChart, lineChart, pieChart, polarChart, radarChart } from "@/composables/chart-colors";

const { layoutConfig } = useLayout();
let documentStyle = getComputedStyle(document.documentElement);
let textColor = documentStyle.getPropertyValue("--text-color");
// let textColorSecondary = documentStyle.getPropertyValue("--text-color-secondary");
// let surfaceBorder = documentStyle.getPropertyValue("--surface-border");

const lineData = ref(null);
const pieData = ref(null);
const polarData = ref(null);
const barData = ref(null);
const radarData = ref(null);

const lineOptions = lineChart();
const pieOptions = pieChart();
const polarOptions = polarChart();
const barOptions = barChart();
const radarOptions = radarChart();

const setChart = () => {
	barData.value = {
		labels: ["January", "February", "March", "April", "May", "June", "July"],
		datasets: [
			{
				label: "First Dataset",
				backgroundColor: documentStyle.getPropertyValue("--chart-indigo"),
				borderColor: documentStyle.getPropertyValue("--chart-indigo"),
				data: [65, 59, 80, 81, 56, 55, 40],
			},
			{
				label: "Second Dataset",
				backgroundColor: documentStyle.getPropertyValue("--chart-green"),
				borderColor: documentStyle.getPropertyValue("--chart-green"),
				data: [28, 48, 40, 19, 86, 27, 90],
			},
		],
	};

	pieData.value = {
		labels: ["A", "B", "C"],
		datasets: [
			{
				data: [540, 325, 702],
				backgroundColor: [documentStyle.getPropertyValue("--indigo-500"), documentStyle.getPropertyValue("--purple-500"), documentStyle.getPropertyValue("--teal-500")],
				hoverBackgroundColor: [documentStyle.getPropertyValue("--indigo-400"), documentStyle.getPropertyValue("--purple-400"), documentStyle.getPropertyValue("--teal-400")],
			},
		],
	};

	lineData.value = {
		labels: ["January", "February", "March", "April", "May", "June", "July"],
		datasets: [
			{
				label: "First Dataset",
				data: [65, 59, 80, 81, 56, 55, 40],
				fill: false,
				backgroundColor: documentStyle.getPropertyValue("--chart-indigo"),
				borderColor: documentStyle.getPropertyValue("--chart-indigo"),
				tension: 0.4,
			},
			{
				label: "Second Dataset",
				data: [28, 48, 40, 19, 86, 27, 90],
				fill: false,
				backgroundColor: documentStyle.getPropertyValue("--chart-green"),
				borderColor: documentStyle.getPropertyValue("--chart-green"),
				tension: 0.4,
			},
		],
	};

	polarData.value = {
		datasets: [
			{
				data: [11, 16, 7, 3],
				backgroundColor: [
					documentStyle.getPropertyValue("--indigo-500"),
					documentStyle.getPropertyValue("--purple-500"),
					documentStyle.getPropertyValue("--teal-500"),
					documentStyle.getPropertyValue("--orange-500"),
				],
				label: "My dataset",
			},
		],
		labels: ["Indigo", "Purple", "Teal", "Orange"],
	};

	radarData.value = {
		labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
		datasets: [
			{
				label: "First dataset",
				borderColor: documentStyle.getPropertyValue("--indigo-400"),
				pointBackgroundColor: documentStyle.getPropertyValue("--indigo-400"),
				pointBorderColor: documentStyle.getPropertyValue("--indigo-400"),
				pointHoverBackgroundColor: textColor,
				pointHoverBorderColor: documentStyle.getPropertyValue("--indigo-400"),
				data: [65, 59, 90, 81, 56, 55, 40],
			},
			{
				label: "Second dataset",
				borderColor: documentStyle.getPropertyValue("--purple-400"),
				pointBackgroundColor: documentStyle.getPropertyValue("--purple-400"),
				pointBorderColor: documentStyle.getPropertyValue("--purple-400"),
				pointHoverBackgroundColor: textColor,
				pointHoverBorderColor: documentStyle.getPropertyValue("--purple-400"),
				data: [28, 48, 40, 19, 96, 27, 100],
			},
		],
	};
};

watch(
	layoutConfig.theme,
	() => {
		setChart();
	},
	{ immediate: true },
);
</script>
