<template>
    <div class="signup-form">
        <div class="signup-form__header | py-2">
            <i class="close-icon | pi pi-times |  hover:text-neutral-500" @click="closeDialog()"></i>
        </div>
        <h2 class="signup-form__title | text-center fs-normal-heading mt-4">Create your account</h2>
        <Form class="px-10 py-8 flex flex-col gap-y-7">
            <FloatLabel>
                <InputText id="username" v-model="value" fluid />
                <label for="username">Username</label>
            </FloatLabel>
            <FloatLabel>
                <InputText id="email" v-model="value" fluid />
                <label for="email">Email</label>
            </FloatLabel>
            <FloatLabel>
                <InputMask id="phone" v-model="phone" mask="9999 999 999" fluid />
                <label for="phone">Phone (Optional)</label>
            </FloatLabel>
            <FloatLabel>
                <Password id="password" v-model="value" fluid toggleMask />
                <label for="password">Password</label>
            </FloatLabel>
            <FloatLabel>
                <Password id="confirm-password" v-model="value" fluid toggleMask />
                <label for="confirm-password">Confirm Password</label>
            </FloatLabel>
            <div class="flex items-center gap-2">
                <Checkbox v-model="checked" binary variant="filled" checked />
                <label for="checked">I agree all statements in terms of service</label>
            </div>
            <Button label="Sign up" severity="success"></Button>
            <div class="divider | flex items-center gap-3 text-gray-400">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="text-sm">or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>
            <div class="flex flex-col items-center gap-y-4">
                <span class="text-center">Sign up with</span>
                <div class="flex gap-3 items-center">
                    <i class="pi pi-facebook text-white bg-blue-600 p-3 rounded-full"></i>
                    <i class="pi pi-google text-white bg-red-500 p-3 rounded-full"></i>
                    <i class="pi pi-github text-white bg-gray-800 p-3 rounded-full"></i>
                </div>
            </div>
            <p class="text-center">I'm already a member!
                <Link class="text-blue-400">Sign in</Link>
            </p>
        </Form>
    </div>
</template>

<script setup>
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import FloatLabel from 'primevue/floatlabel';
import IftaLabel from 'primevue/iftalabel';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import { inject } from "vue";

const dialogRef = inject('dialogRef');

const closeDialog = () => {
    dialogRef.value.close();
}
</script>