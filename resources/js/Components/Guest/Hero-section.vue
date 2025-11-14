<template>
    <section class="hero | align-center justify-center padding-block-400 | mb-8">
        <div class="hero__content">
            <div class="container">
                <div class="even-columns" style="--custom-gap:var(--size-900)">
                    <div class="align-center">
                        <div class="hero__text | flow">
                            <h1 class="fs-primary-heading">Sona A luxury hotel</h1>
                            <p data-width="wide">Here are the best hotel booking sites, including recommendations for
                                international travel and for finding low-priced hotel rooms.</p>
                            <button class="button">Discover now</button>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <div class="booking-form | padding-block-600 flow">
                            <h5 class="fs-normal-heading | text-center">Booking your hotel</h5>

                            <Form v-slot="$form" :initialValues :resolver="zodResolver(schema)" :validateOnBlur="true"
                                @submit="onSubmit" class="space-y-4">
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
                                    <label class="block mb-1">Guest</label>
                                    <FormField name="num_of_guests" v-slot="{ field, error }">
                                        <Select v-bind="field" :options="guestOptions" optionLabel="label"
                                            optionValue="value" fluid />
                                        <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                                    </FormField>
                                </div>

                                <!-- Num of rooms -->
                                <div>
                                    <label class="block mb-1">Room</label>
                                    <FormField name="num_of_rooms" v-slot="{ field, error }">
                                        <Select v-bind="field" :options="roomOptions" optionLabel="label"
                                            optionValue="value" fluid />
                                        <small v-if="error" class="text-red-500 text-md">{{ error.message }}</small>
                                    </FormField>
                                </div>

                                <Button type="submit" label="Check availability" class="w-full mt-4" />
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <swiper :modules="[Autoplay, Pagination, EffectFade]" :spaceBetween="30" :centeredSlides="true"
            :slides-per-view="1" :autoplay="{
                delay: 5000,
                disableOnInteraction: false,
            }" :pagination="{ clickable: true }" effect="fade" class="hero__slider">
            <swiper-slide class="slider-item set-bg-img" v-for="(image, i) in imagesList" :key="i"
                :style="$getBgStyle(image)">
            </swiper-slide>
        </swiper>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination, EffectFade } from 'swiper/modules'

import { Form } from '@primevue/forms';
import { FormField } from "@primevue/forms"
import { z } from "zod"
import { zodResolver } from "@primevue/forms/resolvers/zod"
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';


// import style Swiper
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'

// booking check form
// router
import { router } from '@inertiajs/vue3';

let initialValues = {
    checkIn: '',
    checkOut: '',
    num_of_guests: 1,
    num_of_rooms: 1,
};

let maxNumOfGuests = ref(4);
const guestOptions = computed(() =>
    Array.from({ length: maxNumOfGuests.value }, (_, i) => ({
        label: `${i + 1} guest${i > 0 ? 's' : ''}`,
        value: i + 1,
    }))
)
let maxNumOfRooms = ref(4);
const roomOptions = computed(() =>
    Array.from({ length: maxNumOfRooms.value }, (_, i) => ({
        label: `${i + 1} room${i > 0 ? 's' : ''}`,
        value: i + 1,
    }))
)

// Validation schema
const schema = z
    .object({
        checkIn: z.date({ required_error: "Vui lòng chọn ngày check-in" }),
        checkOut: z.date({ required_error: "Vui lòng chọn ngày check-out" }),
        num_of_guests: z
            .number({ invalid_type_error: "Vui lòng chọn số lượng khách" })
            .min(1, "Số lượng khách phải ít nhất là 1"),

        num_of_rooms: z
            .number({ invalid_type_error: "Vui lòng chọn số lượng phòng" })
            .min(1, "Số lượng phòng phải ít nhất là 1"),
    })
    .refine((data) => data.checkOut > data.checkIn, {
        message: "Ngày check out phải sau check in",
        path: ["checkOut"],
    })

// Submit handler
const onSubmit = (e) => {
    if (e.valid) {
        const values = {
            ...e.values,
            checkIn: e.values.checkIn instanceof Date ? e.values.checkIn.toLocaleDateString('vi-VN') .split('T')[0] : e.values.checkIn,
            checkOut: e.values.checkOut instanceof Date ? e.values.checkOut.toLocaleDateString('vi-VN') .split('T')[0] : e.values.checkOut,
        }
        localStorage.setItem('bookingFromHomePage', '1');
        router.get(route('booking'), values);
    }
}

let imagesList = ref(['/img/hero/hero-1.jpg', '/img/hero/hero-2.jpg', '/img/hero/hero-3.jpg']);

</script>