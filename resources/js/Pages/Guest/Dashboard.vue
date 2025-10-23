<script setup>
import { ref, computed } from 'vue';

import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';


import Avatar from 'primevue/avatar';

const isSidebarClosed = ref(false);
const openIndex = ref(null);

const toggleSidebar = () => {
    // Đóng tất cả submenu khi đóng sidebar
    if (!isSidebarClosed.value) {
        openIndex.value = null
    }
    isSidebarClosed.value = !isSidebarClosed.value
}

// show username 
const page = usePage();
const user = computed(() => page.props.auth.user);
const customer = computed(() => page.props.auth.user.customer);

const customerInfor = ref(JSON.parse(JSON.stringify(page.props.auth.user.customer)));

const dropdownMenu = [
    {
        label: 'Profile',
        icon: 'pi pi-user',
        items: [
            {
                label: 'Edit',
            },
            {
                label: 'Change password'
            }]
    },
    {
        label: 'Rooms',
        icon: 'pi pi-warehouse',
        items: [
            {
                label: 'Booking',
            },
            {
                label: 'History'
            }]
    },
    {
        label: 'Setting',
        icon: 'pi pi-cog',
        items: [
            {
                label: 'Theme',
            },
            {
                label: 'Language'
            }]
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => {
            router.post('/logout');
        }
    }
]

const toggleSubmenu = (index) => {
    // Nếu sidebar đang đóng → mở ra trước
    if (isSidebarClosed.value) {
        isSidebarClosed.value = false
    }
    // Toggle submenu được bấm
    openIndex.value = openIndex.value === index ? null : index
}

// active tag
const activeIndex = ref(0)

const setActive = (index) => {
    activeIndex.value = index
}

// format label
function formatLabel(str) {
    str = str.replace(/[-_]/g, " ");

    return str.split(" ").map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(" ");
}

</script>

<template>
    <div class="dashboard-page py-2 gap-x-1">
        <nav class="sidebar | border-r-1 border-gray-200" :class="{ close: isSidebarClosed }">
            <div class="sidebar-header | flex items-center mb-2">
                <span class="sidebar-title text-lg">Hello <b>{{ user.user_name }}</b></span>
                <button class="toggle-sidebar-btn" :class="{ rotate: isSidebarClosed }" @click="toggleSidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                        <path
                            d="M440-240 200-480l240-240 56 56-183 184 183 184-56 56Zm264 0L464-480l240-240 56 56-183 184 183 184-56 56Z" />
                    </svg>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li v-for="(item, index) in dropdownMenu" :key="index" class="sidebar-menu-item">
                    <button :class="[
                        item.items ? 'dropdown-btn' : 'nodropdown-btn',
                        { rotate: openIndex === index }
                    ]" @click="item.items ? toggleSubmenu(index) : item.command()">
                        <i :class="item.icon"></i>
                        <Link>{{ item.label }}</Link>
                        <template v-if="item.items">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path d="M480-344 240-584l56-56 184 184 184-184 56 56-240 240Z" />
                            </svg>
                        </template>
                    </button>

                    <ul class="sub-menu" v-if="item.items" :class="{ show: openIndex === index }">
                        <div>
                            <li v-for="(subitem, i) in item.items" :key="`${index}-${i}`"
                                @click.prevent="setActive(`${index}-${i}`)"
                                :class="[activeIndex === `${index}-${i}` ? 'active' : '']">
                                <Link>{{ subitem.label }}</Link>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </nav>
        <main class="dashboard-content | border-1 border-gray-200 mx-2">
            <div class="user-header | flex flex-col px-12 py-8 gap-y-1">
                <Avatar class="user-avatar" icon="pi pi-user" size="xlarge" shape="circle" />
                <h2 class="fs-normal-heading">{{ customer.full_name }}</h2>
                <span class="text-gray-600">{{ customer.email }}</span>
            </div>
            <div
                class="user-personal-details | px-12 py-4">
                <ul class="divide-y divide-dashed divide-gray-300">
                    <li class="grid grid-cols-[100px_1fr] md:grid-cols-[200px_1fr] gap-x-6 py-2" v-for="(value, key) in customerInfor" :key="key">
                        <span class="font-semibold text-gray-600">{{ formatLabel(key) }}:</span>
                        <span class="text-left">{{ value }}</span>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</template>

<style scoped>
.dashboard-page {
    display: grid;
    grid-template-columns: auto 1fr;
}

.dashboard-page>* {
    background: var(--neutral-color-100);
}

.sidebar {
    position: sticky;
    top: 0;
    align-self: start;
    width: 250px;
    height: 100vh;

    transition: all 150ms ease;
    overflow: hidden;
    text-wrap: nowrap;
}

.sidebar .sidebar-header {
    padding: 0.25em 1em;
}

.sidebar .sidebar-menu>li>.dropdown-btn,
.sidebar .sidebar-menu>li>.nodropdown-btn {
    padding: 0.75em 1.25em;
}

.sidebar .sidebar-title {
    position: relative;
    opacity: 1;
    visibility: visible;
}

.sidebar.close {
    width: 60px;
    padding: 0.25em;
}

.sidebar-title {
    transition: opacity 0.3s;
}

.sidebar.close .sidebar-title {
    opacity: 0;
    visibility: hidden;
    position: absolute;
}

.sidebar-header {
    background: var(--neutral-color-150);
}

.sidebar svg {
    flex-shrink: 0;
}

.sidebar span {
    flex-grow: 1;
}

.sidebar .toggle-sidebar-btn {
    background: var(--neutral-color-150);
    border: none;
    cursor: pointer;
    margin-left: auto;
    border-radius: .5em;
    display: flex;
    align-items: center;
    padding: 1em 0.15em;
    transition: 150ms ease;
}

.sidebar a,
.sidebar .dropdown-btn,
.sidebar .nodropdown-btn {
    width: 100%;
    display: flex;
    align-items: center;
    padding: .5em .25em;
    gap: 1rem;
}

.sidebar li.active a {
    background-color: var(--accent-color-300);
}

.sidebar .dropdown-btn {
    border: none;
    cursor: pointer;
    width: 100%;
    text-align: left;
    transition: 300ms ease-in-out;
}

.sidebar .dropdown-btn svg:last-child {
    margin-left: auto;
}

.sidebar-menu>li {
    display: block;
    width: 100%;
}

/* hover */
.sidebar-menu .dropdown-btn:hover,
.sidebar-menu .nodropdown-btn:hover {
    background: var(--accent-color-200);
}

.sidebar-menu .sub-menu:hover {
    background: transparent;
}

.sub-menu li:hover {
    background: var(--accent-color-200);
}

.sub-menu {
    display: grid;
    grid-template-rows: 0fr;
    transition: 300ms ease-in-out;

    >div {
        overflow: hidden;
    }
}

.sub-menu.show {
    grid-template-rows: 1fr;
}

.sub-menu li {
    padding-left: 1.5em;
}

.rotate svg:last-child {
    rotate: -180deg;
}

/* dashboard content */
.dashboard-content {
    border-radius: var(--size-100);
}
</style>
