<template>
  <div class="schedule-page">
    <!-- Header -->
    <div class="schedule-header">
      <div class="header-left">
        <h2 class="page-title">Lịch làm việc</h2>
      </div>

      <div class="header-center">
        <!-- Search -->
        <div class="search-wrapper">
          <div class="input-group">
            <span class="input-group-text">
              <i class="pi pi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Theo mã, tên nhân viên, điện thoại"
              v-model="searchQuery" @input="debounceSearch">
          </div>
        </div>

        <!-- Week Navigation -->
        <div class="week-navigation">
          <Button icon="pi pi-chevron-left" @click="previousWeek" class="p-button-text p-button-rounded"
            :disabled="loading" />
          <div class="week-display">
            <span class="week-text">{{ weekDisplayText }}</span>
            <Button label="Tuần này" @click="goToCurrentWeek" class="p-button-text p-button-sm" :disabled="loading" />
          </div>
          <Button icon="pi pi-chevron-right" @click="nextWeek" class="p-button-text p-button-rounded"
            :disabled="loading" />
        </div>
      </div>
    </div>

    <!-- Hiển thị danh sách lịch làm việc -->
    <div class="schedule-grid-container" v-if="!loading && scheduleData.length > 0">
      <div class="schedule-table">
        <!-- Header Row -->
        <div class="schedule-row schedule-header-row">
          <div class="schedule-cell employee-col">Nhân viên</div>
          <div v-for="day in weekDays" :key="day.date" class="schedule-cell day-col" :class="{ 'today': day.isToday }">
            <div class="day-header">
              <div class="day-name">{{ day.dayName }}</div>
              <div class="day-number" :class="{ 'today-circle': day.isToday }">{{ day.dayNumber }}</div>
            </div>
          </div>
          <div class="schedule-cell salary-col">Lương dự kiến</div>
        </div>

        <!-- Employee Rows -->
        <div v-for="employee in filteredScheduleData" :key="employee.id" class="schedule-row">
          <!-- Employee Info -->
          <div class="schedule-cell employee-col">
            <div class="employee-info">
              <div class="employee-name">{{ employee.full_name }}</div>
              <div class="employee-code">{{ employee.employee_code }}</div>
            </div>
          </div>

          <!-- Day Cells -->
          <div v-for="day in weekDays" :key="day.date" class="schedule-cell day-col" :class="{ 'today': day.isToday }">
            <div class="day-content">
              <!-- Shift Blocks -->
              <div class="shift-blocks">
                <div v-for="schedule in getSchedulesForDay(item, day.date)" :key="schedule.id" class="shift-block"
                  :class="getShiftColorClass(schedule.shift)" @click="editSchedule(schedule, employee, day.date)">
                  {{ schedule.shift?.name || 'N/A' }}
                </div>
              </div>

              <!-- Add Schedule Button - Hiển thị khi chưa đạt giới hạn -->
              <button v-if="getSchedulesForDay(employee, day.date).length < 3" class="add-schedule-btn"
                @click="openAddScheduleModal(employee, day.date)" title="Thêm lịch">
                + Thêm lịch
              </button>
            </div>
          </div>

          <!-- Estimated Salary -->
          <div class="schedule-cell salary-col">
            <div class="salary-info">
              <div class="salary-amount">{{ formatCurrency(item.estimated_salary) }}</div>
              <div class="shift-count">{{ item.shift_count }} ca</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && scheduleData.length === 0" class="empty-state">
      <i class="pi pi-calendar-times" style="font-size: 3rem; color: #ccc;"></i>
      <p>Chưa có lịch làm việc</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <ProgressSpinner />
      <p>Đang tải...</p>
    </div>

    <!-- Add/Edit Schedule Modal -->
    <Dialog v-model:visible="showScheduleModal" :header="scheduleModalTitle" :modal="true" :style="{ width: '500px' }"
      @hide="closeScheduleModal">
      <CreateScheduleModal v-if="showScheduleModal" :employee="selectedEmployee" :schedule-date="selectedDate"
        :schedule="editingSchedule" @saved="handleScheduleSaved" @cancel="closeScheduleModal" />
    </Dialog>
  </div>
</template>

<style scoped>
/* Employee Schedules CSS */

.schedule-page {
  padding: 20px;
  background: #f5f5f5;
  min-height: 100vh;
}

.schedule-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 16px;
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}

.header-left {
  flex: 0 0 auto;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0;
  color: #2c3e50;
}

