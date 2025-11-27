<template>
    <div>
        <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt" :value="bookingHistory"
            sortMode="multiple" dataKey="id" removableSort paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            tableStyle="min-width: 60rem">
            <Column expander style="width: 5rem" />
            <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index" :field="col.field"
                :header="formatLabel(col.header)" sortable />
            <template #expansion="slotProps">
                <Panel>
                    <div class="p-2">
                        <div class="detail-infor | grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="booking-section | grid grid-cols-2">
                                <template v-for="(value, key) in slotProps.data" :key="key">
                                    <!-- Các field bình thường -->
                                    <template
                                        v-if="!['id', 'customer', 'customer_id', 'num_adults', 'num_children', 'payments', 'latest_payment'].includes(key)">
                                        <div class="grid gap-x-4">
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
                                </template>
                            </div>

                            <div class="payment-section | grid grid-cols-2" v-if="slotProps.data.latest_payment">
                                <template v-for="(value, key) in slotProps.data.latest_payment" :key="key">
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
                        <div class="mr-10 mt-6">
                            <Button severity="info" label="Đổi thời gian check in" />
                        </div>
                    </div>
                </Panel>
            </template>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { formatLabel, formatDateVN } from "@/Composables/formatData";

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import Button from 'primevue/button';

const bookingHistory = computed(() => {
    return props.bookings;
});

// keyword search
import { FilterMatchMode } from '@primevue/core/api';
const props = defineProps({
    bookings: Array
})

// row expansion
const expandedRows = ref({});

console.log(bookingHistory);

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// toggle column detail
const selectedColumns = ref([]);
const currentColumns = ref([]);

const hiddenColumns = ref(
    [
        'customer',
        'customer_id',
        'payments',
        'created_at',
        'updated_at',
        'num_adults',
        'num_children',
        'latest_payment'
    ]);
watch(hiddenColumns, (val) => {
    localStorage.setItem('hiddenColumns', JSON.stringify(val));
}, { deep: true });

onMounted(() => {
    const savedHidden = localStorage.getItem('hiddenColumns');
    if (savedHidden) {
        hiddenColumns.value = JSON.parse(savedHidden);
    }

    if (props.bookings.length > 0) {
        const bookingKeys = Object.keys(props.bookings[0]);

        currentColumns.value = bookingKeys.map(key => ({
            field: key,
            header: formatLabel(key),
        }));
    }

    currentColumns.value = currentColumns.value.filter(
        item => !hiddenColumns.value.includes(item.field)
    );

    selectedColumns.value = currentColumns.value;
})

watch(() => [props.bookings],
    () => {
        const savedHidden = localStorage.getItem('hiddenColumns');
        if (savedHidden) {
            hiddenColumns.value = JSON.parse(savedHidden);
        }

        if (props.bookings.length > 0) {
            const bookingKeys = Object.keys(props.bookings[0]);

            currentColumns.value = bookingKeys.map(key => ({
                field: key,
                header: formatLabel(key),
            }));
        }

        currentColumns.value = currentColumns.value.filter(
            item => !hiddenColumns.value.includes(item.field)
        );

        selectedColumns.value = currentColumns.value;
    });
// { immediate: true });

const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};
</script>