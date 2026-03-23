<template>
    <div class="qr-code-section">
        <div class="qr-code-img | set-bg-img" :style="$getBgStyle(QRCodeImg)">
        </div>
        <div class="qr-account-info | text-center">
            <p>{{ AccountInfo.name }}</p>
            <span>{{ AccountInfo.bank }}-{{ AccountInfo.number }}</span>
        </div>
    </div>
</template>

<style>
.qr-code-section {
    height: auto;
}

.qr-code-img {
    background-size: contain;
    width: 100%;
    aspect-ratio: 1/1;
}
</style>

<script setup>
import { inject, ref, onMounted } from 'vue';

const dialogRef = inject('dialogRef');
const QRCodeImg = ref('');
const AccountInfo = ref({});

onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        QRCodeImg.value = params.QRCodeImg || '';
        AccountInfo.value = params.AccountInfo || '';
    }
})
</script>