<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { useLayout } from '@/layouts/composables/layout';

const products = ref(null);
const { layoutConfig } = useLayout();

let storeInterval = ref(null);
const storeATotalValue = ref(100);
const storeBTotalValue = ref(120);
const storeCTotalValue = ref(150);
const storeDTotalValue = ref(80);

const storeADiff = ref(0);
const storeAStatus = ref(0);
const storeBDiff = ref(0);
const storeBStatus = ref(0);
const storeCDiff = ref(0);
const storeCStatus = ref(0);
const storeDDiff = ref(0);
const storeDStatus = ref(0);
const chartMonthlyData = ref(null);
const chartMonthlyOptions = ref(null);
const doughnutData = ref(null);
const doughnutOptions = ref(null);
const pieData = ref(null);
const pieOptions = ref(null);

const chartView = ref(null);
const doughnutRef = ref(null);
const menuRef = ref(null);
const optionValue = ref('weekly');
const expensesData = ref(null);
const expensesOptions = ref(null);

const stateOptions = ref([
    { label: 'Weekly', value: 'weekly' },
    { label: 'Monthly', value: 'monthly' }
]);
const labels = computed(() => stateOptions.value.map((option) => option.label));

const setExpensesData = () => ({
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [
        {
            data: [44, 59, 32, 44, 58, 52],
            borderColor: [layoutConfig.colorScheme.value === 'dark' ? '#fff' : '#000'],
            backgroundColor: [layoutConfig.colorScheme.value === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const setExpensesOptions = () => {
    return {
        plugins: {
            legend: {
                display: false
            }
        },
        maintainAspectRatio: false,
        responsive: true,
        aspectRatio: 4,
        scales: {
            y: {
                display: false,
                beginAtZero: true
            },
            x: {
                display: false
            }
        },
        tooltips: {
            enabled: false
        },
        elements: {
            point: {
                radius: 5,
                pointBackgroundColor: [layoutConfig.colorScheme.value === 'dark' ? '#fff' : '#000']
            }
        }
    };
};

const items = ref([
    { label: 'Update', icon: 'pi pi-fw pi-refresh' },
    { label: 'Edit', icon: 'pi pi-fw pi-pencil' }
]);
const storeAData = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [55, 3, 45, 6, 44, 58, 84, 68, 64],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const storeBData = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [81, 75, 63, 100, 69, 79, 38, 37, 76],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const storeCData = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [99, 55, 22, 72, 24, 79, 35, 91, 48],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const storeDData = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [5, 51, 68, 82, 28, 21, 29, 45, 44],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const storeAOptions = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
    aspectRatio: 4,
    scales: {
        y: {
            display: false
        },
        x: {
            display: false
        }
    },
    tooltips: {
        enabled: false
    },
    elements: {
        point: {
            radius: 0
        }
    }
});
const storeBOptions = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
    aspectRatio: 4,
    scales: {
        y: {
            display: false
        },
        x: {
            display: false
        }
    },
    tooltips: {
        enabled: false
    },
    elements: {
        point: {
            radius: 0
        }
    }
});
const storeCOptions = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
    aspectRatio: 4,
    scales: {
        y: {
            display: false
        },
        x: {
            display: false
        }
    },
    tooltips: {
        enabled: false
    },
    elements: {
        point: {
            radius: 0
        }
    }
});
const storeDOptions = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
    aspectRatio: 4,
    scales: {
        y: {
            display: false
        },
        x: {
            display: false
        }
    },
    tooltips: {
        enabled: false
    },
    elements: {
        point: {
            radius: 0
        }
    }
});
const getChartMonthlyData = () => {
    const { limeColor, amberColor, orangeColor, blueColor, lightblueColor, cyanColor, tealColor, greenColor, lightgreenColor } = getColors();

    return {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: '2012',
                data: [6, 25, 97, 12, 7, 70, 42],
                borderColor: blueColor,
                backgroundColor: blueColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2013',
                data: [81, 3, 5, 11, 59, 47, 99],
                borderColor: lightblueColor,
                backgroundColor: lightblueColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2014',
                data: [68, 47, 46, 46, 61, 70, 94],
                borderColor: cyanColor,
                backgroundColor: cyanColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2015',
                data: [31, 9, 18, 76, 6, 11, 79],
                borderColor: tealColor,
                backgroundColor: tealColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2016',
                data: [85, 37, 47, 29, 2, 10, 54],
                borderColor: greenColor,
                backgroundColor: greenColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2017',
                data: [28, 48, 40, 19, 86, 27, 90],
                borderColor: lightgreenColor,
                backgroundColor: lightgreenColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2018',
                data: [89, 18, 95, 18, 97, 61, 54],
                borderColor: limeColor,
                backgroundColor: limeColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2019',
                data: [18, 36, 39, 58, 41, 50, 72],
                borderColor: amberColor,
                backgroundColor: amberColor,
                borderWidth: 2,
                fill: true
            },
            {
                label: '2020',
                data: [31, 4, 35, 74, 47, 35, 46],
                borderColor: orangeColor,
                backgroundColor: orangeColor,
                borderWidth: 2,
                fill: true
            }
        ]
    };
};

