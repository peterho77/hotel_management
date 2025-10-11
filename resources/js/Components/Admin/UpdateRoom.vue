<template>
    <Form v-if="ready" ref="newRoomForm" v-slot="$form" :resolver :initialValues validateOnUpdate="false"
        :validateOnBlur="true" class="grid gap-y-6" @submit="submit">
        <div class="flex flex-col gap-2">
            <label for="name">Name</label>
            <InputText id="name" name="name" />
            <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                {{ $form.name.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="area">Area</label>
            <InputText id="area" name="area" />
            <Message v-if="$form.area?.invalid" severity="error" size="small" variant="simple">
                {{ $form.area.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="note">Note</label>
            <Textarea name="note" rows="4" cols="30" autoResize />
        </div>
        <div class="flex flex-col gap-2">
            <label for="name">Status</label>
            <Select name="status" :options="statusList" optionValue="name" optionLabel="name"
                placeholder="Select status" class="w-full" />
            <Message v-if="$form.status?.invalid" severity="error" size="small" variant="simple">
                {{ $form.status.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="room-type">Room Type</label>
            <Select name="room_type_id" :options="roomTypeList" optionValue="id" optionLabel="name"
                placeholder="Select room type" class="w-full" />
            <Message v-if="$form.room_type_id?.invalid" severity="error" size="small" variant="simple">
                {{ $form.room_type_id.error.message }}</Message>
        </div>
        <div class="flex flex-col gap-2">
            <label for="branch">Branch</label>
            <Select name="branch_id" :options="branchList" optionValue="id" optionLabel="name"
                placeholder="Select branch" class="w-full" />
            <Message v-if="$form.branch_id?.invalid" severity="error" size="small" variant="simple">
                {{ $form.branch_id.error.message }}</Message>
        </div>
        <div class="flex gap-y-4 gap-x-2 justify-center">
            <Button type="submit" label="Submit" severity="success" raised />
            <Button label="Cancel" severity="danger" raised @click="closeDialog" />
        </div>
    </Form>
    <Toast />
</template>

<script setup>
import { ref, reactive, inject, onMounted, toRaw } from "vue";
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Message from 'primevue/message';

import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";

// zod validate
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';

// select status
const statusList = ref([{ name: 'Đang kinh doanh', value: 'active' }, { name: 'Ngừng kinh doanh', value: 'inactive' }]);

const dialogRef = inject('dialogRef');

const branchList = ref([]);
const roomTypeList = ref([]);

const toast = useToast();

const resolver = zodResolver(
    z.object({
        name: z.string().min(1, { message: 'Tên phòng là bắt buộc.' }),
        area: z.string().min(1, { message: 'Diện tích là bắt buộc.' }),
        status: z
            .string()
            .min(1, { message: 'Trạng thái là bắt buộc.' })
            .max(50, { message: 'Tối đa 50 ký tự' }),
        room_type_id: z.coerce.number().min(1, { message: 'Chọn loại phòng.' }),
        branch_id: z.coerce.number().min(1, { message: 'Chọn chi nhánh.' })
    })
)

const initialValues = reactive({
    name: '',
    area: '',
    status: '',
    room_type_id: null,
    branch_id: null
});

const newRoomForm = ref(null);

// confirm update dialog
const confirm = useConfirm();
const ready = ref(false);

const updateConfirm = (onAccept) => {
    confirm.require({
        message: 'Are you sure you want to update the record?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Save'
        },
        accept: () => {
            return onAccept && onAccept(); //call back
        },
        reject: () => {
            toast.add({
                severity: 'error',
                summary: 'Rejected',
                detail: 'You have rejected',
                life: 3000
            });
        }
    });
};

const submit = (e) => {
    if (!e.valid) return
    const id = dialogRef.value.data?.initialData?.id;
    updateConfirm(() => {
        router.put(
            route('admin.room.update', id),
            e.values,
            {
                preserveScroll: true,
                preserveState: true,
            }
        );
        dialogRef.value.close();
    })
}

// pass data from dynamic dialog primevue
onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        branchList.value = params.branchList || [];
        roomTypeList.value = params.roomTypeList || [];

        const { branch, room_type, ...rest } = toRaw(params.initialData);
        Object.assign(initialValues, {
            ...rest,
            branch_id: branch?.id ?? null,
            room_type_id: room_type?.id ?? null,
        });
    }
    ready.value = true;
})

const closeDialog = () => {
    dialogRef.value.close();
};

</script>