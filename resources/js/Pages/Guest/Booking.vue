<template>
    <div class="booking-section pb-10">
        <div class="booking-section-header | padding-block-200">

            <Form v-slot="$form" :initialValues @submit="submit"
                class="booking-filter-section-form | text-center gap-y-3 bg-amber-100 padding-block-400 px-4 rounded-xl shadow-xl">
                <div class="flex gap-x-4 items-center">
                    <!-- Check-in / Check-out -->
                    <div>
                        <label class="block mb-1">Check-in/Check-out</label>
                        <FormField name="rangeDate" v-slot="{ field, error }">
                            <DatePicker v-model="filterRoomBookingForm.dateRange" dateFormat="dd/mm/yy"
                                selectionMode="range" :manualInput="false" showIcon fluid />
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

                    <DataTable :value="bestRateOptions" rowGroupMode="rowspan" groupRowsBy="total_price"
                        :showHeaders="false" sortField="total_price" showGridlines class="text-lg w-full">
                        <!-- Cột thông tin phòng + chính sách -->
                        <Column header="Danh sách phòng">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1 p-2">
                                    <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                        {{ data.count + ' x ' + data.name }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">Price for</span>
                                        <div class="flex gap-1 items-center">
                                            <template v-for="item in data.num_adults">
                                                <i class="pi pi-user" style="color: black;font-size: .85rem"
                                                    v-tooltip.bottom="getLabel('adult', data.num_adults)" />
                                            </template>
                                        </div>
                                        <template v-if="data.num_children > 0">
                                            <div class="flex items-center gap-2">
                                                <i class="pi pi-plus" style="color: black;font-size: .7rem"></i>
                                                <template v-for="item in data.num_children">
                                                    <div>
                                                        <i class="pi pi-user" style="color: black; font-size:0.75rem"
                                                            v-tooltip.bottom="getLabel('child', data.num_children)" />
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <span class="text-gray-600 text-sm">
                                        {{ data.bedType }} bed
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
                                    :v.tooltip.html="getPriceDesc(data.base_price, filterRoomBookingForm.numOfNights)">
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
                                        @click="onSelectOptions" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <div class="other-options">
                    <div class="p-2">
                        <span class="text-md">Other empty options</span>
                    </div>
                    <div class="grid grid-cols-[auto_1fr] gap-x-2">
                        <DataTable :value="roomTypeList" rowGroupMode="rowspan" groupRowsBy="name" sortField="id"
                            showGridlines class="text-lg w-full items-start" tableStyle="max-width: 74rem">
                            <!-- Cột thông tin phòng -->
                            <Column field="name" header="Danh sách phòng"
                                style="min-width:fit-content;vertical-align:top;">
                                <template #body="{ data }">
                                    <div class="flex flex-col items-start justify-start gap-1 p-2">
                                        <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                            {{ data.name }}
                                        </span>
                                        <span class="text-red-600 text-sm">
                                            {{ data.available_quantity + ' ' + (data.available_quantity > 1 ? 'rooms' : 'room') }}
                                            left
                                        </span>
                                        <span class="text-gray-600 text-sm">
                                            {{ data.bed_type }} bed
                                        </span>
                                        <div class="flex flex-wrap gap-y-2 gap-x-3 mt-2">
                                            <template v-for="amenity in data.amenities">
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
                                        <template v-for="(item, idx) in data.num_adults">
                                            <div>
                                                <i class="pi pi-user" style="color: black"
                                                    v-tooltip.bottom="getLabel('adult', data.num_adults)" />
                                            </div>
                                        </template>
                                        <template v-if="data.num_children > 0">
                                            <i class="pi pi-plus" style="color: black;font-size: .75rem"></i>
                                            <template v-for="item in data.num_children">
                                                <div>
                                                    <i class="pi pi-user" style="color: black; font-size:0.85rem"
                                                        v-tooltip.bottom="getLabel('child', data.num_children)" />
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
                                            VND {{ data.price }}
                                        </span>
                                        <span class="font-bold text-red-600 text-md">
                                            VND {{ data.final_price }}
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
                                    <Select v-model="data.selectedRooms" :options="data.options" optionLabel="label"
                                        optionValue="value" optionDisabled="disabled">
                                    </Select>
                                </template>
                            </Column>
                        </DataTable>
                        <div class="box | max-h-50 flow" style="--flow-spacer:1rem">
                            <h3>Vui lòng chọn loại phòng bạn muốn đặt</h3>
                            <div class="summary-infor flow mt-2 text-left" style="--flow-spacer:.5rem">
                                <!-- num of booking rooms -->
                                <span>{{ totalSelectedRooms > 0 ? `${getLabel('room', totalSelectedRooms)} tổng giá` : '' }}
                                </span>
                                <div>
                                    <span
                                        class="line-through text-red-500">{{ totalBasePrice > 0 ? `${totalBasePrice} VND` : '' }}</span>
                                    <span
                                        class="ml-2 *:text-lg">{{ (totalFinalPrice > 0 ? `${totalFinalPrice} VND` : '') }}</span>
                                </div>
                            </div>
                            <Button @click="onBookingDetail">Booking</Button>
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
import { ref, reactive, computed, onMounted, watch } from 'vue';

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
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    roomTypeList: Array,
    checkIn: String,
    checkOut: String,
    num_of_guests: Number,
    num_of_rooms: Number,
    newData: String,
});

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

