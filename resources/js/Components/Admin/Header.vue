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
                            <ul class="financial-tools | nav-list | align-center" role="list">
                                <li>
                                    <a class="align-center" href="">
                                        <SvgSprite symbol="file-invoice-dollar" size="0 0 24 24" role="presentation"
                                            class="icon" />
                                        <span>Hóa đơn điện tử</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="align-center" href="">
                                        <SvgSprite symbol="sack-dollar" size="0 0 24 24" role="presentation"
                                            class="icon" />
                                        <span>Vay vốn</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="align-center" href="">
                                        <SvgSprite symbol="credit-card" size="0 0 24 24" role="presentation"
                                            class="icon" />
                                        <span>Thanh toán</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="align-center" href="">
                                        <SvgSprite symbol="circle-infor" size="0 0 24 24" role="presentation"
                                            class="icon" />
                                        <span>Hỗ trợ</span>
                                    </a>
                                </li>
                            </ul>
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
                        <li class="room-nav" @click="toggle">
                            Phòng
                            <Menu ref="menu" :model="roomItemsMenu" class="sub-menu padding-block-200" :popup="true">
                                <template #item="{ item, props }">
                                    <div class="sub-menu-item">
                                        <Link :href="route('admin.room-management')">{{ item.label }}</Link>
                                    </div>
                                </template>
                            </Menu> 
                        </li>
                        <li>Hàng hóa</li>
                        <li>Giao dịch</li>
                        <li>Đối tác</li>
                        <li>Nhân viên</li>
                        <li>Số quỹ</li>
                        <li>Bán online</li>
                        <li>Báo cáo</li>
                    </ul>
                    <button class="admin-button" data-type="inverted">
                        <SvgSprite symbol="bell-concierge" size="0 0 24 24" role="presentation" class="icon" />
                        Lễ tân
                    </button>
                </nav>

            </div>
        </section>
    </header>
</template>

<script setup>
import Menu from 'primevue/menu';
import { ref } from "vue";

const menu = ref();
const roomItemsMenu = ref([
    { label: 'Hạng phòng & Phòng' },
    { label: 'Thiết lập giá' }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

</script>