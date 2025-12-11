<template>

    <Head :title="pageTitle" />

    <Breadcrumb :breadcrumbList="breadcrumb" />

    <div class="review-section">
        <div class="container">
            <div class="box | flow padding-block-200 my-4" style="--flow-spacer:3rem">
                <div class="room-review-section | grid gap-y-12">
                    <div class="room-review-summary">
                        <label class="text-lg font-medium">Đánh giá của khách</label>
                        <div class="flex items-center gap-2 mt-2">
                            <Badge value="8" size="xlarge" severity="success"></Badge>
                            <span class="font-medium fs-500">Tốt</span>
                            <!-- num_reviews -->
                            <span>{{ reviewList.length }} đánh giá</span>
                            <Button label="Đọc tất cả các đánh giá" variant="link" @click="showFilterReview" />
                        </div>
                    </div>
                    <div class="room-review-detail | grid gap-y-3">
                        <label class="fs-600 font-medium">Hạng mục</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="amenity-bar">
                                <div class="flex justify-between">
                                    <span>Tiện nghi</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                            <div class="cleanliness-bar">
                                <div class="flex justify-between">
                                    <span>Sạch sẽ</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                            <div class="value-bar">
                                <div class="flex justify-between">
                                    <span>Đáng giá tiền</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                            <div class="staff-bar">
                                <div class="flex justify-between">
                                    <span>Nhân viên phục vụ</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                            <div class="whereabout-bar">
                                <div class="flex justify-between">
                                    <span>Địa điểm</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                            <div class="comfort-bar">
                                <div class="flex justify-between">
                                    <span>Thoải mái</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore" />
                            </div>
                        </div>
                    </div>
                    <div class="title-review-detail | flow" style="--flow-spacer:1rem">
                        <label class="fs-600 font-medium">Chọn chủ đề đọc để đánh giá</label>
                        <div class="title-list | flex flex-wrap gap-2">
                            <template v-for="item in titleReviewList.slice(0, 5)">
                                <Button size="small" @click="toggleFilterTitleKeyword(item)" :label="item.label"
                                    variant="outlined" icon="pi pi-plus" rounded
                                    :class="['keyword-btn', { active: isActive(item) }]" />
                            </template>
                        </div>
                    </div>
                    <div class="favourite-review-detail | flow" style="--flow-spacer:1rem">
                        <label class="fs-600 font-medium">Khách lưu trú ở đây thích điều gì</label>
                        <div class="top-three-review-list | grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template v-for="review in reviewList.slice(0, 3)">
                                <div class="review-item | border border-gray-200 rounded-md">
                                    <div class="p-4 flow">
                                        <div class="review-user | flex items-center">
                                            <Avatar label="V" size="large" class="mr-2"
                                                style="background-color: #ece9fc; color: #2a1261" shape="circle" />
                                            <!-- user name and user nationality -->
                                            <div class="grid">
                                                <span
                                                    class="font-semibold">{{ review.booking.customer.full_name }}</span>
                                                <span class="text-xs">{{ review.booking.customer.country }}</span>
                                            </div>
                                        </div>
                                        <div class="review-text">
                                            <p class="text-1-line">{{ review.general_review }}</p>
                                        </div>
                                    </div>
                                    <Button variant="link" label="Tìm hiểu thêm" class="text-xs"
                                        @click="showMoreReviewDetail" />
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

/* hide scroll dialog */
.hide-scroll {
    scrollbar-width: none;
    /* Firefox */
    -ms-overflow-style: none;
    /* IE */
}

.hide-scroll::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari */
}
</style>

<script setup>
import { ref, reactive, defineAsyncComponent, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDialog } from 'primevue/usedialog';

// component
import Breadcrumb from '../../Components/Breadcrumb.vue';
import ProgressBar from 'primevue/progressbar';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Avatar from 'primevue/avatar';

const page = usePage();
const breadcrumb = page.props.breadcrumb;

const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

const props = defineProps({
    customerTypeList: Array,
    reviewList: Array
});
console.log(props.reviewList);

// review list
const reviewList = ref([]);
watch(
    () => props.reviewList,
    (newValue) => {
        reviewList.value = newValue;
    },
    { immediate: true }
);

// room review score
const roomReviewScore = ref(70);

// open review dialog
const dialog = useDialog();
const filterReview = defineAsyncComponent(() => import('../../Components/Dialog/FilterReview.vue'));

const showFilterReview = () => {
    dialog.open(filterReview, {
        props: {
            header: 'Đánh giá của khách hàng',
            blockScroll: true,
            style: {
                width: '50vw',
                minHeight: '100vh',
                minHeight: '100dvh'
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'right',
            contentClass: 'hide-scroll'
        },
        data: {
            customerTypeList: props.customerTypeList,
            reviewList: props.reviewList,
            keywords: activeTitleKeywords.value
        }
    });
}

const showMoreReviewDetail = () => {

}

// filter review title
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

const activeTitleKeywords = ref([]);

const toggleFilterTitleKeyword = (kw) => {
    const index = activeTitleKeywords.value.findIndex(x => x.name === kw.name)

    if (index >= 0) {
        // Nếu đang active → bỏ chọn
        activeTitleKeywords.value.splice(index, 1)
    } else {
        // Nếu chưa active → thêm
        activeTitleKeywords.value.push(kw)
    }

    showFilterReview();
}

const isActive = (item) => {
    return activeTitleKeywords.value.some(k => k.name === item.name)
}

</script>