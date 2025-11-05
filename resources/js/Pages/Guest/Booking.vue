<template>
    <div class="booking-section pb-10">
        <div class="booking-section-header | padding-block-200">

            <Form v-slot="$form" :initialValues @submit="submit"
                class="booking-filter-section-form | text-center gap-y-3 bg-amber-100 padding-block-400 px-4 rounded-xl shadow-xl">
                <div class="flex gap-x-4 items-center">
                    <!-- Check-in / Check-out -->
                    <div>
                        <label class="block mb-1">Check-in/Check-out</label>
                        <FormField name="date" v-slot="{ field, error }">
                            <DatePicker v-bind="field" dateFormat="dd/mm/yy" selectionMode="range" :manualInput="false"
                                showIcon fluid />
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
                                <InputText :placeholder="numOfGuests" disabled />
                                <InputIcon class="pi pi-angle-down" />
                            </IconField>
                        </FormField>

                        <!-- guest option popover -->
                        <Popover ref="guestOptionMenu">
                            <div class="py-2 px-3 space-y-3">
                                <!-- Adult -->
                                <div class="flex justify-between gap-x-3 items-center">
                                    <div class="flex">
                                        <span class="text-base">Người lớn</span>
                                        <span class="fs-300 text-gray-500">18 tuổi trở lên</span>
                                    </div>
                                    <InputNumber v-model="numOfAdult" name="num_of_guests" showButtons size="small"
                                        :min="1" :pt="{
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
                                    <InputNumber v-model="numOfChild" showButtons size="small" :min="0" :max="10" :pt="{
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
                            <InputNumber v-model="numOfRooms" showButtons :min="1" :pt="{
                                incrementButton: { class: 'bg-gray-100' },
                                decrementButton: { class: 'bg-gray-100' },
                                incrementIcon: { class: 'pi pi-plus' },
                                decrementIcon: { class: 'pi pi-minus' }
                            }" :suffix="` room${numOfRooms > 1 ? 's' : ''}`" />
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
                        <h3 class="fs-700">{{ summaryText }}</h3>
                    </div>

                    <DataTable :value="roomOptions" rowGroupMode="rowspan" groupRowsBy="total_cost" :showHeaders="false"
                        sortField="total_cost" showGridlines class="text-lg w-full">
                        <!-- Cột thông tin phòng + chính sách -->
                        <Column header="Danh sách phòng">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1 p-2">
                                    <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                        {{ data.roomType.name }}
                                    </span>
                                    <span class="text-gray-600 text-sm">
                                        {{ data.roomType.bedType }} • 👤 {{ data.roomType.maxAdults }} người lớn
                                    </span>
                                    <span class="text-red-600 font-medium text-sm">
                                        {{ data.ratePolicy.name }}
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        {{ data.ratePolicy.paymentRequirement }}
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <!-- Cột giá -->
                        <Column header="Giá" style="text-align: center">
                            <template #body="{ data }">
                                <div class="flex flex-col items-center">
                                    <span class="line-through text-gray-400 text-sm">
                                        VND {{ data.price.original.toLocaleString() }}
                                    </span>
                                    <span class="font-bold text-red-600 text-md">
                                        VND {{ data.price.discounted.toLocaleString() }}
                                    </span>
                                    <span class="text-gray-500 text-xs">Đã gồm thuế & phí</span>
                                </div>
                            </template>
                        </Column>

                        <!-- Cột nhóm: tổng giá -->
                        <Column field="total_cost" header="Tổng giá (VNĐ)">
                            <template #body="{ data }">
                                <div class="text-left flex flex-col gap-3">
                                    <span class="text-sm">{{ `${numOfNights} đêm, ${numOfGuests}` }}</span>
                                    <div class="flex gap-x-2">
                                        <span>Total: </span>
                                        <h3 class="text-lg fw-bold">{{ data.total_cost }} VND</h3>
                                    </div>
                                    <Button label="Booking" fluid />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <div class="other-options">
                    <div class="p-2">
                        <span class="text-md">Other empty options</span>
                    </div>
                    <DataTable :value="filteredRoomTypeList" rowGroupMode="rowspan" groupRowsBy="name"
                        :showHeaders="false" sortField="name" showGridlines class="text-lg w-full">
                        <!-- Cột thông tin phòng + chính sách -->
                        <Column field="name" header="Danh sách phòng">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1 p-2">
                                    <span class="font-semibold text-blue-700 hover:underline cursor-pointer">
                                        {{ data.name }}
                                    </span>
                                    <span class="text-red-600 text-sm">
                                        {{ data.total_quantity }} left
                                    </span>
                                    <span class="text-gray-600 text-sm">
                                        {{ data.bed_type }} bed
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        Amenities
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <!-- Num of guests -->
                        <Column header="capacity_options" style="text-align: center">
                            <template #body="{ data }">
                                <div class="flex gap-1">
                                    <template v-for="i in data.capacity_option" :key="i">
                                        <i class="pi pi-user" style="color: black"></i>
                                    </template>
                                </div>
                            </template>
                        </Column>   

                        <!-- Cột giá -->
                        <Column header="Giá" style="text-align: center">
                            <template #body="{ data }">
                                <div class="flex flex-col items-center">
                                    <span class="line-through text-gray-400 text-sm">
                                        VND {{ data.base_price_per_night }}
                                    </span>
                                    <span class="font-bold text-red-600 text-md">
                                        VND Discounted
                                    </span>
                                    <span class="text-gray-500 text-xs">Đã gồm thuế & phí</span>
                                </div>
                            </template>
                        </Column>

                        <!-- Rate policy -->
                        <!-- Cột giá -->
                        <Column header="rate_policy" style="text-align: center">
                            <template #body="{ data }">
                                Rate policy
                            </template>
                        </Column>
                    </DataTable>
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
import { ref, computed, onMounted } from 'vue';

