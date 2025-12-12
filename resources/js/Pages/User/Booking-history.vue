<template>
    <div>
        <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt" :value="bookingHistory"
            sortMode="multiple" dataKey="id" removableSort paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            tableStyle="min-width: 60rem">
            <template #empty>
                <h3 class="text-center text-lg font-medium">No bookings found.</h3>
            </template>
            <Column expander style="width: 3.5rem" />
            <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index" :field="col.field"
                :header="formatLabel(col.header)" sortable />
            <template #expansion="slotProps">
                <Panel>
                    <div class="detail-infor | grid items-start grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="booking-section | flow">
                            <h3 class="text-lg font-medium">Thông tin đặt phòng</h3>
                            <div class="grid grid-cols-2">
                                <template v-for="(value, key) in getDetailRows(slotProps.data, hiddenBookingDetailRows)"
                                    :key="key">
                                    <!-- Các field bình thường -->
                                    <div class="grid gap-x-4">
                                        <div class="flex gap-x-1">
                                            <span class="font-semibold text-gray-700 min-w-42">
                                                {{ formatLabel(key) }}:
                                            </span>
                                            <span class="text-gray-900 grow">
                                                {{ value }}
                                            </span>
                                        </div>
                                        <Divider type="dashed" />
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="payment-section | flow">
                            <h3 class="text-lg font-medium">Thông tin thanh toán</h3>
                            <div class=" | grid grid-cols-2" v-if="slotProps.data.latest_payment">
                                <template
                                    v-for="(value, key) in getDetailRows(slotProps.data.latest_payment, hiddenPaymentDetailRows)"
                                    :key="key">
                                    <div class="grid gap-x-4" v-if="value">
                                        <div class="flex gap-x-1">
                                            <span class="font-semibold text-gray-700 min-w-42">
                                                {{ formatLabel(key) }}:
                                            </span>
                                            <span class="text-gray-900 grow">
                                                <template
                                                    v-if="['created_at', 'updated_at', 'check_in', 'check_out'].includes(key)">
                                                    {{ formatDateVN(value) }}
                                                </template>
                                                <template v-else>
                                                    {{ value }}
                                                </template>
                                            </span>
                                        </div>
                                        <Divider type="dashed" />
                                    </div>

                                </template>
                            </div>
                        </div>

                        <!-- Field travel services -->
                        <!-- <template v-if="slotProps.data.amenities">
                            <div v-for="(amenityValue, amenityKey) in JSON.parse(slotProps.data.amenities)"
                                :key="amenityKey" class="flex flex-col gap-y-1">
                                <div class="flex">
                                    <span class="min-w-44 font-semibold text-gray-700 shrink-0">
                                        {{ formatLabel(amenityKey) }}:
                                    </span>
                                    <span class="text-gray-900 flex-1">
                                        {{ Array.isArray(amenityValue) ? amenityValue.join(', ') : amenityValue }}
                                    </span>
                                </div>
                                <Divider type="dashed" />
                            </div>
                        </template> -->
                    </div>
                    <div class="mr-10 mt-6 flex gap-4">
                        <Button severity="info" label="Đổi thời gian check in" raised />
                        <Button severity="secondary" label="Viết bài đánh giá" raised
                            @click="showAddNewReview(slotProps.data.id, slotProps.data.customer_id)" />
                    </div>
                </Panel>
            </template>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineAsyncComponent } from 'vue';
import { formatLabel, formatDateVN } from "@/Composables/formatData";
import { formatDataTable, getColumns, getDetailRows } from "@/Composables/formatDataTable";
import { useDialog } from 'primevue/usedialog';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import Button from 'primevue/button';

// keyword search
import { FilterMatchMode } from '@primevue/core/api';
const props = defineProps({
    bookings: Array,
    user_name: String
})

const bookingHistory = computed(() => {
    return formatDataTable(props.bookings);
});
console.log(bookingHistory.value);

// row expansion
const expandedRows = ref({});

const hiddenBookingDetailRows = ref([
    'id',
    'customer',
    'customer_id',
    'num_adults',
    'num_children',
    'payments',
    'latest_payment'
]);
const hiddenPaymentDetailRows = ref([
    'id',
    'booking_id',
    'transaction_id',
    'created_at',
    'updated_at'
])

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// toggle column detail
const columns = ref([]);
const selectedColumns = ref([]);
const hiddenColumns = ref(
    [
        'customer',
        'customer_id',
        'payments',
        'created_at',
        'updated_at',
        'num_adults',
        'num_children',
        'amount_paid',
        'deposit_amount',
        'latest_payment'
    ]);
watch(hiddenColumns, (val) => {
    localStorage.setItem('hiddenColumns', JSON.stringify(val));
}, { deep: true });
onMounted(() => {
    const savedHiddenCols = localStorage.getItem('hiddenColumns');
    if (savedHiddenCols) {
        hiddenColumns.value = JSON.parse(savedHidden);
    }
    console.log(props.bookings);
    if (props.bookings.length > 0) {
        columns.value = getColumns(props.bookings[0], hiddenColumns.value);
    }

    selectedColumns.value = columns.value;
})

watch(() => [props.bookings],
    () => {
        const savedHidden = localStorage.getItem('hiddenColumns');
        if (savedHidden) {
            hiddenColumns.value = JSON.parse(savedHidden);
        }

        if (props.bookings.length > 0) {
            columns.value = getColumns(props.bookings[0]);
        }

        columns.value = columns.value.filter(
            item => !hiddenColumns.value.includes(item.field)
        );

        selectedColumns.value = columns.value;
    });
// { immediate: true });

const toggleColumn = (val) => {
    selectedColumns.value = columns.value.filter(col => {
        return val.includes(col);
    })
};

// open add review dialog
const dialog = useDialog();
const addNewReview = defineAsyncComponent(() => import('../../Components/Dialog/AddNewReview.vue'));

const showAddNewReview = (bookingId, userId) => {
    dialog.open(addNewReview, {
        props: {
            header: 'Đánh giá của bạn',
            style: {
                width: '40vw',
                minHeight: '100vh',
                minHeight: '100dvh'
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'right',
        },
        data: {
            bookingId,
            userId,
            userName: props.user_name
        }
    });
}
</script>