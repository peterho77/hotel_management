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
            <Form class="grid gap-y-6" @submit="confirmRetailOrder">
                <div class="flex gap-2 justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm">Nhân viên tạo HĐ</span>
                        <Select v-model="retailInfoForm.creator_id" :options="employeeList" optionLabel="full_name"
                            optionValue="id" :placeholder="employeeList[0]?.full_name ?? 'Chọn nhân viên'"
                            class="w-full md:w-56" size="small" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm">Ngày tạo HĐ</span>
                        <DatePicker v-model="retailInfoForm.created_at" showTime :placeholder="currentDateTime"
                            class="w-full md:w-56" size="small" />
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
                        <div
                            class="basis-1/3 text-right | cursor-pointer border-b border-gray-300 hover:border-green-500">
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
                        <InputNumber inputClass="text-right" v-model="paidAmount" mode="currency" currency="VND"
                            locale="vi-VN" size="small" />
                    </div>
                    <div class="payment_method-section flex flex-col gap-2 mt-2">
                        <div class="flex justify-between flex-wrap gap-4">
                            <template v-for="method in paymentMethodList" :key="method.value">
                                <div class="flex items-center gap-2">
                                    <RadioButton v-model="paymentMethod" :inputId="`ingredient-${method.value}`"
                                        :name="`payment-method-${method.value}`" :value="method" size="small" />
                                    <label :for="`ingredient-${method.value}`">{{ method.label }}</label>
                                </div>
                            </template>
                        </div>
                        <!-- <div class="payment-account | flex flex-col items-center gap-2 my-1" v-if="toggleAddPaymentAccount">
                            <span class="text-gray-500">Bạn chưa có tài khoản</span>
                            <Button label="Thêm tài khoản" size="small" severity="success" variant="outlined"
                                icon="pi pi-plus" @click="showAddPaymentAccountDialog()" />
                        </div> -->
                        <div class="payment-account-info" v-if="toggleAddPaymentAccount">
                            <div class="flex gap-3">
                                <div class="qr-code | set-bg-img" :style="$getBgStyle(QRCodeImg)">
                                </div>
                                <div class="account-desc">
                                    <p class="mb-2">{{ Object.values(defaultAccountInfo).join(' - ') }}</p>
                                    <Button label="Hiển thị mã QR" size="small" severity="secondary"
                                        @click="showQRCode"></Button>
                                </div>
                            </div>
                        </div>
                        <div
                            class="suggested-amount | rounded-md bg-slate-100 border border-gray-200 p-4 | flex flex-wrap gap-2">
                            <template v-for="amount in getSuggestedAmounts(finalPay)" :key="amount">
                                <Button class="suggested-amount-btn shadow-sm" severity="secondary" variant="outlined"
                                    :label="amount.toLocaleString() + 'đ'" rounded text size="small"
                                    @click="paidAmount = amount" />
                            </template>
                        </div>
                    </div>
                    <div class="flex justify-between items-center" v-if="changeAmount != 0">
                        <span class="text-sm">Tiền thừa trả khách</span>
                        <span class="text-green-600">{{ changeAmount.toLocaleString() }}đ</span>
                    </div>
                </div>
                <Button label="Hoàn thành" type="submit" severity="success" class="mt-auto"></Button>
            </Form>
        </div>
    </div>
</template>

<style scoped>
/* custom button color */
.suggested-amount-btn {
    color: var(--neutral-color-500);
    font-weight: var(--fw-semi-bold);
}

.suggested-amount-btn:hover {
    color: var(--neutral-color-500);
    background-color: var(--neutral-color-300);
}

/* qr code img */
.payment-account-info {
    height: auto;
    min-height: 100px;
}

.qr-code {
    background-size: contain;
    width: 30%;
    aspect-ratio: 3/4;
}

.account-desc {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<script setup>
import { ref, reactive, watch, onMounted, inject, computed, defineAsyncComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDialog } from 'primevue/usedialog';
import { Form } from '@primevue/forms';
import { router } from '@inertiajs/vue3';

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
    { field: 'order', header: 'STT' },
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
        console.log(employeeList.value);
    }
})

