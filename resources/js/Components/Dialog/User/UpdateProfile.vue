<template>
    <Form v-slot="$form" :initialValues ref="formRef" class="grid gap-8" :resolver="customUserProfileResolver"
        :validateOnValueUpdate="false" :validateOnBlur="true" @submit="onUpdateUserProfile">
        <FormField v-slot="$field" name="full_name" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Họ và tên</label>
            <InputText v-model="$field.value" type="text" placeholder="Nhập họ và tên" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="gender" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Giới tính</label>
            <Select v-model="$field.value" :options="genders" optionLabel="label" optionValue="value"
                placeholder="Chọn giới tính" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="birth_date" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Ngày sinh</label>
            <DatePicker v-model="$field.value" placeholder="Nhập ngày sinh" dateFormat="dd/mm/yy" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="phone" class="flex flex-col gap-1">
            <label class="text-gray-500" for="">Số điện thoại</label>
            <InputText v-model="$field.value" fluid disabled />
        </FormField>
        <FormField v-slot="$field" name="email" class="flex flex-col gap-1">
            <label class="text-gray-500" for="">Email</label>
            <InputText v-model="$field.value" fluid disabled />
        </FormField>

        <div class="flex items-center justify-between mt-4 gap-x-4">
            <Button class="flex-1" label="Thiết lập lại" severity="info" @click="$form.reset()" />
            <Button class="flex-1" type="submit" label="Cập nhật thông tin" severity="danger" />
        </div>
    </Form>
</template>

<script setup>
import { ref, reactive, inject, watch, onMounted, nextTick } from 'vue';

import { Form, FormField } from '@primevue/forms';
import { router } from '@inertiajs/vue3';
import Message from 'primevue/message';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';

const genders = ref([
    {
        label: 'Nam',
        value: 'male'
    },
    {
        label: 'Nữ',
        value: 'female'
    }
])

// lỗi lùi 1 ngày
function parseDate(dateStr) {
    if (!dateStr) return null;
    const dateOnly = dateStr.split('T')[0]; // "2025-10-31"
    const [y, m, d] = dateOnly.split('-').map(Number);
    return new Date(y, m - 1, d); // tạo Date local, không lệch
}

// pass user infor
const dialogRef = inject('dialogRef');

const username = ref('');

const initialValues = ref({
    full_name: '',
    gender: '',
    birth_date: '',
    phone: '',
    email: '',
})

// lấy dữ liệu ban đầu khi dialog mounted
onMounted(() => {
    const params = dialogRef.value.data;
    if (params) {
        username.value = params.username;
        const userData = {
            full_name: params.userInfo.full_name ?? "",
            gender: params.userInfo.gender ?? "",
            birth_date: parseDate(params.userInfo.birth_date) ?? "",
            email: params.userInfo.email,
            phone: params.userInfo.phone ?? ""
        };
        console.log(userData)

        // Gán lại initial values để dùng sau này
        initialValues.value = {
            full_name: userData.full_name || "",
            gender: userData.gender || null,
            birth_date: userData.birth_date || null,
            email: userData.email,
            phone: userData.phone ?? ""
        };

        // IMPORTANT: cập nhật lại giá trị vào FormField
        nextTick(() => {
            formRef.value.setValues(userData);
        });
    }
});

// validation
const formRef = ref();

const customUserProfileResolver = ({ values }) => {
    const errors = {};

    // FULL NAME
    if (!values.full_name || values.full_name.trim() === "") {
        errors.full_name = [
            { message: "Họ và tên không được để trống." }
        ];
    } else if (values.full_name.length > 30) {
        errors.full_name = [
            { message: "Họ và tên không được vượt quá 30 ký tự." }
        ];
    }

    // GENDER
    if (!values.gender) {
        errors.gender = [
            { message: "Giới tính không được để trống." }
        ];
    }

    // BIRTH DATE
    if (!values.birth_date) {
        errors.birth_date = [
            { message: "Ngày sinh không được để trống." }
        ];
    }

    return {
        errors
    };
};

// submit
const onUpdateUserProfile = (e) => {
    const payload = {};

    for (const [key, field] of Object.entries(e.states)) {
        if (key === 'birth_date' && field.value instanceof Date) {
            const d = field.value;
            payload.birth_date = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        } else {
            payload[key] = field.value;
        }
    }
    console.log(payload);
    router.post(
        route('user.update_profile', { user_name: username.value }),
        payload,
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['auth'] });

                setTimeout(() => {
                    dialogRef.value.close();
                }, 1000);
            }
        }
    );
}
</script>