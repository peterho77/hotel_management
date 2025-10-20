<template>
    <div class="box">
        <MultiSelect :options="list" 
        :modelValue="modelValue" 
        @update:modelValue="value => emit('update:modelValue', value)" 
        display="chip" 
        :optionLabel 
        :optionValue
        filter :placeholder :maxSelectedLabels="1" class="w-full md:w-52">
        </MultiSelect>
    </div>
</template>

<script setup>
import MultiSelect from 'primevue/multiselect';

const props = defineProps({
    list: {
        type: Array,
        required: true
    },
    placeholder: {
        type: String,
        required: true
    },
    optionLabel: {
        type: String,
        default: 'name'
    },
    optionValue: {
        type: String,
        default: 'id'
    },
    modelValue: {
        required: true
    }
});
const emit = defineEmits(['update:modelValue']);
</script>