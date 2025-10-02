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

                        <!-- --custom mutiple select tag--  -->
                        <!-- <div class="box">
                            <x-utility.multiple-select-tag id="filter-branch-select" :list="$branchList"
                                placeholder="Chọn chi nhánh" />
                        </div> -->

                        <form class="filter-status | box | flow" style="--flow-spacer:1em">
                            <label class="label admin-label" for="room-status">Trạng thái</label>
                            <div class="radio-select | form-check">
                                <input class="form-check-input" type="radio" name="filter-status" value="active"
                                    id="active-room">
                                <label class="form-check-label" for="filter-status">
                                    Đang kinh doanh
                                </label>
                            </div>
                            <div class="radio-select | form-check">
                                <input class="form-check-input" type="radio" name="filter-status" value="inactive"
                                    id="inactive-room">
                                <label class="form-check-label" for="filter-status">
                                    Ngừng kinh doanh
                                </label>
                            </div>
                            <div class="radio-select | form-check">
                                <input class="form-check-input" type="radio" name="filter-status" value="all"
                                    id="all-room" checked>
                                <label class="form-check-label" for="filter-status">
                                    Tất cả
                                </label>
                            </div>
                        </form>

                        <section class="record-quantity">
                            <label for="record">Số bản ghi: </label>
                            <select name="record" id="record" class="nice-select">
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </section>
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
                    <Tabs>
                        <TabList>
                            <Tab v-for="tab in tabs" :key="tab" :value="tab" @click="goToTab(tab)">
                                {{ capitalizeFirst(tab) }}
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel>
                                <DataTable :key="currentTab" v-show="roomList" :value="roomList">
                                    <Column v-for="col in roomColumns" :key="col"
                                        :field="col" :header="capitalizeFirst(col)" />
                                </DataTable>
                            </TabPanel>
                             <TabPanel>
                                <DataTable :key="currentTab" v-show="roomTypeList" :value="roomTypeList">
                                    <Column v-for="col in roomTypeColumns" :key="col"
                                        :field="col" :header="capitalizeFirst(col)" />
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
// tabs
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

// data-table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

// router
import { router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';


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
    activeTab: {
        type: String,
        default: "room_type"
    }
})


const tabs = ['room-type', 'room'];
const currentTab = ref(props.activeTab);

const goToTab = (tab) => {
    if (currentTab.value === tab) return // tránh reload route hiện tại
    currentTab.value = tab;
    // router.visit(route(`admin.${tab}-management`), { replace: true })
}

watch(
    currentTab , (newValue, oldValue) => {
        router.visit(route(`admin.${newValue}-management`))
    }
)

// console.log(props.roomColumns);
// console.log(props.roomTypeColumns);
console.log(props.activeTab);

</script>