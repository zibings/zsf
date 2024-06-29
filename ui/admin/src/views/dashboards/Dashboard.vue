<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import { ProductService } from '@/service/ProductService';
import { useLayout } from '@/layouts/composables/layout';

const productService = new ProductService();
const products = ref(null);
const { layoutConfig } = useLayout();
const chatInput = ref('');

const ordersOptions = ref(null);
const menu = ref(null);
const op = ref(null);

const items = ref([
    { label: 'Update', icon: 'pi pi-fw pi-refresh' },
    { label: 'Edit', icon: 'pi pi-fw pi-pencil' }
]);
const items2 = ref([
    { label: 'New', icon: 'pi pi-fw pi-plus' },
    { label: 'Edit', icon: 'pi pi-fw pi-pencil' },
    { label: 'Delete', icon: 'pi pi-fw pi-trash' }
]);
const items3 = ref([
    { label: 'View Media', icon: 'pi pi-fw pi-images' },
    { label: 'Starred Messages', icon: 'pi pi-fw pi-star' },
    { label: 'Search', icon: 'pi pi-fw pi-search' }
]);
const timelineEvents = ref([
    { status: 'Ordered', date: '15/10/2020 10:30', icon: 'pi pi-shopping-cart', color: '#E91E63', description: 'Richard Jones (C8012) has ordered a blue t-shirt for $79.' },
    { status: 'Processing', date: '15/10/2020 14:00', icon: 'pi pi-cog', color: '#FB8C00', description: 'Order #99207 has processed succesfully.' },
    { status: 'Shipped', date: '15/10/2020 16:15', icon: 'pi pi-compass', color: '#673AB7', description: 'Order #99207 has shipped with shipping code 2222302090.' },
    { status: 'Delivered', date: '16/10/2020 10:00', icon: 'pi pi-check-square', color: '#0097A7', description: 'Richard Jones (C8012) has recieved his blue t-shirt.' }
]);
const chatMessages = ref([
    { from: 'Ioni Bowcher', url: '/demo/images/avatar/ionibowcher.png', messages: ['Hey M. hope you are well.', 'Our idea is accepted by the board. Now it’s time to execute it'] },
    { messages: ['We did it! 🤠'] },
    { from: 'Ioni Bowcher', url: '/demo/images/avatar/ionibowcher.png', messages: ["That's really good!"] },
    { messages: ['But it’s important to ship MVP ASAP'] },
    { from: 'Ioni Bowcher', url: '/demo/images/avatar/ionibowcher.png', messages: ['I’ll be looking at the process then, just to be sure 🤓'] },
    { messages: ['That’s awesome. Thanks!'] }
]);
const chatEmojis = ref([
    '😀',
    '😃',
    '😄',
    '😁',
    '😆',
    '😅',
    '😂',
    '🤣',
    '😇',
    '😉',
    '😊',
    '🙂',
    '🙃',
    '😋',
    '😌',
    '😍',
    '🥰',
    '😘',
    '😗',
    '😙',
    '😚',
    '🤪',
    '😜',
    '😝',
    '😛',
    '🤑',
    '😎',
    '🤓',
    '🧐',
    '🤠',
    '🥳',
    '🤗',
    '🤡',
    '😏',
    '😶',
    '😐',
    '😑',
    '😒',
    '🙄',
    '🤨',
    '🤔',
    '🤫',
    '🤭',
    '🤥',
    '😳',
    '😞',
    '😟',
    '😠',
    '😡',
    '🤬',
    '😔',
    '😟',
    '😠',
    '😡',
    '🤬',
    '😔',
    '😕',
    '🙁',
    '😬',
    '🥺',
    '😣',
    '😖',
    '😫',
    '😩',
    '🥱',
    '😤',
    '😮',
    '😱',
    '😨',
    '😰',
    '😯',
    '😦',
    '😧',
    '😢',
    '😥',
    '😪',
    '🤤'
]);
const overviewChartData1 = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [50, 64, 32, 24, 18, 27, 20, 36, 30],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const overviewChartData2 = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [11, 30, 52, 35, 39, 20, 14, 18, 29],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const overviewChartData3 = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [20, 29, 39, 36, 45, 24, 28, 20, 15],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const overviewChartData4 = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            data: [30, 39, 50, 21, 33, 18, 10, 24, 20],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});
const overviewChartOptions1 = ref({
    plugins: {
        legend: {
            display: false
        }
    },
    responsive: true,
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
const overviewChartOptions2 = ref({
    plugins: {
        legend: {
            display: false
        }
    },
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
const overviewChartOptions3 = ref({
    plugins: {
        legend: {
            display: false
        }
    },
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
const overviewChartOptions4 = ref({
    plugins: {
        legend: {
            display: false
        }
    },
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
const ordersChart = ref({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
    datasets: [
        {
            label: 'New Orders',
            data: [31, 83, 69, 29, 62, 25, 59, 26, 46],
            borderColor: ['#4DD0E1'],
            backgroundColor: ['rgba(77, 208, 225, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        },
        {
            label: 'Completed Orders',
            data: [67, 98, 27, 88, 38, 3, 22, 60, 56],
            borderColor: ['#3F51B5'],
            backgroundColor: ['rgba(63, 81, 181, 0.8)'],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }
    ]
});

onMounted(async () => {
    const data = await productService.getProducts();
    products.value = data;
    refreshChart();
});

watch(
    layoutConfig.colorScheme,
    () => {
        refreshChart();
    },
    { immediate: true }
);
function formatCurrency(value) {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
}

function toggleMenu(event) {
    menu.value.toggle(event);
}

function onEmojiOverlayPanel(chatInput, emoji) {
    op.value.hide();
    onEmojiClick(chatInput, emoji);
}

function toggleEmojis(event) {
    op.value.toggle(event);
}

function onEmojiClick(emoji) {
    chatInput.value += emoji;
}

const onChatKeydown = async (event) => {
    if (event.key === 'Enter') {
        const message = event.target.value;
        const lastMessage = chatMessages.value[chatMessages.value.length - 1];

        if (lastMessage.from) {
            chatMessages.value.push({
                self: true,
                from: 'Jerome Bell',
                url: '/demo/images/avatar/ivanmagalhaes.png',
                messages: [message]
            });
        } else {
            lastMessage.messages.push(message);
        }

        if (message.match(/primeng|primereact|primefaces|primevue/i)) {
            chatMessages.value.push({
                nth: false,
                from: 'Ioni Bowcher',
                url: '/demo/images/avatar/ionibowcher.png',
                messages: ['Always bet on Prime!']
            });
        }

        event.target.value = '';

        await nextTick();
        const chatContainer = document.querySelector('.chat-container');
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: 'smooth'
        });
    }
};

function refreshChart() {
    ordersOptions.value = getOrdersOptions();
    setOverviewColors();
}
function getOrdersOptions() {
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
        maintainAspectRatio: false,
        responsive: true,
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
        }
    };
}
function setOverviewColors() {
    const { pinkBorderColor, pinkBgColor, tealBorderColor, tealBgColor } = getOverviewColors();

    overviewChartData1.value.datasets[0].borderColor[0] = tealBorderColor;
    overviewChartData1.value.datasets[0].backgroundColor[0] = tealBgColor;

    overviewChartData2.value.datasets[0].borderColor[0] = tealBorderColor;
    overviewChartData2.value.datasets[0].backgroundColor[0] = tealBgColor;

    overviewChartData3.value.datasets[0].borderColor[0] = pinkBorderColor;
    overviewChartData3.value.datasets[0].backgroundColor[0] = pinkBgColor;

    overviewChartData4.value.datasets[0].borderColor[0] = tealBorderColor;
    overviewChartData4.value.datasets[0].backgroundColor[0] = tealBgColor;
}
function getOverviewColors() {
    const isLight = true;
    return {
        pinkBorderColor: isLight ? '#E91E63' : '#EC407A',
        pinkBgColor: isLight ? '#F48FB1' : '#F8BBD0',
        tealBorderColor: isLight ? '#009688' : '#26A69A',
        tealBgColor: isLight ? '#80CBC4' : '#B2DFDB',
        whiteBorderColor: isLight ? '#ffffff' : '#ffffff',
        whiteBgColor: isLight ? 'rgba(255,255,255,.35)' : 'rgba(255,255,255,.35)'
    };
}
</script>

<template>
    <div class="grid">
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-shopping-cart text-color"></i>
                    <h6 class="m-0 text-color pl-2">Orders</h6>
                    <div class="ml-auto">
                        <Button icon="pi pi-ellipsis-h" rounded text @click="toggleMenu"></Button>
                        <Menu ref="menu" :popup="true" :model="items"></Menu>
                    </div>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 80px">
                        <span class="mb-1 text-4xl">640</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">1420 Completed</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="overviewChartData1" :options="overviewChartOptions1" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-dollar text-color"></i>
                    <h6 class="m-0 text-color pl-2">Revenue</h6>
                    <div class="ml-auto">
                        <Button icon="pi pi-ellipsis-h" rounded text @click="toggleMenu"></Button>
                        <Menu ref="menu2" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 80px">
                        <span class="mb-1 text-4xl">$57K</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">$9,640 Income</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="overviewChartData2" :options="overviewChartOptions2" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-users text-color"></i>
                    <h6 class="m-0 text-color pl-2">Customers</h6>
                    <div class="ml-auto">
                        <Button icon="pi pi-ellipsis-h" rounded text @click="toggleMenu"></Button>
                        <Menu ref="menu3" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 80px">
                        <span class="mb-1 text-4xl">8572</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-pink-500 text-sm">25402 Registered</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="overviewChartData3" :options="overviewChartOptions3" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-comments text-color"></i>
                    <h6 class="m-0 text-color pl-2">Comments</h6>
                    <div class="ml-auto">
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu4" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 80px">
                        <span class="mb-1 text-4xl">805</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">85 Responded</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="overviewChartData4" :options="overviewChartOptions4" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 lg:col-6">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5 class="m-0">Contact</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu5" :popup="true" :model="items2"> </Menu>
                    </div>
                </div>
                <ul class="p-0 m-0 border-none list-none">
                    <li class="flex align-items-center py-3">
                        <div class="flex align-items-center">
                            <img src="/demo/images/avatar/xuxuefeng.png" />
                            <div class="ml-2">
                                <div>Xuxue Feng</div>
                                <small class="text-color-secondary">feng@ultima.org</small>
                            </div>
                        </div>
                        <span class="bg-indigo-500 p-1 font-medium text-white border-round-xs text-sm ml-auto">Accounting</span>
                        <span class="bg-orange-500 p-1 text-sm font-medium text-white border-round-xs ml-2">Sales</span>
                    </li>

                    <li class="flex align-items-center py-3">
                        <div class="flex align-items-center">
                            <img src="/demo/images/avatar/elwinsharvill.png" />
                            <div class="ml-2">
                                <div>Elwin Sharvill</div>
                                <small class="text-color-secondary">sharvill@ultima.org</small>
                            </div>
                        </div>
                        <span class="bg-teal-500 p-1 text-sm font-medium text-white border-round-xs ml-auto">Finance</span>
                        <span class="bg-orange-500 p-1 text-sm font-medium text-white border-round-xs ml-2">Sales</span>
                    </li>

                    <li class="flex align-items-center py-3">
                        <div class="flex align-items-center">
                            <img src="/demo/images/avatar/asiyajavayant.png" />
                            <div class="ml-2">
                                <div>Anna Fali</div>
                                <small class="text-color-secondary">fali@ultima.org</small>
                            </div>
                        </div>
                        <span class="bg-pink-500 p-1 text-sm font-medium text-white border-round-xs ml-auto">Management</span>
                    </li>

                    <li class="flex align-items-center py-3">
                        <div class="flex align-items-center">
                            <img src="/demo/images/avatar/ivanmagalhaes.png" />
                            <div class="ml-2">
                                <div>Jon Stone</div>
                                <small class="text-color-secondary">stone@ultima.org</small>
                            </div>
                        </div>
                        <span class="bg-pink-500 p-1 text-sm font-medium text-white border-round-xs ml-auto">Management</span>
                        <span class="bg-teal-500 p-1 text-sm font-medium text-white border-round-xs ml-2">Finance</span>
                    </li>

                    <li class="flex align-items-center py-3">
                        <div class="flex align-items-center">
                            <img src="/demo/images/avatar/stephenshaw.png" />
                            <div class="ml-2">
                                <div>Stephen Shaw</div>
                                <small class="text-color-secondary">shaw@ultima.org</small>
                            </div>
                        </div>
                        <span class="bg-teal-500 p-1 text-sm font-medium text-white border-round-xs ml-auto">Finance</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 lg:col-6">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5>Order Graph</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu6" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <Chart type="line" :data="ordersChart" :options="ordersOptions" :height="375" :width="300"></Chart>
            </div>
        </div>

        <div class="col-12 lg:col-6">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5>Timeline</h5>
                    <div>
                        <Button icon=" pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu7" :popup="true" :model="items"> </Menu>
                    </div>
                </div>

                <Timeline :value="timelineEvents" align="left" class="customized-timeline">
                    <template #marker="slotProps">
                        <span class="custom-round-md shadow-2 p-2" :style="{ backgroundColor: slotProps.item.color }">
                            <i class="text-white" :class="slotProps.item.icon"></i>
                        </span>
                    </template>
                    <template #content="slotProps">
                        <Card class="mb-3">
                            <template #title>
                                {{ slotProps.item.status }}
                            </template>
                            <template #subtitle>
                                {{ slotProps.item.date }}
                            </template>
                            <template #content>
                                <img v-if="slotProps.item.image" :src="'assets/showcase/images/demo/product/' + slotProps.item.image" :alt="slotProps.item.name" width="200" class="shadow-2" />
                                <p>{{ slotProps.item.description }}</p>
                            </template>
                        </Card>
                    </template>
                </Timeline>
            </div>
        </div>

        <div class="col-12 md:col-12 lg:col-6">
            <div class="card h-full">
                <DataTable :value="products" :paginator="true" :rows="8" :style="{ minWidth: '40rem' }">
                    <Column header="Logo" class="w-5rem" bodyStyle="height:5rem;">
                        <template #body="slotProps">
                            <img :src="'/demo/images/product/' + slotProps.data.image" class="shadow-4 w-3rem" :alt="slotProps.data.image" />
                        </template>
                    </Column>
                    <Column field="name" header="Name" :sortable="true" headerStyle=" min-width:14rem;">
                        <template #body="slotProps">
                            {{ slotProps.data.name }}
                        </template>
                    </Column>
                    <Column field="category" header="Category" :sortable="true" headerStyle="min-width:8rem;">
                        <template #body="slotProps">
                            {{ slotProps.data.category }}
                        </template>
                    </Column>
                    <Column field="price" header="Price" :sortable="true" headerStyle="min-width:8rem;">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.price) }}
                        </template>
                    </Column>
                    <Column header="View" class="w-5rem">
                        <template #body>
                            <Button icon="pi pi-search" class="mb-1" rounded text></Button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
        <div class="col-12 lg:col-6">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5>Chat</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" class="mb-1 mr-2" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu8" :popup="true" :model="items3"> </Menu>
                    </div>
                </div>
                <div>
                    <ul class="chat-container m-0 px-3 pt-3 pb-0 border-none list-none h-30rem overflow-y-auto outline-none" ref="chatcontainer">
                        <li
                            v-for="(chartMessage, index) in chatMessages"
                            :key="index"
                            class="flex align-items-start"
                            :class="{ from: !!chartMessage.from, 'text-right justify-content-end': !chartMessage.from, 'mb-3': index !== chatMessages.length - 1, 'mb-1': index === chatMessages.length - 1 }"
                        >
                            <img v-if="!!chartMessage.url" :src="chartMessage.url" alt="avatar" :width="32" class="mr-2" />
                            <div
                                class="flex flex-column"
                                :class="{
                                    'align-items-start': !!chartMessage.from,
                                    'align-items-end': !chartMessage.from
                                }"
                            >
                                <span
                                    style="word-break: break-word"
                                    v-for="(message, j) in chartMessage.messages"
                                    :key="j"
                                    class="p-3 border-round-3xl text-white"
                                    :class="{
                                        'bg-cyan-500': !!chartMessage.from,
                                        'bg-pink-500': !chartMessage.from,
                                        'mt-1': j !== 0
                                    }"
                                >
                                    {{ message }}
                                </span>
                            </div>
                        </li>
                    </ul>
                    <InputGroup class="mt-3">
                        <InputGroupAddon class="p-0 overflow-hidden border-round-3xl border-noround-right">
                            <Button icon="pi pi-plus-circle" class="h-full" text plain></Button>
                        </InputGroupAddon>
                        <InputText ref="input" v-model="chatInput" placeholder="Write your message (Hint: 'PrimeVue')" @keydown="onChatKeydown($event)" />
                        <InputGroupAddon class="p-0 overflow-hidden">
                            <Button icon="pi pi-video" class="h-full" text plain></Button>
                        </InputGroupAddon>
                        <InputGroupAddon class="p-0 overflow-hidden border-round-3xl border-noround-left">
                            <Button icon="pi pi-clock" @click="toggleEmojis" class="h-full" text plain></Button>
                            <OverlayPanel ref="op" class="emoji" style="width: 45em">
                                <Button type="button" v-for="emoji in chatEmojis" :key="emoji" @click="onEmojiOverlayPanel(emoji)" :label="emoji" class="emoji-button p-2" text plain></Button>
                            </OverlayPanel>
                        </InputGroupAddon>
                    </InputGroup>
                </div>
            </div>
        </div>

        <div class="col-12 lg:col-3">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5>Activity</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu9" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <ul class="widget-activity p-0 list-none">
                    <li class="py-3 px-0 border-bottom-1 surface-border">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Income</div>
                            <div class="text-sm text-color-secondary mb-2">30 November, 16.20</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-yellow-500 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                    <li class="py-3 px-0 border-bottom-1 surface-border">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Tax</div>
                            <div class="text-sm text-color-secondary mb-2">1 December, 15.27</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-pink-500 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                    <li class="py-3 px-0 border-bottom-1 surface-border">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Invoices</div>
                            <div class="text-sm text-color-secondary mb-2">1 December, 15.28</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-cyan-600 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                    <li class="py-3 px-0 border-bottom-1 surface-border">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Expanses</div>
                            <div class="text-sm text-color-secondary mb-2">3 December, 09.15</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-cyan-600 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                    <li class="py-3 px-0 border-bottom-1 surface-border">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Bonus</div>
                            <div class="text-sm text-color-secondary mb-2">1 December, 23.55</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-cyan-600 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                    <li class="py-3 px-0">
                        <div class="activity-item flex flex-column">
                            <div class="font-medium mb-1">Revenue</div>
                            <div class="text-sm text-color-secondary mb-2">30 November, 16.20</div>
                            <div class="surface-50" style="height: 6px">
                                <div class="bg-pink-500 w-6 h-full border-round-lg"></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 lg:col-3">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <h5>Best Sellers</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-h" rounded text plain @click="toggleMenu"></Button>
                        <Menu ref="menu10" :popup="true" :model="items"> </Menu>
                    </div>
                </div>
                <ul class="m-0 p-0 border-none outline-none list-none">
                    <li class="py-3 px-0">
                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/blue-band.jpg" alt="product" />
                            <span>Blue Band</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/bracelet.jpg" alt="product" />
                            <span>Bracelet</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/black-watch.jpg" alt="product" />
                            <span>Black Watch</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/bamboo-watch.jpg" alt="product" />
                            <span>Bamboo Watch</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/blue-t-shirt.jpg" alt="product" />
                            <span>Blue T-Shirt</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/game-controller.jpg" alt="product" />
                            <span>Game Controller</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>

                        <div class="border-round-sm h-4rem surface-200 transition-transform transition-duration-200 flex align-items-center p-3 mb-2 cursor-pointer hover:surface-100">
                            <img width="32px" height="32px" class="border-circle mr-3" src="/demo/images/product/gold-phone-case.jpg" alt="product" />
                            <span>Phone Case</span>
                            <span class="ml-auto"
                                ><a href="#"><i class="pi pi-chevron-right text-color-secondary"></i></a
                            ></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
::v-deep(.customized-timeline) {
    .p-timeline-event:nth-child(even) {
        flex-direction: row !important;

        .p-timeline-event-content {
            text-align: left !important;
        }
    }

    .p-timeline-event-opposite {
        flex: 0;
    }

    .p-card {
        margin-top: 1rem;
    }
}
</style>
