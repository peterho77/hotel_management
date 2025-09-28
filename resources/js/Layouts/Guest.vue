<template>

    <!-- Header -->
    <Header/>

    <!-- Main content -->
    <main>
        <slot />
    </main>

    <!-- Footer -->
    <Footer/>
</template>

<script setup>
import Header from "../Components/Guest/Header.vue";
import Footer from "../Components/Guest/Footer.vue";
</script>
