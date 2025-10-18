<template>
    <main class="room-type-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <div class="search-bar | box | flow" style="--flow-spacer:1em">
                            <label class="admin-label" for="room-type-search">Tìm kiếm</label>
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
                        <div class="filter-status | box | flow flex flex-col">
                            <label class="admin-label" for="room-status">Trạng thái</label>
                            <div v-for="(status, index) in statusList" :key="index" class="flex items-center gap-2">
                                <RadioButton v-model="filterStatus" :inputId="status.name" name="status"
                                    :value="status.label" />
                                <label :for="status.name">{{ status.label }}</label>
                            </div>
                        </div>

                    </div>
                </section>
                <section class="main-content__right | flow" style="--flow-spacer:1em">
                    <nav class="table-toolbar">
                        <div class="nav-wrapper">
                            <div class="filter-search">
                                <span class="admin-label | fs-700">Hạng phòng & Phòng</span>
                            </div>
                            <div class="table-toolbar-buttons">
                                <div class="text-right flex items-center justify-end gap-x-4">
                                    <SplitButton severity="success" size="small" :model="addNewItems"
                                        raised>
                                        <div class="flex items-center gap-x-2 sub-menu">
                                            <i class="pi pi-plus" style="font-size: .875rem"></i>
                                            <span>Add new</span>
                                        </div>
                                    </SplitButton>
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
                                    <Button icon="pi pi-external-link" label="Export" severity="info" size="small"
                                        @click="exportCSV($event)" class="text-neutral-50" />
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
                                    :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                                    <Column expander style="width: 5rem" />
                                    <Column v-for="(col, index) of selectedColumns" :key="col.field + '_' + index"
                                        :field="col.field" :header="formatLabel(col.header)" sortable />
                                    <template #expansion="slotProps">
                                        <Panel header="Detail information">
                                            <div class="p-4">
                                                <div
                                                    class="grid grid-cols-[auto_1fr] md:grid-cols-[auto_1fr] gap-x-6 gap-y-3">
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
import SplitButton from 'primevue/splitbutton';
import RadioButton from 'primevue/radiobutton';

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

// dynamic dialog
import { useDialog } from 'primevue/usedialog';

// router
import { router } from '@inertiajs/vue3';
import { ref, watch, computed, defineAsyncComponent } from 'vue';

// confirm dialog
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { usePage } from '@inertiajs/vue3'

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

// tabs
const tabs = ['room-type', 'room'];
const currentTab = ref(props.activeTab);

// row expansion
const expandedRoomRows = ref({});
const expandedRoomTypeRows = ref({});

const expandedRows = computed(
    () => currentTab.value === 'room' ? expandedRoomRows.value : expandedRoomTypeRows.value,
)

// keyword search
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// filter room type and room by branch
const selectedBranches = ref([])

// filter room and room type by status
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

// filter room type by branch ans status
const filteredRoomTypeList = computed(() => {
    return (props.roomTypeList || []).filter(roomType => {
        const branchIds = (roomType.branches || []).map(b => b.id)
        const branchMatch = !selectedBranches.value.length || selectedBranches.value.some(id => branchIds.includes(id));
        const statusMatch = filterStatus.value === 'Tất cả' || roomType.status === filterStatus.value;
        return branchMatch && statusMatch;
    })
});

// filter room by branch and status
const filteredRoomList = computed(() => {
    return (props.roomList || []).filter(room => {
        const branchMatch = selectedBranches.value.length === 0 ||
            selectedBranches.value.includes(room.branch.id)
        const statusMatch = filterStatus.value === 'Tất cả' || room.status === filterStatus.value;

        return branchMatch && statusMatch;
    });
})

// add new menu
import Menu from 'primevue/menu';

const addMenu = ref();
const addNewItems = [
    {
        label: 'Hạng phòng',
        command: () => {
            showAddNewRoomType();
        }
    },
    {
        label: 'Phòng',
        command: () => {
            showAddNewRoom();
        }
    },
];

const toggle = (event) => {
    addMenu.value.toggle(event);
};


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

// open add new or update dialog
const addNewRoom = defineAsyncComponent(() => import('../../Components/Utility/Dialog/AddNewRoom.vue'));
const addNewRoomType = defineAsyncComponent(() => import('../../Components/Utility/Dialog/AddNewRoomType.vue'));
const updateRoom = defineAsyncComponent(() => import('../../Components/Utility/Dialog/UpdateRoom.vue'));
const updateRoomType = defineAsyncComponent(() => import('../../Components/Utility/Dialog/UpdateRoomType.vue'));

const dialog = useDialog();
// add new dialog
const showAddNewRoom = () => {
    const dialogRef = dialog.open(addNewRoom, {
        props: {
            header: 'Add new room',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true
        },
        data: {
            roomTypeList: props.roomTypeList,
            branchList: props.branchList
        }
    });
}
const showAddNewRoomType = () => {
    const dialogRef = dialog.open(addNewRoomType, {
        props: {
            header: 'Add new room type',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
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
const showUpdateRoom = (oldData) => {
    const dialogRef = dialog.open(updateRoom, {
        props: {
            header: 'Update room',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true
        },
        data: {
            roomTypeList: props.roomTypeList,
            branchList: props.branchList,
            initialData: oldData
        }
    });
}
const showUpdateRoomType = (oldData) => {
    const dialogRef = dialog.open(updateRoomType, {
        props: {
            header: 'Update room type',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true
        },
        data: {
            branchList: props.branchList,
            initialData: oldData
        }
    });
}

// confirm dialog 
const confirm = useConfirm();
const toast = useToast();
const page = usePage();

const deleteConfirm = (id, tab) => {
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
                    route('admin.room.delete', id), // hoặc '/users/123'
                    {
                        preserveScroll: true, // không cuộn lại đầu trang
                        preserveState: true, // reset state page
                    }
                );
            }
            else {
                router.delete(
                    route('admin.room-type.delete', id), // hoặc '/users/123'
                    {
                        preserveScroll: true, // không cuộn lại đầu trang
                        preserveState: true, // reset state page
                    }
                );
            }
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    })
};

// flash message
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'info', summary: 'Confirmed', detail: flash.success, life: 3000 });
        }
    },
)

// export CSV
const dt = ref(null);
const exportCSV = () => {
    // dt.value.exportCSV();
};

</script>