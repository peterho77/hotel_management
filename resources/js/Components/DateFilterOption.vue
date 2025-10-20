<template>
    <div class="filter-date | box | flow">
        <Accordion value="0">
            <AccordionPanel value="0">
                <AccordionHeader>
                    <label for="" class="admin-label">{{ label }}</label>
                </AccordionHeader>
                <AccordionContent class="!p-2">
                    <div class="flex flex-col gap-y-2">
                        <div class="flex flex-col flex-wrap gap-y-2">
                            <div class="flex items-center gap-x-2">
                                <RadioButton v-model="filterOption" inputId="option1" name="filter-option"
                                    value="option1" />
                                <Select v-model="selectedOption1" :options optionLabel="name" placeholder="Toàn thời gian"
                                    checkmark :highlightOnSelect="false" class="md:w-56" fluid/>
                            </div>
                        </div>
                        <div class="flex items-center gap-x-2">
                            <RadioButton v-model="filterOption" inputId="option2" name="filter-option" value="option2" />
                            <DatePicker v-model="buttondisplay" placeholder="Lựa chọn khác" dateFormat="dd/mm/yy" showIcon
                                fluid :showOnFocus="false" />
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

:deep(.p-accordion.p-component .p-accordionpanel) {
    border: none !important;
}
</style>

<script setup>
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import RadioButton from 'primevue/radiobutton';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';

const options = [{
    name: 'Toàn thời gian',
    alias: 'All time'
},
{
    name: 'Hôm nay',
    alias: 'Today'
},
{
    name: 'Hôm qua',
    alias: 'Yesterday'
},
{
    name: '7 ngày gần đây',
    alias: 'Last 7 days'
}
]

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