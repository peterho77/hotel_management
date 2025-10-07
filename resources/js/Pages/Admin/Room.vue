<template>
    <main class="room-type-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <div class="search-bar | box | flow" style="--flow-spacer:1em">
                            <label class="admin-label label" for="room-type-search">Tìm kiếm</label>
                            <IconField iconPosition="left" class="flex items-center gap-x-2">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" size="small"
                                    placeholder="Tìm kiếm hạng phòng" class="!pl-8" />
                            </IconField>
                        </div>

                        <!-- mutiple select tag -->
                        <div class="box">
                            <MultiSelect :options="branchList" v-model="selectedBranches" display="chip"
                                optionLabel="name" optionValue="id" filter placeholder="Chọn chi nhánh"
                                :maxSelectedLabels="1" class="w-full md:w-52">
                            </MultiSelect>
                        </div>

                        <!-- filter status -->
                        <RadioButtonGroup class="filter-status | box | flow flex flex-col">
                            <label class="admin-label" for="room-status">Trạng thái</label>
                            <div v-for="status in statusList" key="filter-status" class="flex items-center gap-2">
                                <RadioButton :inputId="status" name="status" :value="status" />
                                <label :for="status">{{ status }}</label>
                            </div>
                        </RadioButtonGroup>

                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <div class="filter-search">
                                <span class="label admin-label | fs-normal-heading">Hạng phòng & Phòng</span>
                                <!-- --code thêm nút search-- -->
                            </div>
                            <!-- <div class="table-toolbar-buttons">
                                <x-utility.button.add-new-button :newList="['room', 'room_type']"
                                    :branchList="$branchList" :selectList="$roomTypeList" />
                                <x-utility.button.toggle-column-button :$columns />
                            </div> -->
                        </div>
                    </nav>

                    <!-- tabs -->
                    <Tabs v-model:value="currentTab">
                        <TabList>
                            <Tab v-for="tab in tabs" :key="tab" :value="tab">
                                {{ formatLabel(tab) }}
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel v-for="tab in tabs" :key="tab" :value="tab">
                                <DataTable v-model:expandedRows="expandedRows" v-model:filters="filters" ref="dt"
                                    :value="currentTab === 'room' ? roomList : filteredRoomTypeList" sortMode="multiple"
                                    dataKey="id" removableSort paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                                    tableStyle="min-width: 50rem">
                                    <template #header>
                                        <div class="text-right flex items-center justify-end gap-x-4">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText v-model="filters['global'].value" size="small"
                                                    placeholder="Keyword Search" />
                                            </IconField>
                                            <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                                optionLabel="header" @update:modelValue="toggleColumn"
                                                placeholder="Select Columns" class="w-full md:w-50" size="small"
                                                :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />
                                            <Button icon="pi pi-external-link" label="Export" severity="info"
                                                size="small" @click="exportCSV($event)" class="text-neutral-50" />
                                        </div>
                                    </template>
                                    <Column expander style="width: 5rem" />
                                    <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                                        :field="col.field" :header="formatLabel(col.header)" sortable />
                                    <template #expansion="slotProps">
                                        <Panel header="Detail information">
                                            <div class="p-4">
                                                <table class="w-full border-collapse">
                                                    <tbody>
                                                        <template v-for="(value, key) in slotProps.data" :key="key">
                                                            <tr v-if="key !== 'branches' && key !== 'branch' && key !== 'room_type'">
                                                                <td class="font-semibold border p-2 w-1/3">
                                                                    {{ formatLabel(key) }}</td>
                                                                <td class="border p-2">{{ value }}</td>
                                                            </tr>
                                                            <tr v-else-if="key === 'branch' || key === 'room_type'">
                                                                <td class="font-semibold border p-2 w-1/3">
                                                                    {{ formatLabel(key) }}</td>
                                                                <td class="border p-2">{{ value.name }}</td>
                                                            </tr>
                                                            <tr v-else>
                                                                <td class="font-semibold border p-2 w-1/3">
                                                                    {{ formatLabel(key) }}</td>
                                                                <td class="border p-2">
                                                                    {{slotProps.data.branches.map(branch => branch.name).join(', ')}}
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </Panel>
                                    </template>
                                </DataTable>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </section>
            </div>

            <!-- <div class="modals">
                <x-utility.modal.update-modal :item="$activeTab" :$columns :branchList="$branchList"
                    :selectList="$roomTypeList" />
                <x-utility.modal.update-status-modal />
                <x-utility.modal.delete-modal :item="$activeTab" />
            </div> -->
        </div>
    </main>
</template>

<script setup>
// mutiple select
import MultiSelect from 'primevue/multiselect';

// radio button
import Button from 'primevue/button';
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';

// keyword search
import { FilterMatchMode } from '@primevue/core/api';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

// tabs
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

// panel
import Panel from 'primevue/panel';

// data-table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// router
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';


function formatLabel(str) {
    // Thay tất cả dấu '-' hoặc '_' bằng dấu cách
    str = str.replace(/[-_]/g, " ");

    // Viết hoa chữ cái đầu của mỗi từ
    return str.split(" ").map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(" ");
}

const props = defineProps({
    roomList: {
        type: Array,
        required: false,
    },
    roomTypeList: {
        type: Array,
        required: false,
    },
    branchList: {
        type: Array,
        required: false,
    },
    roomColumns: Object,
    roomTypeColumns: Object,
    activeTab: String
})

// status
const statusList = ['Đang kinh doanh', 'Ngừng kinh doanh', 'Tất cả'];

// tabs
const tabs = ['room-type', 'room'];
const currentTab = ref(props.activeTab);

// row expansion
const expandedRows = ref({});

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter room type and room by branch
const selectedBranches = ref([])

// filter room type by branch
const filteredRoomTypeList = computed(() => {
    // default filter behaviour
    if (!selectedBranches.value.length) {
        return props.roomTypeList;
    }

    // selecting branch will affect the data table
    return props.roomTypeList.filter(roomType => {
        const branchIds = roomType.branches.map(b => b.id)
        return selectedBranches.value.every(id => branchIds.includes(id))
    })
})

const filteredRoomList = computed(() => {
    // default filter behaviour
    if (!selectedBranches.value.length) {
        return props.roomList;
    }

    // selecting branch will affect the data table
    return props.roomTypeList.filter(roomType => {
        const branchIds = roomType.branches.map(b => b.id)
        return selectedBranches.value.every(id => branchIds.includes(id))
    })
})

// toggle column
const selectedColumns = ref([]);
const currentColumns = ref({});

watch(
    currentTab, (newValue) => {
        router.visit(route(`admin.${newValue}-management`), {
            preserveState: true, // giữ state nếu muốn
            preserveScroll: true,
        });
    }
);

watch(() => [props.roomColumns, props.roomTypeColumns, currentTab.value],
    () => {
        currentColumns.value = currentTab.value === 'room'
            ? Object.values(props.roomColumns || {}).map(field => ({ field, header: formatLabel(field) }))
            : Object.values(props.roomTypeColumns || {}).map(field => ({ field, header: formatLabel(field) }));

        selectedColumns.value = currentColumns.value;
    },
    { immediate: true });

const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};

console.log(props.roomList);

// export CSV
const dt = ref(null);

const exportCSV = () => {
    // dt.value.exportCSV();
};

</script>