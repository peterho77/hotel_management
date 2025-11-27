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
                                    <AddNewItemsButton label="Thêm mới" :hasMenu="true" :menuItems="addNewItems" />

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
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <div class="image-gallery">
                                                        <Galleria :value="getGalleryImages(slotProps.data.images)"
                                                            :responsiveOptions="responsiveOptions" :numVisible="4"
                                                            containerStyle="max-width: 460px" :circular="true">
                                                            <template #item="slotProps">
                                                                <img :src="slotProps.item.itemImageSrc"
                                                                    :alt="slotProps.item.alt"
                                                                    class="w-full max-h-96 object-cover rounded-lg" />
                                                            </template>

                                                            <template #thumbnail="slotProps">
                                                                <img :src="slotProps.item.itemImageSrc"
                                                                    :alt="slotProps.item.alt"
                                                                    class="max-h-30 w-20 object-cover rounded-md border-[0.5px]" />
                                                            </template>
                                                        </Galleria>
                                                    </div>
                                                    <div class="detail-infor">
                                                        <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] gap-x-3">
                                                            <template v-for="(value, key) in slotProps.data" :key="key">
                                                                <!-- Các field bình thường -->
                                                                <template
                                                                    v-if="!['rooms', 'amenities', 'images', 'description', 'branches', 'id'].includes(key)">
                                                                    <div class="grid gap-y-1 gap-x-2 columns-2">
                                                                        <div>
                                                                            <div class="flex gap-x-1">
                                                                                <span
                                                                                    class="font-semibold text-gray-700 min-w-42">
                                                                                    {{ formatLabel(key) }}:
                                                                                </span>
                                                                                <span class="text-gray-900 flex-grow">
                                                                                    <template
                                                                                        v-if="key === 'branch' || key === 'room_type'">
                                                                                        {{ value.name }}
                                                                                    </template>
                                                                                    <!-- <template v-else-if="key === 'branches'">
                                                                                        {{slotProps.data.branches.map(branch => branch.name).join(', ')}}
                                                                                    </template> -->
                                                                                    <template v-else>
                                                                                        {{ value }}
                                                                                    </template>
                                                                                </span>
                                                                            </div>
                                                                            <Divider type="dashed" />
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                        </div>

                                                        <!-- Field amenities -->
                                                        <template v-if="slotProps.data.amenities">
                                                            <div v-for="(amenityValue, amenityKey) in JSON.parse(slotProps.data.amenities)"
                                                                :key="amenityKey" class="flex flex-col gap-y-1">
                                                                <div class="flex">
                                                                    <span
                                                                        class="min-w-44 font-semibold text-gray-700 shrink-0">
                                                                        {{ formatLabel(amenityKey) }}:
                                                                    </span>
                                                                    <span class="text-gray-900 flex-1">
                                                                        {{ Array.isArray(amenityValue) ? amenityValue.join(', ') : amenityValue }}
                                                                    </span>
                                                                </div>
                                                                <Divider type="dashed" />
                                                            </div>
                                                        </template>
                                                    </div>
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

// galleria
import Galleria from 'primevue/galleria';

// dynamic dialog
import { useDialog } from 'primevue/usedialog';

// router
import { router } from '@inertiajs/vue3';
import { ref, watch, computed, defineAsyncComponent, onMounted } from 'vue';

// confirm dialog
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { usePage } from '@inertiajs/vue3';

// component
import Searchbar from "../../Components/Searchbar.vue";
import Multiselect from "../../Components/Multiselect.vue";
import Radioselect from "../../Components/Radioselect.vue";
import AddNewItemsButton from "../../Components/AddNewItemsButton.vue";

// format
import { formatLabel } from "@/Composables/formatData";

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
const filterBranches = ref([])
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

// filter room by branch and status
const filteredRoomList = computed(() => {
    return (props.roomList || []).filter(room => {
        const branchMatch = filterBranches.value.length === 0 ||
            filterBranches.value.includes(room.branch.id)
        const statusMatch = filterStatus.value === 'all' || room.status === filterStatus.value;

        return branchMatch && statusMatch;
    });
})

// toggle add new item menu
const addNewItems = ref([
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
]);

// toggle column
const selectedColumns = ref([]);
const currentColumns = ref([]);

watch(
    currentTab, (newValue) => {
        router.visit(route(`admin.${newValue}-management`), {
            preserveState: true, // giữ state nếu muốn
            preserveScroll: true,
        });
    }
);

const hiddenColumns = ref(['hourly_rate', 'full_day_rate', 'overnight_rate', 'amenities', 'rooms']);
watch(hiddenColumns, (val) => {
    localStorage.setItem('hiddenColumns', JSON.stringify(val));
}, { deep: true });

onMounted(() => {
    const savedHidden = localStorage.getItem('hiddenColumns');
    if (savedHidden) {
        hiddenColumns.value = JSON.parse(savedHidden);
    }

    currentColumns.value = currentTab.value === 'room'
        ? Object.values(props.roomColumns || {}).map(field => ({ field, header: formatLabel(field) }))
        : Object.values(props.roomTypeColumns || {}).map(field => ({ field, header: formatLabel(field) }));
    currentColumns.value = currentColumns.value.filter(
        item => !hiddenColumns.value.includes(item.field)
    );

    selectedColumns.value = currentColumns.value;
})

watch(() => [props.roomColumns, props.roomTypeColumns, currentTab.value],
    () => {
        const savedHidden = localStorage.getItem('hiddenColumns');
        if (savedHidden) {
            hiddenColumns.value = JSON.parse(savedHidden);
        }

        currentColumns.value = currentTab.value === 'room'
            ? Object.values(props.roomColumns || {}).map(field => ({ field, header: formatLabel(field) }))
            : Object.values(props.roomTypeColumns || {}).map(field => ({ field, header: formatLabel(field) }));
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

// open add new or update dialog
const addNewRoom = defineAsyncComponent(() => import('../../Components/Dialog/AddNewRoom.vue'));
const addNewRoomType = defineAsyncComponent(() => import('../../Components/Dialog/AddNewRoomType.vue'));
const updateRoom = defineAsyncComponent(() => import('../../Components/Dialog/UpdateRoom.vue'));
const updateRoomType = defineAsyncComponent(() => import('../../Components/Dialog/UpdateRoomType.vue'));

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
                width: '40%',
            },
            breakpoints: {
                '960px': '80%',
                '640px': '90%'
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

// room type images gallery
const getGalleryImages = (images) => {
    // Nếu có ảnh thật từ DB
    if (images && images.length > 0) {
        return images.map(img => ({
            itemImageSrc: `/storage/${img.path}`,
            thumbnailImageSrc: `/storage/${img.path}`,
            alt: img.alt_text
        }))
    }

    // Nếu không có ảnh → trả về 5 ảnh mặc định
    return Array.from({ length: 5 }, (_, i) => ({
        itemImageSrc: `/img/default-blank-img.jpg`,
        thumbnailImageSrc: `/img/default-blank-img.jpg`,
        alt: `default-blank-${i + 1}`
    }))
}

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