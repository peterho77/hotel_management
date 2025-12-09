<template>
    <div class="grid gap-y-8">
        <div class="flow" style="--flow-spacer:.75rem">
            <label class="fs-600 font-medium">Bộ lọc</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-2">
                <div class="grid gap-1">
                    <span>Nhóm khách</span>
                    <Select :options="customerTypeList" optionLabel="name" optionValue="id" placeholder="Nhóm khách"
                        v-model="selectedCustomerType" />
                </div>
                <div class="grid gap-1">
                    <span>Điểm đánh giá</span>
                    <Select :options="ratingRangeList" :optionLabel="opt => opt.range ? `${opt.name} (${opt.range})` : opt.name"
                        optionValue="range" placeholder="Tất cả" v-model="selectedRatingRange" />
                </div>
                <div class="grid gap-1">
                    <span>Thời gian</span>
                    <Select :options="reviewTimeRangeList" optionLabel="label" optionValue="label"
                        v-model="selectedTimeRange" placeholder="Tất cả" />
                </div>
            </div>
        </div>
        <div class="title-review-detail | flow" style="--flow-spacer:.75rem">
            <label class="fs-600 font-medium">Chọn chủ đề đọc để đánh giá</label>
            <div class="title-list | flex flex-wrap gap-2">
                <template v-for="item in (showAllTitles ? titleReviewList : titleReviewList.slice(0, 5))">
                    <Button size="small" @click="toggleTitleKeyword(item)" :label="item.label" variant="outlined"
                        icon="pi pi-plus" rounded :class="['keyword-btn', { active: isActive(item) }]" />
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
            <Select :options="reviewType" v-model="filterReviewType" optionLabel="label" optionValue="value"
                :placeholder="reviewType[0].label" />
        </div>
        <div class="detail-review-section | grid gap-y-16">
            <template v-for="review in finalReviewList">
                <div class="detail-review-item | flex gap-12">
                    <div class="flow" style="--flow-spacer:1rem">
                        <div class="review-user | flex items-center">
                            <Avatar label="V" size="large" class="mr-2"
                                style="background-color: #ece9fc; color: #2a1261" shape="circle" />
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
                                <span
                                    class="fs-300">{{ getCustomerTypeName(review.booking.customer.customer_type_id) }}</span>
                            </div>
                            <div class="num-nights | flex items-center gap-x-2">
                                <i class="pi pi-moon"></i>
                                <!-- num-nights / checkin time -->
                                <span class="fs-300">{{ review.booking.num_nights }} đêm - tháng 12/2025</span>
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
                                <span
                                    class="general-review-text | fs-700 font-semibold">{{ review.general_review }}</span>
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

:deep(.p-button-outlined:hover) {
    border-color: var(--primary-color-500);
    color: var(--primary-color-500);
}

.keyword-btn.active {
    border-color: var(--primary-color-500);
    color: var(--primary-color-500);
}
</style>

<script setup>
import { ref, reactive, onMounted, inject, computed } from 'vue';

import ScrollTop from 'primevue/scrolltop';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';

const dialogRef = inject('dialogRef');

// take list data from page
const customerTypeList = ref([
    { id: null, name: "Tất cả" },
])
const reviewList = ref([]);

onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        let moreCustomerTypeList = params.customerTypeList || []

        customerTypeList.value = [
            { id: null, name: "Tất cả" },
            ...moreCustomerTypeList
        ]
        reviewList.value = params.reviewList || [];
    }
})

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

const showAllTitles = ref(false);

const showMoreReviewTitle = () => {
    showAllTitles.value = !showAllTitles.value;
}

const activeTitleKeywords = ref([]);

const toggleTitleKeyword = (kw) => {
    const index = activeTitleKeywords.value.findIndex(x => x.name === kw.name)

    if (index >= 0) {
        // Nếu đang active → bỏ chọn
        activeTitleKeywords.value.splice(index, 1)
    } else {
        // Nếu chưa active → thêm
        activeTitleKeywords.value.push(kw)
    }
}

const isActive = (item) => {
    return activeTitleKeywords.value.some(k => k.name === item.name)
}

// filter customer type list
const getCustomerTypeName = (id) => {
    return customerTypeList.value.find(item => item.id === id).name;
}

const selectedCustomerType = ref(null);

