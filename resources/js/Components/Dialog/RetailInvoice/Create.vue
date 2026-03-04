<template>
    <div class="mt-2 grid grid-cols-1 md:grid-cols-[4fr_1fr] gap-2 h-full">
        <div class=" pr-4">
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
        <div class="pl-4 flex flex-col gap-3 h-full border-l border-gray-200">
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
                <div class="flex justify-between mb-1">
                    <span class="text-sm">Tổng tiền</span>
                    <span>{{ totalAmount.toLocaleString() }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm">Giảm giá ({{ discountPercent ?? 0 }}%)</span>
                    <div class="basis-1/3 text-right | cursor-pointer border-b border-gray-300 hover:border-green-500"
                        @click="toggleDiscountInput">
                        <span>{{ discountAmount.toLocaleString() }}đ</span>
                    </div>
                </div>
                <Popover ref="discountInput" :pt="{
                    root: {
                        class: 'left-0 right-auto'
                    }
                }">
                    <div class="flex gap-2">
                        <InputNumber v-model="discountInputDisplay" :suffix="discountType === 'percent' ? '%' : 'đ'"
                            size="small" showClear @update:modelValue="calculateDiscount"
                            :min="discountType === 'percent' ? 1 : totalAmount * 0.01"
                            :max="discountType === 'percent' ? 100 : totalAmount" />
                        <SelectButton v-model="discountType" :options="discountTypeOptions" size="small"
                            optionLabel="label" optionValue="value" @update:modelValue="switchDiscountType" />
                    </div>
                </Popover>
                <div class="flex justify-between">
                    <span class="text-sm">Thu khác</span>
                    <div class="basis-1/3 text-right | cursor-pointer border-b border-gray-300 hover:border-green-500">
                        <span>0đ</span>
                    </div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-sm font-semibold">Tổng tiền cần trả</span>
                    <span>{{ finalPay.toLocaleString() }}đ</span>
                </div>
            </div>
            <div class="grid gap-y-3 | bg-gray-200 rounded-sm p-4">
                <h2 class="text-lg font-semibold">Phương thức thanh toán</h2>
                <div class="flex justify-between items-center">
                    <span class="text-sm">Khách thanh toán</span>
                    <InputNumber mode="currency" currency="VND" locale="vi-VN" size="small" />
                </div>
                <div class="payment_method-section flex flex-col gap-2 mt-2">
                    <div class="flex justify-between flex-wrap gap-4">
                        <template v-for="method in paymentMethodList" :key="method.value">
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="paymentMethod" :inputId="`ingredient-${method.value}`"
                                    :name="`payment-method-${method.value}`" :value="method.value" size="small" />
                                <label :for="`ingredient-${method.value}`">{{ method.label }}</label>
                            </div>
                        </template>
                    </div>
                    <div class="suggested-amount | rounded-md bg-gray-100 border border-gray-200 p-4 | flex flex-wrap">
                        <template v-for="amount in getSuggestedAmounts(finalPay)" :key="amount">
                            <Button class="suggested-amount-btn" variant="outlined" :label="amount.toLocaleString() + 'đ'" rounded text size="small"/>
                        </template>
                    </div>
                </div>
            </div>
            <Button label="Hoàn thành" severity="success" class="mt-auto"></Button>
        </div>
    </div>
</template>

<style scoped>
/* custom button color */
:deep(.p-button-outlined) {
    color: var(--neutral-color-500);
    font-weight: var(--fw-semi-bold);
}

.suggested-amount-btn:hover{
    color: var(--neutral-color-500);
    background-color: var(--neutral-color-300);
}
</style>

<script setup>
import { ref, reactive, onMounted, inject, computed, watch } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import RadioButton from 'primevue/radiobutton';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import SelectButton from 'primevue/selectbutton';


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

// toggle discount input
const discountInput = ref();
const toggleDiscountInput = (event) => {
    discountInput.value.toggle(event);
}

// switch discount type
const discountType = ref('amount');
const discountTypeOptions = [
    { label: 'VNĐ', value: 'amount' },
    { label: '%', value: 'percent' },
];

// discount value
const discountInputDisplay = ref(0);
const discountPercent = computed(() => {
    if (discountType.value === 'percent') {
        return discountInputDisplay.value;
    } else {
        const total = totalAmount.value || 0;
        return total > 0 ? Math.round((discountInputDisplay.value / total) * 10000) / 100 : 0;
    }
});
const discountAmount = ref(0);
const calculateDiscount = () => {
    const val = discountInputDisplay.value || 0;
    if (discountType.value === 'percent') {
        const percent = Math.min(Math.max(val, 0), 100);
        discountAmount.value = totalAmount.value * (percent / 100);
    }
    else {
        discountAmount.value = Math.min(Math.max(val, 0), totalAmount.value);
    }
    console.log(discountAmount.value);
}
const switchDiscountType = (newType) => {
    const total = totalAmount.value || 0;
    if (total === 0) return;

    if (newType === 'amount') {
        // Đang từ % chuyển sang số tiền (10% -> 30,000đ)
        discountInputDisplay.value = (total * discountInputDisplay.value) / 100;
    } else {
        // Đang từ số tiền chuyển sang % (30,000đ -> 10%)
        discountInputDisplay.value = Math.round(((discountInputDisplay.value / total) * 100) * 100) / 100;
    }
    // Sau khi đổi input, tính lại discountAmount
    calculateDiscount();
};

// final pay
const finalPay = computed(() => {
    return totalAmount.value - discountAmount.value;
});

// suggested amount
const getSuggestedAmounts = (total) => {
    const suggestions = new Set();
    
    // Thêm chính xác số tiền (Ví dụ: 102,300)
    suggestions.add(total);

    // Thêm các mốc làm tròn (Lưu ý: Tiền Việt thường lẻ ở mức 5k hoặc 10k)
    [5000, 10000, 20000, 50000, 100000].forEach(step => {
        suggestions.add(Math.ceil(total / step) * step);
    });

    // Thêm các tờ tiền chẵn lớn hẳn
    [200000, 500000].forEach(bill => {
        if (bill > total) suggestions.add(bill);
    });

    return Array.from(suggestions)
        .filter(amount => amount >= total)
        .sort((a, b) => a - b)
        .slice(0, 6); // Lấy 6 số tối ưu nhất
};
</script>