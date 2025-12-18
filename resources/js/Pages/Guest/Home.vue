<script setup>
import HeroSection from "../../Components/Guest/Hero-section.vue";
import AboutSection from "../../Components/Guest/About-section.vue";
import ServicesSection from "../../Components/Guest/Services-section.vue";
import RoomSection from "../../Components/Guest/Room-section.vue";

import { usePage } from '@inertiajs/vue3'
const page = usePage()
const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

const props = defineProps({
    roomTypeList: {
        type: Array,
        required: false
    },
});

</script>

<template>
    <!-- Page title -->
    <Head :title="pageTitle" />

    <!-- Hero section -->
    <HeroSection />

    <!-- About section -->
    <AboutSection />

    <!-- Services section -->
    <ServicesSection />

    <!-- Room section -->
    <RoomSection :rooms="roomTypeList"/>

</template>