// convert to ISO format
const parseVNDate = (value) => {
    if (!value) return null;

    // Nếu là Date object thì giữ nguyên
    if (value instanceof Date) return value;

    // Nếu là ISO format (yyyy-mm-dd)
    if (value.includes('-')) return new Date(value);

    // Nếu là dạng VN dd/mm/yyyy
    const [day, month, year] = value.split('/');
    return new Date(`${year}-${month}-${day}`);
};

// other room booking options
const roomTypeList = ref([]);

const initRoomTypeList = () => {
    roomTypeList.value = props.roomTypeList.flatMap(roomType =>
        roomType.room_rate_options.map(option => ({
            ...roomType,
            ...option,
            name: roomType.name,
            selectedRooms: 0,
            options: []
        }))
    );
};

// Hàm tạo options cho mỗi select room quantity
const getOptions = (row) => {
    const sameGroup = roomTypeList.value.filter(r => r.name === row.name);
    // Tổng số phòng đã chọn của các select khác
    const totalOthers = sameGroup
        .filter(r => (r.rate_policy_id !== row.rate_policy_id) || (r.num_adults !== row.num_adults || r.num_children !== row.num_children))
        .reduce((sum, r) => sum + (r.selectedRooms || 0), 0);

    // Tạo options từ 0 -> available_quantity, disable nếu vượt quá
    return Array.from({ length: row.available_quantity + 1 }, (_, i) => ({
        label: String(i),
        value: i,
        disabled: totalOthers + i > row.available_quantity
    }));
};

const updateAllRoomQuantityOptions = () => {
    roomTypeList.value.forEach(row => {
        row.options = getOptions(row);
    });
    totalSelectedRooms.value = roomTypeList.value.reduce((sum, r) => sum + (r.selectedRooms || 0), 0);
    totalFinalPrice.value = roomTypeList.value.filter(r => r.selectedRooms > 0).reduce((sum, r) => sum + r.final_price * r.selectedRooms * filterRoomBookingForm.numOfNights, 0);
    totalBasePrice.value = roomTypeList.value.filter(r => r.selectedRooms > 0).reduce((sum, r) => sum + r.base_price * r.selectedRooms * filterRoomBookingForm.numOfNights, 0);
};

watch(
    () => roomTypeList.value.map(r => r.selectedRooms),
    updateAllRoomQuantityOptions,
    { deep: true }
);

// summary booking
const totalSelectedRooms = ref(0);
const totalFinalPrice = ref(0);
const totalBasePrice = ref(0);

// Initialize
initRoomTypeList();
updateAllRoomQuantityOptions();
console.log(roomTypeList.value);

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

