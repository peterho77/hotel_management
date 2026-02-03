<template>
    <main class="room-type-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- keyword search -->
                        <Searchbar v-model="filters['global'].value" />

                        <!-- filter branches -->
                        <Multiselect :list="branchList" placeholder="Chọn chi nhánh" v-model="filterBranches" />

                        <template v-if="currentTab === 'room'">
                            <!-- filter room type -->
                            <Radioselect :list="roomTypeList" v-model="filterRoomType" label="Hạng phòng" />

                            <!-- filter floor -->
                            <Radioselect :list="floorList" v-model="filterFloor" label="Tầng" />
                        </template>

                        <!-- filter status -->
                        <Radioselect :list="statusList" v-model="filterStatus" label="Trạng thái" />
                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <span class="admin-label | fs-700">Hạng phòng & Phòng</span>
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <!-- toggle add new items menu -->
                                    <AddNewItemsButton label="Thêm mới" :hasMenu="true" :menuItems="createItemsList" />

                                    <MultiSelect :modelValue="selectedColumns" :options="currentColumns"
                                        optionLabel="header" @update:modelValue="toggleColumn"
                                        placeholder="Select Columns" class="w-full md:w-50" size="small"
                                        :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="2" />

                                    <Button icon="pi pi-external-link" label="Export" severity="success" size="small"
                                        @click="exportCSV()" class="text-neutral-50" />
                                </div>
                            </div>
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
                                    :value="currentTab === 'room' ? filteredRoomList : filteredRoomTypeList"
                                    sortMode="multiple" dataKey="id" removableSort paginator :rows="5"
                                    :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 60rem">
                                    <Column expander style="width: 5rem" />
                                    <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                                        :field="col.field" :header="formatLabel(col.header)" sortable />
                                    <template #expansion="slotProps">
                                        <Panel>
                                            <div class="p-2">
                                                <div
                                                    :class="['grid grid-cols-1 gap-6', { 'md:grid-cols-2': currentTab !== 'room' }]">
                                                    <ImagesGallery v-if="currentTab !== 'room'"
                                                        :list="slotProps.data.images" />
                                                    <div class="detail-infor">
                                                        <div class="grid grid-cols-1 lg:grid-cols-2">
                                                            <template
                                                                v-for="(value, key) in getDetailRows(slotProps.data, hiddenRows)"
                                                                :key="key">
                                                                <!-- Các field bình thường -->
                                                                <div class="grid gap-x-4">
                                                                    <div class="flex gap-x-1">
                                                                        <span
                                                                            class="font-semibold text-gray-700 min-w-42">
                                                                            {{ formatLabel(key) }}:
                                                                        </span>
                                                                        <template
                                                                            v-if="key === 'branch' || key === 'room_type'">
                                                                            <span class="text-gray-900 grow">
                                                                                {{ value.name }}
                                                                            </span>
                                                                        </template>
                                                                        <template v-else>
                                                                            <span class="text-gray-900 grow">
                                                                                {{ value }}
                                                                            </span>
                                                                        </template>
                                                                    </div>
                                                                    <Divider type="dashed" />
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <!-- Amenities -->
                                                        <div v-if="slotProps.data.amenities?.length">
                                                            <div class="flex mt-2">
                                                                <span
                                                                    class="min-w-44 font-semibold text-gray-700 shrink-0">
                                                                    Amenity:
                                                                </span>
                                                                <span class="text-gray-900 flex-1">
                                                                    {{slotProps.data.amenities.map(a => formatLabel(a.name)).join(', ')}}
                                                                </span>
                                                            </div>
                                                            <Divider type="dashed" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="update-buttons | flex mr-10">
                                                    <Button raised severity="success"
                                                        @click="currentTab === 'room' ? showUpdateRoomDialog(slotProps.data) : showUpdateRoomTypeDialog(slotProps.data)">Update</Button>
                                                    <Button raised severity="danger"
                                                        @click="showDeleteConfirmDialog(slotProps.data.id, currentTab)">Delete</Button>
                                                </div>
                                            </div>
                                        </Panel>
                                    </template>
                                </DataTable>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </section>
            </div>
        </div>
    </main>
