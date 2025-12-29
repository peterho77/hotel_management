<template>
    <div class="employee-layout">
        <Header />
        <main>
            <slot />
        </main>
    </div>
    <DynamicDialog/>
    <ConfirmDialog/>
    <Toast/>
</template>

<script setup>
import "../../css/admin.scss";
import Header from "../Components/Employee/Header.vue";

// flash message
import { useFlashToast } from "@/Composables/useFlashToast";
useFlashToast();
</script>