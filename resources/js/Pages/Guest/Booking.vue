<template>
    <div class="booking-section pb-10">
        <div class="booking-section-header | padding-block-200">

            <Form v-slot="$form" :initialValues
                class="booking-filter-section-form | text-center gap-y-3 bg-amber-100 padding-block-400 px-4 rounded-xl shadow-xl">
                <div class="flex gap-x-4 items-center">
                    <!-- Check-in -->
                    <div>
                        <label class="block mb-1">Check-in</label>
                        <FormField name="checkIn" v-slot="{ field, error }">
                            <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon fluid />
                            <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                        </FormField>
                    </div>

                    <!-- Check-out -->
                    <div>
                        <label class="block mb-1">Check-out</label>
                        <FormField name="checkOut" v-slot="{ field, error }">
                            <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon fluid />
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

                <Button class="mt-6" severity="info" size="large" label="Tìm phòng" raised />
            </Form>
        </div>
        <div class="container">
            <div class="booking-section-content | grid grid-cols-[auto_1fr] mt-20">
                <section class="booking-section-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- slide price range -->
                        <div class="box | flex flex-col flow">
                            <label for="" class="admin-label">Giá mỗi đêm</label>
                            <Slider v-model="roomPriceRange" range :min="0" :max="5000000" class="my-3" />
                            <div class="flex justify-between items-center gap-x-2">
                                <span>Tối thiểu</span>
                                <InputNumber v-model="minRoomPrice" size="small" :maxFractionDigits="0" mode="currency"
                                    currency="VND" locale="vi-VN" showButtons :step="5000" />
                            </div>
                            <div class="flex justify-between items-center gap-x-2">
                                <span>Tối đa</span>
                                <InputNumber v-model="maxRoomPrice" size="small" :maxFractionDigits="0" mode="currency"
                                    currency="VND" locale="vi-VN" showButtons :step="5000" />
                            </div>
                        </div>

                        <!-- filter room -->
                        <Radioselect :list="bedTypeList" v-model="filterBedType" label="Loại giường" />

                        <!-- filter room amenities and features-->
                        <Checkboxselect :list="roomAmenitiesList" v-model="filterRoomAmenities"
                            label="Tiện nghi phòng" />

                        <!-- filter room services -->
                        <Checkboxselect :list="roomServicesList" v-model="filterRoomServices" label="Dịch vụ phòng" />

                        <!-- filter payment method -->
                        <Checkboxselect :list="nonUserPaymentMethodList" v-model="filterPaymentMethod"
                            label="Lựa chọn thanh toán" />

                    </div>
                </section>
                <section class="booking-section-content__right">
                    <div class="card">
                        <DataView :value="formattedRooms">
                            <template #list="slotProps">
                                <div class="flex flex-col">
                                    <div v-for="(item, index) in slotProps.items" :key="index">
                                        <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4"
                                            :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                                            <div class="md:w-40 relative">
                                                <img class="block xl:block mx-auto rounded w-full" :src="item.images
                                                    ? `/storage/${item.images}`
                                                    : '/img/default-blank-img.jpg'" />
                                                <div class="absolute bg-black/70 rounded-border"
                                                    style="left: 4px; top: 4px">
                                                    <!-- <Tag :value="item.inventoryStatus" :severity="getSeverity(item)">
                                                    </Tag> -->
                                                </div>
                                            </div>
                                            <div
                                                class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                                <div
                                                    class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                    <div>
                                                        <span
                                                            class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{ item.category }}</span>
                                                        <div class="text-lg font-medium mt-2">{{ item.name }}</div>
                                                    </div>
                                                    <div class="bg-surface-100 p-1" style="border-radius: 30px">
                                                        <div class="bg-surface-0 flex items-center gap-2 justify-center py-1 px-2"
                                                            style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                                            <span
                                                                class="text-surface-900 font-medium text-sm">{{ item.rating }}</span>
                                                            <i class="pi pi-star-fill text-yellow-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col md:items-end gap-8">
                                                    <span class="text-xl font-semibold">${{ item.price }}</span>
                                                    <div class="flex flex-row-reverse md:flex-row gap-2">
                                                        <Button variant="outlined" label="View details"></Button>
                                                        <Button icon="pi pi-shopping-cart" label="Book Now"
                                                            :disabled="item.inventoryStatus === 'OUTOFSTOCK'"
                                                            class="flex-auto md:flex-initial whitespace-nowrap"></Button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </DataView>
                    </div>
                </section>
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
import { ref, reactive, computed, onMounted } from 'vue';

