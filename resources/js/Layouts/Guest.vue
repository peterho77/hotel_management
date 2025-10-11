<template>
    <div class="guest-layout">
        <!-- Header -->
        <Header/>
        <!-- Main content -->
        <main>
            <slot />
        </main>
        <!-- Footer -->
        <Footer/>
    </div>
    <DynamicDialog/>
    <ConfirmDialog/>
    <Toast/>
</template>

<script setup>
import "../../css/guest.scss";
import Header from "../Components/Guest/Header.vue";
import Footer from "../Components/Guest/Footer.vue";
</script>