const getMonthlyChartOptions = () => {
    const textColor = getComputedStyle(document.body).getPropertyValue('--text-color') || 'rgba(0, 0, 0, 0.87)';
    const gridLinesColor = getComputedStyle(document.body).getPropertyValue('--divider-color') || 'rgba(160, 167, 181, .3)';
    const fontFamily = getComputedStyle(document.body).getPropertyValue('--font-family');
    return {
        plugins: {
            legend: {
                display: true,
                labels: {
                    font: {
                        family: fontFamily
                    },
                    color: textColor
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'x'
        },
        scales: {
            y: {
                ticks: {
                    font: {
                        family: fontFamily
                    },
                    color: textColor
                },
                grid: {
                    color: gridLinesColor
                }
            },
            x: {
                categoryPercentage: 0.9,
                barPercentage: 0.8,
                ticks: {
                    font: {
                        family: fontFamily
                    },
                    color: textColor
                },
                grid: {
                    color: gridLinesColor
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };
};

const getPieData = () => {
    const { limeColor, blueColor, tealColor } = getColors();
    const borderColor = getComputedStyle(document.body).getPropertyValue('--divider-color') || 'rgba(160, 167, 181, .3)';
    return {
        labels: ['O', 'D', 'R'],
        datasets: [
            {
                data: [300, 50, 100],
                backgroundColor: [blueColor, tealColor, limeColor],
                borderColor
            }
        ]
    };
};
const getPieOptions = () => {
    const textColor = getComputedStyle(document.body).getPropertyValue('--text-color') || 'rgba(0, 0, 0, 0.87)';
    const fontFamily = getComputedStyle(document.body).getPropertyValue('--font-family');
    return {
        responsive: true,
        aspectRatio: 1,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: fontFamily
                    },
                    color: textColor
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };
};
const getDoughnutData = () => {
    const { blueColor, lightblueColor, cyanColor, tealColor, greenColor, lightgreenColor, orangeColor } = getColors();
    const borderColor = getComputedStyle(document.body).getPropertyValue('--divider-color') || 'rgba(160, 167, 181, .3)';

    return {
        labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        datasets: [
            {
                data: [11, 29, 71, 33, 28, 95, 6],
                backgroundColor: [blueColor, lightblueColor, cyanColor, tealColor, greenColor, lightgreenColor, orangeColor],
                borderColor
            }
        ]
    };
};
const getDoughnutOptions = () => {
    const textColor = getComputedStyle(document.body).getPropertyValue('--text-color') || 'rgba(0, 0, 0, 0.87)';
    const fontFamily = getComputedStyle(document.body).getPropertyValue('--font-family');
    return {
        responsive: true,
        aspectRatio: 2,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: fontFamily
                    },
                    color: textColor
                }
            }
        },
        circumference: 180,
        rotation: -90,
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };
};
const getColors = () => {
    const isLight = true;
    return {
        pinkColor: isLight ? '#EC407A' : '#F48FB1',
        purpleColor: isLight ? '#AB47BC' : '#CE93D8',
        deeppurpleColor: isLight ? '#7E57C2' : '#B39DDB',
        indigoColor: isLight ? '#5C6BC0' : '#9FA8DA',
        blueColor: isLight ? '#42A5F5' : '#90CAF9',
        lightblueColor: isLight ? '#29B6F6' : '#81D4FA',
        cyanColor: isLight ? '#00ACC1' : '#4DD0E1',
        tealColor: isLight ? '#26A69A' : '#80CBC4',
        greenColor: isLight ? '#66BB6A' : '#A5D6A7',
        lightgreenColor: isLight ? '#9CCC65' : '#C5E1A5',
        limeColor: isLight ? '#D4E157' : '#E6EE9C',
        yellowColor: isLight ? 'FFEE58' : '#FFF59D',
        amberColor: isLight ? '#FFCA28' : '#FFE082',
        orangeColor: isLight ? '#FFA726' : '#FFCC80',
        deeporangeColor: isLight ? '#FF7043' : '#FFAB91',
        brownColor: isLight ? '#8D6E63' : '#BCAAA4'
    };
};

