<template>
    <div class="mt-2 grid grid-cols-1 md:grid-cols-[auto_1fr] gap-2">
        <div class="border-r border-gray-200 pr-4 min-h-dvh">
            <DataTable :value="services">
                <template v-for="col of columns" :key="col.field">
                    <Column :field="col.field" :header="col.header">
                        <template #body="slotProps" v-if="['price', 'total'].includes(col.field)">
                            {{ slotProps.data[col.field].toLocaleString() }}đ
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
        <div class="pl-4 flex flex-col gap-3">
            <div class="flex gap-2 justify-between">
                <div class="flex flex-col">
                    <span class="text-sm">Nhân viên tạo HĐ</span>
                    <Select v-model="creator" :options="employeeList" optionLabel="full_name"
                        :placeholder="employeeList[0]?.full_name ?? 'Chọn nhân viên'" class="w-full md:w-56"
                        size="small" />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm">Ngày tạo HĐ</span>
                    <DatePicker v-model="createdAt" showTime :placeholder="currentDateTime" class="w-full md:w-56"
                        size="small" />
                </div>
            </div>
            <div class="grid gap-y-3 | bg-gray-200 rounded-sm p-4">
                <div class="flex justify-between">
                    <span class="text-sm">Tổng tiền</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm">Giảm giá</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm">Thu khác</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-semibold">Tổng tiền cần trả</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
            </div>
            <div class="grid gap-y-3 | bg-gray-200 rounded-sm p-4">
                <h2 class="text-lg font-semibold">Phương thức thanh toán</h2>
                <div class="flex justify-between">
                    <span class="text-sm">Khách thanh toán</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
                <div class="payment_method-section mt-2">
                    <div class="flex justify-between flex-wrap gap-4">
                        <template v-for="method in paymentMethodList" :key="method.value">
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="paymentMethod" :inputId="`ingredient-${method.value}`"
                                    :name="`payment-method-${method.value}`" :value="method.value" size="small" />
                                <label :for="`ingredient-${method.value}`">{{ method.label }}</label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, inject, computed, watch } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import RadioButton from 'primevue/radiobutton';

const columns = [
    { field: 'id', header: 'STT' },
    { field: 'name', header: 'Hạng mục' },
    { field: 'selected_quantity', header: 'Số lượng' },
    { field: 'price', header: 'Đơn giá' },
    { field: 'total', header: 'Thành tiền' },
];

const services = ref([]);
const employeeList = ref([]);

const dialogRef = inject('dialogRef');
onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        services.value = params.services || [];
        employeeList.value = params.employeeList || [];
    }
})

// creator
const creator = ref(null);
const createdAt = ref(null);
const currentDateTime = new Date().toLocaleString('vi-VN');

// total amount
const totalAmount = computed(() => {
    return (services.value || []).reduce((sum, service) => sum + service.total, 0);
});

// payment method
const paymentMethodList = [
    { label: 'Tiền mặt', value: 'cash' },
    { label: 'Thẻ', value: 'card' },
    { label: 'Chuyển khoản', value: 'bank_transfer' },
    { label: 'Ví điện tử', value: 'digital_wallet' },
];
const paymentMethod = ref(null);
</script>