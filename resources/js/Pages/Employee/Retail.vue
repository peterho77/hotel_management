<template>
    <div class="service-order-wrapper bg-[#f3f4f6] font-sans h-full">
        <div class="container | flex flex-col h-full">
            <header
                class="header-section | flex-none | bg-white p-3 rounded-lg shadow-sm mb-4 flex flex-col md:flex-row gap-4">
                <div class="w-64 flex-initial | flex flex-col pr-3 border-r border-gray-200">
                    <span class="text-gray-500">Khách hàng</span>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Lương Duyên Hùng</span>
                        <Button icon="pi pi-plus" severity="secondary" size="small" variant="text"
                            @click="showCreateCustomerDialog()" />
                    </div>
                </div>

                <div class="relative w-54 flex-initial | flex flex-col pr-3 border-r border-gray-200">
                    <span class="text-gray-500">Kênh bán</span>
                    <div class="toggle-booking-channel-menu | flex items-center justify-between"
                        :class="{ 'active': bookingChannelMenu === true }">
                        <span class="font-medium">{{ currentBookingChannel }}</span>
                        <Button icon="pi pi-angle-down" severity="secondary" size="small" variant="text"
                            @click="toggleBookingChannelsMenu" />
                        <div class="booking-channel-menu">
                            <template v-for="item in bookingChannelItems">
                                <Button :label="item.label" :icon="item.icon" severity="secondary" size="small"
                                    @click="selectBookingChannel(item)" variant="text" class="justify-start">
                                    <div class="w-full flex gap-2 items-center px-2 py-1">
                                        <i :class="item.icon" style="font-size:.75rem"></i>
                                        <span class="font-medium text-gray-600">{{ item.label }}</span>
                                    </div>
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <span class="text-gray-500">Ghi chú</span>
                    <div class="flex items-center justify-between">
                        <p>Chưa có ghi chú</p>
                        <Button icon="pi pi-pencil" severity="secondary" size="small" variant="text"
                            @click="toggleNoteDialog = true" />
                        <!-- Note dialog -->
                        <Dialog v-model:visible="toggleNoteDialog" modal header="Ghi chú" :style="{ width: '30rem' }">
                            <IftaLabel>
                                <Textarea id="retailDescription" v-model="value" rows="7" fluid style="resize: none" />
                                <label for="retailDescription">Nhập ghi chú</label>
                            </IftaLabel>
                            <div class="flex justify-end gap-2">
                                <Button type="button" label="Cancel" severity="secondary"
                                    @click="toggleNoteDialog = false" />
                                <Button type="button" label="Save" severity="success"
                                    @click="toggleNoteDialog = false" />
                            </div>
                        </Dialog>
                    </div>
                </div>
            </header>

            <div class="retail-content | flex-1 min-h-0 overflow-hidden | flex gap-4 mt-2">
                <div class="section-left h-full max-w-1/3 | bg-white p-3 rounded-lg shadow-sm flex flex-col gap-4">
                    <!-- keyword search -->
                    <IconField>
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search" size="small" class="w-full" />
                    </IconField>

                    <!-- service type list -->
                    <div class="service-type-list | flex flex-wrap gap-2">
                        <template v-for="item in serviceCategoryList">
                            <Button :class="['service-type-btn', { 'active': selectedCategory === item.name }]"
                                size="small" :label="item.label" variant="outlined" rounded text
                                @click="filterServiceItem(item.name)" />
                        </template>
                    </div>

                    <!-- service list -->
                    <div class="service-list | grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template v-for="service in allServiceList" :key="service.code">
                            <div class="service-item | flex items-start gap-2 cursor-pointer px-2 py-3 rounded-xl | hover:bg-sky-100 transition-colors duration-300 ease-in-out"
                                @click="addServiceToInvoice(service)">
                                <div class="w-20 shrink-0 aspect-4/3 overflow-hidden rounded-xl border border-gray-200">
                                    <img class="block w-full h-full object-cover" :src="'/img/default-blank-img.jpg'"
                                        :alt="'Ảnh'" />
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-medium truncate">{{ service.name }}</span>
                                    <span
                                        class="text-sm text-gray-500">{{ formatCurrency(service.selling_price) }}</span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="mt-auto flex justify-center border-t border-gray-200 pt-4">
                        <Paginator v-model:first="first" :rows="rowsPerPage" :totalRecords="totalCount"
                            template="PrevPageLink PageLinks NextPageLink" class="bg-transparent"
                            @page="onPageChange" />
                    </div>
                </div>

                <div class="section-right h-full flex-none md:flex-1 flex flex-col">
                    <template v-if="totalAmount">
                        <div class="flex flex-col flex-1">
                            <div class="bg-white p-3 rounded-lg shadow-sm mb-4 flex-1">
                                <DataTable :value="services">
                                    <Column v-for="col of columns" :key="col.field" :field="col.field"
                                        :header="col.header">
                                        <template #body="slotProps" v-if="['price', 'total'].includes(col.field)">
                                            {{ slotProps.data[col.field].toLocaleString() }}đ
                                        </template>
                                        <template #body="slotProps" v-if="col.field == 'quantity'">
                                            <div class="flex items-center gap-2">
                                                <InputNumber inputClass="w-20"
                                                    v-model="slotProps.data.selected_quantity" :min="1"
                                                    :max="slotProps.data.quantity" size="small" showButtons
                                                    @value-change="() => slotProps.data.total = slotProps.data.selected_quantity * slotProps.data.price">
                                                </InputNumber>
                                                <span>{{ slotProps.data.unit }}</span>
                                            </div>
                                        </template>
                                    </Column>
                                    <Column style="flex: 0 0 4rem">
                                        <template #body="{ index }">
                                            <Button type="button" text severity="secondary" size="small"
                                                icon="pi pi-times" @click="removeServiceItem(index)" />
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
                                <Button label="Thanh toán" severity="success"
                                    @click="showCreateRetailInvoiceDialog(services)" />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="bg-white p-4 rounded-lg shadow-sm flex-1 flex justify-center items-center h-full">
                            <span>Chưa có dịch vụ nào được chọn</span>
                        </div>
                    </template>
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

