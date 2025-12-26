<template>
  <div class="create-schedule-modal">
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
        <Select v-model="scheduleFormData.shift_id" :options="shiftList" optionLabel="name" optionValue="id"
          placeholder="Chọn ca làm việc" class="field-input" :class="{ 'p-invalid': errors.shift_id }" />
        <small v-if="errors.shift_id" class="p-error">{{ errors.shift_id[0] }}</small>
      </div>

      <!-- Notes -->
      <div class="form-field">
        <label class="field-label">Ghi chú</label>
        <Textarea v-model="scheduleFormData.note" rows="3" placeholder="Nhập ghi chú (nếu có)" class="field-input"
          :class="{ 'p-invalid': errors.notes }" />
        <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
      </div>

      <!-- Actions -->
      <div class="form-actions">
        <Button type="submit" label='Thêm lịch' severity="success" />
        <Button label="Hủy" @click="closeDialog" variant="text" />
      </div>
    </Form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, inject } from 'vue'
import { useToast } from 'primevue/usetoast'

// Import các components
import { Form } from '@primevue/forms';
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

import { router } from '@inertiajs/vue3';
import { formatDateVN } from "@/Composables/formatData";

// 2. Định nghĩa Emits
const emit = defineEmits(['saved', 'close'])

// 3. Khởi tạo State và Hooks
const toast = useToast()
const errors = ref({}) // Lỗi validation

const scheduleFormData = reactive({
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

onMounted(() => {
  const params = dialogRef.value.data;

  if (params) {
    // 1. Lấy danh sách ca làm việc & thông tin nhân viên
    shiftList.value = params.shiftList || [];
    employeeInfo.value = params.employee || null;

    console.log(params.scheduleDate);

    if (params) {
      // 1. Lấy danh sách ca làm việc & thông tin nhân viên
      shiftList.value = params.shiftList || [];
      employeeInfo.value = params.employee || null;

      Object.assign(scheduleFormData, {
        employee_id: params.employee ? params.employee.id : null,
        shift_id: null,
        schedule_date: params.scheduleDate,
        note: ''
      });

      console.log(scheduleFormData);
    }
  }

  ready.value = true;
});

// 4. Computed
const employeeDisplay = computed(() => {
  if (!employeeInfo.value) return ''
  return `${employeeInfo.value.full_name}`
})

const saveSchedule = () => {
  errors.value = {}
  console.log(scheduleFormData);
}

const closeDialog = () => {
  dialogRef.value.close();
};
</script>

<style scoped>
.create-schedule-modal {
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
