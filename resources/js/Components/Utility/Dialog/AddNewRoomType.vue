<template>
    <Form ref="newRoomTypeForm" v-slot="$form" :resolver :initialValues validateOnUpdate="false" :validateOnBlur="true"
        class="grid gap-y-6" @submit="submit">
        <div class="flex flex-col gap-2">
            <label for="name">Name</label>
            <InputText id="name" name="name" />
            <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                {{ $form.name.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="description">Description</label>
            <Textarea name="description" rows="4" cols="30" autoResize />
            <Message v-if="$form.description?.invalid" severity="error" size="small" variant="simple">
                {{ $form.description.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="quantity">Quantity</label>
            <InputNumber name="quantity" :maxFractionDigits="0" :min="1" :max="10" showButtons default="1" />
            <Message v-if="$form.quantity?.invalid" severity="error" size="small" variant="simple">
                {{ $form.quantity.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="hourly_rate">Hourly rate</label>
            <InputNumber name="hourly_rate" :maxFractionDigits="0" mode="currency" currency="VND" locale="vi-VN"
                showButtons buttonLayout="horizontal" :step="5000" />
            <Message v-if="$form.hourly_rate?.invalid" severity="error" size="small" variant="simple">
                {{ $form.hourly_rate.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="overnight_rate">Overnight rate</label>
            <InputNumber name="overnight_rate" :maxFractionDigits="0" mode="currency" currency="VND" locale="vi-VN"
                showButtons buttonLayout="horizontal" :step="5000" />
            <Message v-if="$form.overnight_rate?.invalid" severity="error" size="small" variant="simple">
                {{ $form.overnight_rate.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="full_day_rate">Full day rate</label>
            <InputNumber name="full_day_rate" :maxFractionDigits="0" mode="currency" currency="VND" locale="vi-VN"
                showButtons buttonLayout="horizontal" :step="5000" />
            <Message v-if="$form.full_day_rate?.invalid" severity="error" size="small" variant="simple">
                {{ $form.full_day_rate.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="status">Status</label>
            <Select name="status" :options="statusList" optionValue="name" optionLabel="name"
                placeholder="Select status" class="w-full" />
            <Message v-if="$form.status?.invalid" severity="error" size="small" variant="simple">
                {{ $form.status.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="branch_id">Branch</label>
            <MultiSelect :options="branchList" name="branch_id" display="chip" optionLabel="name" optionValue="id"
                filter placeholder="Chọn chi nhánh" :maxSelectedLabels="3" fluid>
            </MultiSelect>
            <Message v-if="$form.branch_id?.invalid" severity="error" size="small" variant="simple">
                {{ $form.branch_id.error.message }}</Message>
        </div>
        <div class="flex gap-y-4 gap-x-2 justify-center">
            <Button type="submit" label="Submit" severity="success" raised />
            <Button label="Cancel" severity="danger" raised @click="closeDialog" />
        </div>
    </Form>
    <Toast />
</template>

<script setup>
import { ref, inject, onMounted } from "vue";
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import MultiSelect from 'primevue/multiselect';
import Button from 'primevue/button';
import Message from 'primevue/message';

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

const resolver = zodResolver(
    z
        .object({
            name: z.string({ required_error: "name.required" }).min(1, { message: "name.required" }),
            description: z.string({ required_error: "description.required" }).min(1, { message: "description.required" }),

            quantity: z.preprocess(toNumberOrUndefined,
                z.number({
                    required_error: "quantity.required",
                    invalid_type_error: "quantity.numeric",
                }).int({ message: "quantity.integer" }).min(1, { message: "quantity.min" }).max(10, { message: "quantity.max" })
            ),

            hourly_rate: z.preprocess(toNumberOrUndefined,
                z.number({ required_error: "hourly_rate.required", invalid_type_error: "hourly_rate.numeric" })
            ),

            overnight_rate: z.preprocess(toNumberOrUndefined,
                z.number({ required_error: "overnight_rate.required", invalid_type_error: "overnight_rate.numeric" })
            ),

            full_day_rate: z.preprocess(toNumberOrUndefined,
                z.number({ required_error: "full_day_rate.required", invalid_type_error: "full_day_rate.numeric" })
            ),

            status: z.string({ required_error: "status.required" }).min(1, { message: "status.required" }).max(50, { message: "status.max" }),
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
            const { hourly_rate, overnight_rate, full_day_rate } = data;

            // nếu đều là số thì kiểm tra quan hệ
            if (typeof hourly_rate !== "undefined" && typeof overnight_rate !== "undefined" && typeof full_day_rate !== "undefined") {
                if (!(hourly_rate < overnight_rate)) {
                    ctx.addIssue({
                        code: 'custom',
                        path: ["hourly_rate"],
                        message: "Giá thuê theo giờ phải nhỏ hơn giá thuê qua đêm",
                    });
                    errorsOvernight.push("Giá thuê qua đêm phải lớn hơn giá thuê theo giờ");
                }

                if (!(overnight_rate < full_day_rate)) {
                    errorsOvernight.push("Giá thuê qua đêm phải nhỏ hơn giá thuê qua ngày");
                    ctx.addIssue({
                        code: 'custom',
                        path: ["full_day_rate"],
                        message: "Giá thuê qua ngày phải lớn hơn giá thuê qua đêm",
                    });
                }

                if (errorsOvernight.length) {
                    ctx.addIssue({
                        code: "custom",
                        path: ["overnight_rate"],
                        message: errorsOvernight.join(`, `),
                    });
                }
            }
        }
        ));

const initialValues = {
    name: '',
    description: '',
    quantity: 1,
    hourly_rate: 20000,
    overnight_rate: 50000,
    full_day_rate: 200000,
    status: '',
    branch_id: null,
};

const newRoomTypeForm = ref(null);

const submit = (e) => {
    if (e.valid) {
        router.post('/admin/room-type/add-new', JSON.parse(JSON.stringify(e.values)))
        toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
        dialogRef.value.close();
    }
}

// pass data from dynamic dialog primevue
onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        branchList.value = params.branchList || [];
    }
})

const closeDialog = () => {
    dialogRef.value.close();
};

</script>