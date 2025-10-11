<template>
    <div class="admin-layout">
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
import Header from "../Components/Admin/Header.vue";
</script>