const containerClassesYellow = computed(() => {
    return {
        'bg-yellow-50': layoutConfig.colorScheme.value === 'light',
        'bg-yellow-800': layoutConfig.colorScheme.value === 'dark'
    };
});
const containerClassesRed = computed(() => {
    return {
        'bg-red-50': layoutConfig.colorScheme.value === 'light',
        'bg-red-800': layoutConfig.colorScheme.value === 'dark'
    };
});
const containerClassesTeal = computed(() => {
    return {
        'bg-teal-50': layoutConfig.colorScheme.value === 'light',
        'bg-teal-800': layoutConfig.colorScheme.value === 'dark'
    };
});
const containerClassesPrimary = computed(() => {
    return {
        'bg-primary-50': layoutConfig.colorScheme.value === 'light',
        'bg-primary-800': layoutConfig.colorScheme.value === 'dark'
    };
});

onMounted(async () => {
    refreshChart();
    storeInterval.value = setInterval(() => {
        requestAnimationFrame(() => {
            let { diff: newStoreADiff, totalValue: newStoreATotalValue, status: newStoreAStatus } = calculateStore(storeAData.value, storeATotalValue.value);
            storeADiff.value = newStoreADiff;
            storeATotalValue.value = newStoreATotalValue;
            storeAStatus.value = newStoreAStatus;

            let { diff: newStoreBDiff, totalValue: newStoreBTotalValue, status: newStoreBStatus } = calculateStore(storeBData.value, storeBTotalValue.value);
            storeBDiff.value = newStoreBDiff;
            storeBTotalValue.value = newStoreBTotalValue;
            storeBStatus.value = newStoreBStatus;

            let { diff: newStoreCDiff, totalValue: newStoreCTotalValue, status: newStoreCStatus } = calculateStore(storeCData.value, storeCTotalValue.value);
            storeCDiff.value = newStoreCDiff;
            storeCTotalValue.value = newStoreCTotalValue;
            storeCStatus.value = newStoreCStatus;

            let { diff: newStoreDDiff, totalValue: newStoreDTotalValue, status: newStoreDStatus } = calculateStore(storeDData.value, storeDTotalValue.value);
            storeDDiff.value = newStoreDDiff;
            storeDTotalValue.value = newStoreDTotalValue;
            storeDStatus.value = newStoreDStatus;
        });
    }, 2000);
    expensesData.value = setExpensesData();
    expensesOptions.value = setExpensesOptions();
});

watch(layoutConfig.colorScheme, () => {
    refreshChart();
    expensesData.value = setExpensesData();
    expensesOptions.value = setExpensesOptions();
});

const toggleMenu = (event) => {
    menuRef.value.toggle(event);
};

const formatCurrency = (value) => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};

const calculateStore = (storeData, totalValue) => {
    let randomNumber = +(Math.random() * 500).toFixed(2);
    let data = [...storeData.datasets[0].data];
    let length = data.length;
    data.push(randomNumber);
    data.shift();
    storeData.datasets[0].data = data;

    let diff = +(data[length - 1] - data[length - 2]).toFixed(2);
    let status = diff === 0 ? 0 : diff > 0 ? 1 : -1;
    totalValue = +(totalValue + diff).toFixed(2);

    return { diff, totalValue, status };
};
const changeMonthlyDataView = () => {
    if (chartView.value.chart.options.scales.x.stacked) {
        chartView.value.chart.options.scales.x.stacked = false;
        chartView.value.chart.options.scales.y.stacked = false;
    } else {
        chartView.value.chart.options.scales.x.stacked = true;
        chartView.value.chart.options.scales.y.stacked = true;
    }

    chartView.value.chart.update();
};

const changeDoughnutDataView = () => {
    if (doughnutRef.value.chart.config.options.circumference === 360) {
        doughnutRef.value.chart.config.options.circumference = 180;
        doughnutRef.value.chart.config.options.rotation = -1 * 90;
    } else {
        doughnutRef.value.chart.config.options.circumference = 360;
        doughnutRef.value.chart.config.options.rotation = 0;
    }

    doughnutRef.value.chart.update();
};

