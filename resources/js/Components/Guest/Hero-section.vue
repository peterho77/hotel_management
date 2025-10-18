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

                            <Form v-model="checkBooking" :resolver="zodResolver(schema)" @submit="onSubmit"
                                class="space-y-4">
                                <!-- Check-in -->
                                <div>
                                    <label class="block mb-1">Check-in</label>
                                    <FormField name="checkIn" v-slot="{ field, error }">
                                        <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon class="w-full" />
                                        <small v-if="error" class="text-red-500">{{ error.message }}</small>
                                    </FormField>
                                </div>

                                <!-- Check-out -->
                                <div>
                                    <label class="block mb-1">Check-out</label>
                                    <FormField name="checkOut" v-slot="{ field, error }">
                                        <DatePicker v-bind="field" dateFormat="dd/mm/yy" showIcon class="w-full" />
                                        <small v-if="error" class="text-red-500">{{ error.message }}</small>
                                    </FormField>
                                </div>

                                <!-- Guests -->
                                <div>
                                    <label class="block mb-1">Guest</label>
                                    <FormField name="guests" v-slot="{ field, error }">
                                        <Select v-bind="field" :options="guestOptions" class="w-full" />
                                        <small v-if="error" class="text-red-500">{{ error.message }}</small>
                                    </FormField>
                                </div>

                                <!-- Rooms -->
                                <div>
                                    <label class="block mb-1">Room</label>
                                    <FormField name="rooms" v-slot="{ field, error }">
                                        <Select v-bind="field" :options="roomOptions" class="w-full" />
                                        <small v-if="error" class="text-red-500">{{ error.message }}</small>
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
import { ref, reactive } from 'vue';
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
let checkBooking = reactive({
    checkIn: '',
    checkOut: '',
    guests: 1,
    rooms: 1,
});

let guestOptions = ['1 Adult', '2 Adults', '3 Adults'];
let roomOptions = ['1 Room', '2 Rooms', '3 Rooms'];

// Validation schema
const schema = z
    .object({
        checkIn: z.date({ required_error: "Vui lòng chọn ngày check-in" }),
        checkOut: z.date({ required_error: "Vui lòng chọn ngày check-out" }),
        guests: z.string().nonempty("Vui lòng chọn số lượng khách"),
        rooms: z.string().nonempty("Vui lòng chọn số lượng phòng"),
    })
    .refine((data) => data.checkOut > data.checkIn, {
        message: "Ngày check-out phải sau check-in",
        path: ["checkOut"],
    })

// Submit handler
const onSubmit = (values) => {
    console.log("Form data:", values)
    alert("Kiểm tra phòng thành công ✅")
}


let imagesList = ref(['/img/hero/hero-1.jpg', '/img/hero/hero-2.jpg', '/img/hero/hero-3.jpg']);

</script>