</template>

<script setup>
// mutiple select
import MultiSelect from 'primevue/multiselect';

// buttons
import Button from 'primevue/button';

// keyword search
import { FilterMatchMode } from '@primevue/core/api';

// sleketon
import Skeleton from 'primevue/skeleton';

// tabs
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Divider from 'primevue/divider';

// panel
import Panel from 'primevue/panel';

// data-table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// dynamic dialog
import { useDialog } from 'primevue/usedialog';

// router
import { router, useRemember } from '@inertiajs/vue3';
import { ref, reactive, watch, computed, defineAsyncComponent } from 'vue';

// confirm dialog
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

// component
import Searchbar from "../../Components/Searchbar.vue";
import Multiselect from "../../Components/Multiselect.vue";
import Radioselect from "../../Components/Radioselect.vue";
import AddNewItemsButton from "../../Components/AddNewItemsButton.vue";
import ImagesGallery from "../../Components/ImagesGallery.vue";

// format
import { formatLabel } from "@/Composables/formatData";
import { getColumns, getDetailRows } from "@/Composables/formatDataTable";

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
    activeTab: String,
})
console.log(props.roomTypeList);

// hidden fields
const hiddenColumns = reactive([
    'hourly_rate',
    'full_day_rate',
    'overnight_rate',
    'amenities',
    'rooms',
    'description',
    'branch',
    'branches',
    'room_type',
    'images'
]);
const hiddenRows = reactive([
    'rooms',
    'amenities',
    'images',
    'description',
    'branches',
    'id'
]);

// tabs
const tabs = ['room-type', 'room'];
const currentTab = useRemember(props.activeTab, 'admin-current-tab')

// row expansion
const expandedRoomRows = ref({});
const expandedRoomTypeRows = ref({});

const expandedRows = computed({
    get: () => {
        return currentTab.value === 'room'
            ? expandedRoomRows.value
            : expandedRoomTypeRows.value
    },
    set: (val) => {
        if (currentTab.value === 'room') {
            expandedRoomRows.value = val
        } else {
            expandedRoomTypeRows.value = val
        }
    }
})

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter room type and room by branch and status
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
const filterBranches = ref([]);
const filterStatus = ref('all');

// filter room type by branch ans status
const filteredRoomTypeList = computed(() => {
    return (props.roomTypeList || []).filter(roomType => {
        const branchIds = (roomType.branches || []).map(b => b.id)
        const branchMatch = !filterBranches.value.length || filterBranches.value.some(id => branchIds.includes(id));
        const statusMatch = filterStatus.value === 'all' || roomType.status === filterStatus.value;
        return branchMatch && statusMatch;
    })
});

// filter room by room type
const roomTypeList = reactive([
    { label: 'Tất cả', value: 'all', name: 'all' },
    ...props.roomTypeList
]);
const filterRoomType = ref('all');

// filter room by floors
const floorList = reactive([
    { label: 'Tất cả', value: 'all' },
    ...Array.from({ length: 5 }, (_, index) => ({
        label: `Tầng ${index + 1}`,
        value: index + 1,
    }))
]);
const filterFloor = ref('all');

// filter room by branch, status, room type and floor
const filteredRoomList = computed(() => {
    return (props.roomList || []).filter(room => {
        const branchMatch = filterBranches.value.length === 0 ||
            filterBranches.value.includes(room.branch.id)
        const statusMatch = filterStatus.value === 'all' || room.status === filterStatus.value;
        const floorMatch = filterFloor.value === 'all' || room.floor === filterFloor.value;
        const roomTypeMatch = filterRoomType.value === 'all' || room.room_type.name === filterRoomType.value;

        return branchMatch && statusMatch && roomTypeMatch && floorMatch;
    });
})

