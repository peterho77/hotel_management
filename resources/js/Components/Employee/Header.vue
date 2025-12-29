<template>
    <header>
        <div class="container | flex justify-between">
            <div class="buttons-group | p-2 flex gap-3">
                <Button label="Lịch làm việc của tôi" icon="pi pi-user" size="small" variant="text" raised />
                <Button label="Lịch đặt phòng" icon="pi pi-calendar" size="small" variant="text" raised />
                <Button label="Hóa đơn" icon="" severity="success" size="small" raised />
            </div>
            <div class="setting-group | flex items-center gap-4">
                <div class="user-avatar | flex items-center">
                    <Avatar icon="pi pi-user" class="mr-2" style="background-color: #ece9fc; color: #2a1261" />
                    <span>{{ user.user_name }}</span>
                </div>
                <span>{{ user.phone ?? '999999' }}</span>
                <div>
                    <div class="user-menu | flex items-center">
                        <Button icon="pi pi-ellipsis-v" @click="toggleUserMenu" severity="success" variant="text"/>
                        <TieredMenu v-if="user" ref="userMenu" :popup="true" :model="userMenuItems" appendTo="self">
                            <!-- Header user info -->
                            <template #start>
                                <button
                                    class="relative overflow-hidden w-full border-0 bg-transparent flex items-center gap-x-3 p-2 pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200">
                                    <Avatar icon="pi pi-user" style="background-color: #dee9fc; color: #1a2551" />
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
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.setting-group>* {
    padding-inline: .5rem;
}

.setting-group>*:first-child {
    position: relative;
}

.setting-group>*:first-child::after {
    content: "";
    display: inline-block;
    width: 1px;
    height: 100%;
    background-color: var(--neutral-color-400);
    position: absolute;
    top: 0;
    right: -8px;
}
</style>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Avatar from 'primevue/avatar';
import TieredMenu from 'primevue/tieredmenu';
import Badge from 'primevue/badge';

// check authentication to show user name
const page = usePage();
const user = computed(() => page.props.auth.user);

// toggle user menu
const userMenu = ref();
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
const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
}

</script>