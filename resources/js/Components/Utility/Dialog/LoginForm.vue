<template>
    <div class="login-form">
        <div class="login-form__header | set-bg-img h-[300px]" :style="$getBgStyle(loginImg)">
            <i class="close-icon | pi pi-times | text-neutral-300 hover:text-neutral-100" @click="closeDialog()"></i>
        </div>
        <Form v-slot="$form" :initialValues :resolver="loginSchema" :validateOnValueUpdate="false"
            :validateOnBlur="true" @submit="submit" class="px-12 py-8 flex flex-col gap-y-8">
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
            <div class="flex flex-col gap-y-2">
                <FloatLabel>
                    <InputText id="account_name" name="account_name" fluid />
                    <label for="account_name">Username</label>
                </FloatLabel>
                <Message v-if="$form.account_name?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.account_name.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel>
                    <Password id="password" name="password" fluid :feedback="false" toggleMask />
                    <label for="password">Password</label>
                </FloatLabel>
                <Message v-if="$form.password?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.password.error.message }}</Message>
            </div>
            <Button label="Sign in" type="submit" severity="success"></Button>
            <div class="flex justify-between">
                <div class="flex items-center gap-2">
                    <Checkbox name="checked" binary variant="filled" checked />
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
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

import { inject, defineAsyncComponent, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import { z } from "zod";
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { useToast } from 'primevue/usetoast';
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
// sign up
const initialValues = {
    account_name: '',
    password: ''
};

const loginSchema = zodResolver(z.
    object({
        account_name: z
            .string()
            .min(1, "Please enter your email / username / phone.")
            .max(30, "Acount name is too long.")
            .transform((v) => v.trim()),

        password: z
            .string()
            .min(8, "Password must be at least 8 characters.")
            .transform((v) => v.trim()),
    }));

// login
const toast = useToast();
const submit = (e) => {
    if (e.valid) {
        router.post('/login', JSON.parse(JSON.stringify(e.values)), {
            onSuccess: () => {
                setTimeout(() => {
                    router.reload({ only: ['auth', 'flash'] })
                }, 100);
                dialogRef.value.close()
            },
            onError: (errors) => {
                toast.add({
                    severity: 'error',
                    summary: 'Đăng nhập thất bại!',
                    detail: Object.values(errors).join(', '),
                    life: 3000,
                })
            },
        })
    }
}

const loginImg = '/img/login-bg.jpg';
</script>
