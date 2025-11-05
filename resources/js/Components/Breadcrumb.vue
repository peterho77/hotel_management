<template>
    <Breadcrumb :model="breadcrumbList" :pt="{ root: { class: 'justify-center' }, item: { class: 'text-xl' } }">
        <template #item="{ item, props: slotProps }">
            <a v-if="item === breadcrumbList[breadcrumbList.length - 1]" class="font-medium text-orange-300">
                {{ item.label }}
            </a>
            <a v-else v-bind="slotProps.action">{{ item.label }}</a>
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