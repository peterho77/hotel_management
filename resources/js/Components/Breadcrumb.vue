<template>
    <Breadcrumb :model="breadcrumbList" :pt="{ root: { class: 'justify-center' }, item: { class: 'text-xl' } }">
        <template #item="{ item, props: slotProps }">
            <a v-if="item === breadcrumbList[breadcrumbList.length - 1]" :href="item.url" class="font-medium text-amber-600 cursor-pointer hover:underline transition duration-300">
                {{ item.label }}
            </a>
            <a v-else :href="item.url" class="cursor-pointer hover:underline transition duration-500">{{ item.label }}</a>
        </template>
    </Breadcrumb>
</template>

<script setup>
const props = defineProps({
    breadcrumbList: {
        type: Array,
        required: true,
        default: []
    }
});
</script>