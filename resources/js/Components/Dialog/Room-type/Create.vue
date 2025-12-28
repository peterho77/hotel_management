<template>
    <div class="card">
        <Tabs value="0">
            <TabList>
                <Tab value="0">Thông tin</Tab>
                <Tab value="1">Hình ảnh, mô tả</Tab>
                <Tab value="2">Danh sách phòng</Tab>
            </TabList>
            <TabPanels>
                <Form v-slot="$form" :resolver :initialValues :validateOnUpdate="false"
                    :validateOnBlur="true" @submit="submit">
                    <TabPanel value="0">
                        <div class="grid gap-y-4">
                            <div class="flex flex-col gap-2">
                                <label for="name">Name</label>
                                <InputText v-model="form.name" name="name" />
                                <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                                    {{ $form.name.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="quantity">Quantity</label>
                                <InputNumber name="total_quantity" :maxFractionDigits="0" :min="1" :max="10" showButtons
                                    default="1" />
                                <Message v-if="$form.quantity?.invalid" severity="error" size="small" variant="simple">
                                    {{ $form.quantity.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="base_price">Base price</label>
                                <InputNumber name="base_price" :maxFractionDigits="0" mode="currency" currency="VND"
                                    locale="vi-VN" showButtons buttonLayout="horizontal" :step="5000" />
                                <Message v-if="$form.base_price?.invalid" severity="error" size="small"
                                    variant="simple">
                                    {{ $form.base_price.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="hourly_rate">Hourly rate</label>
                                <InputNumber name="hourly_rate" :maxFractionDigits="0" mode="currency" currency="VND"
                                    locale="vi-VN" showButtons buttonLayout="horizontal" :step="5000" />
                                <Message v-if="$form.hourly_rate?.invalid" severity="error" size="small"
                                    variant="simple">
                                    {{ $form.hourly_rate.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="overnight_rate">Overnight rate</label>
                                <InputNumber name="overnight_rate" :maxFractionDigits="0" mode="currency" currency="VND"
                                    locale="vi-VN" showButtons buttonLayout="horizontal" :step="5000" />
                                <Message v-if="$form.overnight_rate?.invalid" severity="error" size="small"
                                    variant="simple">
                                    {{ $form.overnight_rate.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="full_day_rate">Full day rate</label>
                                <InputNumber name="full_day_rate" :maxFractionDigits="0" mode="currency" currency="VND"
                                    locale="vi-VN" showButtons buttonLayout="horizontal" :step="5000" />
                                <Message v-if="$form.full_day_rate?.invalid" severity="error" size="small"
                                    variant="simple">
                                    {{ $form.full_day_rate.error.message }}</Message>
                            </div>
                            <Fieldset legend="Capacity">
                                <div class="p-2 grid grid-cols-[auto_1fr] items-center gap-x-5">
                                    <span>Tối đa:</span>
                                    <div class="flex gap-x-2 items-center">
                                        <div class="flex items-center gap-x-1">
                                            <InputNumber v-model="form.max_adults" name="max_adults" showButtons
                                                :min="1" :pt="{
                                                    incrementButton: { class: 'bg-gray-100' },
                                                    decrementButton: { class: 'bg-gray-100' },
                                                    incrementIcon: { class: 'pi pi-plus' },
                                                    decrementIcon: { class: 'pi pi-minus' },

                                                }" />
                                            <span class="w-2/3">người lớn</span>
                                        </div>
                                        <span>và</span>
                                        <div class="flex items-center gap-x-1">
                                            <InputNumber v-model="form.max_children" name="max_children" showButtons
                                                :min="1" :pt="{
                                                    incrementButton: { class: 'bg-gray-100' },
                                                    decrementButton: { class: 'bg-gray-100' },
                                                    incrementIcon: { class: 'pi pi-plus' },
                                                    decrementIcon: { class: 'pi pi-minus' },

                                                }" />
                                            <span class="w-2/3">trẻ em</span>
                                        </div>
                                    </div>
                                </div>
                            </Fieldset>
                            <div class="flex flex-col gap-2">
                                <label for="status">Status</label>
                                <Select name="status" :options="statusList" optionValue="value" optionLabel="name"
                                    placeholder="Select status" class="w-full" />
                                <Message v-if="$form.status?.invalid" severity="error" size="small" variant="simple">
                                    {{ $form.status.error.message }}</Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="branch_id">Branch</label>
                                <MultiSelect :options="branchList" name="branch_id" display="chip" optionLabel="name"
                                    optionValue="id" filter placeholder="Chọn chi nhánh" :maxSelectedLabels="3" fluid>
                                </MultiSelect>
                                <Message v-if="$form.branch_id?.invalid" severity="error" size="small" variant="simple">
                                    {{ $form.branch_id.error.message }}</Message>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel value="1">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="upload-img">Upload images:</label>
                                <FileUpload v-model="form.images" :multiple="true"
                                    accept="image/*" :maxFileSize="1000000" @select="onSelectedFiles">
                                    <template #header="{ chooseCallback, clearCallback, files }">
                                        <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                            <div class="flex gap-2">
                                                <Button @click="chooseCallback()" icon="pi pi-images" rounded
                                                    variant="outlined" severity="secondary"></Button>
                                                <Button @click="onClearTemplatingUpload(clearCallback)"
                                                    icon="pi pi-times" rounded variant="outlined" severity="danger"
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
                                                    <div v-for="(file, index) of files"
                                                        :key="file.name + file.type + file.size"
                                                        class="border border-surface rounded">
                                                        <div
                                                            class="w-full aspect-5/6 bg-surface-100 dark:bg-surface-800">
                                                            <img role="presentation" :alt="file.name"
                                                                :src="file.objectURL"
                                                                class="w-full h-full object-cover" />
                                                        </div>
                                                        <div class="p-2 items-center flex flex-col gap-2">
                                                            <span
                                                                class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name.split(' ')[0] }}</span>
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
                                                        <div class="w-full aspect-4/3 overflow-hidden">
                                                            <img role="presentation" :alt="file.name"
                                                                :src="file.objectURL"
                                                                class="w-full h-full  object-cover" />
                                                        </div>
                                                        <div class="p-4 items-center flex flex-col gap-2">
                                                            <span
                                                                class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name.split(' ')[0] }}</span>
                                                            <div>{{ formatSize(file.size) }}</div>
                                                            <Badge value="Completed" class="mt-4" severity="success" />
                                                            <Button icon="pi pi-times"
                                                                @click="removeUploadedFileCallback(index)"
                                                                variant="outlined" rounded severity="danger" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template #empty>
                                        <div class="flex items-center justify-center flex-col">
                                            <i
                                                class="pi pi-cloud-upload border! border-gray-300! rounded-full! p-8! text-4xl! text-gray-500!" />
                                            <p class="mt-6 mb-0">Drag and drop files to here to upload.</p>
                                        </div>
                                    </template>
                                </FileUpload>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="description">Description</label>
                                <Textarea name="description" rows="4" cols="30" autoResize />
                                <Message v-if="$form.description?.invalid" severity="error" size="small"
                                    variant="simple">
                                    {{ $form.description.error.message }}</Message>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel value="2">
                        <DataTable :value="roomList">
                            <Column field="name" header="Room Name" />
                            <Column field="bed_type" header="Bed Type" />
                            <Column field="status" header="Status" />
                        </DataTable>
                    </TabPanel>
                    <div class="flex gap-y-3 gap-x-2 justify-end mt-4">
                        <Button type="submit" label="Submit" severity="success" raised />
                        <Button label="Cancel" severity="danger" raised @click="closeDialog" />
                    </div>
                </Form>
            </TabPanels>
        </Tabs>
    </div>

    <Toast />
</template>

<style scoped>
:deep(.p-component.p-inputtext.p-inputnumber-input) {
    width: 6rem !important;
    /* tương đương w-24 */
}
</style>

<script setup>
import { ref, reactive, inject, onMounted, watch } from "vue";
import { usePrimeVue } from 'primevue/config';
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import MultiSelect from 'primevue/multiselect';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Fieldset from 'primevue/fieldset';

// tabs
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

// upload img
import FileUpload from 'primevue/fileupload';

// badge
import Badge from 'primevue/badge';

// data table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

// zod validate
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';

// select status
const statusList = ref([{ name: 'Đang kinh doanh', value: 'active' }, { name: 'Ngừng kinh doanh', value: 'inactive' }]);

const dialogRef = inject('dialogRef');

const branchList = ref([])
const roomTypeList = ref([])

const toast = useToast();

const toNumberOrUndefined = (val) => {
    if (val === "" || val === null || typeof val === "undefined") return undefined;
    const n = Number(val);
    return Number.isNaN(n) ? val : n;
};

// validation
const resolver = zodResolver(
    z
        .object({
            name: z.string({ required_error: "name.required" }).min(5, { message: "Tên hạng phòng ít nhất phải 5 ký tự" }),

            description: z.string({ required_error: "description.required" }).min(1, { message: "Yêu cầu nhập mô tả hạng phòng." }),

            total_quantity: z.preprocess(toNumberOrUndefined,
                z.number({
                    required_error: "quantity.required",
                    invalid_type_error: "quantity.numeric",
                }).int({ message: "Yêu cầu nhập tổng số phòng." }).min(1, { message: "Tổng số phòng ít nhất là 1." }).max(30, { message: "Tổng số phòng tối đa nhất là 30." })
            ),

            base_price: z.preprocess(
                toNumberOrUndefined,
                z.number({
                    required_error: "base_price.required",
                    invalid_type_error: "base_price.numeric",
                })
            ),

            hourly_rate: z.preprocess(
                toNumberOrUndefined,
                z.number({ invalid_type_error: "hourly_rate.numeric" }).optional()
            ),

            overnight_rate: z.preprocess(
                toNumberOrUndefined,
                z.number({ invalid_type_error: "overnight_rate.numeric" }).optional()
            ),

            full_day_rate: z.preprocess(
                toNumberOrUndefined,
                z.number({ invalid_type_error: "full_day_rate.numeric" }).optional()
            ),

            status: z.string({ required_error: "status.required" }).min(1, { message: "Yêu cầu chọn trạng thái phòng." }),
            branch_id: z.preprocess(
                (val) => {
                    if (!val || (typeof val === 'string' && val.trim() === '')) return []
                    if (typeof val === 'number') return [val]
                    if (Array.isArray(val)) return val
                    return []
                },
                z.array(z.number().int().positive()).min(1, {
                    message: 'Vui lòng chọn ít nhất một chi nhánh',
                })
            ),
        })
        .superRefine((data, ctx) => {
            const { hourly_rate, overnight_rate, full_day_rate, base_price } = data;

            const isNum = (v) => typeof v === "number";

            let overnightRateErrors = [];
            let basePriceErrors = [];

            // hourly < overnight
            if (isNum(hourly_rate) && isNum(overnight_rate)) {
                if (hourly_rate >= overnight_rate) {
                    ctx.addIssue({
                        code: 'custom',
                        path: ["hourly_rate"],
                        message: "Giá thuê theo giờ phải nhỏ hơn giá thuê qua đêm",
                    });
                    overnightRateErrors.push("Giá thuê qua đêm phải lớn hơn giá thuê theo giờ");
                }
            }

            // overnight < full_day
            if (isNum(overnight_rate) && isNum(full_day_rate)) {
                if (overnight_rate >= full_day_rate) {
                    ctx.addIssue({
                        code: 'custom',
                        path: ["full_day_rate"],
                        message: "Giá thuê qua ngày phải lớn hơn giá thuê qua đêm",
                    });
                    overnightRateErrors.push("Giá thuê qua đêm phải nhỏ hơn giá thuê qua ngày");
                }
            }

            // base > hourly
            if (isNum(base_price) && isNum(hourly_rate)) {
                if (base_price <= hourly_rate) {
                    basePriceErrors.push("Giá thuê phòng cơ bản phải lớn hơn giá thuê theo giờ");
                }
            }

            // base < full_day
            if (isNum(base_price) && isNum(full_day_rate)) {
                if (base_price >= full_day_rate) {
                    basePriceErrors.push("Giá thuê phòng cơ bản phải nhỏ hơn giá thuê qua ngày");
                }
            }

            if (overnightRateErrors.length) {
                ctx.addIssue({
                    code: "custom",
                    path: ["overnight_rate"],
                    message: overnightRateErrors.join(", "),
                });
            }

            if (basePriceErrors.length) {
                ctx.addIssue({
                    code: "custom",
                    path: ["base_price"],
                    message: basePriceErrors.join(", "),
                });
            }
        }
        ));

const initialValues = reactive({
    name: '',
    description: '',
    total_quantity: 1,
    base_price: 0,
    hourly_rate: 20000,
    overnight_rate: 50000,
    full_day_rate: 200000,
    max_adults: 1,
    max_children: 1,
    status: '',
    branch_id: null,
    images: []
});

// pass data from dynamic dialog primevue
const roomList = ref([]);
onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        branchList.value = params.branchList || [];
        roomTypeList.value = params.roomTypeList || [];
        roomList.value = roomTypeList.value.flatMap(type => type.rooms);
        console.log(roomList);
    }
})

// upload room type images
const $primevue = usePrimeVue();
const totalSize = ref(0);
const totalSizePercent = ref(0);

const form = reactive({
    name: '',
    images: [],
    max_adults: 1,
    max_children: 1,
})

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
    () => form.name,
    (newName) => {
        if (form.images.length) {
            form.images = form.images.map((image, index) => renameImageFile(image, newName, index))
        }
    }
)
const onSelectedFiles = (event) => {
    form.images = event.files.map((file, index) => renameImageFile(file, form.name, index))
    console.log(form.images);
};

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
    totalSize.value -= parseInt(formatSize(file.size));
    totalSizePercent.value = totalSize.value / 10;
    form.images.splice(index, 1);
    if (form.images.length) {
        form.images = form.images.map((image, index) => renameImageFile(image, newName, index))
    }
    console.log(form.images);
};

