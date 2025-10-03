<template>
    <main class="room-type-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <div class="search-bar | box | flow" style="--flow-spacer:1em">
                            <label class="admin-label label" for="room-type-search">Tìm kiếm</label>
                            <input type="text" name="room-type-search" placeholder="Tìm kiếm hạng phòng">
                        </div>

                        <!-- mutiple select tag -->
                        <div class="box">
                            <MultiSelect :options="branchList" display="chip" optionLabel="name" filter
                                placeholder="Chọn chi nhánh" :maxSelectedLabels="3" class="w-full md:w-52">
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
                                {{ capitalizeFirst(tab) }}
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel v-for="tab in tabs" :key="tab" :value="tab">
                                <DataTable v-model:expandedRows="expandedRows"
                                    :value="tab === 'room' ? roomList : roomTypeList" sortMode="multiple" dataKey="id"
                                    removableSort paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                                    tableStyle="min-width: 50rem">
                                    <!-- <template #header>
                                        <div style="text-align:left">
                                            <MultiSelect :modelValue="selectedColumns"
                                                :options="currentTab === 'room' ? Object.values(props.roomColumns) : Object.values(props.roomTypeColumns)"
                                                @update:modelValue="toggleColumn" display="chip"
                                                placeholder="Select Columns" />
                                        </div>
                                    </template> -->
                                    <Column expander style="width: 5rem" />
                                    <Column v-for="col in tab === 'room' ? roomColumns : roomTypeColumns" :key="col"
                                        :field="col" :header="capitalizeFirst(col)" sortable />
                                    <template #expansion="slotProps">
                                        <Panel header="Detail information">
                                            <div class="p-4">
                                                <table class="w-full border-collapse">
                                                    <tbody>
                                                        <tr v-for="(value, key) in slotProps.data" :key="key">
                                                            <td class="font-semibold border p-2 w-1/3">{{ key }}</td>
                                                            <td class="border p-2">{{ value }}</td>
                                                        </tr>
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
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';

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
import { ref, watch,computed } from 'vue';


function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, " ");
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

watch(
    currentTab, (newValue) => {
        router.visit(route(`admin.${newValue}-management`), {
            preserveState: true, // giữ state nếu muốn
            preserveScroll: true,
        })
    }
);

// row expansion
const expandedRows = ref({});

// toggle column
const selectedColumns = computed(() => {
  return currentTab.value === 'room'
    ? Object.values(props.roomColumns || [])
    : Object.values(props.roomTypeColumns || []);
});
const toggleColumn = (val) => {
    selectedColumns.value = selectedColumns.value.filter(col => val.includes(col));
};

</script>