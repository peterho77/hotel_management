<template>
    <div class="update-schedule-modal">
        <Form v-if="ready" @submit="saveSchedule">
            <!-- Employee Info (Read-only) -->
            <div class="form-field">
                <label class="field-label">Nhân viên</label>
                <InputText :value="employeeDisplay" disabled class="field-input" />
            </div>

            <!-- Date -->
            <div class="form-field">
                <label class="field-label">Ngày làm việc</label>
                <DatePicker v-model="scheduleFormData.schedule_date" name="schedule_date" dateFormat="dd/mm/yy"
                    class="field-input" :class="{ 'p-invalid': errors.schedule_date }" />
            </div>

            <!-- ca làm việc -->
            <div class="form-field">
                <label class="field-label">Ca làm việc <span class="required">*</span></label>
                <Select v-model="scheduleFormData.shift_id" :options="processedShiftList" optionLabel="name"
                    optionValue="id" placeholder="Chọn ca làm việc" class="field-input"
                    :class="{ 'p-invalid': errors.shift_id }" />
            </div>

            <!-- Notes -->
            <div class="form-field">
                <label class="field-label">Ghi chú</label>
                <Textarea v-model="scheduleFormData.note" rows="3" placeholder="Nhập ghi chú (nếu có)"
                    class="field-input" :class="{ 'p-invalid': errors.notes }" />
                <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <Button type="submit" label='Cập nhật' severity="success" />
                <Button label="Hủy" @click="closeDialog" variant="text" />
            </div>
        </Form>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, inject, watch } from 'vue'
import { useToast } from 'primevue/usetoast'

// Import các components
import { Form } from '@primevue/forms';
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

import { router } from '@inertiajs/vue3';
import { formatDateVN, formatDateOnlyVN } from "@/Composables/formatData";

// 2. Định nghĩa Emits
const emit = defineEmits(['saved', 'close'])

// 3. Khởi tạo State và Hooks
const toast = useToast()
const errors = ref({}) // Lỗi validation

const scheduleFormData = reactive({
    id: null,
    employee_id: null,
    shift_id: null,
    schedule_date: null,
    note: ''
})

// pass data from dynamic dialog primevue
const dialogRef = inject('dialogRef');
const ready = ref(false); // wait for data to mount
const shiftList = ref([]);
const employeeInfo = ref(null);
const existingSchedules = ref([]);
const currentSchedule = ref(null);

onMounted(() => {
    const params = dialogRef.value.data;

    if (params) {
        // 1. Lấy danh sách ca làm việc & thông tin nhân viên
        shiftList.value = params.shiftList || [];
        employeeInfo.value = params.employee || null;
        existingSchedules.value = params.existingSchedules || [];
        currentSchedule.value = params.schedule || null;

        console.log(currentSchedule.value);

        Object.assign(scheduleFormData, {
            id: params.schedule.id,
            employee_id: params.schedule.employee_id,
            shift_id: params.schedule.shift_id,
            schedule_date: params.schedule.schedule_date,
            note: params.schedule.note ?? ''
        });

        console.log(typeof scheduleFormData.shift_id);
    }

    ready.value = true;
});

// 4. Computed
const employeeDisplay = computed(() => {
    if (!employeeInfo.value) return ''
    return `${employeeInfo.value.full_name}`
})

// check if schedule is duplicate
const checkDuplicateSchedule = (shift_id) => {
    if (!scheduleFormData.schedule_date) return false;

    let currentScheduleDate = '';

    if (typeof scheduleFormData.schedule_date === 'string') {
        currentScheduleDate = scheduleFormData.schedule_date;
    } else {
        currentScheduleDate = formatDateOnlyVN(scheduleFormData.schedule_date);
    }

    return existingSchedules.value.some(schedule => {
        const existingShiftId = schedule.shift_id || (schedule.shift ? schedule.shift.id : null);
        const existingScheduleDate = schedule.schedule_date;
        return existingShiftId === shift_id && existingScheduleDate === currentScheduleDate;
    });
};

const processedShiftList = computed(() => {
    return shiftList.value.map(shift => {
        return {
            ...shift,
            // Gọi hàm check cho từng ca trong danh sách
            disabled: checkDuplicateSchedule(shift.id)
        };
    });
});
console.log(processedShiftList);

const saveSchedule = () => {
    console.log(scheduleFormData);
    const payload = { ...scheduleFormData };

    // convert schedule date format to dd/mm/yyyy before sending
    if (payload.schedule_date instanceof Date) {
        payload.schedule_date = formatDateOnlyVN(payload.schedule_date);
    }
    console.log(payload);
    router.post(route('manager.work-schedule.update', scheduleFormData.id), payload, {
        preserveScroll: true,
        onSuccess: () => {
            dialogRef.value.close();
        },
        onError: (errors) => {
            // Nếu có lỗi Validation, dialog vẫn mở để user sửa
            toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Vui lòng kiểm tra lại thông tin.', life: 3000 });
        },
    })
}

const closeDialog = () => {
    dialogRef.value.close();
};
</script>

<style scoped>
.update-schedule-modal {
    padding: 8px;
}

.form-field {
    margin-bottom: 20px;
}

.field-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.field-label .required {
    color: #e74c3c;
}

.field-input {
    width: 100%;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #e0e0e0;
}
</style>
