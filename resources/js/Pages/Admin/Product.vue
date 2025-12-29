<template>
    <main class="product-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- keyword search -->
                        <Searchbar v-model="filters['global'].value" />

                        <!-- filter category -->
                        <Checkboxselect :list="categoryList" v-model="filterCategory" label="Trạng thái" />

                        <!-- filter status -->
                        <Radioselect :list="statusList" v-model="filterStatus" label="Trạng thái" />
                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <span class="admin-label | fs-700">Hàng hóa</span>
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <!-- toggle add new items menu -->
                                    <AddNewItemsButton label="Thêm mới" :hasMenu="true" :menuItems="createItemsList" />

                                    <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                        optionLabel="header" @update:modelValue="toggleColumn"
                                        placeholder="Select Columns" class="w-full md:w-50" size="small"
                                        :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />
                                </div>
                            </div>
                        </div>
                    </nav>

                    <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt"
                        :value="productList" sortMode="multiple" dataKey="id" removableSort paginator :rows="5"
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
                                    <template v-if="slotProps.data.category === 'service'">
                                        <h3 class="text-lg font-medium">Thông tin dịch vụ</h3>
                                    </template>
                                    <template v-else>
                                        <h3 class="text-lg font-medium">Thông tin hàng hóa</h3>
                                    </template>
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

// data-table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// component
import Searchbar from "../../Components/Searchbar.vue";
import Radioselect from "../../Components/Radioselect.vue";
import Checkboxselect from "../../Components/Checkboxselect.vue";
import AddNewItemsButton from "../../Components/AddNewItemsButton.vue";

// format
import { formatLabel } from "@/Composables/formatData";
import { formatDataTable, getColumns, getDetailRows } from "@/Composables/formatDataTable";

// router
import { router } from '@inertiajs/vue3';
import { ref, reactive, watch, computed, defineAsyncComponent, onMounted } from 'vue';
import { useDialog } from 'primevue/usedialog';

const props = defineProps({
    productList: {
        type: Array,
        default: () => [],
    },
    serviceList: {
        type: Array,
        default: () => [],
    },
});

// hidden fields
const hiddenColumns = reactive(
    [
        'email_verified_at',
        'password_changed_at'
    ]);
const hiddenRows = reactive([
    'id'
])

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter status
const statusList = ref([
    {
        name: true,
        label: 'Đang kinh doanh'
    },
    {
        name: false,
        label: 'Ngừng kinh doanh'
    },
    {
        name: 'all',
        label: 'Tất cả'
    },
]);
const filterStatus = ref('all');

// filter category
const categoryList = ref([
    {
        name: 'all',
        label: 'Tất cả'
    },
    {
        name: 'service',
        label: 'Dịch vụ'
    },
    {
        name: 'food',
        label: 'Đồ ăn'
    },
    {
        name: 'drink',
        label: 'Đồ uống'
    },
]);
const filterCategory = ref([]);

// toggle column
const generalColumns = [
    { field: 'code', header: 'Mã' },
    { field: 'name', header: 'Tên sản phẩm' },
    { field: 'category', header: 'Nhóm hàng' },
    { field: 'selling_price', header: 'Giá bán' },
    { field: 'cost_price', header: 'Giá vốn' },
];
const selectedColumns = ref([]);
const currentColumns = ref([]);
onMounted(() => {
    currentColumns.value = generalColumns
    selectedColumns.value = currentColumns.value.filter(col =>
        !hiddenColumns.includes(col.field)
    );
})
const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};

// row expansion
const expandedRows = ref({});

// filter account by role and active
const initialProductList = computed(() => {
    const products = props.productList || [];
    const services = props.serviceList || [];

    return formatDataTable([...products, ...services]);
});
console.log(initialProductList.value);
const productList = computed(() => {
    // 1. Lấy danh sách gốc
    let list = initialProductList.value || [];

    // 2. Lọc
    return list.filter(item => {
        if (filterCategory.value.length === 0 || filterCategory.value.includes('all')) {
            return true;
        }

        const categoryMatch = filterCategory.value.some(category => {
            if (category === 'service') {
                return item.category === 'service';
            }
            return item.category === category;
        });
        const statusMatch = filterStatus.value === 'all' || item.is_active === filterStatus.value;

        return statusMatch && categoryMatch;
    });
});
watch(() => [props.serviceList, props.productList], (newVal) => {
    // Cập nhật lại danh sách khi có dữ liệu mới
    initialProductList.splice(0, initialProductList.length, ...formatDataTable(newVal));
}, { deep: true });

// dialog
const dialog = useDialog();
// const createAccountDialog = defineAsyncComponent(() => import('../../Components/Dialog/Account/Create.vue'));
// const showCreateAccountDialog = () => {
//     dialog.open(createAccountDialog, {
//         props: {
//             header: 'Thêm tài khoản mới',
//             style: {
//                 width: '50vw',
//             },
//             breakpoints: {
//                 '960px': '50vw',
//                 '640px': '40vw'
//             },
//             modal: true,
//             position: 'center',
//         },
//         data: {
//         }
//     });
// }

// toggle add new item menu
const createItemsList = ref([
    {
        label: 'Hàng hóa',
        command: () => {

        }
    },
    {
        label: 'Dịch vụ',
        command: () => {

        }
    },
]);


</script>