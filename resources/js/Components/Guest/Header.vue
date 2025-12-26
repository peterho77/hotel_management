<template>
    <header class="primary-header">
        <section class="top-nav | bg-sky-200 padding-block-200 section-divider">
            <div class="container">
                <div class="even-columns">
                    <nav class="top-nav__left">
                        <ul class="nav-list" role="list">
                            <li class="padding-block-200">
                                <SvgSprite symbol="telephone-fill" size="0 0 24 24" role="presentation" class="icon" />
                                <span class="fw-semi-bold">037 619 3244</span>
                            </li>
                            <li>
                                <SvgSprite symbol="envelope-fill" size="0 0 24 24" role="presentation" class="icon" />
                                <span class="fw-semi-bold">peterho5477@gmail.com</span>
                            </li>
                        </ul>
                    </nav>
                    <nav class="top-nav__right">
                        <div class="nav-wrapper gap-x-18">
                            <ul class="social-list" role="list">
                                <li>
                                    <a href="">
                                        <SvgSprite symbol="facebook" size="0 0 24 24" role="presentation"
                                            class="icon social-icon--facebook" data-color="original" />
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <SvgSprite symbol="instagram" size="0 0 24 24" role="presentation"
                                            class="icon social-icon--instagram text-[#DD2A7B] transition-colors duration-300" />
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <SvgSprite symbol="twitch" size="0 0 24 24" role="presentation"
                                            class="icon social-icon--twitch" />
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <SvgSprite symbol="twitter-x" size="0 0 24 24" role="presentation"
                                            class="icon social-icon--x" />
                                    </a>
                                </li>
                            </ul>
                            <!-- UI show user name -->
                            <div class="show-login | flex justify-center gap-x-2">
                                <template v-if="!user">
                                    <Button label="Login" icon="pi pi-user" class="fs-600" severity="info" raised
                                        @click="showLoginForm" />
                                    <Button label="Register" class="fs-600" severity="info" variant="text" raised
                                        @click="showRegisterForm" />
                                </template>
                                <template v-else>
                                    <Button class="button" label="Book now" raised
                                        @click="router.get(route('booking.index'))"></Button>
                                </template>
                            </div>
                            <div class="menu-user | relative flex justify-center items-center gap-x-4">
                                <Button icon="pi pi-bell" rounded raised></Button>
                                <Button icon="pi pi-ellipsis-v" rounded raised @click="toggleUserMenu" />
                                <TieredMenu v-if="user" ref="userMenu" :popup="true" :model="userMenuItems"
                                    appendTo="self">
                                    <!-- Header user info -->
                                    <template #start>
                                        <button
                                            class="relative overflow-hidden w-full border-0 bg-transparent flex items-center gap-x-3 p-2 pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200">
                                            <Avatar icon="pi pi-user"
                                                style="background-color: #dee9fc; color: #1a2551" />
                                            <span class="inline-flex flex-col items-start">
                                                <span class="font-bold">{{ user.user_name }}</span>
                                                <span class="text-sm">{{ user.role }}</span>
                                            </span>
                                        </button>
                                    </template>

                                    <!-- Custom item template -->
                                    <template #item="{ item, props }">
                                        <a class="flex items-center w-full px-3 py-2 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors duration-200"
                                            v-bind="props.action">
                                            <span :class="item.icon" class="mr-2" />
                                            <img v-if="item.img" :src="item.img" alt="">
                                            <span>{{ item.label }}</span>
                                            <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                                            <span v-if="item.shortcut"
                                                class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">
                                                {{ item.shortcut }}
                                            </span>
                                        </a>
                                    </template>
                                </TieredMenu>
                                <TieredMenu v-else ref="userMenu" :popup="true" :model="guestMenuItems" appendTo="self">
                                    <!-- Custom item template -->
                                    <template #item="{ item, props }">
                                        <a class="flex items-center w-full px-3 py-2 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors duration-200"
                                            v-bind="props.action">
                                            <span :class="item.icon" class="mr-2" />
                                            <img v-if="item.img" :src="item.img" alt="">
                                            <span>{{ item.label }}</span>
                                            <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                                            <span v-if="item.shortcut"
                                                class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">
                                                {{ item.shortcut }}
                                            </span>
                                        </a>
                                    </template>
                                </TieredMenu>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </section>
        <section class="bottom-nav | padding-block-400">
            <div class="container">
                <div class="even-columns">
                    <div class="header-logo">
                        <img src="../../../../public/img/logo.png" alt="" />
                    </div>
                    <nav class="primary-bottom-nav">
                        <div class="search-bar"></div>
                        <ul class="nav-list" role="list">
                            <li>
                                <Link :href="route('home')"><span>Home</span></Link>
                            </li>
                            <li v-if="user?.role === 'customer'">
                                <Link :href="route('user.profile', user.user_name)"><span>Dashboard</span></Link>
                            </li>
                            <li>
                                <Link :href="route('rooms.index')"><span>Rooms</span></Link>
                            </li>
                            <li>
                                <a href=""><span>Blogs</span></a>
                            </li>
                            <li>
                                <Link :href="route('about')"><span>About us</span></Link>
                            </li>
                            <li>
                                <a :href="route('review')"><span>Review</span></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
    </header>