const onClearTemplatingUpload = (clear) => {
    clear();
    totalSize.value = 0;
    totalSizePercent.value = 0;
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

const closeDialog = () => {
    dialogRef.value.close();
};

// submit form
const submit = (e) => {
    if (e.valid) {
        console.log(e.values);
        e.values.max_adults = form.max_adults;
        e.values.max_children = form.max_children;
        const payload = new FormData()

        // duyệt qua toàn bộ field trong form
        for (const key in e.values) {
            const value = e.values[key];

            // Nếu là date object (ví dụ birth_date)
            if (value instanceof Date) {
                payload.append(key, value.toISOString().split('T')[0]) // => "2025-10-26"
            }
            // Còn lại là text / number
            else {
                payload.append(key, value ?? '')
            }
        }
        if (Array.isArray(form.images) && form.images.length && form.images[0] instanceof File) {
            form.images.forEach((file, idx) => {
                payload.append(`images[${idx}]`, file, file.name);
            });
        }
        console.log(payload);

        //Gửi form qua Inertia
        // router.post(route('admin.room-type.store'), payload, {
        //     forceFormData: true,
        //     onSuccess: () => {
        //         console.log('Thêm loại phòng thành công!')
        //     },
        // })
        // toast.add({ severity: 'success', summary: 'Thêm loại phòng thành công!', life: 3000 });
        // dialogRef.value.close();
    }
}

</script>