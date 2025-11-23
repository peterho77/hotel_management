<template>
    <Form v-slot="$form" :initialValues class="grid gap-8" :validateOnValueUpdate="true"
        :resolver="customPasswordResolver" @submit="onChangePassword">
        <FormField v-slot="$field" name="current_password" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Mật khẩu cũ:</label>
            <Password type="text" placeholder="Nhập mật khẩu cũ của bạn" :feedback="false" toggleMask fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="new_password" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Mật khẩu mới:</label>
            <Password type="text" placeholder="Nhập mật khẩu mới của bạn" :feedback="false" toggleMask fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="confirm_new_password" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Nhập lại mật khẩu mới:</label>
            <Password type="text" placeholder="Nhập mật khẩu cũ của bạn" :feedback="false" toggleMask fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <Button type="submit" class="mt-4" label="Đổi mật khẩu" severity="danger" fluid />
    </Form>
</template>

<script setup>
import { ref, reactive, inject, onMounted } from 'vue';

import { Form, FormField } from '@primevue/forms';
import { router } from '@inertiajs/vue3';
import Password from 'primevue/password';
import Message from 'primevue/message';
import Button from 'primevue/button';

const initialValues = reactive({
    current_password: '',
    new_password: '',
    confirm_new_password: ''
});

// pass user name
const dialogRef = inject('dialogRef');
const username = ref('');
onMounted(() => {
    const params = dialogRef.value.data;
    if (params) {
        username.value = params.username;
    }
});

// password validation
const customPasswordResolver = ({ values }) => {
    if (!values) {
        return { errors: {} };
    }

    const errors = {};

    // Lấy dữ liệu field
    const currentPassword = values.current_password;
    const newPassword = values.new_password;
    const confirmNewPassword = values.confirm_new_password;

    // Validate mật khẩu cũ
    if (currentPassword && currentPassword.length < 8) {
        errors.current_password = [
            { message: "Mật khẩu hiện tại phải có ít nhất 8 ký tự." }
        ];
    }

    // Validate mật khẩu mới
    if (newPassword && newPassword.length < 8) {
        errors.new_password = [
            { message: "Mật khẩu mới phải có ít nhất 8 ký tự." }
        ];
    }

    // Validate xác nhận mật khẩu
    if (confirmNewPassword) {
        if (confirmNewPassword.length < 8) {
            errors.confirm_new_password = [
                { message: "Xác nhận mật khẩu phải có ít nhất 8 ký tự." }
            ];
        } else if (newPassword && confirmNewPassword !== newPassword) {
            errors.confirm_new_password = [
                { message: "Xác nhận mật khẩu mới không trùng khớp." }
            ];
        }
    }

    return { errors };
};

const onChangePassword = (e) => {
    const payload = {};

    for (const [key, field] of Object.entries(e.states)) {
        if (key === "confirm_new_password") {
            payload["new_password_confirmation"] = field.value;
        } else {
            payload[key] = field.value;
        }
    }
    router.post(route('user.change_password', { user_name: username.value }), payload);
    dialogRef.value.close();
}

</script>