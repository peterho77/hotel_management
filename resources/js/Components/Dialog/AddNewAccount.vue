<template>
    <div class="grid grid-cols-1 md:grid-cols-2">

    </div>
</template>

<script setup>
import { ref, reactive, inject, onMounted, watch } from 'vue';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { usePrimeVue } from 'primevue/config';
import { router } from '@inertiajs/vue3'; 

// component
import { Form, FormField } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Message from 'primevue/message';
import Button from 'primevue/button';
import Badge from 'primevue/badge';
</script>