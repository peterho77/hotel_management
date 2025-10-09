import "./bootstrap";
import "../css/base.css";
import "primeicons/primeicons.css";

import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { svgSpritePlugin } from "vue-svg-sprite";
import Guest from "./Layouts/Guest.vue";
import Admin from "./Layouts/Admin.vue";
import PrimeVue from "primevue/config";
import Breadcrumb from "primevue/breadcrumb";
import Aura from "@primeuix/themes/aura";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import DialogService from 'primevue/dialogservice';
import DynamicDialog from 'primevue/dynamicdialog';
import ToastService from 'primevue/toastservice';

createInertiaApp({
    title: (title) => `Sona Hotel ${title}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        // set layout theo folder
        if (name.startsWith("Admin/")) {
            page.default.layout = page.default.layout || Admin;
        } else if (name.startsWith("Guest/")) {
            page.default.layout = page.default.layout || Guest;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(DialogService)
            .use(ToastService)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .component("Breadcrumb", Breadcrumb)
            .component('DynamicDialog', DynamicDialog);

        vueApp.use(plugin);
        vueApp.use(svgSpritePlugin, {
            url: "/icon/sprite-icons.svg",
        });
        vueApp.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: false,
                },
            },
        });

        // Định nghĩa hàm global

        // hàm set bg img
        vueApp.config.globalProperties.$getBgStyle = (url) => {
            let finalUrl = url;
            if (!finalUrl.startsWith("http")) {
                finalUrl = "/" + finalUrl.replace(/^\/+/, "");
            }
            return {
                backgroundImage: `url(${finalUrl})`,
            };
        };

        vueApp.mount(el);

        return vueApp;  
    },
});
