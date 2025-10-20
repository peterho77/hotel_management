<template>
    <main class="partner-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- advanced keyword search -->
                        <div class="advanced-keyword-search | box | flow">
                            <div class="flex justify-between">
                                <label class="admin-label">Nhóm khách hàng</label>
                                <i class="pi pi-plus-circle"></i>
                            </div>
                            <div class="flex gap-x-2">
                                <Select v-model="selectedCustomerGroup" :options="customerGroupList" optionLabel="name"
                                    optionValue="alias" placeholder="Tất cả các nhóm" class="w-full md:w-52" />
                                <Button icon="pi pi-pencil" class="!border-1 !border-gray-300" severity="info"
                                    variant="text" />
                            </div>
                        </div>

                        <!-- double keyword search -->
                        <div class="double-keyword-search | box | flow">
                            <label class="admin-label">Tìm kiếm</label>
                            <FloatLabel variant="on">
                                <InputText id="on_label" v-model="value3" fluid />
                                <label for="on_label">Theo mã, tên, điện thoại</label>
                            </FloatLabel>
                            <FloatLabel variant="on">
                                <InputText id="on_label" v-model="value3" fluid />
                                <label for="on_label">Theo mã hóa đơn</label>
                            </FloatLabel>
                        </div>

                        <!-- date filter -->
                        <DateFilterOption label="Ngày tạo" />
                        <DateFilterOption label="Sinh nhật" />
                        <DateFilterOption label="Ngày giao dịch cuối cùng" />
                        <DateFilterOption label="Tổng bán" />

                        <!-- range filter -->
                        <RangeFilter label="Nợ hiện tại" />

                        <!-- radio select -->
                        <Radioselect :list="customerTypeList" v-model="filterCustomerType" label="Loại khách" />
                        <Radioselect :list="statusList" v-model="filterStatus" label="Trạng thái" />

                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt"
                        :value="customersList" sortMode="multiple" dataKey="id" removableSort paginator :rows="5"
                        :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                        <Column expander style="width: 5rem" />
                        <Column v-for="(col, index) of currentColumns" :key="col.field + '_' + index"
                            :field="col.field" :header="formatLabel(col.header)" sortable />
                        <template #expansion="slotProps">
                            <Panel header="Detail information">
                                <div class="p-4">
                                    <div class="grid grid-cols-[auto_1fr] md:grid-cols-[auto_1fr] gap-x-6 gap-y-3">
                                        <template v-for="(value, key) in slotProps.data" :key="key">
                                            <template
                                                v-if="key !== 'branches' && key !== 'branch' && key !== 'room_type'">
                                                <div class="font-semibold text-gray-700">
                                                    {{ formatLabel(key) }}:</div>
                                                <div class="text-gray-900">{{ value }}</div>
                                            </template>

                                            <template v-else-if="key === 'branch' || key === 'room_type'">
                                                <div class="font-semibold text-gray-700">
                                                    {{ formatLabel(key) }}:</div>
                                                <div class="text-gray-900">{{ value.name }}</div>
                                            </template>

                                            <template v-else>
                                                <div class="font-semibold text-gray-700">
                                                    {{ formatLabel(key) }}:</div>
                                                <div class="text-gray-900">
                                                    {{slotProps.data.branches.map(branch => branch.name).join(', ')}}
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                    <div class="update-buttons | flex mr-10">
                                        <Button raised severity="success"
                                            @click="currentTab === 'room' ? showUpdateRoom(slotProps.data) : showUpdateRoomType(slotProps.data)">Update</Button>
                                        <Button raised severity="danger"
                                            @click="deleteConfirm(slotProps.data.id, currentTab)">Delete</Button>
                                    </div>
                                </div>
                            </Panel>
                        </template>
                    </DataTable>
                </section>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3'

import Select from 'primevue/select';
import Button from 'primevue/button';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';

// component
import DateFilterOption from '../../Components/DateFilterOption.vue';
import RangeFilter from '../../Components/RangeFilter.vue';
import Radioselect from '../../Components/Radioselect.vue';

// data table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// format label
function formatLabel(str) {
    str = str.replace(/[-_]/g, " ");

    return str.split(" ").map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(" ");
}

const selectedCustomerGroup = ref();
const customerGroupList = ref([
    {
        name: 'Khách lẻ',
        alias: 'FIT(Free Independent Traveler)'
    },
    {
        name: 'Khách đoàn',
        alias: 'Group'
    },
    {
        name: 'Khách doanh nghiệp',
        alias: 'Corporate'
    },
    {
        name: 'Khách hội nghị - sự kiện',
        alias: 'MICE: Meeting, Incentive, Conference, Exhibition'
    },
    {
        name: 'Khách trung gian',
        alias: 'Travel Agent / OTA'
    },
    {
        name: 'Khách địa phương',
        alias: 'Local Guests'
    },
])

// keyword search
import { FilterMatchMode } from '@primevue/core/api';
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter customer type
const customerTypeList = ref([
    {
        label: 'Cá nhân',
        name: 'Individual / FIT'
    },
    {
        label: 'Đoàn',
        name: 'Group'
    },
    {
        label: 'Công ty',
        name: 'Corporate'
    },
    {
        label: 'Nước ngoài',
        name: 'International'
    },
    {
        label: 'Nội địa',
        name: 'Domestic'
    },
    {
        label: 'Dài hạn',
        name: 'Long-stay'
    }
])
const filterCustomerType = ref('Tất cả');

// filter status
const statusList = ref([
    {
        name: 'active',
        label: 'Đang kinh doanh'
    },
    {
        name: 'inactive',
        label: 'Ngừng kinh doanh'
    },
    {
        name: 'all',
        label: 'Tất cả'
    },
]);
const filterStatus = ref('Tất cả');

// data table
const props = defineProps({
    customersList: {
        type: Array,
        required: false,
    },
    columns: {
        type: Array,
        required: false
    }
})
const customersList = ref(props.customersList);
console.log(customersList);

// toggle column
const selectedColumns = ref([]);
const currentColumns = computed(() => Object.values(props.columns || {}).map(field => ({ field, header: formatLabel(field) })));

// expand row data table
const expandedRows = ref({});

</script>