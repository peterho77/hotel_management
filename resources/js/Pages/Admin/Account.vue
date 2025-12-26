<template>
    <main class="account-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- keyword search -->
                        <Searchbar v-model="filters['global'].value" />

                        <!-- filter role -->
                        <div class="filter-role | box flow grid">
                            <label class="admin-label">Vai trò</label>
                            <Select v-model="filterRole" 
                            :options="roles" 
                            optionLabel="label" 
                            optionValue="name" 
                            placeholder="Select a role" class="w-full" />
                        </div>

                        <!-- filter status -->
                        <Radioselect :list="statusList" v-model="filterStatus" label="Trạng thái" />
                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <span class="admin-label | fs-700">Tài khoản</span>
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <!-- toggle add new items menu -->
                                    <Button label="Tài khoản" icon="pi pi-plus" severity="success" size="small" @click="showAddNewAccount"/>

                                    <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                        optionLabel="header" @update:modelValue="toggleColumn"
                                        placeholder="Select Columns" class="w-full md:w-50" size="small"
                                        :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />
                                </div>
                            </div>
                        </div>
                    </nav>

                    <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt" :value="usersList"
                        sortMode="multiple" dataKey="id" removableSort paginator :rows="5"
                        :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 60rem">
                        <template #empty>
                            <h3 class="text-center text-lg font-medium">No users found.</h3>
                        </template>
                        <Column expander style="width: 3.5rem" />
                        <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                            :field="col.field" :header="formatLabel(col.header)" sortable />
                        <template #expansion="slotProps">
                            <Panel>
                                <div class="detail-infor | grid gap-6">
                                    <h3 class="text-lg font-medium">Thông tin đặt phòng</h3>
                                    <div class="grid grid-cols-2">
                                        <template v-for="(value, key) in getDetailRows(slotProps.data, hiddenRows)"
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
    usersList: {
        type: Array,
        required: false,
    },
});

// hidden fields
const hiddenColumns = reactive(
    [
        'email_verified_at',
        'password_changed_at'
    ]);
const hiddenRows = reactive([
])

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter status
const statusList = ref([
    {
        name: 'active',
        label: 'Đang hoạt động'
    },
    {
        name: 'inactive',
        label: 'Ngừng hoạt động'
    },
    {
        name: 'all',
        label: 'Tất cả'
    },
]);
const filterStatus = ref('all');

// toggle column
const selectedColumns = ref([]);
const currentColumns = ref([]);
onMounted(() => {
    if (props.usersList.length > 0) {
        currentColumns.value = getColumns(props.usersList[0], hiddenColumns);
    }

    selectedColumns.value = currentColumns.value;
})
const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};

// row expansion
const expandedRows = ref({});

// filter row
const roles = reactive([
    {
        name: 'all',
        label: 'Tất cả'
    },
    {
        name: 'admin',
        label: 'admin'
    },
    {
        name: 'manager',
        label: 'manager'
    },
    {
        name: 'employee',
        label: 'employee'
    },
    {
        name: 'customer',
        label: 'customer'
    },
])
const filterRole = ref('all');

// filter account by role and active
const initialUsersList = reactive(formatDataTable(props.usersList));
const usersList = computed(() => {
    return (initialUsersList || []).filter(user => {
        const roleMatch = filterRole.value === 'all' || user.role === filterRole.value;
        return roleMatch;
    })
});
watch(() => props.usersList, (newVal) => {
    // Cập nhật lại danh sách khi có tài khoản mới
    initialUsersList.splice(0, initialUsersList.length, ...formatDataTable(newVal));
}, { deep: true });

// open add review dialog
const dialog = useDialog();
const addNewAccount = defineAsyncComponent(() => import('../../Components/Dialog/AddNewAccount.vue'));

const showAddNewAccount = () => {
    dialog.open(addNewAccount, {
        props: {
            header: 'Thêm tài khoản mới',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'center',
        },
        data: {
        }
    });
}

</script>