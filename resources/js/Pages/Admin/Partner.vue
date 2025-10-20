<template>
    <main class="partner-section | padding-block-600">
        <div class="container">
            <div class="main-content">
                <section class="main-content__left">
                    <div class="side-bar | flow" style="--flow-spacer:1em">
                        <!-- advanced keyword search -->
                        <div class="advanced-keyword-search | box | flow">
                            <div class="flex justify-between">
                                <label class="admin-label">Nhóm khách hàng</label>
                                <i class="pi pi-plus-circle"></i>
                            </div>
                            <div class="flex gap-x-2">
                                <Select v-model="selectedCustomerGroup" :options="customerGroupList" optionLabel="name"
                                    optionValue="alias" placeholder="Tất cả các nhóm" class="w-full md:w-52" />
                                <Button icon="pi pi-pencil" class="!border-1 !border-gray-300" severity="info"
                                    variant="text" />
                            </div>
                        </div>

                        <!-- double keyword search -->
                        <div class="double-keyword-search | box | flow">
                            <label class="admin-label">Tìm kiếm</label>
                            <FloatLabel variant="on">
                                <InputText id="on_label" v-model="value3" fluid />
                                <label for="on_label">Theo mã, tên, điện thoại</label>
                            </FloatLabel>
                            <FloatLabel variant="on">
                                <InputText id="on_label" v-model="value3" fluid />
                                <label for="on_label">Theo mã hóa đơn</label>
                            </FloatLabel>
                        </div>

                        <!-- filter date -->
                        <DateFilterOption label="Ngày tạo"/>
                        <DateFilterOption label="Sinh nhật"/>
                        <DateFilterOption label="Ngày giao dịch cuối cùng"/>
                    </div>
                </section>
            </div>
        </div>
    </main>
</template>

<style scoped>
:deep(.p-accordion.p-component .p-accordioncontent-content) {
    padding: 0 !important;
}
:deep(.p-accordion.p-component .p-accordionpanel)
{
    border: none !important;
}
</style>

<script setup>
import { ref } from 'vue';

import Select from 'primevue/select';
import Button from 'primevue/button';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';

import DateFilterOption from '../../Components/DateFilterOption.vue';

const selectedCustomerGroup = ref();
const customerGroupList = ref([
    {
        name: 'Khách lẻ',
        alias: 'FIT(Free Independent Traveler)'
    },
    {
        name: 'Khách đoàn',
        alias: 'Group'
    },
    {
        name: 'Khách doanh nghiệp',
        alias: 'Corporate'
    },
    {
        name: 'Khách hội nghị - sự kiện',
        alias: 'MICE: Meeting, Incentive, Conference, Exhibition'
    },
    {
        name: 'Khách trung gian',
        alias: 'Travel Agent / OTA'
    },
    {
        name: 'Khách địa phương',
        alias: 'Local Guests'
    },
])
</script>