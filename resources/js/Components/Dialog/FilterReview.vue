<template>
    <div class="grid gap-y-8">
        <div class="flow" style="--flow-spacer:.75rem">
            <label class="fs-600 font-medium">Bộ lọc</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-2">
                <div class="grid gap-1">
                    <span>Nhóm khách</span>
                    <Select :options="customerTypeList" optionLabel="name" placeholder="Nhóm khách" />
                </div>
                <div class="grid gap-1">
                    <span>Điểm đánh giá</span>
                    <Select :options="reviewScore" :optionLabel="(opt) => `${opt.name} (${opt.score})`"
                        placeholder="Tất cả" />
                </div>
                <div class="grid gap-1">
                    <span>Thời gian</span>
                    <Select :options="reviewTime" optionLabel="label" placeholder="Tất cả" />
                </div>
            </div>
        </div>
        <div class="title-review-detail | flow" style="--flow-spacer:.75rem">
            <label class="fs-600 font-medium">Chọn chủ đề đọc để đánh giá</label>
            <div class="title-list | flex flex-wrap gap-2">
                <template v-for="item in (showAllTitles ? titleReviewList : titleReviewList.slice(0, 5))">
                    <Button size="small" :label="item.label" variant="outlined" icon="pi pi-plus" rounded />
                </template>
                <Button severity="secondary" size="small" @click="showMoreReviewTitle">
                    {{ showAllTitles ? 'Thu gọn' : 'Hiển thị thêm' }}
                </Button>
            </div>
        </div>
    </div>
    <Divider />
    <div>
        <div class="filter-review-section | flex justify-end items-center gap-2 mb-4">
            <span>Sắp xếp đánh giá theo</span>
            <Select :options="filterReviewList" optionLabel="label" :placeholder="filterReviewList[0].label" />
        </div>
        <div class="detail-review-section | grid gap-y-16">
            <template v-for="review in reviewList">
                <div class="detail-review-item | flex gap-12">
                    <div class="flow" style="--flow-spacer:1rem">
                        <div class="review-user | flex items-center">
                            <Avatar label="V" size="large" class="mr-2"
                                style="background-color: #ece9fc; color: #2a1261" shape="circle" />
                            <!-- user name and user nationality -->
                            <div class="grid">
                                <span class="font-semibold">{{ review.booking.customer.full_name }}</span>
                                <span class="text-xs">Việt Nam</span>
                            </div>
                        </div>
                        <div class="booking-info | grid gap-2">
                            <div class="customer-type | flex items-center gap-x-2">
                                <i class="pi pi-users"></i>
                                <!-- customer-type-name -->
                                <span class="fs-300">Khách lẻ</span>
                            </div>
                            <div class="num-nights | flex items-center gap-x-2">
                                <i class="pi pi-moon"></i>
                                <!-- num-nights / checkin time -->
                                <span class="fs-300">2 đêm - tháng 12/2025</span>
                            </div>
                            <div class="room | flex items-center gap-x-2">
                                <i class="pi pi-home"></i>
                                <!-- room-name -->
                                <span class="fs-300">Phòng standard</span>
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
                <Divider />
            </template>
        </div>
        <ScrollTop />
    </div>
</template>

<style scoped>
:deep(.no-text .p-progressbar-value .p-progressbar-label) {
    display: none !important;
}

:deep(.p-progressbar-value) {
    background: var(--primary-color-700);
}

:deep(.p-button-outlined) {
    border-color: var(--neutral-color-500);
    color: var(--neutral-color-700);
}
</style>

<script setup>
import { ref, reactive, onMounted, inject } from 'vue';

import ScrollTop from 'primevue/scrolltop';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';

const dialogRef = inject('dialogRef');

// take list data from page
const customerTypeList = ref();
const reviewList = ref();

onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        customerTypeList.value = params.customerTypeList || [];
        reviewList.value = params.reviewList || [];
    }
})

// title review list
const titleReviewList = reactive([
    {
        label: 'Phòng',
        name: 'room'
    },
    {
        label: 'Vị trí',
        name: 'location'
    },
    {
        label: 'Giường',
        name: 'bed'
    },
    {
        label: 'Bữa sáng',
        name: 'breakfast'
    },
    {
        label: 'Tiếng ồn',
        name: 'noise'
    },
    {
        label: 'Phòng tắm',
        name: 'bathroom'
    },
    {
        label: 'Bữa tối',
        name: 'dinner'
    },
    {
        label: 'Nhận phòng',
        name: 'checkin'
    },
    {
        label: 'Cửa sổ',
        name: 'window'
    }
])

const showAllTitles = ref(false);

const showMoreReviewTitle = () => {
    showAllTitles.value = !showAllTitles.value;
}

// review score
const reviewScore = reactive([
    {
        name: 'Tuyệt vời',
        score: '>9'
    },
    {
        name: 'Tốt',
        score: '8-9'
    },
    {
        name: 'Khá',
        score: '7-8'
    },
    {
        name: 'Trung bình',
        score: '5-7'
    },
    {
        name: 'Tệ',
        score: '3-5'
    },
    {
        name: 'Rất tệ',
        score: '1-3'
    },
])

// review time
const reviewTime = reactive([
    {
        label: 'Tháng 1-3',
        monthStart: 1,
        monthEnd: 3
    },
    {
        label: 'Tháng 4-6',
        monthStart: 4,
        monthEnd: 6
    },
    {
        label: 'Tháng 7-9',
        monthStart: 7,
        monthEnd: 9
    },
    {
        label: 'Tháng 10-12',
        monthStart: 10,
        monthEnd: 12
    },
])

// filter review list
const filterReviewList = reactive([
    {
        label: 'Phù hợp nhất',
        value: 'suitable'
    },
    {
        label: 'Mới nhất',
        value: 'newest'
    },
    {
        label: 'Cũ nhất',
        value: 'oldest'
    },
    {
        label: 'Điểm cao nhất',
        value: 'highest-score'
    },
    {
        label: 'Điểm thấp nhất',
        value: 'lowest-score'
    },
])

</script>