// form check in, check out, num of guests, num of rooms
const calculateNumOfNights = (checkInStr, checkOutStr) => {
    const checkIn = parseVNDate(checkInStr);
    const checkOut = parseVNDate(checkOutStr);

    if (!checkIn || !checkOut || isNaN(checkIn) || isNaN(checkOut)) return 0;

    const diffTime = checkOut - checkIn;
    const nights = diffTime / (1000 * 60 * 60 * 24);

    return Math.max(0, Math.floor(nights));
};

const filterRoomBookingForm = reactive({
    numOfNights: calculateNumOfNights(props.checkIn, props.checkOut),
    numOfAdults: props.num_of_guests,
    numOfChildren: 0,
    numOfRooms: props.num_of_rooms,
    dateRange: [parseVNDate(props.checkIn), parseVNDate(props.checkOut)]
})

watch(
    () => ({
        adults: filterRoomBookingForm.numOfAdults,
        children: filterRoomBookingForm.numOfChildren,
        rooms: filterRoomBookingForm.numOfRooms,
        dateRange: filterRoomBookingForm.dateRange
    }),
    (value) => {
        localStorage.setItem('bookingFilter', JSON.stringify(value));
    },
    { deep: true }
);

const initialValues = ref({
    rangeDate: null,
    num_of_guests: 1,
    num_of_rooms: 1,
})

onMounted(() => {
    initialValues.value = {
        rangeDate: [parseVNDate(props.checkIn), parseVNDate(props.checkOut)],
        num_of_guests: props.num_of_guests || 1,
        num_of_rooms: props.num_of_rooms || 1,
    }

    const saved = localStorage.getItem('bookingFilter');
    const fromHome = localStorage.getItem('bookingFromHomePage') === '1';

    if (fromHome) {
        // Từ Home → Booking: dùng props và xoá storage cũ
        localStorage.removeItem('bookingFilter');
        localStorage.removeItem('bookingFromHomePage');

        filterRoomBookingForm.numOfAdults = props.num_of_guests;
        filterRoomBookingForm.numOfChildren = 0;
        filterRoomBookingForm.numOfRooms = props.num_of_rooms;
        filterRoomBookingForm.dateRange = [
            parseVNDate(props.checkIn),
            parseVNDate(props.checkOut)
        ];
        filterRoomBookingForm.numOfNights = calculateNumOfNights(props.checkIn, props.checkOut);

    } else if (saved) {
        // Quay lại từ Booking Info → ưu tiên storage
        const parsed = JSON.parse(saved);
        filterRoomBookingForm.numOfAdults = parsed.adults;
        filterRoomBookingForm.numOfChildren = parsed.children;
        filterRoomBookingForm.numOfRooms = parsed.rooms;
        filterRoomBookingForm.dateRange = parsed.dateRange.map(d => new Date(d));
        filterRoomBookingForm.numOfNights = calculateNumOfNights(
            parsed.dateRange[0],
            parsed.dateRange[1]
        );
    } else {
        // fallback props (Home → Booking) nếu storage rỗng
        filterRoomBookingForm.numOfAdults = props.num_of_guests;
        filterRoomBookingForm.numOfChildren = 0;
        filterRoomBookingForm.numOfRooms = props.num_of_rooms;
        filterRoomBookingForm.dateRange = [
            parseVNDate(props.checkIn),
            parseVNDate(props.checkOut)
        ];
        filterRoomBookingForm.numOfNights = calculateNumOfNights(props.checkIn, props.checkOut);
    }

    // summary text
    summaryBookingInfor.value = getLabel('night', filterRoomBookingForm.numOfNights) + ', ' + numOfGuestsSummary.value;
    summarySearchInfor.value = `${getLabel('room', props.num_of_rooms)} for ${numOfGuestsSummary.value}`;

    // caculate num of nights
    const [checkIn, checkOut] = filterRoomBookingForm.dateRange;
    filterRoomBookingForm.numOfNights = calculateNumOfNights(checkIn, checkOut);

    // find best rate option
    bestRateOptions.value = getBestRateOption(roomTypeList, filterRoomBookingForm);
    console.log(bestRateOptions.value);
})

