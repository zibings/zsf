<script setup>
import { ref, onBeforeMount } from 'vue';
import { usePrimeVue } from 'primevue/config';
import { useLayout } from '@/layouts/composables/layout';

const $primevue = usePrimeVue();

defineExpose({
    $primevue
});

const { changeColorScheme } = useLayout();
const parallaxBody = ref(null);
const collaboration = ref(null);
const events = ref(null);
const video = ref(null);
const features = ref(null);

onBeforeMount(() => {
    changeColorScheme('dark');
});

const scrollBehavior = (el) => {
    el.scrollIntoView({ behavior: 'smooth' });
};

const scrollToElement = ($element) => {
    setTimeout(() => {
        $element.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
    }, 200);
};
const handleMouseEnter = (event) => {
    event.target.style.background = 'linear-gradient(110.43deg, rgba(134,140,208,.5) 0.04%, rgba(255,87,89,.5) 100.11%)';
};

const handleMouseLeave = (event) => {
    event.target.style.background = 'unset';
};
</script>

<template>
    <div class="surface-ground">
        <div style="height: 68px; backdrop-filter: blur(17px)" class="flex justify-content-between align-items-center z-3 px-5 bg-black-alpha-40 top-0 w-full fixed">
            <div>
                <img :draggable="false" src="/layout/images/logo/logo.png" alt="" />
            </div>

            <a v-ripple class="p-ripple cursor-pointer block lg:hidden text-white" v-styleclass="{ selector: '@next', hideOnOutsideClick: true, enterClass: 'hidden', leaveToClass: 'hidden' }">
                <i class="pi pi-bars text-4xl"></i>
            </a>

            <div id="menu" class="align-items-center flex-grow-1 hidden lg:flex absolute lg:static w-full lg:px-0 z-3 shadow-2 lg:shadow-none fadein" style="top: 68px; right: 0%">
                <ul class="list-none p-3 lg:p-0 m-0 ml-auto flex lg:align-items-center select-none flex-column lg:flex-row cursor-pointer surface-0 lg:bg-transparent">
                    <li>
                        <a class="flex m-0 md:ml-5 px-0 py-3 text-900 line-height-3" v-styleclass="{ selector: '#menu', enterClass: 'hidden', leaveToClass: 'hidden' }">
                            <span>HOME</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex m-0 md:ml-5 px-0 py-3 text-900 line-height-3" @click="scrollToElement(features)" v-styleclass="{ selector: '#menu', enterClass: 'hidden', leaveToClass: 'hidden' }">
                            <span>FEATURES</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex m-0 md:ml-5 px-0 py-3 text-900 line-height-3" @click="scrollToElement(collaboration)" v-styleclass="{ selector: '#menu', enterClass: 'hidden', leaveToClass: 'hidden' }">
                            <span>COLLABORATION</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex m-0 md:ml-5 px-0 py-3 text-900 line-height-3" @click="scrollToElement(events)" v-styleclass="{ selector: '#menu', enterClass: 'hidden', leaveToClass: 'hidden' }">
                            <span>EVENTS</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex m-0 md:ml-5 px-0 py-3 text-900 line-height-3" @click="scrollToElement(video)" v-styleclass="{ selector: '#menu', enterClass: 'hidden', leaveToClass: 'hidden' }">
                            <span>VIDEO</span>
                        </a>
                    </li>
                    <li>
                        <Button label="LET'S TRY" class="surface-900 text-0 m-0 mt-3 md:mt-0 md:ml-5"></Button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="h-screen">
            <div style="perspective: 101px" class="h-screen overflow-x-hidden overflow-y-auto absolute top-0 left-0 right-0 bottom-0">
                <div class="hidden lg:block absolute top-0 right-0 bottom-0 left-0 parallax__layer__0">
                    <img :draggable="false" src="/demo/images/landing/layer-0-opt.jpg" class="w-full h-full block absolute bottom-0" />
                </div>
                <div class="hidden lg:block absolute top-0 right-0 bottom-0 left-0 parallax__layer__1">
                    <img :draggable="false" src="/demo/images/landing/layer-1-opt.png" class="w-full block absolute bottom-0" />
                </div>

                <div class="hidden lg:flex absolute right-0 bottom-0 left-0 parallax__layer__3 justify-content-center" style="top: 27%">
                    <div>
                        <h1 class="m-0 -mb-4 text-4xl text-white">LET'S</h1>
                        <h1 class="m-0 text-white" style="font-size: 15rem">EXPLORE</h1>
                        <div class="flex -mt-4 justify-content-end">
                            <h1 class="m-0 text-white text-4xl">TO ULTIMA</h1>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block absolute top-0 right-0 bottom-0 left-0 parallax__layer__2">
                    <img :draggable="false" src="/demo/images/landing/layer-2-opt.png" class="w-full block absolute bottom-0" />
                </div>

                <div class="absolute flex justify-content-center align-items-center w-full h-full parallax__layer__3" style="top: 112px">
                    <Button
                        label="GET STARTED"
                        @click="scrollBehavior(features)"
                        style="backdrop-filter: blur(7px)"
                        class="text-900 bg-white-alpha-20 text-lg px-5 py-3 font-semibold hover:bg-white hover:text-0 border-round-xl border-white-alpha-30 border-1"
                        text
                    ></Button>
                </div>

                <div class="block lg:hidden h-28rem bg-center bg-no-repeat bg-cover" style="top: 68px; background: url(/demo/images/landing/landing-hero-image.jpg)">
                    <div class="flex justify-content-center align-items-center h-full">
                        <div class="flex flex-column">
                            <h1 class="m-0 text-base text-white">LET'S</h1>
                            <h1 class="m-0 text-white">EXPLORE</h1>
                            <div class="flex justify-content-end">
                                <h1 class="m-0 text-white text-sm">TO ULTIMA</h1>
                            </div>
                            <div class="flex justify-content-center mt-2">
                                <Button
                                    label="GET STARTED"
                                    @click="scrollBehavior(parallaxBody)"
                                    style="backdrop-filter: blur(7px)"
                                    class="text-900 text-xs bg-white-alpha-20 font-semibold hover:bg-white hover:text-0 border-white-alpha-30 border-1"
                                    text
                                ></Button>
                            </div>
                        </div>
                    </div>
                </div>
                <div ref="parallaxBody" class="lg:top-100 block absolute left-0 right-0 h-full z-2">
                    <div>
                        <div class="grid grid-nogutter p-2 lg:p-5" style="background-color: #000">
                            <div class="col-6 lg:col-3 flex align-items-center justify-content-center gap-3">
                                <img :draggable="false" src="/demo/images/landing/landing-logo1.png" alt="" />
                            </div>
                            <div class="col-6 lg:col-3 flex align-items-center justify-content-center gap-3">
                                <img :draggable="false" src="/demo/images/landing/landing-logo2.png" alt="" />
                            </div>
                            <div class="col-6 lg:col-3 flex align-items-center justify-content-center gap-3">
                                <img :draggable="false" src="/demo/images/landing/landing-logo3.png" alt="" />
                            </div>
                            <div class="col-6 lg:col-3 flex align-items-center justify-content-center gap-3">
                                <img :draggable="false" src="/demo/images/landing/landing-logo4.png" alt="" />
                            </div>
                        </div>
                    </div>
                    <div ref="features" class="pt-5" style="background-color: #000">
                        <div ref="parallaxBody" class="flex flex-column align-items-center gap-3">
                            <div class="surface-500 text-white text-sm p-2 border-round-lg">FEATURES</div>
                            <span class="text-white text-2xl uppercase">All you need is the Ultima.</span>
                            <h1 class="m-0 text-6xl" style="background: linear-gradient(110.43deg, #868cd0 0.04%, #ff5759 100.11%); background-clip: text; -webkit-background-clip: text; color: transparent">ULTIMA</h1>
                        </div>
                        <div class="flex flex-column align-items-center">
                            <div class="grid pt-6 p-4" style="max-width: 1200px">
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl p-5 cursor-pointer h-full">
                                        <i class="pi pi-palette text-4xl text-white"></i>
                                        <span class="font-semibold text-white">Creative Design</span>
                                        <span class="text-white">Unleash your brand's full potential with our creative design services.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl p-5 cursor-pointer h-full">
                                        <i class="pi pi-mobile text-4xl text-white"></i>
                                        <span class="font-semibold text-white">Responsive Design</span>
                                        <span class="text-white">Make sure your website looks great and functions seamlessly on any device with responsive design.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl p-5 cursor-pointer h-full">
                                        <i class="pi pi-sync text-4xl text-white"></i>
                                        <span class="font-semibold text-white">Cross Browser Support</span>
                                        <span class="text-white">Don't let browser compatibility hold you back. Our cross browser support ensures your website works perfectly on all browsers.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl p-5 cursor-pointer h-full">
                                        <i class="pi pi-th-large text-4xl text-white"></i>
                                        <span class="font-semibold text-white">Well Organized</span>
                                        <span class="text-white">Our well-organized approach guarantees a smooth design process from start to finish.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl text-white p-5 cursor-pointer h-full">
                                        <i class="pi pi-code text-4xl text-white"></i>
                                        <span class="font-semibold text-white">Easy to Product</span>
                                        <span class="text-white">We make product creation easy with straightforward and intuitive processes.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                                <div class="col-6 lg:col-4">
                                    <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" class="flex justify-content-center flex-column gap-3 border-1 border-50 border-round-2xl text-white p-5 cursor-pointer h-full">
                                        <i class="pi pi-box text-4xl"></i>
                                        <span class="font-semibold">Top Notch Quality</span>
                                        <span>Expect top notch quality in all of our work, delivering exceptional results every time.</span>
                                        <span class="text-primary-300 font-medium flex align-items-center">Learn more <i class="pi pi-arrow-right ml-2"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div ref="collaboration" style="max-width: 1200px" class="flex flex-column lg:flex-row gap-8 mt-6 py-6 px-4 w-full">
                                <div class="flex flex-column align-items-end justify-content-center">
                                    <div class="surface-50 text-white text-sm p-2 border-round-lg mb-4">PLAY AS A TEAM</div>
                                    <div>
                                        <h1 class="m-0 text-6xl font-bold text-right" style="background: linear-gradient(110.43deg, #868cd0 0.04%, #ff5759 100.11%); background-clip: text; -webkit-background-clip: text; color: transparent">
                                            COLLABORATION
                                        </h1>
                                        <p class="text-white text-right mt-3">
                                            The Earth is a very small stage in a vast cosmic arena. Think of the rivers of blood spilled by all those generals and emperors so that, in glory and triumph, they could become the momentary masters of a
                                            fraction of a dot.
                                        </p>
                                    </div>
                                </div>
                                <img :draggable="false" src="/demo/images/landing/collaboration-image.png" alt="" />
                            </div>
                            <div ref="events" style="max-width: 1200px" class="flex flex-column lg:flex-row gap-8 mt-6 py-6 px-4 w-full">
                                <img :draggable="false" src="/demo/images/landing/easyfollow-image.png" alt="" />
                                <div class="flex flex-column align-items-start justify-content-center">
                                    <div class="surface-50 text-white text-sm p-2 border-round-lg mb-4">EVENTS</div>
                                    <div>
                                        <h1 class="m-0 text-6xl font-bold" style="background: linear-gradient(110.43deg, #868cd0 0.04%, #ff5759 100.11%); background-clip: text; -webkit-background-clip: text; color: transparent">EASY FOLLOW</h1>
                                        <p class="text-white mt-2">
                                            It has been said that astronomy is a humbling and character-building experience. There is perhaps no better demonstration of the folly of human conceits than this distant image of our tiny world.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div ref="video" style="max-width: 1200px" class="flex gap-4 mt-6 py-6 flex-column align-items-center w-full">
                                <div class="surface-50 text-white text-sm p-2 border-round-lg">VIDEO</div>
                                <h1 class="m-0 text-6xl font-bold" style="background: linear-gradient(110.43deg, #868cd0 0.04%, #ff5759 100.11%); background-clip: text; -webkit-background-clip: text; color: transparent">SHOWREELS</h1>
                                <div class="mt-3 relative">
                                    <iframe class="border-none border-round-lg z-3 max-w-full" width="600" height="400" src="https://www.youtube.com/embed/tgbNymZ7vqY"> </iframe>
                                    <div class="absolute z-0" style="top: -50px; left: -50px"><img :draggable="false" class="opacity-40" src="/demo/images/landing/landing-ellipse.png" alt="" /></div>
                                    <div class="absolute z-0" style="bottom: -50px; right: -50px; filter: blur(4px)"><img :draggable="false" class="opacity-40" src="/demo/images/landing/landing-ellipse2.png" alt="" /></div>
                                    <div class="absolute z-0" style="bottom: -50px; right: 10px; filter: blur(4px)"><img :draggable="false" class="opacity-40" src="/demo/images/landing/landing-ellipse2.png" alt="" /></div>
                                </div>
                            </div>
                            <div class="py-6 px-4 mt-6 w-full flex justify-content-center">
                                <div
                                    class="p-5 flex flex-column gap-3 align-items-start justify-content-between lg:flex-row lg:align-items-center border-round-2xl w-full"
                                    style="background: linear-gradient(110.43deg, #868cd0 0.04%, #ff5759 100.11%); max-width: 1200px"
                                >
                                    <div class="flex flex-column gap-2">
                                        <h1 class="m-0 text-white font-bold text-2xl">NEWSLETTER</h1>
                                        <p class="m-0 text-white">Sign up for our newsletter and stay up-to-date on the latest features and updates for our platform.</p>
                                    </div>
                                    <div class="flex align-items-center gap-2">
                                        <InputText placeholder="Email Address" class="bg-transparent border-white border-round-md" />
                                        <Button class="text-white border-1 border-white border-round-md h-full" text label="JOIN"></Button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-4" style="max-width: 1200px">
                                <div class="grid gap-3 lg:gap-0 lg:grid-nogutter pb-6">
                                    <div class="col-12 lg:col-3">
                                        <div class="w-full text-white flex flex-column gap-3">
                                            <h1 class="m-0 font-medium text-sm text-500">CONTACT US</h1>
                                            <div class="flex gap-5">
                                                <a href="#"><i class="pi pi-github text-white text-2xl"></i></a>
                                                <a href="#"><i class="pi pi-twitter text-white text-2xl"></i></a>
                                                <a href="#"><i class="pi pi-linkedin text-white text-2xl"></i></a>
                                            </div>
                                            <p class="m-0 text-base lg:text-sm">
                                                (415) 931-1624 794 Mcallister <br />
                                                St San Francisco, 94102
                                            </p>
                                            <span class="text-base lg:text-sm">©Copyright 202X</span>
                                        </div>
                                    </div>
                                    <div class="col-12 lg:col-3">
                                        <div class="w-full text-white flex flex-column gap-3">
                                            <h1 class="m-0 font-medium text-sm text-500">ABOUT US</h1>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Our Values</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Our Team</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Our History</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Career & Join Us</span></a>
                                        </div>
                                    </div>
                                    <div class="col-12 lg:col-3">
                                        <div class="w-full text-white flex flex-column gap-3">
                                            <h1 class="m-0 font-medium text-sm text-500">SITE MAP</h1>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Dashboard</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">CRUD</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Invoice</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Help </span></a>
                                        </div>
                                    </div>
                                    <div class="col-12 lg:col-3">
                                        <div class="w-full text-white flex flex-column gap-3">
                                            <h1 class="m-0 font-medium text-sm text-500">CALENDAR</h1>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Widgets</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Documentation</span></a>
                                            <a href="#"><span class="text-base lg:text-sm block text-white">Buy Now</span></a>
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

<style scoped>
::placeholder {
    color: #fff;
}
</style>
