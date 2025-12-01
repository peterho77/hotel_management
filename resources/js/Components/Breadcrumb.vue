<template>
    <Breadcrumb :model="breadcrumbList" :pt="{ root: { class: 'justify-center' }, item: { class: 'text-xl' } }">
        <template #item="{ item, props: slotProps }">
            <a v-if="item === breadcrumbList[breadcrumbList.length - 1]" :href="item.url" class="font-medium text-orange-300 cursor-pointer">
                {{ item.label }}
            </a>
            <a v-else v-bind="slotProps.action" :href="item.url" class="cursor-pointer">{{ item.label }}</a>
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