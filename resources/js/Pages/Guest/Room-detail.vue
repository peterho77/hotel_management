<template>

    <Head :title="pageTitle" />

    <Breadcrumb :breadcrumbList="breadcrumb" />

    <div class="room-detail-section">
        <div class="container">
            <div class="grid md:grid-cols-2">
                <div class="image-gallery">

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { formatLabel } from "@/Composables/formatData";
import Galleria from 'primevue/galleria';

// router
import { router } from '@inertiajs/vue3';

// component
import Breadcrumb from '../../Components/Breadcrumb.vue';

const page = usePage();
const breadcrumb = page.props.breadcrumb;
const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

const props = defineProps({
    roomType: Object,
});
console.log(props.roomType);
</script>