// toggle add new item menu
const createItemsList = ref([
    {
        label: 'Hạng phòng',
        command: () => {
            showCreateRoomTypeDialog();
        }
    },
    {
        label: 'Phòng',
        command: () => {
            showCreateRoomDialog();
        }
    },
]);

// toggle column
const selectedColumns = ref([]);
const currentColumns = ref([]);

// switch tab
watch(currentTab, (newValue, oldValue) => {
    if (newValue === oldValue) return

    router.visit(route(`admin.${newValue}.index`), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
})

watch(
    () => [
        currentTab.value,
        props.roomList,
        props.roomTypeList
    ],
    () => {
        const data =
            currentTab.value === 'room'
                ? props.roomList?.[0]
                : props.roomTypeList?.[0]

        if (!data) return

        currentColumns.value = getColumns(data, hiddenColumns)
        selectedColumns.value = currentColumns.value
    },
    { immediate: true }
)

const toggleColumn = (val) => {
    selectedColumns.value = currentColumns.value.filter(col => {
        return val.includes(col);
    })
};

// dialog
const createRoomDialog = defineAsyncComponent(() => import('../../Components/Dialog/Room/Create.vue'));
const createRoomTypeDialog = defineAsyncComponent(() => import('../../Components/Dialog/Room-type/Create.vue'));
const updateRoomDialog = defineAsyncComponent(() => import('../../Components/Dialog/Room/Update.vue'));
const updateRoomTypeDialog = defineAsyncComponent(() => import('../../Components/Dialog/Room-type/Update.vue'));

const dialog = useDialog();
// create dialog
const showCreateRoomDialog = () => {
    dialog.open(createRoomDialog, {
        props: {
            header: 'Add new room',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '60vw',
                '640px': '50vw'
            },
            modal: true
        },
        data: {
            roomTypeList: props.roomTypeList,
            branchList: props.branchList
        }
    });
}
const showCreateRoomTypeDialog = () => {
    dialog.open(createRoomTypeDialog, {
        props: {
            header: 'Add new room type',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '60vw',
                '640px': '50vw'
            },
            modal: true
        },
        data: {
            roomTypeList: props.roomTypeList,
            branchList: props.branchList
        }
    });
}

// update dialog
const showUpdateRoomDialog = (room) => {
    dialog.open(updateRoomDialog, {
        props: {
            header: 'Update room',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '60vw',
                '640px': '50vw'
            },
            modal: true
        },
        data: {
            roomTypeList: props.roomTypeList,
            branchList: props.branchList,
            currentRoom: room
        }
    });
}
const showUpdateRoomTypeDialog = (roomType) => {
    dialog.open(updateRoomTypeDialog, {
        props: {
            header: 'Update room type',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '60vw',
                '640px': '50vw'
            },
            modal: true
        },
        data: {
            branchList: props.branchList,
            currentRoomType: roomType
        }
    });
}

// confirm dialog 
const confirm = useConfirm();
const toast = useToast();

const showDeleteConfirmDialog = (id, tab) => {
    confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Danger Zone',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            if (tab === 'room') {
                router.delete(
                    route('admin.room.delete', id),
                    {
                        preserveScroll: true, // không cuộn lại đầu trang
                        preserveState: true, // reset state page
                    }
                );
            }
            else {
                router.delete(
                    route('admin.room-type.delete', id), 
                    {
                        preserveScroll: true, 
                        preserveState: true,
                    }
                );
            }
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    })
};

// export CSV
const dt = ref();
const exportCSV = () => {
    if (dt.value) {
        console.log(currentTab);
        if (currentTab.value === 'room-type') {
            dt.value[0].exportCSV();
        }
        else {
            dt.value[1].exportCSV();
        }
    }
};

const responsiveOptions = ref([
    {
        breakpoint: '1300px',
        numVisible: 4
    },
    {
        breakpoint: '575px',
        numVisible: 1
    }
]);

</script>