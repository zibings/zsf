<script setup>
import { ref, onMounted, watch } from 'vue';
import { useLayout } from '@/layout/composables/layout';

const { layoutConfig } = useLayout();
let completeTask = ref(1);
const selectedProjectID = ref(1);
const selectedTeam = ref('UX Researchers');
const basicData = ref(null);
const basicOptions = ref(null);
const filteredTeamMembers = ref([]);

const dailyTasks = ref([
    {
        id: 1,
        checked: true,
        label: 'Prepare personas',
        description: 'Create profiles of fictional users representing target audience for product or service.',
        avatar: '/demo/images/avatar/circle/avatar-f-6.png',
        borderColor: 'border-pink-500'
    },
    {
        id: 2,
        checked: false,
        label: 'Prepare a user journey map',
        description: 'Visual representation of steps a user takes to accomplish a goal within product or service.',
        avatar: '/demo/images/avatar/circle/avatar-f-7.png',
        borderColor: 'border-purple-500'
    },
    {
        id: 3,
        checked: false,
        label: 'Prepare wireframes for onboarding screen',
        description: 'Create low-fidelity mockups of onboarding screen. Include layout, hierarchy, functionality.',
        avatar: '/demo/images/avatar/circle/avatar-f-8.png',
        borderColor: 'border-blue-500'
    },
    {
        id: 4,
        checked: false,
        label: 'Review benchmarks',
        description: 'Conduct research on similar products or services to understand market standards and identify opportunities.',
        avatar: '/demo/images/avatar/circle/avatar-f-9.png',
        borderColor: 'border-green-500'
    },
    {
        id: 5,
        checked: false,
        label: 'Let a plan with UI Team',
        description: 'Collaborate with UI design team to create plan for visual design of product or service.',
        avatar: '/demo/images/avatar/circle/avatar-f-10.png',
        borderColor: 'border-yellow-500'
    }
]);
const teamMembers = ref([
    {
        avatar: '/demo/images/avatar/circle/avatar-f-1.png',
        name: 'Theresa Webb',
        title: 'UX Researchers',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-2.png',
        name: 'Courtney Henry',
        title: 'President of Sales',
        taskCount: 22,
        doneCount: 11,
        sprintCount: 3,
        onProjectsCount: 12,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-3.png',
        name: 'Kathryn Murphy',
        title: 'Web Designer',
        taskCount: 21,
        doneCount: 33,
        sprintCount: 11,
        onProjectsCount: 44,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-4.png',
        name: 'Diana Ross',
        title: 'Project Manager',
        taskCount: 34,
        doneCount: 11,
        sprintCount: 45,
        onProjectsCount: 23,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-5.png',
        name: 'Emily Smith',
        title: 'Software Engineer',
        taskCount: 22,
        doneCount: 3,
        sprintCount: 12,
        onProjectsCount: 1,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-6.png',
        name: 'Olivia Johnson',
        title: 'Human Resources Manager',
        taskCount: 54,
        doneCount: 23,
        sprintCount: 29,
        onProjectsCount: 14,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-7.png',
        name: 'Sarah Williams',
        title: 'Marketing Specialist',
        taskCount: 46,
        doneCount: 33,
        sprintCount: 12,
        onProjectsCount: 14,
        team: 'UX Researchers'
    },
    {
        avatar: '/demo/images/avatar/circle/avatar-f-8.png',
        name: 'Madison Davis',
        title: 'Graphic Designer',
        taskCount: 23,
        doneCount: 55,
        sprintCount: 31,
        onProjectsCount: 15,
        team: 'UX Researchers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-9.png',
        name: 'Abigail Rodriguez',
        title: 'Content Writer',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'UX Designers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-10.png',
        name: 'Elizabeth Taylor',
        title: 'Customer Support Representative',
        taskCount: 12,
        doneCount: 32,
        sprintCount: 14,
        onProjectsCount: 16,
        team: 'UX Designers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-11.png',
        name: 'Chloe Anderson',
        title: 'Financial Analyst',
        taskCount: 11,
        doneCount: 17,
        sprintCount: 12,
        onProjectsCount: 14,
        team: 'UI Designers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-12.png',
        name: 'Sophia Lee',
        title: 'Product Manager',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'UI Designer'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-3.png',
        name: 'Aria Jackson',
        title: 'Product Manager',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'Front-End Developers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-7.png',
        name: 'Aria Jackson',
        title: 'Product Manager',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'Front-End Developers'
    },

    {
        avatar: '/demo/images/avatar/circle/avatar-f-9.png',
        name: 'John Doe',
        title: 'Product Manager',
        taskCount: 79,
        doneCount: 15,
        sprintCount: 72,
        onProjectsCount: 33,
        team: 'Back-End Developers'
    }
]);
const projectList = ref([
    {
        id: 1,
        title: 'Ultima Sales',
        totalTasks: 50,
        completedTask: 25
    },
    {
        id: 2,
        title: 'Ultima Landing',
        totalTasks: 50,
        completedTask: 25
    },
    {
        id: 3,
        title: 'Ultima SaaS',
        totalTasks: 50,
        completedTask: 25
    },
    {
        id: 4,
        title: 'Ultima SaaS',
        totalTasks: 50,
        completedTask: 25
    }
]);
const teams = ref([
    {
        title: 'UX Researchers',
        avatar: ['/demo/images/avatar/circle/avatar-f-1.png', '/demo/images/avatar/circle/avatar-f-6.png', '/demo/images/avatar/circle/avatar-f-11.png', '/demo/images/avatar/circle/avatar-f-12.png'],
        avatarText: '+4',
        badgeClass: 'bg-pink-500'
    },
    {
        title: 'UX Designers',
        avatar: ['/demo/images/avatar/circle/avatar-f-2.png'],
        badgeClass: 'bg-blue-500'
    },
    {
        title: 'UI Designers',
        avatar: ['/demo/images/avatar/circle/avatar-f-3.png', '/demo/images/avatar/circle/avatar-f-8.png'],
        avatarText: '+1',
        badgeClass: 'bg-green-500'
    },
    {
        title: 'Front-End Developers',
        avatar: ['/demo/images/avatar/circle/avatar-f-4.png', '/demo/images/avatar/circle/avatar-f-9.png'],
        badgeClass: 'bg-yellow-500'
    },
    {
        title: 'Back-End Developers',
        avatar: ['/demo/images/avatar/circle/avatar-f-10.png'],
        badgeClass: 'bg-purple-500'
    }
]);