const refreshChart = () => {
    chartMonthlyData.value = getChartMonthlyData();
    chartMonthlyOptions.value = getMonthlyChartOptions();
    doughnutData.value = getDoughnutData();
    doughnutOptions.value = getDoughnutOptions();
    pieData.value = getPieData();
    pieOptions.value = getPieOptions();
};
</script>

<template>
    <div class="grid">
        <div class="col-12 md:col-8">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5 class="m-0">Monthly Comparison</h5>
                    <Button label="Vertical/Stacked Data" class="ml-auto" text @click="changeMonthlyDataView()"></Button>
                </div>

                <Chart ref="chartView" type="bar" :data="chartMonthlyData" :options="chartMonthlyOptions" :height="400"></Chart>
            </div>
        </div>

        <div class="col-12 md:col-4">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-2">
                    <h5 class="m-0">Insights</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menuRef" :popup="true" :model="items"></Menu>
                    </div>
                </div>
                <div class="border-bottom-1 surface-border text-sm text-color-secondary mb-2 flex align-items-center">
                    <span>November 22 - November 29</span>
                    <Button label="Semi/Full Data" class="ml-auto" text @click="changeDoughnutDataView"></Button>
                </div>
                <Chart ref="doughnutRef" type="doughnut" :data="doughnutData" :options="doughnutOptions" :height="200"></Chart>
                <div class="flex flex-column justify-content-center">
                    <div class="flex flex-row align-items-center mt-4 px-3">
                        <i class="pi pi-thumbs-up p-3 border-circle bg-green-400 text-white"></i>
                        <div class="flex flex-column ml-3">
                            <span>Best Day of the Week</span>
                            <small>Friday</small>
                        </div>
                        <span class="text-indigo-500 ml-auto">95</span>
                    </div>
                    <div class="flex flex-row align-items-center my-4 px-3">
                        <i class="pi pi-thumbs-down border-circle p-3 bg-orange-400 text-white"></i>
                        <div class="flex flex-column ml-3">
                            <span>Worst Day of the Week</span>
                            <small>Saturday</small>
                        </div>
                        <span class="text-indigo-500 ml-auto">6</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-4">
            <div class="card">
                <div class="flex justify-content-between align-items-center p-3">
                    <div>
                        <i class="pi pi-twitter text-blue-500 text-6xl"></i>
                    </div>
                    <div class="text-right flex flex-column">
                        <span class="text-4xl">44.995</span>
                        <span class="text-color-secondary mt-2">Retweets</span>
                    </div>
                </div>

                <div class="border-top-1 surface-border flex justify-content-between mt-3">
                    <div class="w-6 text-center p-3 flex flex-column border-right-1 surface-border">
                        <span class="font-medium">Target</span>
                        <span class="text-color-secondary mb-2">10.000</span>
                        <ProgressBar :value="50" :showValue="false"></ProgressBar>
                    </div>
                    <div class="w-6 text-center p-3 flex flex-column">
                        <span class="font-medium">All Time Record</span>
                        <span class="text-color-secondary mb-2">50.702</span>
                        <ProgressBar :value="24" :showValue="false"></ProgressBar>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-4">
            <div class="card">
                <div class="flex justify-content-between align-items-center p-3">
                    <div>
                        <i class="pi pi-facebook text-indigo-500 text-6xl"></i>
                    </div>
                    <div class="text-right flex flex-column">
                        <span class="text-4xl">44.995</span>
                        <span class="text-color-secondary mt-2">Facebook Interactions</span>
                    </div>
                </div>

                <div class="border-top-1 surface-border flex justify-content-between mt-3">
                    <div class="w-6 text-center p-3 flex flex-column border-right-1 surface-border">
                        <span class="font-medium">Target</span>
                        <span class="text-color-secondary mb-2">10.000</span>
                        <ProgressBar :value="23" :showValue="false"></ProgressBar>
                    </div>
                    <div class="w-6 text-center p-3 flex flex-column">
                        <span class="font-medium">All Time Record</span>
                        <span class="text-color-secondary mb-2">99.028</span>
                        <ProgressBar :value="38" :showValue="false"></ProgressBar>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-4">
            <div class="card">
                <div class="flex justify-content-between align-items-center p-3">
                    <div>
                        <i class="pi pi-github text-color text-6xl"></i>
                    </div>
                    <div class="text-right flex flex-column">
                        <span class="text-4xl">81.002</span>
                        <span class="text-color-secondary mt-2">Stars</span>
                    </div>
                </div>

                <div class="border-top-1 surface-border flex justify-content-between mt-3">
                    <div class="w-6 text-center p-3 flex flex-column border-right-1 surface-border">
                        <span class="font-medium">Target</span>
                        <span class="text-color-secondary mb-2">10.000</span>
                        <ProgressBar :value="62" :showValue="false"></ProgressBar>
                    </div>
                    <div class="w-6 text-center p-3 flex flex-column">
                        <span class="font-medium">All Time Record</span>
                        <span class="text-color-secondary mb-2">162.550</span>
                        <ProgressBar :value="14" :showValue="false"></ProgressBar>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-12">
            <div class="card grid grid-nogutter block sm:flex">
                <div class="lg:col-3 md:col-6 sm:col-12 p-0">
                    <div class="flex flex-column p-4">
                        <span class="text-color-secondary">Store A Sales</span>
                        <span class="text-2xl mt-2">
                            <i v-if="storeADiff !== 0" :class="['font-bold text-2xl pi pr-1', { 'pi-arrow-up text-green-500': storeADiff > 0, 'pi-arrow-down text-pink-500': storeADiff < 0 }]"></i>
                            ${{ storeATotalValue }}
                            <span v-if="storeADiff !== 0" :class="['font-medium text-base ml-1', { 'text-green-500': storeADiff > 0, 'text-pink-500': storeADiff < 0 }]"> {{ storeADiff > 0 ? '+' : '' }}{{ storeADiff }} </span>
                        </span>
                    </div>
                    <div class="px-4">
                        <Chart ref="storeA" type="line" :data="storeAData" :options="storeAOptions"></Chart>
                    </div>
                </div>
                <div class="lg:col-3 md:col-6 sm:col-12 p-0">
                    <div class="flex flex-column p-4">
                        <span class="text-color-secondary">Store B Sales</span>
                        <span class="text-2xl mt-2">
                            <i v-if="storeBDiff !== 0" :class="['font-bold text-2xl pi pr-1', { 'pi-arrow-up text-green-500': storeBDiff > 0, 'pi-arrow-down text-pink-500': storeBDiff < 0 }]"></i>
                            ${{ storeBTotalValue }}
                            <span v-if="storeBDiff !== 0" :class="['font-medium text-base ml-1', { 'text-green-500': storeBDiff > 0, 'text-pink-500': storeBDiff < 0 }]"> {{ storeBDiff > 0 ? '+' : '' }}{{ storeBDiff }} </span>
                        </span>
                    </div>
                    <div class="px-4">
                        <Chart ref="storeB" type="line" :data="storeBData" :options="storeBOptions"></Chart>
                    </div>
                </div>
                <div class="lg:col-3 md:col-6 sm:col-12 p-0">
                    <div class="flex flex-column p-4">
                        <span class="text-color-secondary">Store C Sales</span>
                        <span class="text-2xl mt-2">
                            <i v-if="storeCDiff !== 0" :class="['font-bold text-2xl pi pr-1', { 'pi-arrow-up text-green-500': storeCDiff > 0, 'pi-arrow-down text-pink-500': storeCDiff < 0 }]"></i>
                            ${{ storeCTotalValue }}
                            <span v-if="storeCDiff !== 0" :class="['fw-500 fs-normal ml-1', { 'text-green-500': storeCDiff > 0, 'text-pink-500': storeCDiff < 0 }]"> {{ storeCDiff > 0 ? '+' : '' }}{{ storeCDiff }} </span>
                        </span>
                    </div>
                    <div class="px-4">
                        <Chart ref="storeC" type="line" :data="storeCData" :options="storeCOptions"></Chart>
                    </div>
                </div>
                <div class="lg:col-3 md:col-6 sm:col-12 p-0">
                    <div class="sales-info flex flex-column p-4">
                        <span class="text-color-secondary">Store D Sales</span>
                        <span class="text-2xl mt-2">
                            <i v-if="storeDDiff !== 0" :class="['fw-700 fs-large pi pr-1', { 'pi-arrow-up text-green-500': storeDDiff > 0, 'pi-arrow-down text-pink-500': storeDDiff < 0 }]"></i>
                            ${{ storeDTotalValue }}
                            <span v-if="storeDDiff !== 0" :class="['fw-500 fs-normal ml-1', { 'text-green-500': storeDDiff > 0, 'text-pink-500': storeDDiff < 0 }]"> {{ storeDDiff > 0 ? '+' : '' }}{{ storeDDiff }} </span>
                        </span>
                    </div>
                    <div class="px-4">
                        <Chart ref="storeD" type="line" :data="storeDData" :options="storeDOptions"></Chart>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-6">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5 class="m-0">Top Searchs</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu12" :popup="true" :model="items"></Menu>
                    </div>
                </div>
                <div class="flex justify-content-between surface-200 p-3 border-bottom-1 surface-border">
                    <span>Mat Orange Case</span>
                    <span class="font-medium text-green-500">82% CONV RATE</span>
                </div>
                <div class="flex justify-content-between p-3 border-bottom-1 surface-border">
                    <span>Space T-Shirt</span>
                    <span class="font-medium text-green-500">78% CONV RATE</span>
                </div>
                <div class="flex justify-content-between surface-200 p-3 border-bottom-1 surface-border">
                    <span>Orange Black Hoodie</span>
                    <span class="font-medium text-green-500">61% CONV RATE</span>
                </div>
                <div class="flex justify-content-between p-3 border-bottom-1 surface-border">
                    <span>Wonders Notebook</span>
                    <span class="font-medium text-orange-500">48% CONV RATE</span>
                </div>
                <div class="flex justify-content-between surface-200 p-3 border-bottom-1 surface-border">
                    <span>Robots T-Shirt</span>
                    <span class="font-medium text-orange-500">34% CONV RATE</span>
                </div>
                <div class="flex justify-content-between p-3">
                    <span>Green Portal Sticker</span>
                    <span class="font-medium text-pink-500">11% CONV RATE</span>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-6">
            <div class="card">
                <DataTable :value="products" class="p-datatable-customers" :rows="4" style="margin-bottom: 20px" :paginator="true">
                    <Column header="Logo" headerStyle="width:20%; min-width:10rem;">
                        <template #body="slotProps">
                            <img :src="'/demo/images/product/' + slotProps.data.image" class="shadow-4" :alt="slotProps.data.image" width="50" />
                        </template>
                    </Column>
                    <Column field="name" header="Name" :sortable="true" headerStyle="width:20%; min-width:10rem;">
                        <template #body="slotProps">
                            {{ slotProps.data.name }}
                        </template>
                    </Column>
                    <Column field="category" header="Category" :sortable="true" headerStyle="width:20%; min-width:10rem;">
                        <template #body="slotProps">
                            {{ slotProps.data.category }}
                        </template>
                    </Column>
                    <Column field="price" header="Price" :sortable="true" headerStyle="width:20%; min-width:10rem;">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.price) }}
                        </template>
                    </Column>
                    <Column header="View" headerStyle="width:20%; min-width:10rem;">
                        <template #body>
                            <Button icon="pi pi-search" class="mb-1" rounded text></Button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <div class="col-12 md:col-4">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5 class="m-0">Expenses</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu13" :popup="true" :model="items"></Menu>
                    </div>
                </div>
                <div class="border-bottom-1 surface-border text-sm text-color-secondary mb-2 pb-3">November 22 - November 29</div>

                <div class="flex justify-content-between align-items-center my-2 p-2 border-bottom-1 surface-border">
                    <div class="flex flex-column">
                        <i class="pi pi-cloud text-cyan-700 text-2xl mb-2"></i>
                        <span class="font-medium mb-1">$30.247</span>
                        <span class="text-color-secondary">Cloud Infrastructure</span>
                    </div>
                    <span
                        ><a href="#" class="text-color-secondary"><i class="pi pi-chevron-right"></i></a
                    ></span>
                </div>
                <div class="flex justify-content-between align-items-center my-2 p-2 border-bottom-1 surface-border">
                    <div class="flex flex-column">
                        <i class="pi pi-tag text-cyan-700 text-2xl mb-2"></i>
                        <span class="font-medium mb-1">$29.550</span>
                        <span class="text-color-secondary">General Goods</span>
                    </div>
                    <span
                        ><a href="#" class="text-color-secondary"><i class="pi pi-chevron-right"></i></a
                    ></span>
                </div>
                <div class="flex justify-content-between align-items-center my-2 p-2 border-bottom-1 surface-border">
                    <div class="flex flex-column">
                        <i class="pi pi-desktop text-cyan-700 text-2xl mb-2"></i>
                        <span class="font-medium mb-1">$16.660</span>
                        <span class="text-color-secondary">Consumer Electronics</span>
                    </div>
                    <span
                        ><a href="#" class="text-color-secondary"><i class="pi pi-chevron-right"></i></a
                    ></span>
                </div>
                <div class="flex justify-content-between align-items-center my-2 p-2">
                    <div class="flex flex-column">
                        <i class="pi pi-compass text-cyan-700 text-2xl mb-2"></i>
                        <span class="font-medium mb-1">$5.801</span>
                        <span class="text-color-secondary">Incalculables</span>
                    </div>
                    <span
                        ><a href="#" class="text-color-secondary"><i class="pi pi-chevron-right"></i></a
                    ></span>
                </div>
            </div>
        </div>

        <div class="col-12 md:col-8">
            <div class="card h-full">
                <div class="flex flex-column gap-3">
                    <div :class="containerClassesPrimary" class="flex flex-column p-3 gap-3 w-full justify-content-between border-round-md">
                        <div class="flex justify-content-between align-items-center">
                            <h5 class="m-0 text-900 text-2xl font-medium">Expenses</h5>
                            <SelectButton :options="labels" v-model="optionValue"></SelectButton>
                        </div>
                        <div>
                            <Chart type="line" :data="expensesData" :options="expensesOptions"></Chart>
                        </div>
                    </div>
                    <div class="flex flex-column lg:flex-row gap-3 justify-content-between flex-1">
                        <div :class="containerClassesRed" class="flex flex-column p-3 w-full justify-content-between border-round-md">
                            <div class="flex justify-content-between">
                                <div class="flex flex-column gap-1">
                                    <span class="text-900 text-4xl">23</span>
                                    <span class="text-700">Product Questions</span>
                                </div>
                                <a href="#/dashboard-analytics"><i class="pi pi-arrow-up-right text-900 text-2xl"></i></a>
                            </div>
                            <div class="flex flex-column gap-2">
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/black-watch.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Black Watch</span>
                                        <span class="text-sm text-900">Is the Black Watch product water-resistant?</span>
                                    </div>
                                </div>
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/blue-t-shirt.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Blue T-Shirt</span>
                                        <span class="text-sm text-900">Can I return or exchange the blue t-shirt if I am not satisfied with it?</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div :class="containerClassesTeal" class="flex flex-column p-3 w-full justify-content-between border-round-md">
                            <div class="flex justify-content-between">
                                <div class="flex flex-column gap-1">
                                    <span class="text-900 text-4xl">54</span>
                                    <span class="text-700">Product Reviews</span>
                                </div>
                                <a href="#/dashboard-analytics"><i class="pi pi-arrow-up-right text-900 text-2xl"></i></a>
                            </div>
                            <div class="flex flex-column gap-2">
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/blue-band.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Blue Band</span>
                                        <span class="text-sm text-900">Loved the blue band from this e-commerce site!</span>
                                    </div>
                                </div>
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/bamboo-watch.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Bamboo Watch</span>
                                        <span class="text-sm text-900">I purchased the bamboo watch and I'm really happy with it.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div :class="containerClassesYellow" class="flex flex-column p-3 gap-3 bg-yellow-50 w-full justify-content-between border-round-md">
                            <div class="flex justify-content-between">
                                <div class="flex flex-column gap-1">
                                    <span class="text-900 text-4xl">99+</span>
                                    <span class="text-700">Will Shipping Orders</span>
                                </div>
                                <a href="#/dashboard-analytics"><i class="pi pi-arrow-up-right text-900 text-2xl"></i></a>
                            </div>
                            <div class="flex flex-column gap-2">
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/blue-t-shirt.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Black Tshirt</span>
                                        <span class="text-sm text-900">Last Shipping Date</span>
                                    </div>
                                </div>
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/black-watch.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Black Watch</span>
                                        <span class="text-sm text-900">Last Shipping Date</span>
                                    </div>
                                </div>
                                <div class="flex gap-2 p-2 surface-0 shadow-1 border-round-md">
                                    <img src="/demo/images/product/bamboo-watch.jpg" class="w-2rem h-2rem border-circle" />
                                    <div class="flex flex-column gap-1">
                                        <span class="text-sm font-medium text-900">Blue T-Shirt</span>
                                        <span class="text-sm text-900">Last Shipping Date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
