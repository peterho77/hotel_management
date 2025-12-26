<template>
    <div class="add-new-review-section">
        <Form :initialValues :resolver="reviewResolver" class="grid gap-y-6" @submit="onBookingReview">
            <div class="grid gap-y-6">
                <div class="grid gap-x-6 gap-y-4">
                    <FormField v-slot="$field" name="rating" class="flex flex-col gap-1">
                        <label for="">Điểm</label>
                        <SelectButton v-bind="$field" :options="ratingOptions" optionLabel="name" optionValue="value" />
                    </FormField>
                    <FormField v-slot="$field" name="general_review" class="flex flex-col gap-1">
                        <label for="">Đánh giá chung</label>
                        <InputText v-bind="$field" type="text" />
                        <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">
                            {{ $field.error?.message }}</Message>
                    </FormField>
                    <FormField v-slot="$field" name="positive" class="flex flex-col gap-1">
                        <label for="">Những điều hài lòng</label>
                        <Textarea v-bind="$field" type="text" />
                        <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">
                            {{ $field.error?.message }}</Message>
                    </FormField>
                    <FormField v-slot="$field" name="negative" class="flex flex-col gap-1">
                        <label for="">Những điều chưa hài lòng</label>
                        <Textarea v-bind="$field" type="text" />
                        <Message v-if="$field?.invalid" severity="error" size="small" variant="simple">
                            {{ $field.error?.message }}</Message>
                    </FormField>
                    <div class="flex flex-col gap-1">
                        <label>Thêm ảnh</label>
                        <FileUpload v-model="reviewImages" @upload="onTemplatedUpload($event)" :multiple="true"
                            accept="image/*" :maxFileSize="1000000" @select="onSelectedFiles">
                            <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                                <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                    <div class="flex gap-2">
                                        <Button @click="chooseCallback()" icon="pi pi-images" rounded variant="outlined"
                                            severity="secondary"></Button>
                                        <Button @click="uploadEvent(uploadCallback)" icon="pi pi-cloud-upload" rounded
                                            variant="outlined" severity="success"
                                            :disabled="!files || files.length === 0"></Button>
                                        <Button @click="onClearTemplatingUpload(clearCallback)" icon="pi pi-times"
                                            rounded variant="outlined" severity="danger"
                                            :disabled="!files || files.length === 0"></Button>
                                    </div>
                                </div>
                            </template>
                            <template
                                #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                                <div class="flex flex-col gap-8 pt-4">
                                    <div v-if="files.length > 0">
                                        <h5>Pending</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                                class="border border-surface rounded">
                                                <div>
                                                    <img role="presentation" :alt="file.name" :src="file.objectURL"
                                                        class="w-full max-h-60 object-cover" />
                                                </div>
                                                <div class="p-4 items-center flex flex-col gap-2">
                                                    <span
                                                        class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                    <div>{{ formatSize(file.size) }}</div>
                                                    <Badge value="Pending" severity="warn" />
                                                    <Button icon="pi pi-times"
                                                        @click="onRemoveTemplatingFile(file, removeFileCallback, index)"
                                                        variant="outlined" rounded severity="danger" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="uploadedFiles.length > 0">
                                        <h5>Completed</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            <div v-for="(file, index) of uploadedFiles"
                                                :key="file.name + file.type + file.size"
                                                class="rounded-border border border-surface">
                                                <div>
                                                    <img role="presentation" :alt="file.name" :src="file.objectURL"
                                                        class="w-full max-h-60 object-cover" />
                                                </div>
                                                <div class="p-4 items-center flex flex-col gap-2">
                                                    <span
                                                        class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                    <div>{{ formatSize(file.size) }}</div>
                                                    <Badge value="Completed" class="mt-4" severity="success" />
                                                    <Button icon="pi pi-times"
                                                        @click="removeUploadedFileCallback(index)" variant="outlined"
                                                        rounded severity="danger" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </FileUpload>
                    </div>
                    <Button type="submit" label="Gửi" />
                </div>
            </div>
        </Form>
    </div>
</template>

<script setup>
import { ref, reactive, inject, onMounted, watch } from 'vue';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { usePrimeVue } from 'primevue/config';
import { router } from '@inertiajs/vue3';   

// flash message
import { useFlashToast } from "@/Composables/useFlashToast";
useFlashToast();

