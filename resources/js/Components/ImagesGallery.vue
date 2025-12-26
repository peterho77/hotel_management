<template>
    <Galleria :value="getImagesList(list)" :responsiveOptions :numVisible="4" containerStyle="max-width: 600px"
        :circular="true">
        <template #item="slotProps">
            <div class="w-full aspect-4/3 overflow-hidden rounded-lg">
                <img :src="slotProps.item.src" :alt="slotProps.item.alt" class="w-full h-full object-cover" />
            </div>
        </template>

        <template #thumbnail="slotProps">
            <div class="w-20 h-20 overflow-hidden">
                <img :src="slotProps.item.src" :alt="slotProps.item.alt"
                    class="w-full h-full object-cover rounded-md border-[0.5px]" />
            </div>
        </template>
    </Galleria>
</template>

<script setup>
import { ref } from 'vue';
import Galleria from 'primevue/galleria';

const props = defineProps({
    list: {
        type: Array,
        required: false
    },
});

const getImagesList = (images) => {
    // Nếu có ảnh thật từ DB
    if (images && images.length > 0) {
        return images.map(img => ({
            src: img.path.startsWith('/img/') ? img.path : `/storage/${img.path}`,
            thumbnailImageSrc: `/storage/${img.path}`,
            alt: img.alt_text
        }))
    }

    // Nếu không có ảnh → trả về 5 ảnh mặc định
    return Array.from({ length: 5 }, (_, i) => ({
        src: `/img/default-blank-img.jpg`,
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
        numVisible: 2
    }
]);
</script>