// pop over guest option menu
const guestOptionMenu = ref();
const toggle = (event) => {
    guestOptionMenu.value.toggle(event);
}

// filter best option results
const submit = (e) => {
    if (!e.valid) return;

    // summary text
    summaryBookingInfor.value = getLabel('night', filterRoomBookingForm.numOfNights) + ', ' + numOfGuestsSummary.value;
    summarySearchInfor.value = `${getLabel('room', filterRoomBookingForm.numOfRooms)} for ${numOfGuestsSummary.value}`;

    // caculate num of nights
    const [checkIn, checkOut] = filterRoomBookingForm.dateRange;
    filterRoomBookingForm.numOfNights = calculateNumOfNights(checkIn, checkOut);

    // find best rate option
    bestRateOptions.value = getBestRateOption(roomTypeList, filterRoomBookingForm);
    console.log(bestRateOptions.value);
}

// best option results
// function findCheapestCombination(roomTypeList, request) {
//     const {
//         numOfAdults: totalAdults,
//         numOfChildren: totalChildren,
//         numOfRooms: numRooms,
//         numOfNights: numNights,
//     } = request;

//     // Chuẩn hóa roomTypes từ dữ liệu room_rate_options
//     const roomTypes = roomTypeList.map(rt => {
//         return {
//             ...rt,
//             max_total: rt.max_adults + rt.max_children,
//         }
//     }).sort((a, b) => a.final_price - b.final_price); // sắp xếp giá tăng dần;
//     console.log(roomTypes);

//     // Kiểm tra sơ bộ khả năng tổng
//     const maxTotalCapacity =
//         Math.max(...roomTypes.map(r => r.max_total)) * numRooms;
//     if (maxTotalCapacity < totalAdults + totalChildren) {
//         return {
//             valid: false,
//             reason: "Không đủ tổng sức chứa tối đa cho số khách.",
//         };
//     }

//     let best = { pricePerNight: Infinity, selection: null };

//     // Sinh tổ hợp phòng (có lặp)
//     function genCombinations(startIdx, remaining, combo) {
//         if (remaining === 0) {
//             evaluateCombination(combo);
//             return;
//         }
//         for (let i = startIdx; i < roomTypes.length; i++) {
//             combo.push(roomTypes[i]);
//             genCombinations(i, remaining - 1, combo);
//             combo.pop();
//         }
//     }

//     // Đánh giá tổ hợp
//     function evaluateCombination(combo) {
//         const sumMaxTotal = combo.reduce((s, r) => s + r.max_total, 0);
//         if (sumMaxTotal < totalAdults + totalChildren) return;

//         const N = combo.length;
//         const rateOptions = Array.from({ length: N }, () => ({
//             adults: 0,
//             children: 0,
//         }));

//         const prefixMaxTotal = new Array(N + 1).fill(0);
//         for (let i = N - 1; i >= 0; i--) {
//             prefixMaxTotal[i] = prefixMaxTotal[i + 1] + combo[i].max_total;
//         }
//         const sumMaxTotalOfRange = i => prefixMaxTotal[i] || 0;

//         function assignAt(i, remAdults, remChildren, accPrice) {
//             if (accPrice >= best.pricePerNight) return;
//             if (i === N) {
//                 if (remAdults === 0 && remChildren === 0) {
//                     if (accPrice < best.pricePerNight) {
//                         const summary = {};
//                         for (let k = 0; k < N; k++) {
//                             const rt = combo[k];
//                             const key = rt.id;
//                             if (!summary[key])
//                                 summary[key] = {
//                                     ...rt,
//                                     count: 0,
//                                     rateOptions: []
//                                 };
//                             summary[key].count += 1;
//                             summary[key].rateOptions = rateOptions[k].rateOption
//                         }
//                         best = {
//                             pricePerNight: accPrice,
//                             selection: Object.values(summary),
//                         };
//                     }
//                 }
//                 return;
//             }

