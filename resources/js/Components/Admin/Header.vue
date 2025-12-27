<template>
    <header class="primary-header">
        <section class="top-nav | padding-block-200">
            <div class="container">
                <div class="grid grid-cols-12">
                    <div class="header-logo | col-span-12 lg:col-span-2">
                        <img src="../../../../public/img/logo.png" alt="">
                    </div>
                    <div class="col-span-12 lg:col-span-10">
                        <nav class="nav-wrapper">
                            <ul class="setting-options | nav-list">
                                <li class="branch | justify-center align-center">
                                    <SvgSprite symbol="location-dot" size="0 0 24 24" role="presentation"
                                        class="icon" />
                                    <span>Chi nhánh trung tâm</span>
                                </li>
                                <ul class="setting-list | nav-list">
                                    <li class="align-center">
                                        <SvgSprite symbol="bell" size="0 0 24 24" role="presentation" class="icon" />
                                    </li>
                                    <li class="align-center">
                                        <SvgSprite symbol="gear" size="0 0 24 24" role="presentation" class="icon" />
                                    </li>
                                    <li class="align-center">
                                        <SvgSprite symbol="user" size="0 0 24 24" role="presentation" class="icon" />
                                    </li>
                                </ul>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <section class="bottom-nav | padding-block-100">
            <div class="container">
                <nav class="nav-wrapper">
                    <ul class="primary-nav | nav-list">
                        <li>Tổng quan</li>
                        <template v-if="user.role === 'admin'">
                            <li>Chi nhánh</li>
                            <li class="room-nav" @click="toggle">
                                Phòng
                                <Menu ref="menu" :model="roomItemsMenu" class="toggle-menu py-1" :popup="true">
                                    <template #item="{ item, props }">
                                        <div class="toggle-menu-item">
                                            <Link :href="route('admin.room-management')">{{ item.label }}</Link>
                                        </div>
                                    </template>
                                </Menu>
                            </li>
                            <li>Hàng hóa - Dịch vụ</li>
                            <li>
                                <Link :href="route('admin.account')">Tài khoản</Link>
                            </li>
                        </template>
                        <template v-if="user.role === 'manager'">
                            <li>
                                <Link :href="route('manager.customer')">Khách hàng - Đối tác</Link>
                            </li>
                            <li>
                                <Link :href="route('manager.employee')">Nhân viên</Link>
                            </li>
                            <li>Đặt phòng</li>
                            <li>
                                <Link :href="route('manager.work-schedule.index')">Lịch làm việc</Link>
                            </li>
                            <li>Báo cáo thống kê</li>
                        </template>
                    </ul>
                    <div class="flex items-center gap-x-2">
                        <button class="admin-button" data-type="inverted">
                            <SvgSprite symbol="bell-concierge" size="0 0 24 24" role="presentation" class="icon" />
                            Lễ tân
                        </button>
                        <Button severity="warn" label="Đăng xuất" @click="logout()" raised />
                    </div>
                </nav>
            </div>
        </section>
    </header>
</template>

<script setup>
import Menu from 'primevue/menu';
import Button from 'primevue/button';
import { ref, computed } from "vue";

import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const menu = ref();
const roomItemsMenu = ref([
    { label: 'Hạng phòng & Phòng' },
    { label: 'Thiết lập giá' }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

// define user is admin or manager
const page = usePage();
const user = computed(() => page.props.auth.user);

// logout
const logout = () => {
    router.post('/logout');
}

</script>