import { Form } from '@primevue/forms';
import { FormField } from "@primevue/forms";
import DataView from 'primevue/dataview';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Slider from 'primevue/slider';

// component
import Radioselect from "../../Components/Radioselect.vue";
import Checkboxselect from "../../Components/Checkboxselect.vue";

const props = defineProps({
    checkIn: String,
    checkOut: String,
    num_of_guests: Number,
    num_of_rooms: Number,
    roomTypeList: Array,
});
console.log(props.roomTypeList);
const formattedRooms = computed(() =>
    props.roomTypeList.map(room => {
        const amenities = room.amenities ? JSON.parse(room.amenities) : {}
        return {
            id: room.id,
            name: room.name,
            category: amenities.view || 'No View',
            rating: 4.5,
            price: room.base_price_per_night || 0,
            images: room.images?.length
                ? room.images[0].path
                : null,
            description: room.description,
        }
    })
)

const initialValues = ref({
    checkIn: '',
    checkOut: '',
    num_of_guests: 1,
    num_of_rooms: 1,
})

onMounted(() => {
    initialValues.value = {
        checkIn: parseVNDate(props.checkIn),
        checkOut: parseVNDate(props.checkOut),
        num_of_guests: props.num_of_guests || 1,
        num_of_rooms: props.num_of_rooms || 1,
    }
})

const parseVNDate = (str) => {
    const [day, month, year] = str.split('/');
    return new Date(`${year}-${month}-${day}`); // chuẩn ISO, không bị ngược
};

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

// slider price range
const minRoomPrice = ref(0);
const maxRoomPrice = ref(1000000);
const roomPriceRange = computed({
    get() {
        return [minRoomPrice.value, maxRoomPrice.value];
    },
    set([min, max]) {
        minRoomPrice.value = min;
        maxRoomPrice.value = max;
    },
});

// filter bed type
const bedTypeList = reactive([
    {
        label: 'Đôi',
        name: 'double bed'
    },
    {
        label: 'Giường đôi lớn',
        name: 'queen bed'
    },
    {
        label: 'Đơn/hai giường đơn',
        name: 'single bed/twin beds'
    },
    {
        label: 'Giường đơn lớn',
        name: 'large single bed'
    },
]);
const filterBedType = ref();

// filter room amenities and features
const roomAmenitiesList = reactive([
    {
        label: 'TV',
        name: 'television'
    },
    {
        label: 'Điều hòa',
        name: 'air conditioner'
    },
    {
        label: 'Bếp',
        name: 'kitchenette'
    },
    {
        label: 'Máy pha trà/cà phê',
        name: 'tea/coffee maker'
    },
    {
        label: 'Sưởi',
        name: 'heating'
    },
    {
        label: 'Ban công/sân hiên',
        name: 'balcony/patio'
    },
    {
        label: 'View biển',
        name: 'ocean view'
    },
    {
        label: 'View city',
        name: 'city view'
    },
]);
const filterRoomAmenities = ref();

// filter room services
const roomServicesList = reactive([
    {
        label: 'Có ăn sáng',
        name: 'breakfast'
    },
    {
        label: 'Bữa tối',
        name: 'dinner'
    },
    {
        label: 'Bữa trưa',
        name: 'lunch'
    },
    {
        label: 'Miễn phí dịch vụ đưa đón',
        name: 'free shuttle service'
    },
    {
        label: 'Miễn phí đồ ăn nhẹ',
        name: 'complimentary snacks'
    },
    {
        label: 'Order đồ ăn bên ngoài',
        name: 'order food from outside'
    },
]);
const filterRoomServices = ref();

// filter payment method
const nonUserPaymentMethodList = reactive([
    {
        label: 'Thanh toán ngay',
        name: 'pay now(non-refundable)'
    },
    {
        label: 'Đặt cọc trước',
        name: 'deposit required(30-50%)'
    }
])
const userPaymentMethodList = reactive([
    {
        label: 'Đặt trước, trả sau',
        name: 'book now, pay later',
        note: 'free cancellation up to 24 hours before arrival'
    },
    {
        label: 'Thanh toán ngay',
        name: 'pay now(non-refundable)',
        note: 'discount 5-10%'
    },
    {
        label: 'Đặt cọc trước',
        name: 'deposit required(10-20%)'
    }
]);
const filterPaymentMethod = ref();

</script>