// total amount
const totalAmount = computed(() => {
    return (services.value || []).reduce((sum, service) => sum + service.total, 0);
});

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
        const suggestion = Math.ceil(total / step) * step;
        suggestions.add(suggestion);
        suggestions.add(suggestion + step);
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

// payment method
const paymentMethodList = [
    {
        label: 'Tiền mặt',
        value: 'cash',
    },
    {
        label: 'Thẻ',
        value: 'card'
    },
    {
        label: 'Chuyển khoản',
        value: 'bank_transfer'
    },
    {
        label: 'Ví điện tử',
        value: 'digital_wallet'
    },
];
const paymentMethod = ref(null);

// toggle add payment account (for digital wallet and bank transfer)
const toggleAddPaymentAccount = computed(() => {
    return paymentMethod.value && (paymentMethod.value.value === 'digital_wallet' || paymentMethod.value.value === 'bank_transfer');
});

// add payment account
const dialog = useDialog();
const addPaymentAccountDialog = defineAsyncComponent(() => import('../PaymentAccount/Add.vue'));
const showAddPaymentAccountDialog = () => {
    dialog.open(addPaymentAccountDialog, {
        props: {
            header: 'Thêm tài khoản',
            blockScroll: true,
            style: {
                minWidth: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'center',
            contentClass: 'hide-scroll'
        },
        contentClass: 'flex flex-col overflow-hidden',
        data: {
        }
    });
}

// default payment info
const QRCodeImg = ref('/img/qr-code.png');
const defaultAccountInfo = ref({
    name: 'HO CONG THIEN DAT',
    bank: 'BIDV',
    number: '9624STEVENHO'
})
const showQRCodeDialog = defineAsyncComponent(() => import('../PaymentAccount/ShowQRCode.vue'));
const showQRCode = () => {
    dialog.open(showQRCodeDialog, {
        props: {
            header: 'Mã QR Thanh toán',
            blockScroll: true,
            style: {
                width: '30vw'
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'center',
            contentClass: 'hide-scroll'
        },
        contentClass: 'flex flex-col overflow-hidden',
        data: {
            QRCodeImg,
            AccountInfo: defaultAccountInfo
        }
    });
}

// amount paid and change amount
const paidAmount = ref(0);
watch(() => finalPay.value, (newVal) => {
    paidAmount.value = newVal;
}, { immediate: true });
const changeAmount = computed(() => {
    return paidAmount.value - totalAmount.value
})

// form post retail order
const page = usePage();
const currentDateTime = new Date().toLocaleString('vi-VN');
const retailInfoForm = reactive({
    creator_id: employeeList.value[0]?.id ?? 0,
    created_at: new Date(),
    total_amount: 0,
    paid_amount: 0
});

const confirmRetailOrder = () => {
    retailInfoForm.total_amount = finalPay.value;
    retailInfoForm.paid_amount = paidAmount.value;

    const payload = new FormData()

    // duyệt qua toàn bộ field trong form
    for (const key in retailInfoForm) {
        const value = retailInfoForm[key];

        if (value instanceof Date) {
            payload.append(key, value.toISOString().split('T')[0]) // => "2025-10-26"
        }
        // Còn lại là text / number
        else {
            payload.append(key, value ?? '')
        }
    }
    services.value.forEach((item, index) => {
        if (item.category === 'service') {
            payload.append(`items[${index}][service_id]`, item.id);
        }
        else {
            payload.append(`items[${index}][product_id]`, item.id);

        }
        payload.append(`items[${index}][quantity]`, item.selected_quantity);
        payload.append(`items[${index}][price]`, item.price);
        payload.append(`items[${index}][subtotal]`, item.total);
    });
    console.log(retailInfoForm);
    for (var pair of payload.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    router.post(route('employee.retail-order.create'), payload, {
        forceFormData: true,
        onSuccess: () => {
            const newOrderId = page.props.flash.new_order_id;

            if (newOrderId) {
                // Tự động mở tab in hóa đơn PDF
                window.open(route('employee.retail-order.print', { orderId: newOrderId }), '_blank');
            }
        },
    })
    dialogRef.value.close();
}
</script>