<template>
    <div class="employee-layout">
        <Header />
        <main class="flex-1">
            <slot />
        </main>
    </div>
    <DynamicDialog/>
    <ConfirmDialog/>
    <Toast/>
</template>

<script setup>
import Header from "../Components/Employee/Header.vue";

// flash message
import { useFlashToast } from "@/Composables/useFlashToast";
useFlashToast();
</script>