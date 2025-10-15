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
                    <nav class="top-nav__right  ">
                        <div class="nav-wrapper !gap-x-16   ">
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
                            <div class="card flex justify-center gap-x-2">
                                <Button label="Login" icon="pi pi-user" class="fs-600" severity="info" raised
                                    @click="showLoginForm" />
                                <Button label="Register" class="fs-600" severity="info" variant="text" raised
                                    @click="showSignupForm" />
                            </div>
                            <div class="toggle-lang-switch">
                                <img class="flag-icon" src="../../../../public/img/united-states-of-america.png"
                                    alt="" />
                                <div class="current-lang">
                                    <span> EN </span>
                                    <SvgSprite symbol="caret-down-fill" size="0 0 24 24" role="presentation"
                                        class="icon" />
                                </div>
                                <ul class="lang-list | padding-block-200" role="list">
                                    <li class="en">
                                        <a href="">EN</a>
                                    </li>
                                    <li class="vi">
                                        <a href="">VI</a>
                                    </li>
                                </ul>
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
                            <li>
                                <a href=""><span>Rooms</span></a>
                            </li>
                            <li>
                                <a href=""><span>Blogs</span></a>
                            </li>
                            <li>
                                <Link :href="route('about')"><span>About us</span></Link>
                            </li>
                            <li>
                                <a href=""><span>Contact</span></a>
                            </li>
                            <li>
                                <a href=""><span>Account</span></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
    </header>
</template>

<script setup>
import { defineAsyncComponent, h } from 'vue';
import { useDialog } from 'primevue/usedialog';
import Button from 'primevue/button';

// dynamic dialog
const dialog = useDialog();

const loginForm = defineAsyncComponent(() => import('../../Components/Utility/Dialog/LoginForm.vue'));
const signupForm = defineAsyncComponent(() => import('../../Components/Utility/Dialog/SignupForm.vue'));

const loginImg = '/img/login-bg.jpg';

const showLoginForm = () => {
    const dialogRef = dialog.open(loginForm, {
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
const showSignupForm = () => {
    const dialogRef = dialog.open(signupForm, {
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
            class: 'signup-dialog',
        },
    });
}
</script>

<style scoped>
/* Xóa padding mặc định của header và content */
.p-dialog-content {
    padding: 0 !important;
}

.p-dialog-header {
    display: none !important;
    /* ẩn header mặc định */
}

/* Xóa padding mặc định dialog wrapper */
.p-dialog {
    overflow: hidden;
}
</style>
