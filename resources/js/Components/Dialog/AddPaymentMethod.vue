<template>
    <div class="grid gap-y-6">
        <div class="grid gap-y-4">
            <template v-for="item in paymentMethodList">
                <Button variant="text" severity="success" raised :class="{
                    'w-full flex justify-between items-center': true,
                    'active-button': activePayment === item
                }" @click="setActivePayment(item)">
                    <div class="flex items-center justify-items-start gap-x-2 w-full">
                        <img :src="item.imgPath" alt="" class="h-15 w-15 object-contain">
                        <span class="text-gray-700 font-medium">{{ item.label }}</span>
                    </div>
                    <i v-if="activePayment === item" class="pi pi-check text-green-500"
                        style="font-size:1.5rem"></i>
                </Button>
            </template>
        </div>
        <Button label="Xác nhận" severity="danger" @click="setPaymentMethod()" fluid />
    </div>
</template>

<style scoped>
.active-button {
    border: 2px solid rgb(86, 216, 86) !important;
    box-shadow: none;
    border-radius: 8px;
}
</style>

<script setup>
import { ref } from 'vue';
import Button from 'primevue/button';

const emit = defineEmits(['confirmPaymentMethod'])

const paymentMethodList = ref([
    {
        label: 'VNPAY',
        value: 'VNPAY',
        imgPath: '/img/payment/VNPAY.png'
    },
    {
        label: 'Chuyển khoản qua ngân hàng',
        value: 'bank',
        imgPath: '/img/payment/bank.png'
    }
])
const activePayment = ref(null);

const setActivePayment = (val) => {
    activePayment.value = val;
};

const setPaymentMethod = () => {
    if (activePayment.value) {
        emit('confirmPaymentMethod', activePayment.value);
    }
}

</script>