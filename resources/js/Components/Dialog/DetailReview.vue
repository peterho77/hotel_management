<template>
    <div class="detail-review-item | flex gap-12">
        <div class="flow" style="--flow-spacer:1rem">
            <div class="review-user | flex items-center">
                <Avatar label="V" size="large" class="mr-2" style="background-color: #ece9fc; color: #2a1261"
                    shape="circle" />
                <!-- user name and user nationality -->
                <div class="grid">
                    <span class="font-semibold">{{ review.booking.customer.full_name }}</span>
                    <span class="text-xs">{{ review.booking.customer.country }}</span>
                </div>
            </div>
            <div class="booking-info | grid gap-2">
                <div class="customer-type | flex items-center gap-x-2">
                    <i class="pi pi-users"></i>
                    <!-- customer-type-name -->
                    <span class="fs-300">{{ getCustomerTypeName(review.booking.customer.customer_type_id) }}</span>
                </div>
                <div class="num-nights | flex items-center gap-x-2">
                    <i class="pi pi-moon"></i>
                    <!-- num-nights / checkin time -->
                    <span class="fs-300">{{ review.booking.num_nights }} đêm - tháng
                        {{ getBookingTime(review.created_at) }}</span>
                </div>
                <div class="room | flex items-center gap-x-2">
                    <i class="pi pi-home"></i>
                    <!-- room-name -->
                    <span class="fs-300">{{ getBookingRooms(review) }}</span>
                </div>
            </div>
        </div>
        <div class="review-content | flex-1 flow" style="--flow-spacer:.5rem">
            <div class="flex items-start">
                <div>
                    <div class="created-review-date | flex gap-1">
                        <span class="fs-300 text-gray-500">Ngày đánh giá:</span>
                        <!-- created_at -->
                        <span class="fs-300 text-gray-500">{{ review.created_at }}</span>
                    </div>
                    <span class="general-review-text | fs-700 font-semibold">{{ review.general_review }}</span>
                </div>
                <Badge class="ml-auto" :value="review.rating" size="xlarge" severity="success"></Badge>
            </div>
            <div class="pros | flex items-center gap-2">
                <i class="pi pi-plus-circle"></i>
                <p>{{ review.positive }}</p>
            </div>
            <div class="cons | flex items-center gap-2">
                <i class="pi pi-minus-circle"></i>
                <p>{{ review.negative }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject, ref, reactive, onMounted } from 'vue';

import Badge from 'primevue/badge';
import Avatar from 'primevue/avatar';

const dialogRef = inject('dialogRef');

const review = reactive({
    id: null,
    booking_id: null,
    rating: 0,
    general_review: '',
    positive: '',
    negative: '',
    booking: {
        id: null,
        customer: {
            full_name: '',
            avatar: null,
            email: '',
            phone: ''
        },
        room_booking_items: []
    }
});

const customerTypeList = reactive([]);

onMounted(() => {
    const params = dialogRef?.value?.data;
    if (!params) return;

    if (params.review) Object.assign(review, params.review);

    if (Array.isArray(params.customerTypeList))
        customerTypeList.splice(0, customerTypeList.length, ...params.customerTypeList);

    console.log('Review:', review);
    console.log('Customer Type List:', customerTypeList);
});

// get review booking detail
const getBookingTime = (createdAt) => {
    if (!createdAt) return '';
    const [time, date] = createdAt.split(/\s+/).filter(Boolean);
    const [day, month, year] = date.split('/');

    return `${month}/${year}`;
}
const getBookingRooms = (review) => {
    let roomList = review.booking.room_booking_items.map(item => item.room_option.room_type.name);
    return roomList.join(', ');
}
const getCustomerTypeName = (id) => {
    if (!id || !Array.isArray(customerTypeList)) return 'Khách';
    const customer = customerTypeList.find(item => item.id === id);
    return customer ? customer.name : 'Khách';
}

</script>