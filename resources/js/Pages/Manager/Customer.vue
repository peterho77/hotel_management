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
                                <Select v-model="filterCustomerGroup" :options="customerGroupList" optionLabel="name"
                                    optionValue="name" placeholder="Tất cả các nhóm" class="w-full md:w-52" />
                                <Button icon="pi pi-pencil" class="border! border-gray" severity="info"
                                    variant="text" />
                            </div>
                        </div>

                        <!-- double keyword search -->
                        <div class="double-keyword-search | box | flow">
                            <label class="admin-label">Tìm kiếm</label>
                            <FloatLabel variant="on">
                                <InputText id="on_label" fluid />
                                <label for="on_label">Theo mã, tên, điện thoại</label>
                            </FloatLabel>
                            <FloatLabel variant="on">
                                <InputText id="on_label" fluid />
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
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <span class="admin-label | fs-700">Khách hàng</span>
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <AddNewItemsButton label="Khách hàng" :hasMenu="false"
                                        @parentClickEvent="showCreateCustomerDialog" />

                                    <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                        optionLabel="header" @update:modelValue="toggleColumn"
                                        placeholder="Select Columns" class="w-full md:w-50" size="small"
                                        :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />

                                    <Button icon="pi pi-external-link" label="Export" severity="info" size="small"
                                        @click="exportCSV($event)" class="text-neutral-50" />
                                </div>
                            </div>
                        </div>
                    </nav>

                    <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" showGridlines size="small"
                        ref="dt" :value="filteredCustomerList" sortMode="multiple" dataKey="id" removableSort paginator
                        :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem;">
                        <template #empty>
                            <div class="text-center">
                                <span class="py-8">Không tìm thấy khách hàng nào. </span>
                            </div>
                        </template>
                        <Column expander style="width: 3.5rem" />
                        <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                            :field="col.field" :header="formatLabel(col.header)" sortable />
                        <template #expansion="slotProps">
                            <Panel header="Detail information">
                                <div class="p-4">
                                    <div class="grid grid-cols-[auto_1fr] md:grid-cols-[auto_1fr] gap-x-6 gap-y-3">
                                        <template v-for="(value, key) in slotProps.data" :key="key">
                                            <template v-if="key === 'customer_type' || key === 'customer_group'">
                                                <div class="font-semibold text-gray-700">
                                                    {{ formatLabel(key) }}:</div>
                                                <div class="text-gray-900">{{ value.name }}</div>
                                            </template>
                                            <template v-else>
                                                <div class="font-semibold text-gray-700">
                                                    {{ formatLabel(key) }}:</div>
                                                <div class="text-gray-900">{{ value }}</div>
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
import { ref, computed, watch, defineAsyncComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";

import Select from 'primevue/select';
import Button from 'primevue/button';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Panel from 'primevue/panel';

// dynamic dialog
import { useDialog } from 'primevue/usedialog';

// component
import DateFilterOption from '../../Components/DateFilterOption.vue';
import RangeFilter from '../../Components/RangeFilter.vue';
import Radioselect from '../../Components/Radioselect.vue';
import AddNewItemsButton from '../../Components/AddNewItemsButton.vue';

// data table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// format data
import { formatLabel } from "@/Composables/formatData";
import { formatDataTable, getColumns, getDetailRows } from "@/Composables/formatDataTable";

// data table
const props = defineProps({
    customersList: {
        type: Array,
        required: false,
    },
    customerGroupList: {
        type: Array,
        required: false
    },
    customerTypeList: {
        type: Array,
        required: false
    },
    columns: {
        type: Object,
        required: false
    }
})
console.log(ref(props.customersList).value);

// keyword search
import { FilterMatchMode } from '@primevue/core/api';
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter customer group
const customerGroupList = ref(props.customerGroupList);
const filterCustomerGroup = ref('Tất cả');

// filter customer type
const customerTypeList = ref(props.customerTypeList);
const filterCustomerType = ref('Tất cả');

const filteredCustomerList = computed(() => {
    return (formatDataTable(props.customersList) || []).filter(user => {
        const customerGroupMatch = filterCustomerGroup.value === 'Tất cả' || filterCustomerGroup.value === user.customer_group.name;
        const customerTypeMatch = filterCustomerType.value === 'Tất cả' || filterCustomerType.value === user.customer_type.name;

        return customerGroupMatch && customerTypeMatch;
    })
});

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

// toggle column
const selectedColumns = ref([]);

console.log(props.columns);
const currentColumns = ref(getColumns(props.customersList[0],
    ['user_id', 'customer_type', 'customer_group', 'avatar', 'has_account', 'created_at', 'updated_at']));

watch(() => currentColumns, () => {
    selectedColumns.value = currentColumns.value;
}, { immediate: true })

const toggleColumn = (val) => {
    const selectedFields = val.map(v => v.field); // lấy giá trị thực
    selectedColumns.value = currentColumns.value.filter(col =>
        selectedFields.includes(col.field)
    );
};

// expand row data table
const expandedRows = ref({});

// open add new or update dialog
const createCustomerDialog = defineAsyncComponent(() => import('../../Components/Dialog/Customer/Create.vue'));

const dialog = useDialog();
// add new dialog
const showCreateCustomerDialog = () => {
    dialog.open(createCustomerDialog, {
        props: {
            header: 'Add new customer',
            style: {
                width: '60vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true
        },
        data: {
            customerTypeList: props.customerTypeList,
            customerGroupList: props.customerGroupList
        }
    });
}

// flash message
const page = usePage();
const toast = useToast();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'info', summary: 'Confirmed', detail: flash.success, life: 3000, group: 'bc' });
        }
        else if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Confirmed', detail: flash.success, life: 3000, group: 'bc' });

        }
    },
)

</script>