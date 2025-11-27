<template>
    <GuestLayout>
        <div class="dashboard-page py-2 gap-x-1">
            <nav class="sidebar | border-r border-gray-200" :class="{ close: isSidebarClosed }">
                <div class="sidebar-header | flex items-center mb-2 p-6">
                    <span class="sidebar-title text-lg">Hello <b>{{ user.user_name }}</b></span>
                </div>
                <ul class="sidebar-menu">
                    <li v-for="(item, index) in dropdownMenu" :key="index">
                        <div :class="['menu-item-btns flex items-stretch', activeIndex === `${index}` && !item.items ? 'active' : '']"
                            @click="() => { item.command?.(); if (!item.items) setActive(`${index}`) }">
                            <button :class="[
                                item.items ? 'dropdown-btn' : 'nodropdown-btn',
                                { rotate: openIndex === index }
                            ]">
                                <i :class="item.icon"></i>
                                <span>{{ item.label }}</span>
                            </button>
                            <template v-if="item.items">
                                <button @click.stop="toggleSubmenu(index)" class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                        width="24px">
                                        <path d="M480-344 240-584l56-56 184 184 184-184 56 56-240 240Z" />
                                    </svg>
                                </button>
                            </template>
                        </div>

                        <ul class="sub-menu" v-if="item.items" :class="{ show: openIndex === index }">
                            <div>
                                <li v-for="(subitem, i) in item.items" :key="`${index}-${i}`"
                                    @click.prevent="subitem.command?.(); setActive(`${index}-${i}`)"
                                    :class="[activeIndex === `${index}-${i}` ? 'active' : '']">
                                    <Link :href="subitem.link">{{ subitem.label }}</Link>
                                </li>
                            </div>
                        </ul>
                    </li>
                </ul>
            </nav>
            <main class="dashboard-content | flow mx-2">
                <slot />
            </main>
        </div>
    </GuestLayout>
</template>

<style scoped>
.dashboard-page {
    display: grid;
    grid-template-columns: auto 1fr;
}

.sidebar {
    background: var(--neutral-color-100);
    position: sticky;
    top: 0;
    align-self: start;
    width: 250px;
    height: 100vh;

    transition: all 150ms ease;
    overflow: hidden;
    text-wrap: nowrap;
}

.sidebar .sidebar-menu>li>.nodropdown-btn {
    padding: 1em 1.25em;
}

.sidebar .sidebar-menu .menu-item-btns button {
    padding: 1em;
}

.sidebar .sidebar-title {
    position: relative;
    opacity: 1;
    visibility: visible;
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

.sidebar a,
.sidebar .dropdown-btn,
.sidebar .nodropdown-btn {
    width: 100%;
    display: flex;
    align-items: center;
    padding: .5em .25em;
    gap: 1rem;
}

.sidebar-menu *{
    cursor: pointer;
}

.sidebar-menu li.active,
.sidebar-menu .menu-item-btns.active {
    background-color: var(--accent-color-300);
}

.sidebar .dropdown-btn {
    border: none;
    width: 100%;
    text-align: left;
    transition: 300ms ease-in-out;
}

.sidebar .dropdown-btn svg:last-child {
    margin-left: auto;
}

/* hover */
.sidebar-menu .menu-item-btns:hover,
.sidebar-menu .nodropdown-btn:hover {
    background: var(--accent-color-100);
}

.sidebar-menu .dropdown-icon:hover,
.sub-menu li:hover {
    background: var(--accent-color-100);
}

.sidebar-menu .sub-menu:hover {
    background: transparent;
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
    padding-block: .5em;
}

.rotate svg:last-child {
    rotate: -180deg;
}

/* dashboard content */
.dashboard-content {
    border-radius: var(--size-100);
}
</style>

<script setup>
import GuestLayout from "@/Layouts/Guest.vue";

import { ref, reactive, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const isSidebarClosed = ref(false);
const openIndex = ref(null);

const dropdownMenu = reactive([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            router.get(route('user.profile', { user_name: user.value.user_name }));
        }
    },
    {
        label: 'Rooms',
        icon: 'pi pi-warehouse',
        items: [
            {
                label: 'Booking',
                link: route('booking.index')
            },
            {
                label: 'History',
                link: route('user.booking-history', { user_name: user.value.user_name })
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
]);

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
</script>