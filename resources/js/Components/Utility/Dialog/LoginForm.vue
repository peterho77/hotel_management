<template>
    <div class="login-form">
        <div class="login-form__header | set-bg-img h-[300px]" :style="$getBgStyle(loginImg)">
            <i class="close-icon | pi pi-times | text-neutral-300 hover:text-neutral-100" @click="closeDialog()"></i>
        </div>
        <Form class="px-12 py-8 flex flex-col gap-y-8">
            <div class="login-form-title | grid grid-cols-2 gap-20 mb-4">
                <span class="fs-normal-heading">
                    Sign In
                </span>
                <div class="flex gap-3 items-center">
                    <i class="pi pi-facebook text-white bg-blue-600 p-3 rounded-full"></i>
                    <i class="pi pi-google text-white bg-red-500 p-3 rounded-full"></i>
                    <i class="pi pi-github text-white bg-gray-800 p-3 rounded-full"></i>
                </div>
            </div>
            <FloatLabel>
                <InputText id="username" v-model="value" fluid />
                <label for="username">Username</label>
            </FloatLabel>
            <FloatLabel>
                <Password id="password" v-model="value" fluid toggleMask />
                <label for="password">Password</label>
            </FloatLabel>
            <Button label="Sign in" severity="success"></Button>
            <div class="flex justify-between">
                <div class="flex items-center gap-2">
                    <Checkbox v-model="checked" binary variant="filled" checked />
                    <label for="checked">Remember me</label>
                </div>
                <Link>Forgot Password</Link>
            </div>
            <p class="text-center">Not a member?
                <Link class="text-blue-400" @click="showSignupForm()">Sign up</Link>
            </p>
        </Form>
    </div>
</template>

<script setup>
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';

import { inject, defineAsyncComponent, nextTick } from 'vue';
import { useDialog } from 'primevue/usedialog';
// register dynamic dialog
const dialog = useDialog();

// open sign up dialog
const signupForm = defineAsyncComponent(() => import('../Dialog/SignupForm.vue'));
const showSignupForm = async () => {
    closeDialog();
    await nextTick();
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

const dialogRef = inject('dialogRef');

// close dialog
const closeDialog = () => {
    dialogRef.value.close();
}

// login

const loginImg = '/img/login-bg.jpg';
</script>