</template>

<style scoped>
/* custom tiered menu */
:deep(.p-tieredmenu.p-component.p-tieredmenu-overlay) {
    inset-inline-start: auto !important;
    left: auto !important;
    right: 0 !important;
}
</style>

<script setup>
import { defineAsyncComponent, ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDialog } from 'primevue/usedialog';
import { useToast } from "primevue/usetoast";
import { router } from '@inertiajs/vue3';

import TieredMenu from 'primevue/tieredmenu';
import Button from 'primevue/button';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';

// dynamic dialog
const dialog = useDialog();

const loginForm = defineAsyncComponent(() => import('../../Components/Dialog/Login.vue'));
const registerForm = defineAsyncComponent(() => import('../../Components/Dialog/Register.vue'));

const loginImg = '/img/login-bg.jpg';

const showLoginForm = () => {
    dialog.open(loginForm, {
        props: {
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            closable: false,
            class: 'login-dialog',
        },
    })
};
const showRegisterForm = () => {
    dialog.open(registerForm, {
        props: {
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            closable: false,
            class: 'register-dialog',
        },
    });
}

// check authentication to show user name
const page = usePage();
const user = computed(() => page.props.auth.user);

// toggle user menu
const userMenu = ref();

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
}

const userMenuItems = ref([
    {
        separator: true
    },
    {
        label: 'Profile',
        icon: 'pi pi-info-circle',
        items: [
            {
                label: 'Edit',
                icon: 'pi pi-user-edit',
                shortcut: '⌘+N'
            },
            {
                label: 'Messages',
                icon: 'pi pi-inbox',
                badge: 2
            },
            {
                label: 'Password',
                icon: 'pi pi-key',
            }
        ]
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        items: [
            {
                label: 'Theme',
                icon: 'pi pi-sun',
                shortcut: '⌘+O'
            },
            {
                label: 'Language',
                icon: 'pi pi-language',
                items: [
                    {
                        label: 'EN',
                        img: '/img/united-states-of-america.png'
                    },
                    {
                        label: 'VN',
                        img: '/img/vietnam.png'
                    }
                ]
            },
        ]
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        shortcut: '⌘+Q',
        command: () => { logout() },
    }
]);

const guestMenuItems = ref([
    {
        label: 'Theme',
        icon: 'pi pi-sun',
        shortcut: '⌘+O'
    },
    {
        label: 'Language',
        icon: 'pi pi-language',
        items: [
            {
                label: 'EN',
                img: '/img/united-states-of-america.png'
            },
            {
                label: 'VN',
                img: '/img/vietnam.png'
            }
        ]
    },
])

// logout
const logout = () => {
    router.post('/logout');
}

</script>
