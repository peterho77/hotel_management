<template>
    <div class="signup-form">
        <div class="signup-form__header | py-2">
            <i class="close-icon | pi pi-times |  hover:text-neutral-500" @click="closeDialog()"></i>
        </div>
        <h2 class="signup-form__title | text-center fs-normal-heading mt-4">Create your account</h2>
        <Form v-slot="$form" :initialValues :resolver="userRegisterSchema" :validateOnValueUpdate="false"
            :validateOnBlur="true" @submit="submit" class="px-10 py-8 flex flex-col gap-y-6">
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <InputText id="user_name" name="user_name" fluid />
                    <label for="user_name">User name</label>
                </FloatLabel>
                <Message v-if="$form.user_name?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.user_name.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <InputText id="full_name" name="full_name" fluid />
                    <label for="full_name">Full name</label>
                </FloatLabel>
                <Message v-if="$form.full_name?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.full_name.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <InputText id="email" name="email" fluid />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.email.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <InputMask id="phone" name="phone" mask="9999 999 999" fluid />
                    <label for="phone">Phone (Optional)</label>
                </FloatLabel>
                <Message v-if="$form.phone?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.phone.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <Password id="password" name="password" fluid toggleMask />
                    <label for="password">Password</label>
                </FloatLabel>
                <Message v-if="$form.password?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.password.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <Password id="password_confirmation" name="password_confirmation" fluid toggleMask
                        :feedback="false" />
                    <label for="password_confirmation">Confirm password</label>
                </FloatLabel>
                <Message v-if="$form.password_confirmation?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.password_confirmation.error.message }}</Message>
            </div>
            <div class="flex items-center gap-2">
                <Checkbox binary variant="filled" checked />
                <label for="checked">I agree all statements in terms of service</label>
            </div>
            <Button label="Sign up" type="submit" severity="success"></Button>
            <div class="divider | flex items-center gap-3 text-gray-400">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="text-sm">or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>
            <div class="flex flex-col items-center gap-y-4">
                <span class="text-center">Sign up with</span>
                <div class="flex gap-3 items-center">
                    <i class="pi pi-facebook text-white bg-blue-600 p-3 rounded-full"></i>
                    <i class="pi pi-google text-white bg-red-500 p-3 rounded-full"></i>
                    <i class="pi pi-github text-white bg-gray-800 p-3 rounded-full"></i>
                </div>
            </div>
            <p class="text-center">I'm already a member!
                <Link class="text-blue-400" @click="showLoginForm()">Sign in</Link>
            </p>
        </Form>
    </div>
</template>

<script setup>
import { Form } from '@primevue/forms';
import { FormField } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import FloatLabel from 'primevue/floatlabel';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

import { inject, defineAsyncComponent, nextTick } from 'vue';
import { z } from "zod";
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { useDialog } from 'primevue/usedialog';
// register dynamic dialog
const dialog = useDialog();

// open login dialog
const loginForm = defineAsyncComponent(() => import('../Dialog/LoginForm.vue'));
const showLoginForm = async () => {
    closeDialog();
    await nextTick();
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
    });
}

const dialogRef = inject('dialogRef');
// close dialog
const closeDialog = () => {
    dialogRef.value.close();
}

// sign up
const initialValues = {
    user_name: '',
    full_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: ''
};

const userRegisterSchema = zodResolver(z
    .object({
        full_name: z
            .string()
            .min(1, "Họ và tên là bắt buộc.")
            .max(255, "Họ và tên không được vượt quá 255 ký tự."),

        user_name: z
            .string()
            .min(1, "Tên đăng nhập là bắt buộc.")
            .max(50, "Tên đăng nhập không được vượt quá 50 ký tự."),

        email: z
            .string()
            .min(1, "Email là bắt buộc.")
            .email("Email không hợp lệ.")
            .max(255, "Email không được vượt quá 255 ký tự."),

        phone: z
            .string()
            .refine((val) => val.replace(/\s+/g, "") === "" || /^[0-9]+$/.test(val.replace(/\s+/g, "")), {
                message: "Số điện thoại chỉ được chứa chữ số.",
            })
            .refine((val) => val.replace(/\s+/g, "").length <= 15, {
                message: "Số điện thoại không được vượt quá 15 ký tự.",
            }),
        
        password: z
            .string()
            .min(8, "Mật khẩu phải có ít nhất 8 ký tự."),

        password_confirmation: z
            .string(),
    })
    .refine((data) => data.password === data.password_confirmation, {
        path: ["password_confirmation"],
        message: "Mật khẩu xác nhận không khớp.",
    }));

const submit = (e) => {
    if (e.valid) {
        // router.post('/admin/room-type/add-new', JSON.parse(JSON.stringify(e.values)))
        // toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
        // dialogRef.value.close();
        console.log(e.values);
    }
}

</script>