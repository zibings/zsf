<template>
    <div class="card docs">
        <h2>Getting Started</h2>
        <p>
            Ultima is an application template for Vue 3 based on <a href="https://github.com/vuejs/create-vue" class="font-medium text-primary hover:underline">create-vue</a>, the recommended way to start a <strong>Vite-powered</strong> Vue projects.
            To get started, extract the contents of the zip file, cd to the directory and install the dependencies with npm or yarn.
        </p>
        <pre class="app-code"><code>npm install</code></pre>

        <p>Next step is running the application using the serve script and navigate to <i>http://localhost:5173/</i> to view the application. That is it, you may now start with the development using the Ultima template.</p>

        <pre class="app-code"><code>npm run dev</code></pre>

        <h4>Structure</h4>
        <p>Ultima consists of a couple folders, demos and layout have been separated so that you can easily remove what is not necessary for your application.</p>
        <ul class="line-height-3">
            <li><span class="text-primary font-medium">src/layout</span>: Main layout files, need to be present</li>
            <li><span class="text-primary font-medium">src/views</span>: Demo pages</li>
            <li><span class="text-primary font-medium">public/demo</span>: Assets used in demos</li>
            <li><span class="text-primary font-medium">public/layout</span>: Assets used in layout</li>
            <li><span class="text-primary font-medium">src/assets/demo</span>: Styles used in demos</li>
            <li><span class="text-primary font-medium">src/assets/layout</span>: SCSS files of the main layout</li>
        </ul>

        <h4>Default Configuration</h4>
        <p>
            Initial layout configuration can be defined at the main app component by injecting the composable from <span class="text-primary font-medium">src/layout/composables/layout</span>, this step is optional and only necessary when customizing
            the defaults. Note that <span class="text-primary font-medium">theme</span> and <span class="text-primary font-medium">scale</span> are not reactive at the moment since theme is configured outside of Vue at
            <strong class="font-semibold">index.html</strong> by default and initial scale is defined with the <span class="text-primary font-medium">$scale</span> at <strong class="font-semibold">layout.scss</strong>. When default theme or scale is
            changed at their files initially, it is required to configure the layout service with the matching values to avoid sync issues.
        </p>

        <pre class="app-code"><code>&lt;script setup&gt;
import &#123; useLayout &#125; from '@/layout/composables/layout';

const &#123; layoutConfig &#125; = useLayout();
layoutConfig.menuMode.value = 'slim';
&lt;/script&gt;

&lt;template&gt;
    &lt;router-view /&gt;
&lt;/template&gt;

&lt;style scoped&gt;&lt;/style&gt;
</code></pre>

        <h4>Menu</h4>
        <p>
            Main menu is located at <span class="text-primary font-medium">src/layout/AppMenu.vue</span> file. Update the <span class="text-primary font-medium">model</span> property to define the menu of your application using PrimeVue MenuModel
            API.
        </p>
        <pre class="app-code"><code>&lt;script setup&gt;
import &#123; ref &#125; from 'vue';

import AppMenuitem from './AppMenuitem.vue';

