<template>
    <div class="radio-select | box | flow">
        <Accordion class="w-full">
            <AccordionPanel class="flow">
                <AccordionHeader >
                    <label for="" class="admin-label">{{ label }}</label>
                </AccordionHeader>
                <AccordionContent>
                    <div class="flex flex-col gap-y-3">
                        <div v-for="(item, index) in list" :key="index" class="flex items-center gap-2">
                            <template v-if="item.label">
                                <Checkbox :modelValue="modelValue"
                                    @update:modelValue="value => emit('update:modelValue', value)" :inputId="item.name"
                                    :value="item.label"/>
                                <label :for="item.name">{{ item.label }}</label>
                            </template>
                            <template v-else>
                                <Checkbox :modelValue="modelValue"
                                    @update:modelValue="value => emit('update:modelValue', value)" :inputId="item.name"
                                    :value="item.name"/>
                                <label :for="item.name">{{ item.name }}</label>
                            </template>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionPanel>
        </Accordion>
    </div>
</template>

<style scoped>
:deep(.p-accordion.p-component .p-accordioncontent-content) {
    padding: 0 !important;
}

:deep(.p-accordionheader) {
    padding: 0 !important;
}

:deep(.p-accordion.p-component .p-accordionpanel) {
    border: none !important;
}
</style>

<script setup>
import Checkbox from 'primevue/checkbox';

import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';

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
        required: false
    }
});
const emit = defineEmits(['update:modelValue']);
</script>