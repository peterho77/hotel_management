<template>
    <div class="booking-section">
        <div class="booking-section-header | padding-block-200">
            <div class="container">
                <Form v-slot="$form" :initialValues
                    class="booking-filter-section-form | text-center gap-y-3 bg-amber-100 padding-block-400 px-4 rounded-xl shadow-xl">
                    <div class="flex gap-x-4 items-center">
                        <!-- Check-in -->
                        <div>
                            <label class="block mb-1">Check-in</label>
                            <FormField name="checkIn" v-slot="{ field, error }">
                                <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon fluid />
                                <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                            </FormField>
                        </div>

                        <!-- Check-out -->
                        <div>
                            <label class="block mb-1">Check-out</label>
                            <FormField name="checkOut" v-slot="{ field, error }">
                                <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon fluid />
                                <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                            </FormField>
                        </div>

                        <!-- Num of guests -->
                        <div>
                            <div>
                                <label class="block mb-1">Guest</label>
                            </div>
                            <FormField @click="toggle">
                                <IconField>
                                    <InputIcon class="pi pi-users" />
                                    <InputText :placeholder="numOfGuests" disabled />
                                    <InputIcon class="pi pi-angle-down" />
                                </IconField>
                            </FormField>

                            <!-- guest option popover -->
                            <Popover ref="guestOptionMenu">
                                <div class="py-2 px-3 space-y-3">
                                    <!-- Adult -->
                                    <div class="flex justify-between gap-x-3 items-center">
                                        <div class="flex flex-col">
                                            <span class="text-base">Người lớn</span>
                                            <span class="fs-300 text-gray-500">18 tuổi trở lên</span>
                                        </div>
                                        <InputNumber v-model="numOfAdult" name="num_of_guests" showButtons size="small" :min="1" :pt="{
                                            incrementButton: { class: 'bg-gray-100' },
                                            decrementButton: { class: 'bg-gray-100' },
                                            incrementIcon: { class: 'pi pi-plus' },
                                            decrementIcon: { class: 'pi pi-minus' }
                                        }" />
                                    </div>

                                    <!-- Children -->
                                    <div class="flex justify-between gap-x-3 items-center">
                                        <div class="flex flex-col">
                                            <span class="text-base">Trẻ em</span>
                                            <span class="fs-300 text-gray-500">0-17 tuổi</span>
                                        </div>
                                        <InputNumber v-model="numOfChild" showButtons size="small" :min="0" :max="10"
                                            :pt="{
                                                incrementButton: { class: 'bg-gray-100' },
                                                decrementButton: { class: 'bg-gray-100' },
                                                incrementIcon: { class: 'pi pi-plus' },
                                                decrementIcon: { class: 'pi pi-minus' }
                                            }" />
                                    </div>
                                </div>
                            </Popover>
                        </div>

                        <!-- Num of rooms -->
                        <div>
                            <label class="block mb-1">Room</label>
                            <FormField name="num_of_rooms" v-slot="{ field, error }">
                                <InputNumber v-model="numOfRooms" showButtons :min="1" :pt="{
                                    incrementButton: { class: 'bg-gray-100' },
                                    decrementButton: { class: 'bg-gray-100' },
                                    incrementIcon: { class: 'pi pi-plus' },
                                    decrementIcon: { class: 'pi pi-minus' }
                                }" />
                            </FormField>
                        </div>
                    </div>

                    <Button class="mt-6" severity="info" size="large" label="Tìm phòng" raised />
                </Form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.booking-section-header {
    --booking-section-height: 220px;

    background: var(--accent-color-300);
    height: var(--booking-section-height);
    position: relative;
}

.booking-filter-section-form {
    position: absolute;
    min-width: 200px;
    width: fit-content;
    top: 80px;
    left: 50%;
    transform: translateX(-50%);
}

.p-inputtext:disabled {
    background: white;
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';

import { Form } from '@primevue/forms';
import { FormField } from "@primevue/forms";
import DatePicker from 'primevue/datepicker';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

const props = defineProps({
    checkIn: String,
    checkOut: String,
    num_of_guests: Number,
    num_of_rooms: Number,
});

const initialValues = ref({
    checkIn: '',
    checkOut: '',
    num_of_guests: 1,
    num_of_rooms: 1,
})

onMounted(() => {
    initialValues.value = {
        checkIn: parseVNDate(props.checkIn),
        checkOut: parseVNDate(props.checkOut),
        num_of_guests: props.num_of_guests || 1,
        num_of_rooms: props.num_of_rooms || 1,
    }
})

const parseVNDate = (str) => {
  const [day, month, year] = str.split('/');
  return new Date(`${year}-${month}-${day}`); // chuẩn ISO, không bị ngược
};

const numOfRooms = ref(props.num_of_rooms);

// num of guests = num of adult + num of children
const numOfAdult = ref(props.num_of_guests);
const numOfChild = ref(0);

const numOfGuests = computed(() => {
    const adultLabel = numOfAdult.value > 1 ? "adults" : "adult";
    let label = `${numOfAdult.value} ${adultLabel}`;

    if (numOfChild.value > 0) {
        const childLabel = numOfChild.value > 1 ? "children" : "child";
        label += ` and ${numOfChild.value} ${childLabel}`;
    }

    return label;
});

// pop over guest option menu
const guestOptionMenu = ref();
const toggle = (event) => {
    guestOptionMenu.value.toggle(event);
}
</script>