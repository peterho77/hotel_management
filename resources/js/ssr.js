import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
import { createSSRApp, h } from "vue";

import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Guest from "./Layouts/Guest.vue";
import Admin from "./Layouts/Admin.vue";
import PrimeVue from "primevue/config";
import Breadcrumb from "primevue/breadcrumb";
import Aura from "@primeuix/themes/aura";
import { svgSpritePlugin } from "vue-svg-sprite";

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => `Sona Hotel ${title}`,
            resolve: (name) => {
                const pages = import.meta.glob("./Pages/**/*.vue", {
                    eager: true,
                });
                const page = pages[`./Pages/${name}.vue`];

                // set layout theo folder
                if (name.startsWith("Admin/")) {
                    page.default.layout = page.default.layout || Admin;
                } else if (name.startsWith("Guest/")) {
                    page.default.layout = page.default.layout || Guest;
                }

                return page;
            },
            setup({ App, props, plugin }) {
                const vueApp = createSSRApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(ZiggyVue, {
                        ...page.props.ziggy,
                        location: new URL(page.props.ziggy.location),
                    })
                    .component("Head", Head)
                    .component("Link", Link)
                    .component("Breadcrumb", Breadcrumb)
                    .use(svgSpritePlugin, { url: "/icon/sprite-icons.svg" })
                    .use(PrimeVue, {
                        theme: {
                            preset: Aura,
                            options: { darkModeSelector: false },
                        },
                    });

                // Hàm global: set background image
                vueApp.config.globalProperties.$getBgStyle = (url) => {
                    let finalUrl = url;
                    if (!finalUrl.startsWith("http")) {
                        finalUrl = "/" + finalUrl.replace(/^\/+/, "");
                    }
                    return { backgroundImage: `url(${finalUrl})` };
                };

                return vueApp;
            },
        }),
    { cluster: true }
);
