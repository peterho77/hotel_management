<script setup>
import { ref, reactive, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

import DataView from 'primevue/dataview';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import Slider from 'primevue/slider';
import Tag from 'primevue/tag';
import { formatLabel } from "@/Composables/formatData";

// router
import { router } from '@inertiajs/vue3';

// component
import Breadcrumb from '../../Components/Breadcrumb.vue';
import Radioselect from "../../Components/Radioselect.vue";
import Checkboxselect from "../../Components/Checkboxselect.vue";

const page = usePage();
const breadcrumb = page.props.breadcrumb;
console.log(breadcrumb);
const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

const props = defineProps({
    roomTypeList: Array,
});

const formattedRooms = computed(() =>
    props.roomTypeList.map(room => {
        const amenities = room.amenities || [];
        let features = amenities.map(item => formatLabel(item.name));
        return {
            id: room.id,
            name: room.name,
            features: features || [],
            view: amenities.view || '',
            rating: 4.5,
            price: room.base_price || 0,
            images: room.images?.length > 0
                ? room.images[0].path
                : null,
            description: room.description,
        }
    })
)
console.log(formattedRooms.value);

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

// room type list
const getSeverity = (roomType) => {
    switch (roomType.inventoryStatus) {
        case 'INSTOCK':
            return 'success';

        case 'LOWSTOCK':
            return 'warn';

        case 'OUTOFSTOCK':
            return 'danger';

        default:
            return null;
    }
};

</script>

<template>

    <Head :title="pageTitle" />

    <Breadcrumb :breadcrumbList="breadcrumb" />

    <div class="rooms-list-section | padding-block-200">
        <div class="container">
            <div class="room-section-content | grid grid-cols-[auto_1fr] gap-3">
                <section class="room-section-content__left">
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
                <section class="room-section-content__middle">
                    <div class="card">
                        <DataView :value="formattedRooms">
                            <template #list="slotProps">
                                <div class="flex flex-col">
                                    <div v-for="(item, index) in slotProps.items" :key="index">
                                        <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4"
                                            :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                                            <div class="md:w-40 relative">
                                                <div class="w-full aspect-4/3 overflow-hidden rounded-md">
                                                    <img class="block w-full h-full object-cover" :src="(item.images && item.images.length > 0)
                                                        ? (item.images.startsWith('/img/')
                                                            ? item.images
                                                            : '/storage/' + item.images)
                                                        : '/img/default-blank-img.jpg'" :alt="item.name" />
                                                </div>
                                                <div class="absolute bg-black/70 rounded-border"
                                                    style="left: 4px; top: 4px">
                                                    <!-- <Tag :value="item.inventoryStatus" :severity="getSeverity(item)" rounded>
                                                    </Tag> -->
                                                </div>
                                            </div>
                                            <div
                                                class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                                <div
                                                    class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                    <div class="flex flex-col gap-y-2">
                                                        <div class="text-lg font-medium mt-2">{{ item.name }}</div>
                                                        <span
                                                            class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{ item.view }}</span>
                                                        <div class="flex gap-x-2">
                                                            <template v-for="(i, index) in item.features.slice(0, 3)"
                                                                :key="index">
                                                                <Tag severity="success" rounded :label="i" :value="i"
                                                                    :pt="{
                                                                        label: { class: 'text-xs font-medium' }
                                                                    }" />
                                                            </template>
                                                        </div>
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
                                                        <Button variant="outlined" label="View details"
                                                            @click="router.visit(route('rooms.detail', { id: item.id }))"></Button>
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
