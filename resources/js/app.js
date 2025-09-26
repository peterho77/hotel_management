import "./bootstrap";
import "../css/app.scss";
import "../css/guest/base.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { svgSpritePlugin } from "vue-svg-sprite";
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);
        vueApp.use(svgSpritePlugin, {
            url: "/icon/guest-icons.svg",
        });
        vueApp.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: false,    
                }
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
