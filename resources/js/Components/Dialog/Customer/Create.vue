<template>
    <Form class="grid grid-cols-[200px_1fr] gap-x-2" v-slot="$form" :resolver :initialValues
        validateOnUpdate="false" :validateOnBlur="true" @submit="submit">
        <div class="upload-preview-img group relative">
            <label class="upload-btn">
                <img :src="src || '/img/default-blank-img.jpg'" alt="" class="preview-img | object-cover" />
                <button v-if="src" @click.stop="removeImage"
                    class="remove-btn | absolute top-1 right-1 bg-black/60 text-white rounded-full w-5 h-5 p-2 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="pi pi-times" style="font-size:0.75rem"></i>
                </button>
                <div v-if="!src"
                    class="absolute inset-0  flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                    <FileUpload mode="basic" v-model="form.avatar" @select="onFileSelect" customUpload auto
                        chooseLabel="Thêm" accept="image/*"
                        class="p-button-outlined text-gray-700 border-0 border-gray-400 hover:bg-gray-100 !bg-transparent" />
                </div>
            </label>
        </div>
        <div class="grid gap-y-4">
            <div class="flex flex-wrap gap-4">
                <label for="">Customer Type:</label>
                <div class="flex items-center gap-4">
                    <div v-for="item in customerTypeList" class="flex gap-2">
                        <RadioButton name="customer_type_id" :value="item.id" />
                        <label for="ingredient1">{{ item.name }}</label>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-y-5 gap-x-4">
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <InputText id="full_name" v-model="form.full_name" name="full_name" ref="user_name" fluid />
                        <label for="full_name">Full name</label>
                    </FloatLabel>
                    <Message v-if="$form.full_name?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.full_name.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <InputText id="phone" name="phone" fluid />
                        <label for="phone">Phone</label>
                    </FloatLabel>
                    <Message v-if="$form.phone?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.phone.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <InputText id="birth_date" name="birth_date" fluid />
                        <label for="birth_date">Birth date</label>
                    </FloatLabel>
                    <Message v-if="$form.birth_date?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.birth_date.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <InputText id="address" name="address" fluid />
                        <label for="address">Address</label>
                    </FloatLabel>
                    <Message v-if="$form.address?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.address.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1 ml-2">
                    <div class="flex flex-wrap items-center gap-4">
                        <label for="">Gender:</label>
                        <div class="flex items-center gap-x-2">
                            <RadioButton name="gender" value="male" />
                            <label for="gender">Male</label>
                        </div>
                        <div class="flex items-center gap-x-2">
                            <RadioButton name="gender" value="female" />
                            <label for="gender">Female</label>
                        </div>
                    </div>
                    <Message v-if="$form.gender?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.gender.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <InputText id="email" name="email" fluid />
                        <label for="email">Email: </label>
                    </FloatLabel>
                    <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.email.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <Select name="customer_group_id" :options="customerGroupList" optionValue="id"
                            optionLabel="name" fluid />
                        <label for="customer_group_id">Customer group:</label>
                    </FloatLabel>
                    <Message v-if="$form.customer_group_id?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.customer_group_id.error.message }}</Message>
                </div>
                <div class="flex flex-col gap-y-1">
                    <FloatLabel variant="on">
                        <Textarea name="note" rows="4" cols="30" autoResize fluid />
                        <label for="note">Note</label>
                    </FloatLabel>
                </div>
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
import { ref, reactive, inject, onMounted, watch } from "vue";
import { Form } from '@primevue/forms';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Message from 'primevue/message';
import FloatLabel from 'primevue/floatlabel';
import RadioButton from 'primevue/radiobutton';

import { router } from '@inertiajs/vue3';
import { usePrimeVue } from 'primevue/config';

// zod validate
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';

// form 
const dialogRef = inject('dialogRef');

const customerTypeList = ref([])
const customerGroupList = ref([])

const initialValues = {
    full_name: '',
    gender: '',
    phone: '',
    birth_date: '',
    address: '',
    email: '',
    customer_type_id: null,
    customer_group_id: null,
    note: '',
    avatar: ''
};

const resolver = zodResolver(z.object({
    full_name: z.string().min(1, { message: 'Họ tên là bắt buộc' }),

    gender: z.enum(['male', 'female', 'other'], {
        message: 'Giới tính là bắt buộc',
    }),

    birth_date: z.preprocess((arg) => {
        if (!arg) return null
        if (typeof arg === 'string') {
            const d = new Date(arg)
            return isNaN(d.getTime()) ? null : d
        }
        return arg
    }, z.date().nullable().optional()),

    address: z.string().nullable().optional(),

    email: z
        .string()
        .trim()
        .min(8, { message: "Email ít nhất 8 ký tự" })
        .max(30, { message: "Email không được vượt quá 30 ký tự." })
        .refine((val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val), { message: "Email không hợp lệ." })
        .nullable()
        .optional(),

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

const submit = (e) => {
    if (e.valid) {
        e.values['avatar'] = form.avatar;
        const data = new FormData()

        // duyệt qua toàn bộ field trong form
        for (const key in e.values) {
            const value = e.values[key]

            // Nếu là file (ví dụ avatar)
            if (value instanceof File) {
                data.append(key, value, value.name)
            }
            // Nếu là date object (ví dụ birth_date)
            else if (value instanceof Date) {
                data.append(key, value.toISOString().split('T')[0]) // => "2025-10-26"
            }
            // Còn lại là text / number
            else {
                data.append(key, value ?? '')
            }
        }
        for (let [k, v] of data.entries()) {
            console.log(k, v);
        }

        // Gửi form qua Inertia
        // router.post('/manager/customer/add-new', data, {
        //     forceFormData: true,
        //     onSuccess: () => {
        //         console.log('Tạo khách hàng thành công!')
        //     },
        // })
        // toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
        // dialogRef.value.close();
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

// format full name and date to rename customer avatar
const form = reactive({
    full_name: '',
    avatar: null
})
const src = ref(null);

function slugifyVietnamese(text) {
    return text
        .normalize('NFD') // tách dấu
        .replace(/[\u0300-\u036f]/g, '') // xóa dấu
        .replace(/đ/g, 'd').replace(/Đ/g, 'D') // xử lý chữ đ/Đ
        .toLowerCase()
        .replace(/\s+/g, '_') // thay khoảng trắng thành "_"
        .replace(/[^a-z0-9_]/g, '') // xóa ký tự đặc biệt
}

function formatDateNow() {
    const d = new Date()
    const pad = (n) => n.toString().padStart(2, '0')
    return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}_${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`
}

function renameAvatarFile(file, fullName) {
    if (!file && !fullName) return file
    const ext = file.name.split('.').pop()
    const time = formatDateNow()
    const safeName = slugifyVietnamese(fullName)
    const newName = `${safeName}_${time}.${ext}`
    return new File([file], newName, { type: file.type })
}

watch(
    () => form.full_name,
    (newName) => {
        if (form.avatar) {
            form.avatar = renameAvatarFile(form.avatar, newName)
        }
    }
)

function onFileSelect(event) {
    const file = event.files[0];

    // new avatar name 
    const renamedFile = renameAvatarFile(file, form.full_name);
    form.avatar = renamedFile
    console.log(form.avatar);

    const reader = new FileReader();

    reader.onload = async (e) => {
        src.value = e.target.result;
    };

    reader.readAsDataURL(renamedFile);
}

const removeImage = () => {
    form.avatar = null
    src.value = null
}

</script>