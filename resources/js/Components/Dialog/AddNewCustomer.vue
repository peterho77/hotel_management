<template>
    <Form class="grid grid-cols-[200px_1fr] gap-x-2" ref="newCustomerForm" v-slot="$form" :resolver :initialValues
        validateOnUpdate="false" :validateOnBlur="true" @submit="submit">
        <div class="upload-preview-img group relative">
            <label class="upload-btn">
                <img :src="src || '/img/default-blank-img.jpg'" alt="" class="preview-img" />
                <button v-if="src" @click.stop="removeImage"
                    class="remove-btn | absolute top-1 right-1 bg-black/60 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="pi pi-times text-xs"></i>
                </button>
                <div v-if="!src"
                    class="absolute inset-0  flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                    <FileUpload mode="basic" @select="onFileSelect" customUpload auto chooseLabel="Thêm"
                        accept="image/*"
                        class="p-button-outlined text-gray-700 border-0 border-gray-400 hover:bg-gray-100 !bg-transparent" />
                </div>
            </label>
        </div>
        <div class="grid gap-y-4">
            <div class="flex flex-wrap gap-4">
                <label for="">Customer Type:</label>
                <div class="flex items-center gap-4">
                    <div v-for="item in customerTypeList" class="flex gap-2">
                        <RadioButton v-model="ingredient" inputId="ingredient1" name="pizza" value="Cheese" />
                        <label for="ingredient1">{{ item.name }}</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label for="name">Full name:</label>
                <InputText id="full_name" name="full_name" />
                <Message v-if="$form.full_name?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.full_name.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-2">
                <label for="phone">Phone: </label>
                <InputText id="phone" name="phone" />
                <Message v-if="$form.phone?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.phone.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-2">
                <label for="birth_date">Birth date: </label>
                <InputText id="birth_date" name="birth_date" />
                <Message v-if="$form.birth_date?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.birth_date.error.message }}</Message>
            </div>
            <div class="flex flex-wrap gap-4">
                <label for="">Gender:</label>
                <div class="flex items-center gap-x-2">
                    <RadioButton name="gender" value="male" />
                    <label for="ingredient1">Male</label>
                </div>
                <div class="flex items-center gap-x-2">
                    <RadioButton name="gender" value="male" />
                    <label for="ingredient1">Female</label>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label for="email">Email: </label>
                <InputText id="email" name="email" />
                <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.birth_date.error.message }}</Message>
            </div>
            <div class="flex flex-col gap-2">
                <label for="note">Note</label>
                <Textarea name="note" rows="4" cols="30" autoResize />
            </div>
            <div class="flex flex-col gap-2">
                <label for="branch">Customer group:</label>
                <Select name="branch_id" :options="customerGroupList" optionValue="id" optionLabel="name"
                    placeholder="Select branch" class="w-full" />
                <Message v-if="$form.branch_id?.invalid" severity="error" size="small" variant="simple">
                    {{ $form.branch_id.error.message }}</Message>
            </div>
            <div class="mt-3 flex gap-y-4 gap-x-2 justify-end">
                <Button type="submit" label="Submit" severity="success" raised />
                <Button label="Cancel" severity="danger" raised @click="closeDialog" />
            </div>
        </div>
    </Form>
    <Toast />
</template>

<style scoped>
.upload-preview-img {
    width: 200px;
    height: 200px;
    padding: var(--size-500);
    position: relative;
    display: flex;
    flex: 0 0 100px;
}

.upload-btn {
    position: relative;
    border: 1px dashed #bebfc2;
    border-radius: 4px;
    overflow: hidden;
    width: 100%;
    cursor: pointer;
}

.upload-btn .preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>

<script setup>
import { ref, inject, onMounted } from "vue";
import { Form } from '@primevue/forms';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Message from 'primevue/message';

import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';


import { router } from '@inertiajs/vue3';
import { usePrimeVue } from 'primevue/config';
import { useToast } from "primevue/usetoast";

// zod validate
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';

// form 
const dialogRef = inject('dialogRef');

const customerTypeList = ref([])
const customerGroupList = ref([])

const toast = useToast();

const resolver = zodResolver(
    z.object({
        name: z.string().min(1, { message: 'Tên phòng là bắt buộc.' }),
        area: z.string().min(1, { message: 'Diện tích là bắt buộc.' }),
        status: z
            .string()
            .min(1, { message: 'Trạng thái là bắt buộc.' })
            .max(50, { message: 'Tối đa 50 ký tự' }),
        room_type_id: z.coerce.number().min(1, { message: 'Chọn loại phòng.' }),
        branch_id: z.coerce.number().min(1, { message: 'Chọn chi nhánh.' })
    })
)

const initialValues = {
    full_name: '',
    gender: '',
    phone: '',
    birth_date: '',
    address: '',
    email: '',
    customer_type_id: 1,
    customer_group_id: 2,
    note: ''
};

const emptyToNull = (schema) =>
    z.preprocess((v) => (v === '' ? null : v), schema)

const customerSchema = zodResolver(z.object({
    full_name: z.string().min(1, { message: 'Họ tên là bắt buộc' }),

    gender: z.enum(['male', 'female', 'other']).nullable().optional(),

    birth_date: z.preprocess((arg) => {
        if (!arg) return null
        if (typeof arg === 'string') {
            const d = new Date(arg)
            return isNaN(d.getTime()) ? null : d
        }
        return arg
    }, z.date().nullable().optional()),

    address: z.string().nullable().optional(),

    email: emptyToNull(
        z.string().email({ message: 'Email không hợp lệ' })
    ).nullable().optional(),

    phone: z
        .string()
        .nullable()
        .optional()
        .refine(
            (val) => !val || /^[0-9+\-\s()]+$/.test(val),
            { message: 'Số điện thoại không hợp lệ' }
        ),

    customer_type_id: z.number().default(1).optional(),
    customer_group_id: z.number().default(2).optional(),
    note: z.string().nullable().optional(),
}));

const newCustomerForm = ref(null);

const submit = (e) => {
    if (e.valid) {
        // router.post('/admin/room/add-new', JSON.parse(JSON.stringify(e.values)))
        // toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
        // dialogRef.value.close();
        console.log(e.values);
    }
}

// pass data from dynamic dialog primevue
onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        customerTypeList.value = params.customerTypeList || []
        customerGroupList.value = params.customerGroupList || []
    }
})

const closeDialog = () => {
    dialogRef.value.close();
};

// upload customer avatar
const src = ref(null);

function onFileSelect(event) {
    const file = event.files[0];
    const reader = new FileReader();

    reader.onload = async (e) => {
        src.value = e.target.result;
    };

    reader.readAsDataURL(file);
}

const removeImage = () => {
    src.value = null
}

</script>