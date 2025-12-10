<template>
    <div class="booking-section pb-10">
        <div class="booking-section-header | padding-block-200">
            <Form @submit="searchBestRoomOptions"
                class="booking-filter-section-form | text-center gap-y-3 bg-amber-100 padding-block-400 px-4 rounded-xl shadow-xl">
                <div class="flex gap-x-4 items-center">
                    <!-- Check-in / Check-out -->
                    <div>
                        <label class="block mb-1">Check-in/Check-out</label>
                        <FormField name="dateRange" v-slot="{ field, error }">
                            <DatePicker v-model="filterRoomBookingForm.dateRange" dateFormat="dd/mm/yy"
                                selectionMode="range" :manualInput="false" :min-date="new Date()" showIcon fluid />
                            <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                        </FormField>
                    </div>

                    <!-- Num of guests -->
                    <div>
                        <div>
                            <label class="block mb-1">Guest</label>
                        </div>
                        <FormField @click="toggle">
                            <IconField>
                                <InputIcon class="pi pi-users" />
                                <InputText :placeholder="numOfGuestsSummary" disabled />
                                <InputIcon class="pi pi-angle-down" />
                            </IconField>
                        </FormField>

                        <!-- guest option popover -->
                        <Popover ref="guestOptionMenu">
                            <div class="py-2 px-3 space-y-3">
                                <!-- Adult -->
                                <div class="flex justify-between gap-x-3 items-center">
                                    <div class="flex flex-col">
                                        <span class="text-base">Người lớn</span>
                                        <span class="fs-300 text-gray-500">18 tuổi trở lên</span>
                                    </div>
                                    <InputNumber v-model="filterRoomBookingForm.numOfAdults" name="num_of_guests"
                                        showButtons size="small" :min="1" :pt="{
                                            incrementButton: { class: 'bg-gray-100' },
                                            decrementButton: { class: 'bg-gray-100' },
                                            incrementIcon: { class: 'pi pi-plus' },
                                            decrementIcon: { class: 'pi pi-minus' }
                                        }" />
                                </div>

                                <!-- Children -->
                                <div class="flex justify-between gap-x-3 items-center">
                                    <div class="flex flex-col">
                                        <span class="text-base">Trẻ em</span>
                                        <span class="fs-300 text-gray-500">0-17 tuổi</span>
                                    </div>
                                    <InputNumber v-model="filterRoomBookingForm.numOfChildren" showButtons size="small"
                                        :min="0" :max="10" :pt="{
                                            incrementButton: { class: 'bg-gray-100' },
                                            decrementButton: { class: 'bg-gray-100' },
                                            incrementIcon: { class: 'pi pi-plus' },
                                            decrementIcon: { class: 'pi pi-minus' }
                                        }" />
                                </div>
                            </div>
                        </Popover>
                    </div>

                    <!-- Num of rooms -->
                    <div>
                        <label class="block mb-1">Room</label>
                        <FormField name="num_of_rooms" v-slot="{ field, error }">
                            <InputNumber v-model="filterRoomBookingForm.numOfRooms" showButtons :min="1" :pt="{
                                incrementButton: { class: 'bg-gray-100' },
                                decrementButton: { class: 'bg-gray-100' },
                                incrementIcon: { class: 'pi pi-plus' },
                                decrementIcon: { class: 'pi pi-minus' }
                            }" :suffix="` room${filterRoomBookingForm.numOfRooms > 1 ? 's' : ''}`" />
                        </FormField>
                    </div>
                </div>

                <Button type="submit" class="mt-6" severity="info" size="large" label="Tìm phòng" raised />
            </Form>
        </div>
        <div class="container">
            <div class="booking-section-content | mt-20 flow">
                <div class="best-option">
                    <div class="mb-2 p-2">
                        <h3 class="fs-700">{{ summarySearchInfor }}</h3>
                    </div>

                    <DataTable :value="bestRoomOptions" rowGroupMode="rowspan" groupRowsBy="total_price"
                        :showHeaders="false" sortField="total_price" showGridlines class="text-lg w-full">
                        <!-- Cột thông tin phòng + chính sách -->
                        <Column header="Danh sách phòng">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1 p-2">
                                    <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                        {{ data.selected_quantity + ' x ' + data.room_type.name }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">Price for</span>
                                        <div class="flex gap-1 items-center">
                                            <template v-for="item in data.room_option.num_adults">
                                                <i class="pi pi-user" style="color: black;font-size: .85rem"
                                                    v-tooltip.bottom="getLabel('adult', data.room_option.num_adults)" />
                                            </template>
                                        </div>
                                        <template v-if="data.num_children > 0">
                                            <div class="flex items-center gap-2">
                                                <i class="pi pi-plus" style="color: black;font-size: .7rem"></i>
                                                <template v-for="item in data.room_option.num_children">
                                                    <div>
                                                        <i class="pi pi-user" style="color: black; font-size:0.75rem"
                                                            v-tooltip.bottom="getLabel('child', data.room_option.num_children)" />
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <span class="text-gray-600 text-sm">
                                        {{ data.room_type.bed_type }} bed
                                    </span>
                                    <span class="text-red-600 font-medium text-sm">
                                        {{ data.rate_policy.name }}
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        {{ getPaymentRequired(data.rate_policy.payment_requirement) }}
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <!-- Cột giá -->
                        <Column header="Giá" style="text-align: center">
                            <template #body="{ data }">
                                <div class="flex flex-col items-center"
                                    :v.tooltip.html="getPriceDesc(data.room_option.price, filterRoomBookingForm.numOfNights)">
                                    <span class="line-through text-gray-400 text-sm">
                                        VND {{ data.total_base_price_per_room_type }}
                                    </span>
                                    <span class="font-bold text-red-600 text-md">
                                        <!-- discount -->
                                        VND {{ data.total_price_per_room_type }}
                                    </span>
                                    <span class="text-gray-500 text-xs">Đã gồm thuế & phí</span>
                                </div>
                            </template>
                        </Column>

                        <!-- Cột nhóm: tổng giá -->
                        <Column field="total_price" header="Tổng giá (VNĐ)">
                            <template #body="{ data }">
                                <div class="text-left flex flex-col gap-3">
                                    <span class="text-sm">{{ summaryBookingInfor }}</span>
                                    <div class="flex gap-x-2">
                                        <span>Total: </span>
                                        <h3 class="text-lg fw-bold">{{ data.total_price }} VND</h3>
                                    </div>
                                    <Button label="Đặt các lựa chọn của bạn" class="max-w-60"
                                        @click="setSelectedBookingRoomOptions" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <div class="other-options">
                    <div class="p-2">
                        <span class="text-md">Other empty options</span>
                    </div>
                    <div class="grid grid-cols-[auto_1fr] gap-x-2 items-start">
                        <DataTable :value="emptyRoomOptions" rowGroupMode="rowspan" groupRowsBy="room_type.name"
                            sortField="id" showGridlines class="text-lg w-full items-start"
                            tableStyle="max-width: 74rem">
                            <!-- Cột thông tin phòng -->
                            <Column field="room_type.name" header="Danh sách phòng"
                                style="min-width:fit-content;vertical-align:top;">
                                <template #body="{ data }">
                                    <div class="flex flex-col items-start justify-start gap-1 p-2">
                                        <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                            {{ data.room_type.name }}
                                        </span>
                                        <span class="text-red-600 text-sm">
                                            {{ data.room_option.available_quantity + ' ' + (data.room_option.available_quantity > 1 ? 'rooms' : 'room') }}
                                            left
                                        </span>
                                        <span class="text-gray-600 text-sm">
                                            {{ data.room_type.bed_type }} bed
                                        </span>
                                        <div class="flex flex-wrap gap-y-2 gap-x-3 mt-2">
                                            <template v-for="amenity in data.room_type.amenities">
                                                <div class="flex items-center gap-1">
                                                    <i :class="amenity.icon"></i>
                                                    <span class="text-gray-500 text-sm">
                                                        {{ amenity.name.charAt(0).toUpperCase() + amenity.name.slice(1) }}
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </Column>

                            <!-- Num of guests -->
                            <Column header="Số lượng khách" style="vertical-align:top; max-width:5%">
                                <template #body="{ data }">
                                    <div class="flex gap-2 items-center">
                                        <template v-for="(item, idx) in data.room_option.num_adults">
                                            <div>
                                                <i class="pi pi-user" style="color: black"
                                                    v-tooltip.bottom="getLabel('adult', data.room_option.num_adults)" />
                                            </div>
                                        </template>
                                        <template v-if="data.room_option.num_children > 0">
                                            <i class="pi pi-plus" style="color: black;font-size: .75rem"></i>
                                            <template v-for="item in data.room_option.num_children">
                                                <div>
                                                    <i class="pi pi-user" style="color: black; font-size:0.85rem"
                                                        v-tooltip.bottom="getLabel('child', data.room_option.num_children)" />
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </Column>

                            <!-- Cột giá -->
                            <Column header="Giá" style="vertical-align:top;text-align:center;text-wrap:nowrap">
                                <template #body="{ data }">
                                    <div class="flex flex-col items-center">
                                        <span class="line-through text-gray-400 text-sm">
                                            VND {{ data.room_option.price }}
                                        </span>
                                        <span class="font-bold text-red-600 text-md">
                                            VND {{ data.room_option.final_price }}
                                        </span>
                                        <span class="text-gray-500 text-xs">Đã gồm thuế & phí</span>
                                    </div>
                                </template>
                            </Column>

                            <!-- Rate policy -->
                            <Column header="Các lựa chọn" style="vertical-align:top;text-align:left;text-wrap:nowrap">
                                <template #body="{ data }">
                                    <div class="flex flex-col gap-1 p-2">
                                        <span class="font-semibold text-blue-600 cursor-pointer">
                                            {{ data.rate_policy.name }}
                                        </span>
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 flex justify-center">
                                                <i class="pi pi-check text-green-500" style="font-size:0.85rem"></i>
                                            </div>
                                            <span class="text-green-500 text-sm">
                                                {{ getCancellationType(data.rate_policy.cancellation_type) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 flex justify-center">
                                                <i class="pi pi-circle-fill text-gray-400"
                                                    style="font-size:0.25rem"></i>
                                            </div>
                                            <span class="text-gray-600 text-sm">
                                                {{ getPaymentRequired(data.rate_policy.payment_requirement) }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </Column>

                            <Column header="Chọn phòng" style="vertical-align:top">
                                <template #body="{ data }">
                                    <Select v-model="data.selected_quantity" :options="data.options" optionLabel="label"
                                        optionValue="value" optionDisabled="disabled"
                                        @change="onSelectedBookingRooms(data.id, data.selected_quantity)">
                                    </Select>
                                </template>
                            </Column>
                        </DataTable>
                        <div class="box | flow" style="--flow-spacer:1rem">
                            <template v-if="selectedBookingRooms.length > 0">
                                <div class="summary-infor | flow mt-2 text-left" style="--flow-spacer:.5rem">
                                    <!-- num of booking rooms -->
                                    <span>{{ totalSelectedBookingRooms > 0 ? `${getLabel('room', totalSelectedBookingRooms)} tổng giá` : '' }}
                                    </span>
                                    <div>
                                        <span
                                            class="line-through text-red-500">{{ totalBasePrice > 0 ? `${totalBasePrice} VND` : '' }}</span>
                                        <span
                                            class="ml-2 *:text-lg">{{ (totalFinalPrice > 0 ? `${totalFinalPrice} VND` : '') }}</span>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <h3 class="text-md font-medium">Vui lòng chọn loại phòng bạn muốn đặt</h3>
                            </template>
                            <div class="flex justify-start gap-2">
                                <Button @click="sendRoomBookingDetail">Booking</Button>
                                <Button severity="secondary" label="Hủy" @click="clearSelectedBookingRooms" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.booking-section-header {
    --booking-section-height: 220px;

    background: var(--accent-color-300);
    height: var(--booking-section-height);
    position: relative;
}

.booking-filter-section-form {
    position: absolute;
    min-width: 200px;
    width: fit-content;
    top: 80px;
    left: 50%;
    transform: translateX(-50%);
}

.p-inputtext:disabled {
    background: white;
}
</style>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue';

import { Form } from '@primevue/forms';
import { FormField } from "@primevue/forms";
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';

// data table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// router
import { router } from '@inertiajs/vue3';

// page
import { usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";

const props = defineProps({
    roomOptionList: Array,
    checkIn: String,
    checkOut: String,
    num_of_guests: Number,
    num_of_rooms: Number,
});
console.log(props.roomOptionList);

// label rooms, adults, child->children
const getLabel = (key, num) => {
    if (typeof num === 'number' && !isNaN(num) && num >= 1) {
        switch (key) {
            case 'room':
            case 'adult':
            case 'night':
                return `${num} ${key}${num > 1 ? 's' : ''}`;
            case 'child':
                return `${num} ${key}${num > 1 ? 'ren' : ''}`;
        }
    }
};

const numOfGuestsSummary = computed(() => {
    let label = getLabel('adult', filterRoomBookingForm.numOfAdults);

    if (filterRoomBookingForm.numOfChildren > 0) {
        label += ' and ' + getLabel('child', filterRoomBookingForm.numOfChildren);
    }
    return label;
});
const summarySearchInfor = ref('');
const summaryBookingInfor = ref('');

// tool tip
const getPriceDesc = (price, night) => `
    <div class="flex justify-between">
        <span>VND ${price} x ${getLabel('night', night)}</span>
        <span>VND ${price * night}</span>   
    </div>
`;

const parseVNDate = (value) => {
    if (!value) return null;

    // Nếu là Date object -> giữ nguyên
    if (value instanceof Date) {
        return isNaN(value.getTime()) ? null : value;
    }

    // Nếu có dạng dd/mm/yyyy
    if (typeof value === 'string' && value.includes('/')) {
        const [d, m, y] = value.split('/').map(Number);
        const date = new Date(y, m - 1, d);

        return isNaN(date.getTime()) ? null : date;
    }

    // Nếu là ISO thuần yyyy-mm-dd -> an toàn
    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        const date = new Date(value + "T00:00:00");
        return isNaN(date.getTime()) ? null : date;
    }

    // fallback: cố parse
    const date = new Date(value);
    return isNaN(date.getTime()) ? null : date;
};

// other room booking options
const roomOptionList = ref([]);

const initRoomOptionList = () => {
    roomOptionList.value = props.roomOptionList.map(option => {
        return {
            id: option.id,
            room_option: option.room_option,
            rate_policy: option.rate_policy,
            room_type: option.room_option.room_type,
            selected_quantity: 0,
            // field cho các empty select
            options: []
        }
    })
    console.log(roomOptionList.value);
};


// Hàm tạo available quantity cho empty room options
const getOptions = (row) => {
    const sameGroup = emptyRoomOptions.value.filter(r => r.room_type.id === row.room_type.id);
    // Tổng số phòng đã chọn của các select khác
    const totalOthers = sameGroup
        .filter(r => r !== row)
        .reduce((sum, r) => sum + (r.selected_quantity || 0), 0);

    // Tạo options từ 0 -> available_quantity, disable nếu vượt quá
    return Array.from({ length: row.room_option.available_quantity + 1 }, (_, i) => ({
        label: String(i),
        value: i,
        disabled: totalOthers + i > row.room_option.available_quantity
    }));
};

const updateAllRoomQuantityOptions = () => {
    emptyRoomOptions.value.forEach(row => {
        row.options = getOptions(row);
    });
    totalSelectedBookingRooms.value = emptyRoomOptions.value.reduce((sum, r) => sum + (r.selected_quantity || 0), 0);
    totalFinalPrice.value = emptyRoomOptions.value.filter(r => r.selected_quantity > 0).reduce((sum, r) => sum + r.room_option.final_price * r.selected_quantity * filterRoomBookingForm.numOfNights, 0);
    totalBasePrice.value = emptyRoomOptions.value.filter(r => r.selected_quantity > 0).reduce((sum, r) => sum + r.room_option.price * r.selected_quantity * filterRoomBookingForm.numOfNights, 0);
};

// total price booking
const totalSelectedBookingRooms = ref(0);
const totalFinalPrice = ref(0);
const totalBasePrice = ref(0);

// Initialize
initRoomOptionList();

// form check in, check out, num of guests, num of rooms
const calculateNumOfNights = (checkInStr, checkOutStr) => {
    const checkIn = parseVNDate(checkInStr);
    const checkOut = parseVNDate(checkOutStr);

    if (!checkIn || !checkOut || isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) return 0;

    const diffTime = checkOut - checkIn;
    const nights = diffTime / (1000 * 60 * 60 * 24);

    return Math.max(0, Math.floor(nights));
};

const filterRoomBookingForm = reactive({
    numOfNights: 0,
    numOfAdults: 1,
    numOfChildren: 0,
    numOfRooms: 1,
    dateRange: []
})

// find other room options
const emptyRoomOptions = ref(getEmptyRoomOptions(roomOptionList.value, filterRoomBookingForm));
console.log(emptyRoomOptions.value);

updateAllRoomQuantityOptions();

watch(
    () => emptyRoomOptions.value.map(r => r.selected_quantity),
    updateAllRoomQuantityOptions,
    { deep: true }
);

watch(
    () => ({
        adults: filterRoomBookingForm.numOfAdults,
        children: filterRoomBookingForm.numOfChildren,
        rooms: filterRoomBookingForm.numOfRooms,
        dateRange: filterRoomBookingForm.dateRange
    }),
    (value) => {
        localStorage.setItem('bookingFilterOptions', JSON.stringify(value));
    },
    { deep: true }
);

onMounted(() => {
    const savedBookingData = JSON.parse(localStorage.getItem("bookingFilterOptions") || "{}");

    const fromHome = localStorage.getItem('bookingFromHomePage') === '1';
    const fromDetail = localStorage.getItem('bookingFromDetailPage') === '1';

    // 1. Trường hợp quay về từ Booking Detail → ưu tiên storage
    if ((fromDetail && savedBookingData) || (!fromHome && !fromDetail)) {
        filterRoomBookingForm.numOfAdults = savedBookingData.adults;
        filterRoomBookingForm.numOfChildren = savedBookingData.children;
        filterRoomBookingForm.numOfRooms = savedBookingData.rooms;
        const d1 = parseVNDate(savedBookingData.dateRange[0]);
        const d2 = parseVNDate(savedBookingData.dateRange[1]);
        filterRoomBookingForm.dateRange = (d1 && d2) ? [d1, d2] : [];

        localStorage.removeItem('bookingFromDetailPage');

    } // 2. Từ Home → Booking (props đầy đủ)
    else if (fromHome) {
        localStorage.removeItem('bookingFilterOptions');
        localStorage.removeItem('bookingFromHomePage');

        filterRoomBookingForm.numOfAdults = props.num_of_guests;
        filterRoomBookingForm.numOfChildren = 0;
        filterRoomBookingForm.numOfRooms = props.num_of_rooms;
        filterRoomBookingForm.dateRange = [
            parseVNDate(props.checkIn),
            parseVNDate(props.checkOut)
        ];
    }

    // caculate num of nights
    const [checkIn, checkOut] = filterRoomBookingForm.dateRange;
    filterRoomBookingForm.numOfNights = calculateNumOfNights(checkIn, checkOut);

    // summary text
    summaryBookingInfor.value = getLabel('night', filterRoomBookingForm.numOfNights) + ', ' + numOfGuestsSummary.value;
    summarySearchInfor.value = `${getLabel('room', filterRoomBookingForm.numOfRooms)} for ${numOfGuestsSummary.value}`;

    // find best room option
    bestRoomOptions.value = getBestRoomOption(roomOptionList, filterRoomBookingForm);
    console.log(bestRoomOptions.value);
})

const getCancellationType = (type) => {
    switch (type) {
        case 'free_cancellation':
            return 'Hủy miễn phí';

        case 'flexible_change':
            return 'Linh hoạt đổi ngày';

        case 'partial_refund':
            return 'Hủy muộn giữ lại cọc';
    }
};

const getPaymentRequired = (type) => {
    switch (type) {
        case 'full_prepayment':
            return 'Thanh toán trước khi đến';

        case 'deposit_required':
            return 'Yêu cầu đặt cọc';

        case 'pay_at_hotel':
            return 'Không cần thanh toán trước, thanh toán tại chỗ nghỉ';
    }
};

// pop over guest option menu
const guestOptionMenu = ref();
const toggle = (event) => {
    guestOptionMenu.value.toggle(event);
}

// search best option results
const searchBestRoomOptions = async (e) => {
    if (!e.valid) return;

    // caculate num of nights
    const [checkIn, checkOut] = filterRoomBookingForm.dateRange;
    const numNights = calculateNumOfNights(checkIn, checkOut);
    filterRoomBookingForm.numOfNights = numNights;

    await nextTick();

    // summary text
    summaryBookingInfor.value = getLabel('night', filterRoomBookingForm.numOfNights) + ', ' + numOfGuestsSummary.value;
    summarySearchInfor.value = `${getLabel('room', filterRoomBookingForm.numOfRooms)} for ${numOfGuestsSummary.value}`;
    console.log(filterRoomBookingForm);

    const searchRequest = {
        numOfAdults: filterRoomBookingForm.numOfAdults,
        numOfChildren: filterRoomBookingForm.numOfChildren,
        numOfRooms: filterRoomBookingForm.numOfRooms,
        numOfNights: numNights,
    };

    // find best room option
    bestRoomOptions.value = getBestRoomOption(roomOptionList, searchRequest);
    console.log(bestRoomOptions.value);
}

// best option results
const bestRoomOptions = ref([]);

function findCheapestCombination(roomOptionList, request) {
    const {
        numOfAdults: totalAdults,
        numOfChildren: totalChildren,
        numOfRooms: numRooms,
        numOfNights: numNights,
    } = request;

    const roomOption = roomOptionList.map(option => ({
        ...option,
        max_total: option.room_type.max_adults + option.room_type.max_children,
    }));

    const maxTotalCapacity = Math.max(...roomOption.map(r => r.max_total)) * numRooms;
    if (maxTotalCapacity < totalAdults + totalChildren) {
        return { valid: false, reason: "Không đủ tổng sức chứa." };
    }

    let allCombos = [];

    // Sinh tổ hợp phòng (có lặp)
    function genCombinations(startIdx, remaining, combo) {
        if (remaining === 0) {
            evaluateCombination(combo);
            return;
        }
        for (let i = startIdx; i < roomOption.length; i++) {
            combo.push(roomOption[i]);
            genCombinations(i, remaining - 1, combo);
            combo.pop();
        }
    }

    // Đánh giá 1 combo
    function evaluateCombination(combo) {
        const N = combo.length;
        const prefixMaxTotal = new Array(N + 1).fill(0);
        for (let i = N - 1; i >= 0; i--) prefixMaxTotal[i] = prefixMaxTotal[i + 1] + combo[i].max_total;
        const sumMaxTotalOfRange = i => prefixMaxTotal[i] || 0;

        function assignAt(i, remAdults, remChildren, accPrice, guestsPerRoom) {
            if (i === N) {
                if (remAdults === 0 && remChildren === 0) {
                    allCombos.push({ guestsPerRoom: [...guestsPerRoom], pricePerNight: accPrice, combo: [...combo] });
                }
                return;
            }

            const option = combo[i];
            for (let a = 0; a <= remAdults; a++) {
                for (let c = 0; c <= remChildren; c++) {
                    const total = a + c;
                    if (total === 0 || total > option.max_total) continue;
                    const remainingTotal = sumMaxTotalOfRange(i + 1);
                    if (remainingTotal < remAdults + remChildren - total) continue;
                    if (a > option.room_option.num_adults || c > option.room_option.num_children) continue;

                    guestsPerRoom[i] = total;

                    assignAt(i + 1, remAdults - a, remChildren - c, accPrice + option.room_option.final_price, guestsPerRoom);

                    guestsPerRoom[i] = 0;
                }
            }
        }

        assignAt(0, totalAdults, totalChildren, 0, Array(N).fill(0));
    }

    genCombinations(0, numRooms, []);

    if (allCombos.length === 0) return { valid: false, reason: "Không tìm được tổ hợp phù hợp." };

    //Bước 2: chọn combo cân bằng nhất
    allCombos.sort((a, b) => {
        const maxDiffA = Math.max(...a.guestsPerRoom) - Math.min(...a.guestsPerRoom);
        const maxDiffB = Math.max(...b.guestsPerRoom) - Math.min(...b.guestsPerRoom);
        if (maxDiffA !== maxDiffB) return maxDiffA - maxDiffB;
        return a.pricePerNight - b.pricePerNight;
    });

    const bestCombo = allCombos[0];
    const summary = {};
    bestCombo.combo.forEach((option, idx) => {
        const key = option.id;
        if (!summary[key]) summary[key] = { ...option };
        if (summary[key].selected_quantity == null) {
            summary[key].selected_quantity = 0;
        }
        summary[key].selected_quantity += 1;
    });

    return {
        valid: true,
        total_price_per_night: bestCombo.pricePerNight,
        num_of_nights: numNights,
        total_price: bestCombo.pricePerNight * numNights,
        selection: Object.values(summary),
        guestsPerRoom: bestCombo.guestsPerRoom,
    };
}

function getEmptyRoomOptions(roomOptionList, request) {
    const { numOfAdults, numOfChildren, numOfRooms } = request;
    const results = [];

    roomOptionList.forEach(option => {
        const { num_adults, num_children, available_quantity } = option.room_option;

        // tính tổng sức chứa nếu dùng tất cả số phòng yêu cầu nhưng không vượt quá số phòng trống
        const roomsToUse = Math.min(numOfRooms, available_quantity);
        const totalAdultsCapacity = num_adults * roomsToUse;
        const totalChildrenCapacity = num_children * roomsToUse;

        // nếu tổng sức chứa đủ cho số khách và còn ít nhất 1 phòng
        if (totalAdultsCapacity >= numOfAdults && totalChildrenCapacity >= numOfChildren && roomsToUse >= 1) {
            results.push({
                ...option,
            });
        }
    });

    return results;
}

function getBestRoomOption(roomOptionList, filterOptions) {
    const result = findCheapestCombination(roomOptionList.value, filterOptions);
    console.log(result);
    const bestOptions = (result.selection || []).map(option => ({
        ...option,
        total_price: result.total_price,
        total_base_price_per_room_type: option.room_type.base_price * result.num_of_nights,
        total_price_per_room_type: option.room_option.final_price * result.num_of_nights
    }));

    const total_base_price = bestOptions.reduce(
        (sum, room) => sum + room.total_base_price_per_room_type,
        0
    );
    bestOptions.forEach(room => {
        room.total_base_price = total_base_price;
    })
    return bestOptions;
}

// set best room options to selected booking rooms
const setSelectedBookingRoomOptions = () => {
    if (bestRoomOptions.value) {
        clearSelectedBookingRooms();
        emptyRoomOptions.value.forEach(eOption => {
            const matched = bestRoomOptions.value.find(
                bOption => bOption.id === eOption.id
            );

            if (matched) {
                eOption.selected_quantity = matched.selected_quantity;
            }

        });
        selectedBookingRooms.value = bestRoomOptions.value.map(item => ({ ...item }));
    }
}

// add other options to booking detail
const selectedBookingRooms = ref([]);

const onSelectedBookingRooms = (roomOptionId, selectedQuantity) => {
    const roomOption = selectedBookingRooms.value.find(r => r.id === roomOptionId)

    // Kiểm tra nếu phòng đã tồn tại trong selectedBookingRooms
    if (roomOption) {
        // Nếu chọn count = 0 thì xóa khỏi danh sách
        if (selectedQuantity === 0) {
            const idx = selectedBookingRooms.value.indexOf(roomOption);
            selectedBookingRooms.value.splice(idx, 1);
        } else {
            // Nếu đã tồn tại, cập nhật count
            roomOption.selected_quantity = selectedQuantity;
        }
    } else if (selectedQuantity > 0) {
        let newRoomOptions = emptyRoomOptions.value
            .find(r =>
                r.id === roomOptionId
            );
        selectedBookingRooms.value.push(newRoomOptions);
        newRoomOptions.selected_quantity = selectedQuantity;
    }
};

// clear selected booking rooms
const clearSelectedBookingRooms = () => {
    // Xóa toàn bộ phòng đã chọn
    selectedBookingRooms.value = [];

    // Reset toàn bộ số phòng đã chọn trong emptyRoomOptions
    emptyRoomOptions.value.forEach(room => {
        room.selected_quantity = 0;
    });

    // Reset tổng giá
    totalBasePrice.value = 0;
    totalFinalPrice.value = 0;
    totalSelectedBookingRooms.value = 0;
};

// get to booking detail page
const sendRoomBookingDetail = () => {
    if (selectedBookingRooms.value) {
        const roomBookingDetail = {
            date_range: filterRoomBookingForm.dateRange.map(d => formatDate(d)),
            num_adults: filterRoomBookingForm.numOfAdults,
            num_children: filterRoomBookingForm.numOfChildren,
            initial_num_rooms: filterRoomBookingForm.numOfRooms,
            num_rooms: selectedBookingRooms.value.reduce((sum, room) => sum + room.selected_quantity, 0),
            num_nights: filterRoomBookingForm.numOfNights,
            selected_rooms: JSON.parse(JSON.stringify(selectedBookingRooms.value || [])),
            total_final_price: totalFinalPrice.value,
            total_base_price: totalBasePrice.value
        }
        console.log(roomBookingDetail);
        router.post(route('booking.detail'), roomBookingDetail);
    }
}

function formatDate(dateStr) {
    const date = new Date(dateStr);

    const dd = String(date.getDate()).padStart(2, '0');
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const yyyy = date.getFullYear();

    return `${dd}/${mm}/${yyyy}`;
}

</script>