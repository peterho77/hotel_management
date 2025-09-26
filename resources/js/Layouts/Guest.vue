<template>
    <!-- Page reload -->
    <!-- <transition name="fade" appear>
        <div v-if="showPreLoader" class="pre-loader">
            <div class="loader"></div>
        </div>
    </transition> -->

    <!-- Header -->
    <Header />

    <!-- Main content -->
    <main>
        <slot />
    </main>

    <!-- Footer -->
    <Footer />
</template>

<script setup>
import Header from "../Components/Guest/Header.vue";
import Footer from "../Components/Guest/Footer.vue";

import { ref, onMounted } from 'vue'

const showPreLoader = ref(true)

onMounted(() => {
    // Khi window load xong
    window.addEventListener('load', () => {
        // Ẩn loader + pre-loader với delay 300ms
        setTimeout(() => {
            showPreLoader.value = false
        }, 300)
    }, { once: true })
})
</script>