const setChartData = () => {
    return {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [
            {
                label: 'Previous Month',
                borderColor: '#E0E0E0',
                tension: 0.5,
                data: [22, 36, 11, 33, 2]
            },
            {
                label: 'Current Month',
                borderColor: '#6366F1',
                tension: 0.5,
                data: [22, 16, 31, 11, 38]
            }
        ]
    };
};
const setChartOptions = () => {
    const textColor = getComputedStyle(document.body).getPropertyValue('--text-color');
    const surfaceLight = getComputedStyle(document.body).getPropertyValue('--surface-100');
    return {
        plugins: {
            legend: {
                labels: {
                    color: textColor,
                    boxWidth: 12,
                    boxHeight: 4
                },
                position: 'bottom'
            }
        },
        maintainAspectRatio: false,
        elements: { point: { radius: 0 } },
        scales: {
            x: {
                ticks: {
                    color: textColor
                },
                grid: {
                    color: surfaceLight
                }
            },
            y: {
                ticks: {
                    color: textColor,
                    stepSize: 10
                },
                grid: {
                    color: surfaceLight
                }
            }
        }
    };
};

const teamFilter = (team) => {
    selectedTeam.value = team;
    filteredTeamMembers.value = teamMembers.value.filter((item) => item.team === team);
};

const changeChecked = () => {
    completeTask.value = dailyTasks.value.filter((task) => task.checked).length;
};

onMounted(() => {
    basicData.value = setChartData();
    basicOptions.value = setChartOptions();
    filteredTeamMembers.value = teamMembers.value.filter((item) => item.team === selectedTeam.value);
});

watch(layoutConfig.colorScheme, () => {
    basicOptions.value = setChartOptions();
});
</script>

