<template>
    <Form v-slot="$form" class="grid gap-8" :validateOnValueUpdate="true" :resolver="customUserProfileResolver">
        <FormField v-slot="$field" name="full_name" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Họ và tên</label>
            <InputText v-model="userInfo.full_name" type="text" placeholder="Nhập họ và tên" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="gender" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Giới tính</label>
            <Select v-model="userInfo.gender" :options="genders" optionLabel="label" optionValue="value"
                placeholder="Chọn giới tính" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField v-slot="$field" name="birth_date" class="flex flex-col gap-1">
            <label class="text-slate-950" for="">Ngày sinh</label>
            <DatePicker v-model="userInfo.birth_date" placeholder="Nhập ngày sinh" dateFormat="dd/mm/yy" fluid />
            <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">{{ $field.error?.message }}
            </Message>
        </FormField>
        <FormField class="flex flex-col gap-1">
            <label class="text-gray-500" for="">Số điện thoại</label>
            <InputText v-model="userInfo.phone" fluid disabled />
        </FormField>
        <FormField class="flex flex-col gap-1">
            <label class="text-gray-500" for="">Email</label>
            <InputText v-model="userInfo.email" fluid disabled />
        </FormField>

        <div class="flex items-center justify-between mt-4 gap-x-4">
            <Button class="flex-1" label="Thiết lập lại" severity="info" @click="resetInitialValues" />
            <Button class="flex-1" type="submit" label="Cập nhật thông tin" severity="danger" />
        </div>
    </Form>
</template>

<script setup>
import { ref, reactive, inject, watch, onMounted } from 'vue';

import { Form, FormField } from '@primevue/forms';
import { router } from '@inertiajs/vue3';
import Message from 'primevue/message';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
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

// pass user infor
const dialogRef = inject('dialogRef');

const userInfo = ref({});

const initialValues = reactive({
    full_name: '',
    gender: '',
    birth_date: ''
})

const originalValues = ref({});

// lấy dữ liệu ban đầu khi dialog mounted
onMounted(() => {
    const params = dialogRef.value.data;
    if (params) {
        userInfo.value = params.userInfo;

        initialValues.full_name =  userInfo.value.full_name ?? '';
        initialValues.gender =  userInfo.value.gender ?? '';
        initialValues.birth_date =  userInfo.value.birth_date ?? '';

        // lưu bản sao gốc để reset về sau
        originalValues.value = JSON.parse(JSON.stringify(params.userInfo));
        console.log(originalValues.value);
    }
});

const resetInitialValues = () => {
    Object.keys(originalValues.value).forEach(key => {
        userInfo.value[key] = originalValues.value[key];
    });
};

// validation
const customUserProfileResolver = ({ values }) => {
    const errors = {};

    // Validate full_name
    if (!values.full_name || values.full_name.trim() === '') {
        errors.full_name = [{ message: 'Họ và tên không được để trống.' }];
    } else if (values.full_name.length > 30) {
        errors.full_name = [{ message: 'Họ và tên không được vượt quá 30 ký tự.' }];
    }

    // Validate gender
    if (!values.gender) {
        errors.gender = [{ message: 'Giới tính không được để trống.' }];
    }

    // Validate birth_date
    if (!values.birth_date) {
        errors.birth_date = [{ message: 'Ngày sinh không được để trống.' }];
    }

    return { errors };
}
</script>