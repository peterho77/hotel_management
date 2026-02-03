<template>
    <div class="service-order-wrapper min-h-screen bg-[#f3f4f6] p-4 font-sans">
        <div class="container">
            <div class="section | bg-white p-3 rounded-lg shadow-sm mb-4 flex flex-col md:flex-row items-center gap-4">
                <div class="w-64 flex-initial | flex flex-col pr-3 border-r border-gray-200">
                    <span class="text-gray-500">Khách hàng</span>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Lương Duyên Hùng</span>
                        <Button icon="pi pi-plus" severity="secondary" size="small" variant="text" />
                    </div>
                </div>

                <div class="w-54 flex-initial | flex flex-col pr-3 border-r border-gray-200">
                    <span class="text-gray-500">Kênh bán</span>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Khách đến trực tiếp</span>
                        <Button icon="pi pi-angle-down" severity="secondary" size="small" variant="text" />
                    </div>
                </div>

                <div class="flex-1">
                    <span class="text-gray-500">Ghi chú</span>
                    <div class="flex items-center justify-between">
                        <p>Chưa có ghi chú</p>
                        <Button icon="pi pi-pencil" severity="secondary" size="small" variant="text" />
                    </div>
                </div>
            </div>

            <div class="service-order-content | flex gap-4 mt-2">
                <div
                    class="section-left flex-initial min-h-screen | bg-white p-3 rounded-lg shadow-sm flex flex-col gap-4">
                    <!-- keyword search -->
                    <IconField>
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search" size="small" class="w-full" />
                    </IconField>

                    <!-- service type list -->
                    <div class="service-type-list | flex flex-wrap gap-2">
                        <template v-for="item in serviceTypeList">
                            <Button size="small" :label="item.label" variant="outlined" rounded />
                        </template>
                    </div>

                    <!-- service list -->
                    <div class="service-list | grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="service-item | flex items-start gap-2">
                            <div class="w-20 shrink-0 aspect-4/3 overflow-hidden rounded-xl border border-gray-200">
                                <img class="block w-full h-full object-cover" :src="'/img/default-blank-img.jpg'"
                                    :alt="'Ảnh'" />
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium truncate">Dịch vụ 1</span>
                                <span class="text-sm text-gray-500">100.000đ</span>
                            </div>
                        </div>
                        <div class="service-item | flex items-start gap-2">
                            <div class="w-20 shrink-0 aspect-4/3 overflow-hidden rounded-xl border border-gray-200">
                                <img class="block w-full h-full object-cover" :src="'/img/default-blank-img.jpg'"
                                    :alt="'Ảnh'" />
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium truncate">Dịch vụ 2</span>
                                <span class="text-sm text-gray-500">100.000đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-right | flex-none md:flex-1 flex flex-col min-h-screen">
                    <div class="bg-white p-3 rounded-lg shadow-sm mb-4 flex-1">
                        <DataTable :value="services">
                            <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header">
                                <template #body="slotProps" v-if="['price', 'total'].includes(col.field)">
                                    {{ slotProps.data[col.field].toLocaleString() }}đ
                                </template>
                            </Column>

                            <ColumnGroup type="footer">
                                <Row>
                                    <Column footer="Tổng cộng:" :colspan="3"
                                        footerStyle="text-align:right; font-weight:700" />

                                    <Column :footer="totalAmount.toLocaleString() + 'đ'"
                                        footerStyle="color: #dc2626; font-size: 1.25rem; font-weight: var(--fw-semi-bold)" />
                                </Row>
                            </ColumnGroup>
                        </DataTable>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow-sm mt-auto flex justify-end gap-4">
                        <Button label="Thanh toán" severity="success" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom Scrollbar */
.service-order-wrapper::-webkit-scrollbar {
    width: 8px;
}

.service-order-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.service-order-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.service-order-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* custom button color */
:deep(.p-button-outlined) {
    border-color: var(--neutral-color-400);
    color: var(--neutral-color-500);
    font-weight: var(--fw-semi-bold);
}
</style>

<script setup>
import { ref, reactive, watch, computed, defineAsyncComponent } from 'vue';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

// keyword search
import { FilterMatchMode } from '@primevue/core/api';

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter service keyword
const serviceTypeList = ref([
    { label: 'Tất cả', name: 'all' },
    { label: 'Dịch vụ', name: 'service' },
    { label: 'Đồ ăn', name: 'food' },
    { label: 'Đồ uống', name: 'drink' }
]);

// data table
const services = ref([
    {
        name: 'Dịch vụ cắt tóc nam',
        quantity: 1,
        price: 100000,
        total: 100000
    },
    {
        name: 'Gội đầu dưỡng sinh',
        quantity: 2,
        price: 50000,
        total: 100000
    },
    {
        name: 'Combo chăm sóc da mặt',
        quantity: 1,
        price: 250000,
        total: 250000
    },
    {
        name: 'Uốn tóc tiêu chuẩn',
        quantity: 1,
        price: 400000,
        total: 400000
    }
]);

const columns = [
    { field: 'name', header: 'Hạng mục' },
    { field: 'quantity', header: 'Số lượng' },
    { field: 'price', header: 'Đơn giá' },
    { field: 'total', header: 'Thành tiền' },
];

const totalAmount = computed(() => {
    return services.value.reduce((sum, item) => sum + (item.total || 0), 0);
});
</script>