const model = ref([
    &#123;
        label: 'Dashboards',
        icon: 'pi pi-home',
        items: [
            &#123;
                label: 'E-Commerce',
                icon: 'pi pi-fw pi-home',
                to: '/'
            &#125;,
            &#123;
                label: 'Banking',
                icon: 'pi pi-fw pi-image',
                to: '/dashboard-banking'
            &#125;
        ]
    &#125;,
    //...
</code></pre>

        <h4>Breadcrumb</h4>
        <p>Breadcrumb component at the topbar section is dynamic and retrieves the path information from the router using the <span class="text-primary font-medium">meta.breadcrumb</span> property.</p>

        <pre class="app-code"><code>&#123;
    path: '/uikit/formlayout',
    name: 'formlayout',
    meta: {
        breadcrumb: ['UI Kit', 'Form Layout']
    },
    component: () => import('@/views/uikit/FormLayout.vue')
&#125;,</code></pre>

        <h4>Integration with Existing Vite Applications</h4>
        <p>Only the folders that are related to the layout needs to move in to your project. We've created a short tutorial with details. It is based on Sakai but flow is exactly the same with Ultima.</p>

        <div class="video-container">
            <iframe class="video" width="560" height="315" src="https://www.youtube.com/embed/AHeSjJFR3ZE" frameborder="0" allowfullscreen></iframe>
        </div>

        <h4>Component Theme</h4>
        <p>
            Ultima provides 34 PrimeVue themes out of the box. Setup of a theme is simple by including the css of theme to your bundle that are located inside <span class="text-primary font-medium">public/layout/styles/theme/</span> folder such as
            <span class="text-primary font-medium">public/layout/styles/theme/theme-light/blue/theme.css</span>.
        </p>

        <p>A custom theme can be developed by the following steps.</p>
        <ul>
            <li class="line-height-4">Choose a custom theme name such as "mytheme".</li>
            <li class="line-height-4">Create a folder named "mytheme" under <span class="font-semibold">public/layout/styles/theme/theme-light/mytheme</span> folder.</li>
            <li class="line-height-4">Create a file such as theme.scss inside your "mytheme" folder.</li>
            <li class="line-height-4">Define the variables listed below in your file and import the dependencies.</li>
            <li class="line-height-4">
                Include the theme.scss in your application via an import in <i>src/assets/styles.scss</i> or simply refer to the compiled css with a link tag. Note that if you choose the former, the theme will be bundled with the rest of your app.
            </li>
        </ul>

        <p>Here are the variables required to create a theme.</p>
        <pre class="app-code"><code>$primaryColor: #2196f3;
$primaryLightestColor: #e3f2fd;
$primaryMenuTextColor: $primaryColor;
$primaryTextColor: #ffffff;
$accentColor: #f37f21;
$accentTextColor: #212121;

@import '../_variables';
@import '../../_compact';
@import '../../theme-base/_components';
@import '../../extensions/_vendor_extensions';
</code></pre>

        <h4>Theme Switcher</h4>
        <p>
            Dynamic theming is built-in to the template and implemented by including the theme via a link tag instead of bundling the theme along with a configurator component to switch it. In order to switch your theme dynamically as well, it needs
            to be compiled to css. An example sass command to compile the css would be;
        </p>

        <pre class="app-code"><code>sass --update public/layout/styles/theme-light/mytheme.scss:public/layout/styles/theme-light/mytheme.css</code></pre>

        <p class="text-sm">*Note: The sass command above is supported by Dart Sass. Please use Dart Sass instead of Ruby Sass.</p>

        <h4>Theme Customization</h4>
        <p>
            Ultima includes <a href="https://github.com/primefaces/primevue-sass-theme" class="font-medium text-primary hover:underline">PrimeVue Sass Theme</a> internally. Visit
            <a href="https://primevue.org/theming/#customtheme" class="font-medium text-primary hover:underline">theming documentation</a> for details.
        </p>
        
        <h4>TopBar Theme</h4>
        <p>
            Theming of the topbar has 20 options to choose from, still if you'd like to create your own theme then create a file like <span class="text-primary font-medium">_topbar_mytopbar</span> under
            <span class="text-primary font-medium">src/assets/layout/topbar/themes</span> folder with the content below using your own values. The style class name must begin with <span class="text-primary font-medium">.layout-topbar-</span> prefix.
        </p>
        <pre class="app-code"><code>.layout-topbar-mytheme &#123;
    --topbar-bg: #1565C0;
    --topbar-start-bg: #0D47A1;
    --topbar-menu-button-bg:#FBC02D;
    --topbar-menu-button-hover-bg:#dda928;
    --topbar-menu-button-text-color:#212121;
    --topbar-item-text-color: #ffffff;
    --topbar-item-hover-bg: rgba(255,255,255,.12);
&#125;
</code></pre>

        <p>Next step is including your file in <span class="text-primary font-medium">src/assets/layout/topbar/_topbar.scss</span> file.</p>
        <pre class="app-code"><code>@import './themes/_topbar_mytopbar';</code></pre>

        <p>Your topbar theme is now ready to use, navigate to AppComponent and set it as the default. See the <b>Default Configuration</b> section for details.</p>
        <pre class="app-code"><code>const layoutService = ref(&#123;
    //...
    topbarTheme: "mytheme"
    //...
&#125;);</code></pre>

        <h4>Menu Theme</h4>
        <p>
            Main menu offers 12 built-in themes and building your own theme is quite trivial. First create a file like <span class="text-primary font-medium">_menu_mymenu</span> under
            <span class="text-primary font-medium">src/assets/layout/sidebar/themes</span> folder with the content below using your own values. The style class name must begin with <span class="text-primary font-medium">.layout-menu-</span> prefix.
        </p>
        <pre class="app-code"><code>.layout-menu-mytheme &#123;
    --menu-bg:#FDFEFF;
    --root-menuitem-text-color:#657380;
    --menuitem-text-color:#515C66;
    --menuitem-hover-bg:rgba(0,0,0,.04);
    --active-menuitem-text-color:var(--primary-500);
    --active-menuitem-bg:var(--primary-50);
    --inline-menu-border-color:#e4e4e4;
&#125;
</code></pre>

        <p>Next step is including your file in <span class="text-primary font-medium">src/assets/layout/sidebar/_sidebar.scss</span> file.</p>
        <pre class="app-code"><code>@import './themes/_menu_mymenu.scss';</code></pre>

        <p>Your menu theme is now ready to use, navigate to AppComponent and set it as the default. See the <b>Default Configuration</b> section for details.</p>
        <pre class="app-code"><code>const layoutService = ref(&#123;
    //...
    menuTheme: "mytheme"
    //...
&#125;);</code></pre>

        <h4>Migration</h4>
        <p>
            Initial integration with an existing Vite application and the migration process is similar. During an update, only the layout folder and layout assets folder need to be updated, see the "Integration with Existing Vite Applications"
            section for more information. Important changes are also documented at CHANGELOG.md file.
        </p>
    </div>
</template>

<style lang="scss" scoped>
@media screen and (max-width: 991px) {
    .video-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%;

        iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    }
}
</style>
