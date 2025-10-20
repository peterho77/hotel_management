<template>
    <div class="box | flow flex flex-col">
        <label class="admin-label" for="room-status">{{ label }}</label>
        <div v-for="(item, index) in list" :key="index" class="flex items-center gap-2">
            <RadioButton 
            :modelValue="modelValue" 
            @update:modelValue="value => emit('update:modelValue', value)"
            :inputId="item.name" 
            :value="item.label" />
            <label :for="item.name">{{ item.label }}</label>
        </div>
    </div>
</template>

<script setup>
import RadioButton from 'primevue/radiobutton';

const props = defineProps({
    list: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    modelValue: {
        required: true
    }
});
const emit = defineEmits(['update:modelValue']);
</script>