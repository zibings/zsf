<script setup>
import { useLayout } from '@/layout/composables/layout';

const { layoutState } = useLayout();
</script>

<template>
    <Sidebar v-model:visible="layoutState.rightMenuActive.value" position="right" :showCloseIcon="false" :baseZIndex="1000" blockScroll>
        <div class="p-3">
            <div class="flex flex-column mb-5">
                <h6 class="pb-2 mb-2 border-bottom-1 surface-border">ONLINE MEMBERS</h6>
                <div class="flex flex-row flex-wrap gap-1">
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-1.png" alt="avatar-1" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-2.png" alt="avatar-2" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-3.png" alt="avatar-3" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-4.png" alt="avatar-4" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-5.png" alt="avatar-5" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-6.png" alt="avatar-6" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-7.png" alt="avatar-7" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-8.png" alt="avatar-8" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-9.png" alt="avatar-9" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-10.png" alt="avatar-10" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-11.png" alt="avatar-11" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-12.png" alt="avatar-12" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-13.png" alt="avatar-13" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-14.png" alt="avatar-14" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-15.png" alt="avatar-15" />
                    <img class="cursor-pointer" style="width: 32px" src="/layout/images/avatar/avatar-16.png" alt="avatar-16" />
                </div>
                <span class="mt-3"><b class="text-primary">+19</b> Costumers</span>
            </div>
            <div class="flex flex-column mb-5">
                <h6 class="pb-2 mb-2 border-bottom-1 surface-border">LATEST ACTIVITY</h6>
                <div class="flex pt-2">
                    <i class="pi pi-images align-self-start p-2 border-1 border-transparent border-circle mr-2" style="background-color: rgba(0, 0, 0, 0.12)"></i>
                    <div class="flex flex-column">
                        <span class="font-bold mb-1">New Sale</span>
                        <span class="mb-2 line-height-3">Richard Jones has purchased a blue t-shirt for $79.</span>
                        <span class="flex align-items-center">
                            <img class="mr-2" src="/layout/images/avatar/activity-1.png" alt="" />
                            <small>Emmy Adams, 21.40</small>
                        </span>
                    </div>
                </div>
                <div class="flex pt-3">
                    <i class="pi pi-images align-self-start p-2 border-1 border-transparent border-circle mr-2" style="background-color: rgba(0, 0, 0, 0.12)"></i>
                    <div class="flex flex-column">
                        <span class="font-bold mb-1">Withdrawal Initiated</span>
                        <span class="mb-2 line-height-3">Your request for withdrawal of $2500 has been initiated.</span>
                        <span class="flex align-items-center">
                            <img class="mr-2" src="/layout/images/avatar/activity-2.png" alt="avatar-2" />
                            <small>Emily Walter, 21.40</small>
                        </span>
                    </div>
                </div>
                <div class="flex pt-3">
                    <i class="pi pi-images align-self-start p-2 border-1 border-transparent border-circle mr-2" style="background-color: rgba(0, 0, 0, 0.12)"></i>
                    <div class="flex flex-column">
                        <span class="font-bold mb-1">Question Received</span>
                        <span class="mb-2 line-height-3">Jane Davis has posted a new question about your product.</span>
                        <span class="flex align-items-center">
                            <img class="mr-2" src="/layout/images/avatar/activity-3.png" alt="avatar-3" />
                            <small>Jane Davis, 21.45</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-column">
                <h6 class="pb-2 mb-2 border-bottom-1 surface-border">NEXT EVENTS</h6>
                <ul class="m-0 list-none p-0">
                    <li class="py-3 px-2 border-round-md hover:surface-hover cursor-pointer"><i class="pi pi-eye mr-3"></i>A/B Test</li>
                    <li class="py-3 px-2 border-round-md hover:surface-hover cursor-pointer"><i class="pi pi-video mr-3"></i>Video Shoot</li>
                    <li class="py-3 px-2 border-round-md hover:surface-hover cursor-pointer"><i class="pi pi-sitemap mr-3"></i>Board Meeting</li>
                    <li class="py-3 px-2 border-round-md hover:surface-hover cursor-pointer"><i class="pi pi-compass mr-3"></i>Q4 Planning</li>
                    <li class="py-3 px-2 border-round-md hover:surface-hover cursor-pointer"><i class="pi pi-palette mr-3"></i>Design Training</li>
                </ul>
            </div>
        </div>
    </Sidebar>
</template>
