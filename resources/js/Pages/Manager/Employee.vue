<template>
    <main class="employee-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <h3 class="fs-semibold">Danh sách nhân viên</h3>

                        <!-- keyword search -->
                        <Searchbar v-model="filters['global'].value" />

                        <!-- filter status -->
                        <Radioselect :list="statusList" v-model="filterStatus" label="Trạng thái nhân viên" />

                        <!-- filter branches -->
                        <MultiSelect :list="branchList" placeholder="Chọn chi nhánh" v-model="filterBranches" />
                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <!-- toggle add new items menu -->
                                    <Button label="Nhân viên" icon="pi pi-plus" severity="success" size="small" />

                                    <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                        optionLabel="header" @update:modelValue="toggleColumn"
                                        placeholder="Select Columns" class="w-full md:w-50" size="small"
                                        :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />
                                </div>
                            </div>
                        </div>
                    </nav>

                    <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt"
                        :value="employeeList" sortMode="multiple" dataKey="id" removableSort paginator :rows="5"
                        :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 60rem">
                        <template #empty>
                            <h3 class="text-center text-lg font-medium">No employee found.</h3>
                        </template>
                        <Column expander style="width: 3.5rem" />
                        <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                            :field="col.field" :header="formatLabel(col.header)" sortable />
                        <template #expansion="slotProps">
                            <Panel>
                                <div class="detail-infor | grid gap-6">
                                    <h3 class="text-lg font-medium">Thông tin nhân viên</h3>
                                    <div class="grid grid-cols-2">
                                        <template v-for="(value, key) in getDetailRows(slotProps.data, hiddenRows)"
                                            :key="key">
                                            <!-- Các field bình thường -->
                                            <div class="grid gap-x-4">
                                                <div class="flex gap-x-1">
                                                    <span class="font-semibold text-gray-700 min-w-42">
                                                        {{ formatLabel(key) }}:
                                                    </span>
                                                    <span v-if="key === 'branch'" class="text-gray-900 grow">
                                                        {{ value.name }}
                                                    </span>
                                                    <span v-else class="text-gray-900 grow">
                                                        {{ value }}
                                                    </span>
                                                </div>
                                                <Divider type="dashed" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mr-10 mt-6 flex gap-4">
                                    <Button severity="info" label="Cập nhật" raised />
                                    <Button severity="danger" label="Xóa" raised />
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
// keyword search
import { FilterMatchMode } from '@primevue/core/api';

import Button from 'primevue/button';
import Panel from 'primevue/panel';
import MultiSelect from 'primevue/multiselect';
import Divider from 'primevue/divider';
import Select from 'primevue/select';

// data-table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// component
import Searchbar from "../../Components/Searchbar.vue";
import Radioselect from "../../Components/Radioselect.vue";

// format
import { formatLabel } from "@/Composables/formatData";
import { formatDataTable, getColumns, getDetailRows } from "@/Composables/formatDataTable";

// router
import { router } from '@inertiajs/vue3';
import { ref, reactive, watch, computed, defineAsyncComponent, onMounted } from 'vue';
import { useDialog } from 'primevue/usedialog';

const props = defineProps({
    employeeList: {
        type: Array,
        required: false,
    },
    branchList: {
        type: Array,
        required: false,
    },
});

// hidden fields
const hiddenColumns = reactive(
    [
        'branch',
        'branch_id',
        'user_id',
        'has_account',
        'created_at',
        'updated_at'
    ]);
const hiddenRows = reactive([
])

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter employee by status
const filterStatus = ref('all');

// filter employee by branch
const statusList = ref([
    {
        name: 'active',
        label: 'Đang làm việc'
    },
    {
        name: 'inactive',
        label: 'Đã nghỉ'
    },
]);
const filterBranches = ref([]);

const initialEmployeeList = reactive(formatDataTable(props.employeeList));
const employeeList = computed(() => {
    return (initialEmployeeList || []).filter(employee => {
        const branchMatch = !filterBranches.value.length || filterBranches.value.some(id => id === employee.branch.id);
        return branchMatch;
    })
});

// expanded row
const expandedRows = ref({});

// toggle column
const selectedColumns = ref([]);
const currentColumns = ref([]);
onMounted(() => {
    if (props.employeeList.length > 0) {
        currentColumns.value = getColumns(props.employeeList[0], hiddenColumns);
    }

    selectedColumns.value = currentColumns.value;
})
const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};


</script>