//             const rt = combo[i];
//             for (let a = 0; a <= remAdults; a++) {
//                 for (let c = 0; c <= remChildren; c++) {
//                     const total = a + c;
//                     if (total === 0) continue;
//                     if (total > rt.max_total) continue;

//                     const remainingTotal = sumMaxTotalOfRange(i + 1);
//                     if (remainingTotal < remAdults + remChildren - total) continue;

//                     // Chọn rate phù hợp: đủ số người
//                     let rateTotal = rt.num_adults + rt.num_children;
//                     const matchingRate =
//                         rateTotal >= total &&
//                         rt.num_adults >= a &&
//                         rateTotal <= rt.max_total;

//                     if (!matchingRate) continue;

//                     rateOptions[i].rateOption = {
//                         numAdult: matchingRate.numAdult,
//                         numChild: matchingRate.numChild,
//                         base_price: matchingRate.base_price,
//                         final_price: matchingRate.final_price,
//                         ratePolicy: matchingRate.ratePolicy,
//                     };

//                     assignAt(
//                         i + 1,
//                         remAdults - a,
//                         remChildren - c,
//                         accPrice + matchingRate.final_price
//                     );
//                 }
//             }
//         }

//         assignAt(0, totalAdults, totalChildren, 0);
//     }

//     genCombinations(0, numRooms, []);

//     if (!best.selection) {
//         return { valid: false, reason: "Không tìm được tổ hợp phù hợp." };
//     }

//     return {
//         valid: true,
//         total_price_per_night: best.pricePerNight,
//         num_of_nights: numNights,
//         total_price: best.pricePerNight * numNights,
//         selection: best.selection,
//     };
// }
function findCheapestCombination(roomTypeList, request) {
    const {
        numOfAdults: totalAdults,
        numOfChildren: totalChildren,
        numOfRooms: numRooms,
        numOfNights: numNights,
    } = request;

    // Chuẩn hóa roomTypes
    const roomTypes = roomTypeList.map(rt => ({
        ...rt,
        max_total: rt.max_adults + rt.max_children,
    })).sort((a, b) => a.final_price - b.final_price); // sắp xếp giá tăng dần

    // Kiểm tra khả năng tổng sức chứa
    const maxTotalCapacity = Math.max(...roomTypes.map(r => r.max_total)) * numRooms;
    if (maxTotalCapacity < totalAdults + totalChildren) {
        return {
            valid: false,
            reason: "Không đủ tổng sức chứa tối đa cho số khách.",
        };
    }

    let best = { pricePerNight: Infinity, selection: null };

    // Sinh tổ hợp phòng (có lặp)
    function genCombinations(startIdx, remaining, combo) {
        if (remaining === 0) {
            evaluateCombination(combo);
            return;
        }
        for (let i = startIdx; i < roomTypes.length; i++) {
            combo.push(roomTypes[i]);
            genCombinations(i, remaining - 1, combo);
            combo.pop();
        }
    }

    // Đánh giá tổ hợp phòng
    function evaluateCombination(combo) {
        const sumMaxTotal = combo.reduce((s, r) => s + r.max_total, 0);
        if (sumMaxTotal < totalAdults + totalChildren) return;

        const N = combo.length;
        const rateOptions = Array.from({ length: N }, () => ({}));

        const prefixMaxTotal = new Array(N + 1).fill(0);
        for (let i = N - 1; i >= 0; i--) {
            prefixMaxTotal[i] = prefixMaxTotal[i + 1] + combo[i].max_total;
        }
        const sumMaxTotalOfRange = i => prefixMaxTotal[i] || 0;

        function assignAt(i, remAdults, remChildren, accPrice) {
            if (accPrice >= best.pricePerNight) return;
            if (i === N) {
                if (remAdults === 0 && remChildren === 0) {
                    if (accPrice < best.pricePerNight) {
                        // Tổng hợp kết quả tốt nhất
                        const summary = {};
                        for (let k = 0; k < N; k++) {
                            const rt = combo[k];
                            const key = rt.id;
                            if (!summary[key]) {
                                summary[key] = {
                                    ...rt,
                                    count: 0,
                                    rateOption: rateOptions[k],
                                };
                            }
                            summary[key].count += 1;
                        }
                        best = {
                            pricePerNight: accPrice,
                            selection: Object.values(summary),
                        };
                    }
                }
                return;
            }

            const rt = combo[i];
            for (let a = 0; a <= remAdults; a++) {
                for (let c = 0; c <= remChildren; c++) {
                    const total = a + c;
                    if (total === 0) continue;
                    if (total > rt.max_total) continue;

                    const remainingTotal = sumMaxTotalOfRange(i + 1);
                    if (remainingTotal < remAdults + remChildren - total) continue;

                    // kiểm tra rate có đủ sức chứa không
                    const rateTotal = rt.num_adults + rt.num_children;
                    const isMatching =
                        rateTotal >= total &&
                        rt.num_adults >= a &&
                        rateTotal <= rt.max_total;

                    if (!isMatching) continue;

                    rateOptions[i] = {
                        numAdult: rt.num_adults,
                        numChild: rt.num_children,
                        base_price: rt.base_price,
                        final_price: rt.final_price,
                        ratePolicy: rt.ratePolicy,
                    };

                    assignAt(
                        i + 1,
                        remAdults - a,
                        remChildren - c,
                        accPrice + rt.final_price
                    );
                }
            }
        }

        assignAt(0, totalAdults, totalChildren, 0);
    }

    genCombinations(0, numRooms, []);

    if (!best.selection) {
        return { valid: false, reason: "Không tìm được tổ hợp phù hợp." };
    }

    return {
        valid: true,
        total_price_per_night: best.pricePerNight,
        num_of_nights: numNights,
        total_price: best.pricePerNight * numNights,
        selection: best.selection,
    };
}