.header-center {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

.search-wrapper {
  flex: 1;
  max-width: 465px;
  min-width: 280px;
}

.search-wrapper .input-group {
  position: relative;
}

.search-wrapper .input-group-text {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  color: #6c757d;
  z-index: 2;
  pointer-events: none;
}

.search-wrapper .form-control {
  padding-left: 40px !important;
  padding-right: 16px !important;
  border: 2px solid #91C4C3 !important;
  border-radius: 8px !important;
  height: 42px !important;
  font-size: 14px !important;
  background: #fff !important;
  transition: all 0.2s ease !important;
  width: 100%;
}

.search-wrapper .form-control:focus {
  border-color: #007bff !important;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
  outline: none !important;
}

.week-navigation {
  display: flex;
  align-items: center;
  gap: 8px;
}

.week-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  min-width: 150px;
}

.week-text {
  font-weight: 600;
  color: #333;
}

.header-right {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.schedule-grid-container {
  background: white;
  border-radius: 8px;
  overflow-x: auto;
  overflow-y: visible;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  -webkit-overflow-scrolling: touch;
}

.schedule-table {
  display: flex;
  flex-direction: column;
  min-width: 100%;
}

.schedule-row {
  display: grid;
  grid-template-columns: 200px repeat(7, 1fr) 180px;
  border-bottom: 1px solid #e0e0e0;
  min-width: 1200px;
}

.schedule-row:last-child {
  border-bottom: none;
}

.schedule-header-row {
  background: #f8f9fa;
  font-weight: 600;
  position: sticky;
  top: 0;
  z-index: 10;
}

.schedule-cell {
  padding: 12px;
  border-right: 1px solid #e0e0e0;
  min-height: 60px;
  display: flex;
  align-items: center;
}

.schedule-cell:last-child {
  border-right: none;
}

.employee-col {
  position: sticky;
  left: 0;
  background: white;
  z-index: 5;
  font-weight: 600;
  box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
}

.schedule-header-row .employee-col {
  background: #f8f9fa;
  box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
}

.day-col {
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 80px;
}

.day-col.today {
  background: #e3f2fd;
}

.day-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.day-name {
  font-size: 12px;
  color: #666;
  text-transform: uppercase;
}

.day-number {
  font-size: 18px;
  font-weight: 600;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.today-circle {
  background: #2196f3;
  color: white;
}

.employee-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.employee-name {
  font-weight: 600;
  color: #333;
}

.employee-code {
  font-size: 12px;
  color: #666;
}

.day-content {
  width: 100%;
  min-height: 100px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.shift-blocks {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 4px;
}

.shift-block {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
  transition: opacity 0.2s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.shift-block:hover {
  opacity: 0.8;
}

.shift-morning {
  background: #fff3e0;
  color: #e65100;
}

.shift-afternoon {
  background: #e3f2fd;
  color: #1565c0;
}

.shift-evening {
  background: #e8f5e9;
  color: #2e7d32;
}

.shift-default {
  background: #f5f5f5;
  color: #666;
}

.add-schedule-btn {
  padding: 4px 8px;
  border: 1px dashed #ccc;
  background: transparent;
  color: #666;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
  width: 100%;
  transition: all 0.2s;
  white-space: nowrap;
}

.add-schedule-btn:hover {
  border-color: #2196f3;
  color: #2196f3;
}

.salary-col {
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
}

.salary-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.salary-amount {
  font-weight: 600;
  color: #333;
}

.shift-count {
  font-size: 12px;
  color: #666;
}

.empty-state,
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #999;
}

.loading-state {
  gap: 16px;
}

/* Responsive - Tablet */
@media (max-width: 1200px) {
  .schedule-page {
    padding: 16px;
  }

  .schedule-row {
    grid-template-columns: 180px repeat(7, 120px) 160px;
    min-width: 1100px;
  }

  .schedule-cell {
    padding: 10px 8px;
  }

  .page-title {
    font-size: 20px;
  }

  .search-wrapper {
    min-width: 200px;
  }
}

/* Responsive - Mobile Large */
@media (max-width: 992px) {
  .schedule-page {
    padding: 12px;
  }

  .schedule-header {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .header-left {
    width: 100%;
  }

  .header-center {
    flex-direction: column;
    width: 100%;
    gap: 12px;
  }

  .search-wrapper {
    width: 100%;
    min-width: auto;
  }

  .week-navigation {
    width: 100%;
    justify-content: center;
  }

  .week-display {
    min-width: auto;
  }

  .schedule-row {
    grid-template-columns: 160px repeat(7, 110px) 150px;
    min-width: 1000px;
  }

  .schedule-cell {
    padding: 8px 6px;
    font-size: 13px;
  }

  .day-content {
    min-height: 90px;
  }
}

/* Responsive - Mobile Medium */
@media (max-width: 768px) {
  .schedule-page {
    padding: 8px;
  }

  .page-title {
    font-size: 18px;
  }

  .schedule-row {
    grid-template-columns: 140px repeat(7, 95px) 130px;
    min-width: 900px;
  }

  .schedule-cell {
    padding: 6px 4px;
    font-size: 12px;
    min-height: 50px;
  }

  .day-col {
    min-height: 70px;
  }

  .day-content {
    min-height: 80px;
  }
}

/* Responsive - Mobile Small */
@media (max-width: 576px) {
  .schedule-page {
    padding: 8px 4px;
  }

  .page-title {
    font-size: 16px;
  }

  .schedule-header {
    margin-bottom: 12px;
  }

  .schedule-row {
    grid-template-columns: 120px repeat(7, 85px) 110px;
    min-width: 800px;
  }

  .schedule-cell {
    padding: 4px 2px;
    font-size: 11px;
    min-height: 45px;
  }
}

/* Responsive - Very Small Mobile */
@media (max-width: 400px) {
  .schedule-row {
    grid-template-columns: 100px repeat(7, 75px) 90px;
    min-width: 700px;
  }

  .schedule-cell {
    padding: 3px 1px;
    font-size: 10px;
  }
}

/* Employee Modals CSS */

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 20px;
}

.salary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 20px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.address-field {
  grid-column: 1 / -1;
}

.field-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.required {
  color: #ef4444;
}

.field-input {
  width: 100%;
  border-radius: 6px;
  font-size: 14px;
}

.field-textarea {
  width: 100%;
  border-radius: 6px;
  font-size: 14px;
}

.input-group {
  display: flex;
  gap: 8px;
}

.input-group .field-input {
  flex: 1;
}

.generate-btn {
  flex-shrink: 0;
}

/* Dynamic sections */
.dynamic-section {
  margin-bottom: 24px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f9fafb;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.dynamic-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dynamic-item {
  padding: 12px;
  background: white;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.dynamic-item-fields {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Error */
.p-error {
  color: #ef4444;
  font-size: 12px;
}

.p-invalid {
  border-color: #ef4444 !important;
}

.text-muted {
  color: #6b7280;
  font-size: 12px;
  margin-top: 4px;
}

/* Footer */
.flex {
  display: flex;
}

.justify-end {
  justify-content: flex-end;
}

.gap-2 {
  gap: 8px;
}

/* Create Schedule Modal */
.create-schedule-modal {
  padding: 8px;
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

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast' // Assuming you use PrimeVue's toast
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import ProgressSpinner from 'primevue/progressspinner'
import CreateScheduleModal from '../../Components/Dialog/CreateSchedule.vue'

// --- Props & Emits ---
const props = defineProps({
  allEmployees: Array,
  scheduleData: Array,
  filters: Object
})

console.log(props.scheduleData);

// --- State (Replaces data) ---
const loading = ref(false)
const searchQuery = ref('')
const weekStart = ref(null)
const scheduleData = ref([])
const allEmployees = ref(props.allEmployees)
const showScheduleModal = ref(false)
const selectedEmployee = ref(null)
const selectedDate = ref(null)
const editingSchedule = ref(null)
let searchTimeout = null

const toast = useToast()

// --- Computed ---
const weekDays = computed(() => {
  if (!weekStart.value) return []

  const days = []
  const dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy']

  for (let i = 0; i < 7; i++) {
    const date = new Date(weekStart.value)
    date.setDate(date.getDate() + i)

    const dayOfWeek = date.getDay()
    // Logic for naming (matching your original logic)
    const dayName = i === 0 ? 'Thứ hai' : i === 6 ? 'Chủ nhật' : dayNames[dayOfWeek]

    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const checkDate = new Date(date)
    checkDate.setHours(0, 0, 0, 0)

    days.push({
      date: date.toISOString().split('T')[0],
      dayName: dayName,
      dayNumber: date.getDate(),
      isToday: checkDate.getTime() === today.getTime()
    })
  }
  return days
})

const weekDisplayText = computed(() => {
  if (!weekStart.value) return ''

  const start = new Date(weekStart.value)
  const monthNames = ['Th. 1', 'Th. 2', 'Th. 3', 'Th. 4', 'Th. 5', 'Th. 6',
    'Th. 7', 'Th. 8', 'Th. 9', 'Th. 10', 'Th. 11', 'Th. 12']

  const weekNumber = getWeekNumber(start)
  const month = monthNames[start.getMonth()]
  const year = start.getFullYear()

  return `Tuần ${weekNumber} - ${month} ${year}`
})

const filteredScheduleData = computed(() => {
  // Kiểm tra null safety cho props
  const sourceData = props.scheduleData || []

  if (!searchQuery.value || !searchQuery.value.trim()) {
    return sourceData
  }

  const term = searchQuery.value.toLowerCase().trim()
  
  return sourceData.filter(employee => {
    // SỬA: employee chính là item, không cần .employee nữa
    const name = (employee.full_name || '').toLowerCase()
    const code = (employee.employee_code || '').toLowerCase()
    const phone = (employee.phone_number || '').toLowerCase()
    const email = (employee.email || '').toLowerCase() // User relation hoặc column email

    return name.includes(term) || code.includes(term) || phone.includes(term) || email.includes(term)
  })
})

const scheduleModalTitle = computed(() => {
  return editingSchedule.value ? 'Chỉnh sửa lịch làm việc' : 'Thêm lịch làm việc'
})

// --- Methods ---
const initializeWeek = () => {
  const today = new Date()
  const dayOfWeek = today.getDay()
  const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek // Monday
  const monday = new Date(today)
  monday.setDate(today.getDate() + diff)
  monday.setHours(0, 0, 0, 0)

  weekStart.value = monday.toISOString().split('T')[0]
}

const getWeekNumber = (date) => {
  const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
  const dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))
  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
}

// Chuyển đổi tuần (Thay thế cho changeWeek cũ)
const changeWeek = (newWeekStart) => {
  router.get('/manager/work-schedule',
    { week_start: newWeekStart },
    {
      preserveState: true, // Giữ lại trạng thái hiện tại của trang
      preserveScroll: true, // Không cuộn lên đầu trang
      only: ['scheduleData', 'filters'] // Chỉ tải lại đúng dữ liệu cần thiết
    }
  )
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    // Search is handled automatically by computed property
  }, 200)
}

const previousWeek = () => {
  const date = new Date(weekStart.value)
  date.setDate(date.getDate() - 7)
  weekStart.value = date.toISOString().split('T')[0]
  changeWeek()
}

const nextWeek = () => {
  const date = new Date(weekStart.value)
  date.setDate(date.getDate() + 7)
  weekStart.value = date.toISOString().split('T')[0]
  changeWeek()
}

const goToCurrentWeek = () => {
  initializeWeek()
  changeWeek()
}

const getSchedulesForDay = (employee, dateString) => {
  // Kiểm tra an toàn
  if (!employee || !employee.schedules || !Array.isArray(employee.schedules)) {
    return []
  }
  // Lọc mảng schedules để lấy ra các ca trong ngày dateString
  return employee.schedules.filter(s => s.schedule_date === dateString)
}

const getShiftColorClass = (shift) => {
  if (!shift) return 'shift-default'
  const name = shift.name.toLowerCase()
  if (name.includes('sáng') || name.includes('morning')) return 'shift-morning'
  if (name.includes('chiều') || name.includes('afternoon')) return 'shift-afternoon'
  if (name.includes('tối') || name.includes('evening') || name.includes('night')) return 'shift-evening'
  return 'shift-default'
}

const openAddScheduleModal = (employee, date) => {
  selectedEmployee.value = employee
  selectedDate.value = date
  editingSchedule.value = null
  showScheduleModal.value = true
}

const editSchedule = (schedule, employee, date) => {
  selectedEmployee.value = employee
  selectedDate.value = date
  editingSchedule.value = schedule
  showScheduleModal.value = true
}

const closeScheduleModal = () => {
  showScheduleModal.value = false
  selectedEmployee.value = null
  selectedDate.value = null
  editingSchedule.value = null
}

const handleScheduleSaved = () => {
  closeScheduleModal()
  changeWeek()
}

const formatCurrency = (amount) => {
  if (!amount) return '0 ₫'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

// --- Lifecycle ---
onMounted(() => {
  initializeWeek()
})
</script>