import { Form } from '@primevue/forms';
import { FormField } from "@primevue/forms";
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';

// data table
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
    roomTypeList: Array,
    checkIn: String,
    checkOut: String,
    num_of_guests: Number,
    num_of_rooms: Number,
});

const filteredRoomTypeList = computed(() => {
    return props.roomTypeList.flatMap(room => {
        return Array.from({ length: room.max_adults }, (_, i) => ({
            ...room,
            name: room.name,
            capacity_option: i + 1
        }));
    });
});
console.log(filteredRoomTypeList.value);

const initialValues = ref({
    date: [],
    num_of_guests: 1,
    num_of_rooms: 1,
})

onMounted(() => {
    initialValues.value = {
        date: [parseVNDate(props.checkIn), parseVNDate(props.checkOut)],
        num_of_guests: props.num_of_guests || 1,
        num_of_rooms: props.num_of_rooms || 1,
    }
})

// convert to VN format date dd/mm/yyyy
const parseVNDate = (str) => {
    const [day, month, year] = str.split('/');
    return new Date(`${year}-${month}-${day}`); // chuẩn ISO, không bị ngược
};

// convert to ISO format
function parseDate(value) {
    if (!value) return null
    const [day, month, year] = value.split('/').map(Number)
    return new Date(year, month - 1, day)
}

// sample check in, check out search option event
const checkInEg = ref(props.checkIn);
const checkOutEg = ref(props.checkOut);

console.log(checkInEg.value);
console.log(checkOutEg.value);

const numOfNights = ref(0);

// num of rooms
const numOfRooms = ref(props.num_of_rooms);

// num of guests = num of adult + num of children
const numOfAdult = ref(props.num_of_guests);
const numOfChild = ref(0);

const numOfGuests = computed(() => {
    const adultLabel = numOfAdult.value > 1 ? "adults" : "adult";
    let label = `${numOfAdult.value} ${adultLabel}`;

    if (numOfChild.value > 0) {
        const childLabel = numOfChild.value > 1 ? "children" : "child";
        label += ` and ${numOfChild.value} ${childLabel}`;
    }

    return label;
});

// pop over guest option menu
const guestOptionMenu = ref();
const toggle = (event) => {
    guestOptionMenu.value.toggle(event);
}

// filter best option results
const submit = (e) => {
    if (!e.valid) return;

    console.log(e.values);

    const range = e.values.date;

    if (!Array.isArray(range) || range.length < 2) {
        numOfNights.value = 0;
        return;
    }

    // Convert từ ISO string sang Date object
    const [checkIn, checkOut] = range.map(d => new Date(d));

    // Kiểm tra hợp lệ
    if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
        numOfNights.value = 0;
        return;
    }

    // Tính số đêm (đơn vị ngày)
    const diffTime = checkOut.getTime() - checkIn.getTime();
    const nights = diffTime / (1000 * 60 * 60 * 24);

    numOfNights.value = nights > 0 ? nights : 0;

    console.log('Số đêm:', numOfNights.value);
}

// best option results
const summaryText = computed(() => {
    let text = `${numOfRooms.value} room${numOfRooms.value > 1 ? 's' : ''} for `;
    text += numOfGuests.value;
    return text
});

// sample options
const roomOptions = ref([
    {
        id: 1,
        roomType: {
            name: "Phòng 4 Người Có Ban Công",
            bedType: "2 giường đôi lớn",
            maxAdults: 4
        },
        ratePolicy: {
            name: "Không hoàn tiền",
            paymentRequirement: "Thanh toán cho chỗ nghỉ trước khi đến"
        },
        availableQuantity: 3,
        price: { original: 4088700, discounted: 2371446 },
        total_cost: 6554754
    },
    {
        id: 2,
        roomType: {
            name: "Phòng Có Giường Cỡ Queen Với Ban Công",
            bedType: "1 giường đôi lớn",
            maxAdults: 2
        },
        ratePolicy: {
            name: "Không hoàn tiền",
            paymentRequirement: "Thanh toán cho chỗ nghỉ trước khi đến"
        },
        availableQuantity: 2,
        price: { original: 7212600, discounted: 4183308 },
        total_cost: 6554754
    }
])

</script>