// component
import { Form, FormField } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Message from 'primevue/message';
import Button from 'primevue/button';
import SelectButton from 'primevue/selectbutton';
import FileUpload from 'primevue/fileupload';
import Badge from 'primevue/badge';

const dialogRef = inject('dialogRef');

// booking id and user id
const bookingId = ref();
const userId = ref();
const userName = ref();

onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        bookingId.value = params.bookingId || null;
        userId.value = params.userId || null;
        userName.value = params.userId || null;
    }
    console.log(bookingId.value, userId.value);
})

// rating options
const ratingOptions = ref(
    Array.from({ length: 10 }).map((_, idx) => ({
        name: `${idx + 1}`,
        value: idx + 1
    }))
);

// form
const initialValues = reactive({
    general_review: '',
    positive: '',
    negative: '',
    rating: null,
});

const reviewResolver = zodResolver(
    z.object({
        general_review: z.string().min(1, { message: 'Text is required.' }),
        positive: z.string().min(1, { message: 'Positive is required.' }),
        negative: z.string().min(1, { message: 'Negative is required.' }),
        rating: z.number().min(1, { message: 'Rating is required.' })
    })
);

// change image name
function slugifyVietnamese(text) {
    return text
        .normalize('NFD') // tách dấu
        .replace(/[\u0300-\u036f]/g, '') // xóa dấu
        .replace(/đ/g, 'd').replace(/Đ/g, 'D') // xử lý chữ đ/Đ
        .toLowerCase()
        .replace(/\s+/g, '_') // thay khoảng trắng thành "_"
        .replace(/[^a-z0-9_]/g, '') // xóa ký tự đặc biệt
}
function renameImageFile(file, name, index) {
    if (!file && !name) return file
    const ext = file.name.split('.').pop()
    const safeName = slugifyVietnamese(name)
    const newName = `${safeName}_${index + 1}.${ext}`
    return new File([file], newName, { type: file.type })
}

// change file name whenever change name of the room type
watch(
    () => bookingId,
    (newValue) => {
        if (reviewImages.length) {
            reviewImages.value = reviewImages.value.map((image, index) => renameImageFile(image, `booking_${newValue}`, index))
        }
    }
)

const $primevue = usePrimeVue();
const reviewImages = ref([]);

const onSelectedFiles = (event) => {
    reviewImages.value = event.files.map((file, index) => renameImageFile(file, `bookingReview${bookingId.value}`, index))
    console.log(reviewImages);
};

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);

    reviewImages.value.splice(index, 1);
    if (reviewImages.value.length) {
        reviewImages.value = reviewImages.value.map((image, index) => renameImageFile(image, newName, index))
    }
    console.log(reviewImages);
};

const onClearTemplatingUpload = (clear) => {
    clear();
};

const uploadEvent = (callback) => {
    callback();
};

const onTemplatedUpload = () => {
    toast.add({ severity: "info", summary: "Success", detail: "File Uploaded", life: 3000 });
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 3;
    const sizes = $primevue.config.locale.fileSizeTypes;

    if (bytes === 0) {
        return `0 ${sizes[0]}`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizes[i]}`;
};

const onBookingReview = (e) => {
    if (e.valid) {
        console.log(e.values);
        console.log(reviewImages.value);
        if (e.valid) {
            const data = new FormData();

            // duyệt qua toàn bộ field trong form
            for (const key in e.values) {
                const value = e.values[key];

                // Nếu là date object (ví dụ birth_date)
                if (value instanceof Date) {
                    data.append(key, value.toISOString().split('T')[0]) // => "2025-10-26"
                }
                // Còn lại là text / number
                else {
                    data.append(key, value ?? '')
                }
            }
            if (Array.isArray(reviewImages.value) && reviewImages.value.length && reviewImages.value[0] instanceof File) {
                reviewImages.value.forEach((file, idx) => {
                    data.append(`images[${idx}]`, file, file.name);
                });
            }
            data.append(`booking_id`, bookingId.value);
            //Gửi form qua Inertia
            router.post(route('review.store', { user_name: userName.value }), data, {
                forceFormData: true,
                onSuccess: () => {
                    console.log('Gửi đánh giá thành công!')
                },
            })
            dialogRef.value.close();
        }
    }
}

</script>