.service-type-btn.active {
    background-color: var(--success-color-700);
    color: white;
    border: none;
}

.service-type-btn.active:hover {
    background-color: var(--success-color-700);
    color: white;
    border: none;
}


/* Custom booking channel menu */
.toggle-booking-channel-menu {
    position: relative;
}

.toggle-booking-channel-menu.active .booking-channel-menu {
    display: flex;
    flex-direction: column;
}

.booking-channel-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 100;
    min-width: 100%;
    text-wrap: nowrap;
    color: var(--neutral-color-800);
    background: white;
    border-radius: .5rem;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
}
</style>

<script setup>
import { ref, reactive, watch, computed, defineAsyncComponent } from 'vue';
import { useDialog } from 'primevue/usedialog';

import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import IftaLabel from 'primevue/iftalabel';
import Paginator from 'primevue/paginator';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

import { formatCurrency } from "@/Composables/formatData";

// keyword search
import { FilterMatchMode } from '@primevue/core/api';

const props = defineProps({
    serviceList: {
        type: Array,
        default: () => [],
    },
    productList: {
        type: Array,
        default: () => [],
    },
    employeeList: {
        type: Array,
        default: () => [],
    },
    customerTypeList: {
        type: Array,
        default: () => [],
    },
    customerGroupList: {
        type: Array,
        default: () => [],
    },
});

const totalCount = computed(() => {
    const combined = [...props.serviceList, ...props.productList];
    if (selectedCategory.value === 'all') {
        return combined.length;
    }
    return combined.filter(item => item.category === selectedCategory.value).length;
});

const allServiceList = computed(() => {
    const combined = [...props.serviceList, ...props.productList];
    let filteredList = combined;
    if (selectedCategory.value !== 'all') {
        filteredList = combined.filter(item => item.category === selectedCategory.value);
    }
    return filteredList.slice(first.value, first.value + rowsPerPage.value);
});

