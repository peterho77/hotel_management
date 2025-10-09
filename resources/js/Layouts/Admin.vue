<template>
    <div class="admin-layout">
        <Header />
        <main>
            <slot />
        </main>
    </div>
    <DynamicDialog/>
</template>

<script setup>
import "../../css/admin.scss";
import Header from "../Components/Admin/Header.vue";
</script>