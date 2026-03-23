<template>
    <Form class="grid gap-x-2" v-slot="$form" :resolver :initialValues :validateOnValueUpdate="true" @submit="submit">
        <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] items-start gap-4">
            <label class="text-slate-950 text-nowrap pt-2">Ngân hàng:</label>
            <div class="flex flex-col relative pb-4">
                <InputText name="bank_name" placeholder="Nhập tên ngân hàng" fluid />
                <div class="absolute -bottom-2 left-0">
                    <Message v-if="$form.bank_name?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.bank_name.error.message }}</Message>
                </div>
            </div>

            <label class="text-slate-950 text-nowrap pt-2">Số tài khoản:</label>
            <div class="flex flex-col relative pb-4">
                <InputText name="account_number" placeholder="Số tài khoản" fluid />
                <div class="absolute -bottom-2 left-0">
                    <Message v-if="$form.account_number?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.account_number.error.message }}</Message>
                </div>
            </div>

            <label class="text-slate-950 text-nowrap pt-2">Chủ tài khoản:</label>
            <div class="flex flex-col relative pb-4">
                <InputText name="account_holder" placeholder="Tên chủ tài khoản" fluid />
                <div class="absolute -bottom-2 left-0">
                    <Message v-if="$form.account_holder?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.account_holder.error.message }}</Message>
                </div>
            </div>

            <label class="text-slate-950 text-nowrap pt-2">Ghi chú:</label>
            <div class="flex flex-col relative pb-4">
                <Textarea name="note" fluid />
                <div class="absolute -bottom-2 left-0">
                    <Message v-if="$form.note?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.note.error.message }}</Message>
                </div>
            </div>
        </div>
        <div class="mt-6 flex gap-y-4 gap-x-2 justify-end">
            <Button type="submit" label="Submit" severity="success" raised />
            <Button label="Cancel" severity="danger" raised @click="closeDialog" />
        </div>
    </Form>
</template>

<script setup>
import { inject } from 'vue';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { usePrimeVue } from 'primevue/config';
import { router } from '@inertiajs/vue3';

// component
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Message from 'primevue/message';
import Button from 'primevue/button';

const dialogRef = inject('dialogRef');

const initialValues = {
    bank_name: '',
    account_number: '',
    account_holder: '',
    note: '',
};

const resolver = zodResolver(z.
    object({
        full_name: z.string().trim()
            .min(1, { message: 'Họ tên là bắt buộc' })
            .max(30, { message: "Họ và tên không được vượt quá 30 ký tự." }),

        user_name: z
            .string()
            .trim()
            .min(8, { message: 'Tên đăng nhập phải có ít nhất 8 ký tự' })
            .max(16, { message: 'Tên đăng nhập không quá 16 ký tự' }),

        gender: z.enum(['male', 'female', 'other'], {
            message: 'Giới tính là bắt buộc',
        }),

        phone: z
            .string()
            .nullable()
            .optional()
            .refine(
                (val) => !val || /^[0-9+\-\s()]{10,12}$/.test(val),
                { message: 'Số điện thoại không hợp lệ (10-12 số)' }
            ),

        email: z
            .string()
            .trim()
            .min(8, { message: "Email ít nhất 8 ký tự" })
            .max(30, { message: "Email không được vượt quá 30 ký tự." })
            .refine((val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val), { message: "Email không hợp lệ." }),

        password: z
            .string()
            .min(8, { message: 'Mật khẩu phải có ít nhất 8 ký tự' })
            .max(16, { message: 'Mật khẩu quá dài' }),

        password_confirmation: z.string(),

        note: z.string().nullable().optional(),

        role: z.string().min(1, { message: 'Vui lòng chọn vai trò' }),
    })
    .refine((data) => data.password === data.password_confirmation, {
        path: ["password_confirmation"],
        message: "Mật khẩu xác nhận không khớp.",
    }));

const closeDialog = () => {
    dialogRef.value.close();
};
</script>