function getBestRateOption(roomTypeList, filterOptions) {
    const result = findCheapestCombination(roomTypeList.value, filterOptions);
    const bestOptions = result.selection.map(room => ({
        ...room,
        total_price: result.total_price,
        total_base_price_per_room_type: room.base_price * result.num_of_nights,
        total_price_per_room_type: room.final_price * result.num_of_nights
    }));

    const total_base_price = bestOptions.reduce(
        (sum, room) => sum + room.total_base_price_per_room_type,
        0
    );
    bestOptions.forEach(room => {
        room.total_base_price = total_base_price;
    })
    console.log(bestOptions);
    return bestOptions;
}

const bestRateOptions = ref([]);

// set best rate options
const onSelectOptions = () => {
    if (bestRateOptions.value) {
        // reset room selection from other options
        roomTypeList.value.map(roomType => {
            roomType.selectedRooms = 0;
        })

        roomTypeList.value.forEach(roomType => {
            const matched = bestRateOptions.value.find(
                room =>
                    room.id === roomType.id
            );

            if (matched) {
                roomType.selectedRooms = matched.count;
            }
        });
    }
}

// get to booking detail page
const onBookingDetail = () => {
    let selectedRooms = bestRateOptions.value.map(({ room_rate_options, ...rest }) => rest)
    const details = {
        date_range: filterRoomBookingForm.dateRange.map(d => formatDate(d)),
        num_adults: filterRoomBookingForm.numOfAdults,
        num_children: filterRoomBookingForm.numOfChildren,
        num_rooms: filterRoomBookingForm.numOfRooms,
        num_nights: filterRoomBookingForm.numOfNights,
        selected_rooms: JSON.parse(JSON.stringify(selectedRooms || [])),
    }
    console.log(details);
    router.post(route('booking.detail'), details);
}

function formatDate(dateStr) {
    const date = new Date(dateStr);

    const dd = String(date.getDate()).padStart(2, '0');
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const yyyy = date.getFullYear();

    return `${dd}/${mm}/${yyyy}`;
}

</script>