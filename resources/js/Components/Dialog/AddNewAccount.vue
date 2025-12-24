<template>
    <Form class="grid gap-x-2" v-slot="$form" :resolver :initialValues :validateOnValueUpdate="true" @submit="submit">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
            <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] items-start gap-x-4 gap-y-6">
                <label class="text-slate-950 text-nowrap pt-2">Họ và tên:</label>
                <div class="flex flex-col relative pb-4">
                    <InputText name="full_name" placeholder="Nhập họ và tên" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.full_name?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.full_name.error.message }}</Message>
                    </div>
                </div>

                <label class="text-slate-950 text-nowrap pt-2">Tên đăng nhập:</label>
                <div class="flex flex-col relative pb-4">
                    <InputText name="user_name" placeholder="Nhập tên đăng nhập" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.user_name?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.user_name.error.message }}</Message>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-[140px_1fr] items-start gap-x-4 gap-y-6">
                <label class="text-slate-950 text-nowrap pt-2">Email:</label>
                <div class="flex flex-col relative pb-4">
                    <InputText name="email" placeholder="example@mail.com" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.email.error.message }}</Message>
                    </div>
                </div>

                <label class="text-slate-950 text-nowrap pt-2">Phone:</label>
                <div class="flex flex-col relative pb-4">
                    <InputText name="phone" placeholder="" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.phone?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.phone.error.message }}</Message>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-[140px_1fr] items-start gap-x-4 gap-y-6">
                <label class="text-slate-950 text-nowrap pt-2">Vai trò:</label>
                <div class="flex flex-col relative pb-4">
                    <Select name="role" :options="roles" optionLabel="label" optionValue="value"
                        placeholder="Chọn vai trò" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.role?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.role.error.message }}</Message>
                    </div>
                </div>

                <label class="text-slate-950 text-nowrap pt-2">Giới tính:</label>
                <div class="flex flex-col relative pb-4">
                    <Select name="gender" :options="gender" optionLabel="label" optionValue="value"
                        placeholder="Chọn vai trò" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.gender?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.gender.error.message }}</Message>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-[140px_1fr] items-start gap-x-4 gap-y-6">
                <label class="text-slate-950 text-nowrap pt-2">Mật khẩu:</label>
                <div class="flex flex-col relative pb-4">
                    <Password name="password" fluid :feedback="false" toggleMask />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.password?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.password.error.message }}</Message>
                    </div>
                </div>

                <label class="text-slate-950 text-nowrap pt-2">Nhập lại mật khẩu:</label>
                <div class="flex flex-col relative pb-4">
                    <Password name="password_confirmation" fluid :feedback="false" toggleMask />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.password_confirmation?.invalid" severity="error" size="small"
                            variant="simple">
                            {{ $form.password_confirmation.error.message }}</Message>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-[140px_1fr] items-start gap-x-4 gap-y-6">
                <label class="text-slate-950 text-nowrap pt-2">Ghi chú:</label>
                <div class="flex flex-col relative pb-4">
                    <Textarea name="note" fluid />
                    <div class="absolute -bottom-2 left-0">
                        <Message v-if="$form.note?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.note.error.message }}</Message>
                    </div>
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
import { ref, reactive, inject, onMounted, watch } from 'vue';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { usePrimeVue } from 'primevue/config';
import { router } from '@inertiajs/vue3';

// component
import { Form, FormField } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Message from 'primevue/message';
import Button from 'primevue/button';

const dialogRef = inject('dialogRef');

const initialValues = {
    full_name: '',
    user_name: '',
    gender: '',
    phone: '',
    email: '',
    note: '',
    role: '',
    password: '',
    password_confirmation: '',
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

// roles
const roles = ref([
    {
        label: 'Khách hàng',
        value: 'customer'
    },
    {
        label: 'Nhân viên',
        value: 'employee'
    },
])

const gender = ref([
    {
        label: 'Nam',
        value: 'male'
    },
    {
        label: 'Nữ',
        value: 'female'
    }
])

const submit = (e) => {
    if (e.valid) {
        console.log(e.states);

        // Gửi form qua Inertia
        // router.post('/admin/customer/add-new', data, {
        //     forceFormData: true,
        //     onSuccess: () => {
        //         console.log('Tạo tài khoản thành công!')
        //     },
        // })
        // toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
        // dialogRef.value.close();
        // router.post('/register', JSON.parse(JSON.stringify(e.values)))
    }
}

const closeDialog = () => {
    dialogRef.value.close();
};

</script>