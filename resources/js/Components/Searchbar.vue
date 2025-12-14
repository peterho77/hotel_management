<template>
    <div class="search-bar | box | flow" style="--flow-spacer:1em">
        <label class="admin-label" for="room-type-search">{{ label }}</label>
        <IconField iconPosition="left" class="flex items-center gap-x-2">
            <InputIcon>
                <i class="pi pi-search" />
            </InputIcon>
            <InputText :modelValue="modelValue" @update:modelValue="value => emit('update:modelValue', value)"
                size="small" :placeholder class="pl-8" />
        </IconField>
    </div>
</template>

<style scoped>
.search-bar {
    input {
        padding: 0.75em;
        border-radius: var(--size-200);
        border: 1px solid var(--neutral-color-300);
        font-size: var(--fs-400);

        &:focus {
            border-color: var(--primary-color-500);
        }
    }
}
</style>

<script setup>
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

const props = defineProps({
    label: {
        type: String,
        required: false,
        default: 'Tìm kiếm'
    },
    placeholder: {
        type: String,
        required: false,
        default: 'Tìm kiếm hạng phòng'
    },
    modelValue: {
        required: true
    }
});
const emit = defineEmits(['update:modelValue']);
</script>