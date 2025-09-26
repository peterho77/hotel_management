<template>
    <Guest>
        <h1>About Page {{ user }}</h1>
    </Guest>
</template>

<script setup>
import Guest from "../Layouts/Guest.vue"
defineProps({
    user: String,
});
</script>