const filterServiceItem = (category) => {
    selectedCategory.value = category;
};

// add service to invoice
const addServiceToInvoice = (service) => {
    if (services.value.some(item => item.name === service.name)) {
        const existingItem = services.value.find(item => item.name === service.name);
        if (existingItem.selected_quantity < existingItem.quantity) {
            existingItem.selected_quantity += 1;
            existingItem.total = existingItem.selected_quantity * existingItem.price;
        }
        return;
    }
    services.value.push({
        id: services.value.length + 1,
        name: service.name,
        quantity: service.category === 'service' ? 10 : service.quantity,
        selected_quantity: 1,
        price: service.selling_price,
        total: service.selling_price,
        unit: service.unit
    });
}

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter service keyword
const serviceCategoryList = ref([
    { label: 'Tất cả', name: 'all' },
    { label: 'Dịch vụ', name: 'service' },
    { label: 'Đồ ăn', name: 'food' },
    { label: 'Đồ uống', name: 'drink' }
]);
const selectedCategory = ref('all');

// data table
const services = ref([]);

const columns = [
    { field: 'id', header: 'STT' },
    { field: 'name', header: 'Hạng mục' },
    { field: 'quantity', header: 'Số lượng' },
    { field: 'price', header: 'Đơn giá' },
    { field: 'total', header: 'Thành tiền' },
];

const totalAmount = computed(() => {
    let sum = services.value.reduce((sum, item) => sum + (item.total || 0), 0);
    return sum;
});

// booking channel menu
const currentBookingChannel = ref('Khách đến trực tiếp');
const bookingChannelMenu = ref(false);
const bookingChannelItems = ref([
    { label: 'Khách đến trực tiếp', icon: 'pi pi-user' },
    { label: 'Đặt phòng online', icon: 'pi pi-book' }
]);

const toggleBookingChannelsMenu = () => {
    bookingChannelMenu.value = !bookingChannelMenu.value;
};

const selectBookingChannel = (channel) => {
    currentBookingChannel.value = channel.label;
    toggleBookingChannelsMenu();
};

// paginator
const rowsPerPage = ref(10); // Số lượng item hiển thị trên 1 trang (trong ảnh của bạn là khoảng 10-12)
const first = ref(0); // Vị trí bắt đầu

const onPageChange = (event) => {
    first.value = event.first;
};

// remove service item
const removeServiceItem = (index) => {
    services.value.splice(index, 1);
};

// dialog
const dialog = useDialog();
const createRetailInvoiceDialog = defineAsyncComponent(() => import('../../Components/Dialog/RetailInvoice/Create.vue'));
const createCustomerDialog = defineAsyncComponent(() => import('../../Components/Dialog/Customer/Create.vue'));

const showCreateRetailInvoiceDialog = (services) => {
    dialog.open(createRetailInvoiceDialog, {
        props: {
            pt: {
                mask: {
                    // Ép class hoặc style vào thẻ Mask
                    style: 'align-items: stretch'
                },
            },
            header: 'Thêm mới hóa đơn',
            blockScroll: true,
            style: {
                width: '60vw',
                minHeight: '100dvh',
            },
            contentStyle: {
                flex: '1',          // Ép phần content giãn ra để đẩy nút xuống đáy
                display: 'flex',
                flexDirection: 'column'
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'right',
            contentClass: 'hide-scroll'
        },
        contentClass: 'flex flex-col overflow-hidden',
        data: {
            services,
            employeeList: props.employeeList
        }
    });
}

const showCreateCustomerDialog = () => {
    dialog.open(createCustomerDialog, {
        props: {
            header: 'Thêm mới khách hàng',
            blockScroll: true,
            style: {
                width: '60vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'center',
            contentClass: 'hide-scroll'
        },
        data: {
            customerTypeList: props.customerTypeList,
            customerGroupList: props.customerGroupList
        }
    });
}

// toggle note dialog
const toggleNoteDialog = ref(false);
</script>