<template>
    <div class="grid dashboard mt-1">
        <div class="col-12 md:col-8">
            <div class="card h-full">
                <div class="p-2 h-full flex flex-column justify-content-between">
                    <div class="flex aling-items-center justify-content-between mb-3">
                        <div class="flex gap-3 flex-column justify-content-between w-full md:flex-row md:align-items-center">
                            <div class="flex gap-3 align-items-center">
                                <div class="text-4xl">👋</div>
                                <div class="flex flex-column gap-1 text-600">
                                    <span class="text-2xl font-semibold">Hi,<span class="text-color"> Amy!</span></span>
                                    <span>Team Lead <span class="font-bold text-primary">@UX Designer</span></span>
                                </div>
                            </div>
                            <div class="flex align-items-center gap-2">
                                <Button label="Enroll a Ticket" icon="pi pi-send" outlined></Button>
                                <Button label="Upgrade Your Plan" icon="pi pi-chart-line"></Button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-column gap-2 text-primary mt-4 md:mt-0">
                        <span class="font-bold text-sm">Done in Current Month</span>
                        <div class="grid grid-nogutter font-medium">
                            <span class="col-6 text-6xl md:col-3 flex align-items-center">72 <span class="text-base ml-2">tasks</span></span>
                            <span class="col-6 text-6xl md:col-3 flex align-items-center">4 <span class="text-base ml-2">production</span></span>
                            <span class="col-6 text-6xl md:col-3 flex align-items-center">18 <span class="text-base ml-2">tests</span></span>
                            <span class="col-6 text-6xl md:col-3 flex align-items-center">13 <span class="text-base ml-2">meetings</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 md:col-4">
            <div class="h-full bg-primary flex justify-content-between align-items-center pl-5 py-3 border-round-md overflow-hidden">
                <div class="flex flex-column justify-content-center">
                    <div><span class="font-bold text-white text-sm">Carry your team on top</span></div>
                    <div>
                        <h5 class="font-bold text-white mt-1 text-4xl m-0">Upgrade Now</h5>
                    </div>
                    <div>
                        <Button icon="pi pi-database" label="See All Plans" class="bg-white mt-3" outlined></Button>
                    </div>
                </div>
                <div><img class="-mr-3" :draggable="false" src="/demo/images/dashboard/saas-card.png" alt="" /></div>
            </div>
        </div>
        <div class="col-12">
            <div class="card flex justify-content-between align-items-center">
                <div class="p-2 h-full w-full flex flex-column justify-content-between">
                    <div class="flex align-items-center justify-content-between mb-3">
                        <span class="font-semibold text-lg text-900">My Workspace</span>
                    </div>
                    <div class="grid">
                        <div class="col-12 md:col-4">
                            <div class="flex flex-column gap-3 border-round-md border-1 h-full surface-border py-4">
                                <div class="flex justify-content-between align-items-center mx-4">
                                    <div class="flex align-items-center gap-3">
                                        <div>
                                            <Knob :showValue="false" :size="36" rangeColor="#EEEEEE" readonly :max="5" v-model="completeTask" />
                                        </div>
                                        <div class="flex flex-column justify-content-between gap-1">
                                            <span class="font-bold text-900">My Daily Tasks</span>
                                            <span class="text-color-secondary text-sm"
                                                ><span class="font-bold">{{ completeTask }}</span
                                                >/5 Tasks</span
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <Button icon="pi pi-plus" label="New Task" outlined></Button>
                                    </div>
                                </div>
                                <div class="flex flex-column gap-2 h-21rem overflow-auto -mb-4">
                                    <div v-for="task in dailyTasks" :key="task.id" class="flex justify-content-between p-3 surface-50 cursor-pointer text-color-secondary border-round-md mx-4 hover:surface-card hover:shadow-2">
                                        <div class="flex gap-3">
                                            <div>
                                                <checkbox @change="changeChecked()" v-model="task.checked" :binary="true"></checkbox>
                                            </div>
                                            <div class="flex flex-column gap-2">
                                                <span class="font-medium text-sm">{{ task.label }}</span>
                                                <div class="flex gap-2 align-items-center">
                                                    <i class="pi pi-align-left text-color-secondary"></i>
                                                    <i class="pi pi-file text-color-secondary"></i>
                                                    <i class="pi pi-image text-color-secondary"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="flex align-items-end">
                                                <AvatarGroup>
                                                    <Avatar :image="task.avatar" shape="circle"></Avatar>
                                                    <Avatar label="+2" shape="circle" class="surface-200 text-color-secondary"></Avatar>
                                                </AvatarGroup>
                                            </div>
                                            <div class="flex align-items-center">
                                                <i class="pi pi-ellipsis-h text-color-secondary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 md:col-4">
                            <div class="flex flex-column gap-3 border-round-md border-1 h-full surface-border p-4">
                                <div class="flex justify-content-between align-items-center">
                                    <div class="flex align-items-center gap-3">
                                        <div>
                                            <i class="pi pi-chart-bar text-primary text-3xl"></i>
                                        </div>
                                        <div class="flex flex-column justify-content-between gap-1">
                                            <span class="font-bold text-900">My Performance</span>
                                        </div>
                                    </div>
                                    <div class="flex align-items-center gap-1">
                                        <div class="flex align-items-center justify-content-center gap-1 border-round-md p-2 bg-red-100 text-red-400">
                                            <i class="pi pi-arrow-down-right"></i>
                                            <span>-13%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-column gap-2 h-21rem -mb-4 relative">
                                    <Chart :height="300" type="line" :data="basicData" :options="basicOptions"></Chart>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 md:col-4">
                            <div class="flex flex-column gap-3 border-round-md border-1 h-full surface-border py-4">
                                <div class="flex justify-content-between align-items-center mx-4">
                                    <div class="flex align-items-center gap-3">
                                        <div>
                                            <i class="pi pi-calendar text-primary text-3xl"></i>
                                        </div>
                                        <div class="flex flex-column justify-content-between gap-1">
                                            <span class="font-bold text-900">My Calendar</span>
                                            <span class="text-color-secondary text-sm"><span class="font-bold">19</span> Events on this month</span>
                                        </div>
                                    </div>
                                    <div class="flex align-items-center gap-1">
                                        <Button icon="pi pi-filter" outlined></Button>
                                        <Button icon="pi pi-plus" outlined></Button>
                                    </div>
                                </div>
                                <div class="flex flex-column gap-2 h-21rem overflow-auto -mb-4">
                                    <div
                                        v-for="task in dailyTasks"
                                        :key="task.id"
                                        class="flex justify-content-between px-3 py-2 border-left-2 cursor-pointer mx-4 border-round-md surface-50 hover:shadow-2 hover:surface-card"
                                        :class="task.borderColor"
                                    >
                                        <div class="flex justify-content-between gap-3">
                                            <div class="flex flex-column justify-content-center gap-2">
                                                <span class="font-medium text-base text-color">{{ task.label }}</span>
                                                <span class="text-color-secondary text-sm">{{ task.description }}</span>
                                                <span class="flex align-items-center font-medium gap-1 text-color-secondary text-xs"><i class="pi pi-clock"></i> 10:30 AM</span>
                                            </div>
                                        </div>
                                        <div class="flex align-items-center">
                                            <i class="pi pi-ellipsis-h text-color-secondary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card flex justify-content-between align-items-center">
                <div class="p-2 h-full w-full flex flex-column justify-content-between">
                    <div class="flex align-items-center justify-content-between mb-3">
                        <span class="font-semibold text-lg text-900">Projects Overview</span>
                        <div class="flex align-items-center gap-2">
                            <Button label="Organize Teams" icon="pi pi-sliders-h" outlined></Button>
                            <Button label="New Project" icon="pi pi-plus-circle"></Button>
                        </div>
                    </div>
                    <div class="grid">
                        <template v-for="(project, i) in projectList" :key="project.id">
                            <div v-show="i < 2" class="col-12 md:col-4" :class="{ 'hidden md:block': i < 1 }">
                                <div
                                    @click="selectedProjectID = project.id"
                                    :class="['flex flex-column border-1 surface-border justify-content-between p-3 gap-3 cursor-pointer border-round-md', { 'bg-primary border-0': selectedProjectID === project.id }]"
                                >
                                    <div class="flex justify-content-between align-items-center">
                                        <div class="flex gap-2 font-bold text-sm align-items-center">
                                            <i class="pi pi-star-fill" :class="{ 'text-primary': selectedProjectID !== project.id }"></i>
                                            <span>{{ project.title }}</span>
                                        </div>
                                        <div class="flex gap-2 text-sm align-items-center text-xs font-bold">
                                            <div class="surface-200 p-1 px-2 flex font-medium gap-2 border-round-md" :class="{ 'bg-primary-600': selectedProjectID === project.id }">
                                                <span :class="{ 'text-color-secondary': selectedProjectID !== project.id }">25 July</span>
                                                <i class="pi pi-arrow-right" :class="{ 'text-color-secondary': selectedProjectID !== project.id }"></i>
                                                <span :class="{ 'text-color-secondary': selectedProjectID !== project.id }">25 Aug</span>
                                            </div>
                                            <i class="pi pi-ellipsis-h" :class="{ 'text-color-secondary': selectedProjectID !== project.id }"></i>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 align-items-center">
                                        <span class="text-sm" :class="{ 'text-color-secondary': selectedProjectID !== project.id }">
                                            <span class="font-bold">{{ project.completedTask }}</span
                                            >/{{ project.totalTasks }} Tasks
                                        </span>
                                        <div class="surface-200 w-full border-round-lg" :class="{ 'bg-primary-700': selectedProjectID === project.id }">
                                            <div style="height: 6px" class="surface-0 w-5 border-round-left-lg" :class="{ 'bg-primary-700': selectedProjectID !== project.id }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="col-12 md:col-4">
                            <div class="flex justify-content-between align-items-center p-3 cursor-pointer border-1 surface-border border-round-md h-full">
                                <div class="flex flex-column gap-2">
                                    <span>Other Projects</span>
                                    <Tag value="+5 Projects"></Tag>
                                </div>
                                <i class="pi pi-chevron-down"></i>
                            </div>
                        </div>
                        <div class="col-12 md:col-4">
                            <div class="border-1 surface-border p-3 border-round-md flex flex-column gap-3">
                                <div class="flex justify-content-between align-items-center">
                                    <div class="flex flex-column gap-1">
                                        <span class="font-semibold text-900 text-lg">Teams</span>
                                        <span class="text-sm text-color-secondary">18 Members</span>
                                    </div>
                                    <Button label="New Team" icon="pi pi-users"></Button>
                                </div>
                                <div class="flex flex-column gap-1">
                                    <div
                                        v-for="team in teams"
                                        :key="team.title"
                                        @click="teamFilter(team.title)"
                                        class="flex justify-content-between align-items-center border-1 border-transparent border-round-md p-3 -mx-2 cursor-pointer hover:surface-hover"
                                        :class="{
                                            'bg-primary-50 border-primary-100': selectedTeam === team.title
                                        }"
                                    >
                                        <div class="flex align-items-center gap-3">
                                            <div :style="{ width: '7px', height: '7px' }" class="border-circle" :class="team.badgeClass"></div>
                                            <span>{{ team.title }}</span>
                                        </div>
                                        <div class="flex gap-2 align-items-center">
                                            <AvatarGroup>
                                                <Avatar v-for="avatar in team.avatar" :key="avatar" :image="avatar" shape="circle"></Avatar>
                                                <Avatar v-if="team.avatarText" :label="team.avatarText" shape="circle" class="surface-200 text-color-secondary" style="color: #ffffff"></Avatar>
                                            </AvatarGroup>
                                            <i v-if="selectedTeam === team.title" class="pi pi-chevron-right text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 md:col-8">
                            <div class="border-1 surface-border border-round-md h-28rem overflow-y-auto">
                                <div v-for="member in filteredTeamMembers" :key="member.avatar" class="grid grid-nogutter align-items-center p-2 border-bottom-1 surface-border text-700 cursor-pointer hover:text-color">
                                    <div class="col-4">
                                        <div class="flex align-items-center gap-3">
                                            <Avatar size="large" shape="circle" :image="member.avatar"></Avatar>
                                            <div class="flex flex-column flex-wrap">
                                                <span class="font-medium">{{ member.name }}</span>
                                                <span class="text-sm">{{ member.title }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="flex justify-content-between gap-5 flex-1">
                                            <div class="flex flex-column" style="flex-basis: 100%">
                                                <span class="font-medium">{{ member.taskCount }}</span>
                                                <span class="text-sm">Task</span>
                                            </div>
                                            <div class="flex flex-column" style="flex-basis: 100%">
                                                <span class="font-medium">{{ member.doneCount }}</span>
                                                <span class="text-sm">Done</span>
                                            </div>
                                            <div class="flex flex-column" style="flex-basis: 100%">
                                                <span class="font-medium">{{ member.sprintCount }}</span>
                                                <span class="text-sm">Sprint</span>
                                            </div>
                                            <div class="flex flex-column" style="flex-basis: 100%">
                                                <span class="font-medium">{{ member.onProjectsCount }}</span>
                                                <span class="text-sm">On Projects</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="flex justify-content-end">
                                            <Button class="text-900" rounded text icon="pi pi-ellipsis-h"></Button>
                                        </div>
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