// filter review by rating range
const ratingRangeList = reactive([
    { name: "Tất cả", range: '' },
    {
        name: 'Tuyệt vời',
        range: '>9'
    },
    {
        name: 'Tốt',
        range: '8-9'
    },
    {
        name: 'Khá',
        range: '7-8'
    },
    {
        name: 'Trung bình',
        range: '5-7'
    },
    {
        name: 'Tệ',
        range: '3-5'
    },
    {
        name: 'Rất tệ',
        range: '1-3'
    },
])

const selectedRatingRange = ref(null);

const parseRatingRange = (range) => {
    if (range.includes('-')) {
        const [min, max] = range.split('-').map(Number)
        return { min, max }
    }

    if (range.startsWith('>')) {
        return {
            min: Number(range.replace('>', '')),
            max: 10
        }
    }

    return { min: 1, max: 10 }
}

// review review time
const reviewTimeRangeList = reactive([
    { label: "Tất cả", monthStart: 1, monthEnd: 12 },
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

const selectedTimeRange = ref(null);

const parseDateFromString = (str) => {
    // ví dụ: "00:39  08/12/2025"
    const [time, date] = str.trim().split(/\s+/)  // ["00:39", "08/12/2025"]

    const [day, month, year] = date.split('/').map(Number)

    return new Date(year, month - 1, day)
}

// filter review type
const reviewType = reactive([
    {
        label: 'Phù hợp nhất',
        value: 'relevant'
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
        value: 'highest'
    },
    {
        label: 'Điểm thấp nhất',
        value: 'lowest'
    },
])

const filterReviewType = ref(reviewType[0].value);

const sortedReviewList = computed(() => {
    let arr = [...(reviewList.value || [])]

    switch (filterReviewType.value) {
        case 'relevant':
            return arr.sort((a, b) => {
                const lenA =
                    (a.positive?.length || 0) + (a.negative?.length || 0)
                const lenB =
                    (b.positive?.length || 0) + (b.negative?.length || 0)

                // ưu tiên rating cao → nội dung dài
                if (b.rating !== a.rating) return b.rating - a.rating
                return lenB - lenA
            })

        // mới nhất
        case 'newest':
            return arr.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at)
            )

        // cũ nhất
        case 'oldest':
            return arr.sort(
                (a, b) => new Date(a.created_at) - new Date(b.created_at)
            )

        // điểm cao nhất
        case 'highest':
            return arr.sort((a, b) => b.rating - a.rating)

        // điểm thấp nhất
        case 'lowest':
            return arr.sort((a, b) => a.rating - b.rating)

        default:
            return arr
    }
})

// final review list
const finalReviewList = computed(() => {
    let arr = [...sortedReviewList.value]

    // 1. Filter theo nhóm khách hàng
    if (selectedCustomerType.value) {
        arr = arr.filter(r =>
            r.booking?.customer?.customer_type_id === selectedCustomerType.value
        )
    }

    // 2. Filter theo keyword
    if (activeTitleKeywords.value.length > 0) {
        arr = arr.filter((r) => {
            return activeTitleKeywords.value.some((kw) => {
                const k1 = kw.name.toLowerCase()
                const k2 = kw.label.toLowerCase()

                return (
                    r.positive?.toLowerCase().includes(k1) ||
                    r.positive?.toLowerCase().includes(k2) ||
                    r.negative?.toLowerCase().includes(k1) ||
                    r.negative?.toLowerCase().includes(k2) ||
                    r.general_review?.toLowerCase().includes(k1) ||
                    r.general_review?.toLowerCase().includes(k2)
                )
            })
        })
    }

    // 3. Filter theo điểm rating
    if (selectedRatingRange.value) {
        const { min, max } = parseRatingRange(selectedRatingRange.value)
        arr = arr.filter(r => r.rating >= min && r.rating < max)
    }

    // 4. filter theo hời gian (quý/tháng)
    if (selectedTimeRange.value) {
        const selected = reviewTimeRangeList.find(
            r => r.label === selectedTimeRange.value
        )

        if (selected) {
            const { monthStart, monthEnd } = selected

            arr = arr.filter(r => {
                const createdDate = parseDateFromString(r.created_at)
                const month = createdDate.getMonth() + 1

                return month >= monthStart && month <= monthEnd
            })
        }
    }

    return arr
})

</script>