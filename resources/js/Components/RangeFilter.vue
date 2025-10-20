<template>
    <div class="range-filter | box | flow">
        <label for="" class="admin-label">{{ label }}</label>
        <div class="flex items-center justify-between">
            <label class="">Từ</label>
            <InputNumber v-model="fromValue" mode="currency" currency="VND" locale="vi-VN" placeholder="Giá trị" />
        </div>
        <div class="flex items-center justify-between">
            <label class="">Tới</label>
            <InputNumber v-model="toValue" mode="currency" currency="VND" locale="vi-VN" placeholder="Giá trị" />
        </div>
    </div>
</template>

<script setup>
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    // modelValue: {
    //     required: true
    // }
});
// const emit = defineEmits(['update:modelValue']);
</script>