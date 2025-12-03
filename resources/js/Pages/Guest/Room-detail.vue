<template>

    <Head :title="pageTitle" />

    <Breadcrumb :breadcrumbList="breadcrumb" />

    <div class="room-detail-section | padding-block-400">
        <div class="container | grid gap-y-2">
            <div class="box | grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="image-gallery-section">
                    <h1 class="font-medium text-xl mx-2">{{ roomType.name }}</h1>
                    <Galleria :value="getGalleryImages(roomType.images)" :responsiveOptions="responsiveOptions"
                        :numVisible="4" containerStyle="max-width: 600px" :circular="true">
                        <template #item="slotProps">
                            <img :src="slotProps.item.itemImageSrc" :alt="slotProps.item.alt"
                                class="w-full max-h-96 object-cover rounded-lg" />
                        </template>

                        <template #thumbnail="slotProps">
                            <img :src="slotProps.item.itemImageSrc" :alt="slotProps.item.alt"
                                class="max-h-30 w-20 object-cover rounded-md border-[0.5px]" />
                        </template>
                    </Galleria>
                </div>
                <div class="grid mt-6 | flow" style="--flow-spacer:1rem">
                    <div class="detail-info-section | grid gap-y-1">
                        <label class="text-lg font-medium">Thông tin chi tiết</label>
                        <template v-for="(value, key) in detailRoomTypeInfo">
                            <template v-if="key === 'status'">
                                <div class="grid grid-cols-[150px_1fr] gap-1 items-center">
                                    <span class="shrink-0">{{ formatLabel(key) }}:</span>
                                    <Tag :severity="getRoomStatus(value).display" :value="getRoomStatus(value).label"
                                        class="max-w-30 text-nowrap inline-block" rounded></Tag>
                                </div>
                            </template>
                            <template v-else>
                                <div class="grid grid-cols-[150px_1fr] gap-1">
                                    <span class="shrink-0">{{ formatLabel(key) }}:</span>
                                    <span>{{ value }}</span>
                                </div>
                            </template>
                        </template>
                    </div>
                    <div class="amenities-section | grid gap-y-1">
                        <label class="text-lg font-medium">Các tiện nghi phòng</label>
                        <div class="flex flex-wrap gap-x-2">
                            <template v-for="amenity in roomType.amenities">
                                <Tag :icon="amenity.icon" :value="formatLabel(amenity.name)" severity="secondary"></Tag>
                            </template>
                        </div>
                    </div>
                    <div>
                        <Button label="Đặt phòng ngay" severity="success" :disabled="roomType.status === 'inactive'" />
                    </div>
                </div>
            </div>
            <div class="box | flow padding-block-200" style="--flow-spacer:3rem">
                <div class="room-review-section | grid gap-y-3">
                    <label class="text-lg font-medium">Đánh giá của khách</label>
                    <div class="room-review-summary | flex items-center gap-2">
                        <Badge value="8" size="xlarge" severity="success"></Badge>
                        <span class="font-medium fs-500">Tốt</span>
                        <!-- num_reviews -->
                        <span>0 đánh giá</span>
                        <Button label="Đọc tất cả các đánh giá" variant="link" />
                    </div>
                    <div class="room-review-detail | grid gap-y-2">
                        <label class="fs-600 font-medium">Hạng mục</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-3">
                            <div class="amenity-bar">
                                <div class="flex justify-between">
                                    <span>Tiện nghi</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore"/>
                            </div>
                            <div class="cleanliness-bar">
                                <div class="flex justify-between">
                                    <span>Sạch sẽ</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore"/>
                            </div>
                            <div class="value-bar">
                                <div class="flex justify-between">
                                    <span>Đáng giá tiền</span>
                                    <span>{{ roomReviewScore / 10 }}</span>
                                </div>
                                <ProgressBar class="no-text" severity="info" :value="roomReviewScore"/>
                            </div>
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

:deep(.p-progressbar-value){
    background: var(--primary-color-700);
}
</style>

<script setup>
import { ref, reactive, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatLabel } from "@/Composables/formatData";
import Galleria from 'primevue/galleria';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Badge from 'primevue/badge';
import OverlayBadge from 'primevue/overlaybadge';
import ProgressBar from 'primevue/progressbar';

// router
import { router } from '@inertiajs/vue3';

// component
import Breadcrumb from '../../Components/Breadcrumb.vue';

const page = usePage();
const breadcrumb = page.props.breadcrumb;
const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

const props = defineProps({
    roomType: Object,
});
console.log(props.roomType);
const roomType = reactive(props.roomType);
console.log(roomType);

// detail info room type
const removeGeneralKeys = ['id', 'name', 'images', 'amenities', 'total_quantity'];

const detailRoomTypeInfo = computed(() => {
    return Object.fromEntries(
        Object.entries(roomType).filter(([key]) => !removeGeneralKeys.includes(key))
    );
});

// room status
const getRoomStatus = (status) => {
    switch (status) {
        case 'active':
            return {
                label: 'Còn phòng',
                display: 'success'
            }
        case 'inactive':
            return {
                label: 'Hết phòng',
                display: 'danger'
            }
        default:
            return {
                label: 'Bảo trì',
                display: 'warn'
            }
    }
}

// room amenities
const getRoomAmenity = (amenity) => {
    switch (amenity) {
        case 'active':
            return {
                label: 'Còn phòng',
                display: 'success'
            }
        case 'inactive':
            return {
                label: 'Hết phòng',
                display: 'danger'
            }
        default:
            return {
                label: 'Bảo trì',
                display: 'warn'
            }
    }
}

// room review score
const roomReviewScore = ref(70);

// room type images gallery
const getGalleryImages = (images) => {
    // Nếu có ảnh thật từ DB
    if (images && images.length > 0) {
        return images.map(img => ({
            itemImageSrc: `/storage/${img.path}`,
            thumbnailImageSrc: `/storage/${img.path}`,
            alt: img.alt_text
        }))
    }

    // Nếu không có ảnh → trả về 5 ảnh mặc định
    return Array.from({ length: 5 }, (_, i) => ({
        itemImageSrc: `/img/default-blank-img.jpg`,
        thumbnailImageSrc: `/img/default-blank-img.jpg`,
        alt: `default-blank-${i + 1}`
    }))
}

const responsiveOptions = ref([
    {
        breakpoint: '1300px',
        numVisible: 4
    },
    {
        breakpoint: '575px',
        numVisible: 1
    }
]);
</script>