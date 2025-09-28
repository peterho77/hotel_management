import "./bootstrap";
import "../css/app.scss";
import "../css/guest/base.css";
import "primeicons/primeicons.css";

import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { svgSpritePlugin } from "vue-svg-sprite";
import Guest from "./Layouts/Guest.vue";
import PrimeVue from "primevue/config";
import Breadcrumb from "primevue/breadcrumb";
import Aura from "@primeuix/themes/aura";

createInertiaApp({
    title: (title) => `Sona Hotel ${title}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];
        page.default.layout = page.default.layout || Guest;
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .component("Head", Head)
            .component("Link", Link)
            .component("Breadcrumb", Breadcrumb);

        vueApp.use(plugin);
        vueApp.use(svgSpritePlugin, {
            url: "/icon/guest-icons.svg",
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
    },
});
