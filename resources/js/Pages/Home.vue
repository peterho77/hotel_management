<script setup>
import Guest from "../Layouts/Guest.vue";
import HeroSection from "../Components/Guest/Hero-section.vue";
import AboutSection from "../Components/Guest/About-section.vue";
import ServicesSection from "../Components/Guest/Services-section.vue";
import RoomSection from "../Components/Guest/Room-section.vue";

</script>

<template>
    <Guest>
        <!-- Hero section -->
        <HeroSection />

        <!-- About section -->
        <AboutSection />

        <!-- Services section -->
        <ServicesSection />

        <!-- Room section -->
        <RoomSection />
        
    </Guest>
</template>