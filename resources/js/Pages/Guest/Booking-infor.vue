<template>
    <div class="booking-infor-section | padding-block-200">
        <div class="container">
            <div class="p-2">
                <Stepper v-model:value="activeStep" class="basis-160">
                    <StepList>
                        <Step v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="1">
                            <div class="flex flex-row flex-auto gap-2" v-bind="a11yAttrs.root">
                                <button class="bg-transparent border-0 inline-flex gap-2 items-center"
                                    @click="activateCallback" v-bind="a11yAttrs.header">
                                    <span :class="[
                                        'rounded-full border w-7 h-7 inline-flex items-center justify-center transition-colors duration-200',
                                        {
                                            'bg-sky-500 text-white border-sky-500': value <= activeStep,
                                            'text-gray-500 border-gray-300 dark:border-gray-600': value > activeStep,
                                        },
                                    ]">
                                        <i v-if="value < activeStep" class="pi pi-check text-lg"></i>
                                        <span v-else class="font-semibold">{{ value }}</span>
                                    </span>

                                    <span :class="[
                                        'text-sm text-nowrap font-medium',
                                        { 'text-sky-600': value === activeStep, 'text-gray-500': value !== activeStep },
                                    ]">
                                        Room selection
                                    </span>
                                </button>
                                <Divider />
                            </div>
                        </Step>
                        <Step v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="2">
                            <div class="flex flex-row flex-auto gap-2" v-bind="a11yAttrs.root">
                                <button class="bg-transparent border-0 inline-flex gap-2 items-center"
                                    @click="activateCallback" v-bind="a11yAttrs.header">
                                    <span :class="[
                                        'rounded-full border w-7 h-7 inline-flex items-center justify-center transition-colors duration-200',
                                        {
                                            'bg-sky-500 text-white border-sky-500': value <= activeStep,
                                            'text-gray-500 border-gray-300 dark:border-gray-600': value > activeStep,
                                        },
                                    ]">
                                        <i v-if="value < activeStep" class="pi pi-check text-lg"></i>
                                        <span v-else class="font-semibold">{{ value }}</span>
                                    </span>

                                    <span :class="[
                                        'text-sm text-nowrap font-medium',
                                        { 'text-sky-600': value === activeStep, 'text-gray-500': value !== activeStep },
                                    ]">
                                        Detail information
                                    </span>
                                </button>
                                <Divider />
                            </div>
                        </Step>
                        <Step v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="3">
                            <div class="gap-2" v-bind="a11yAttrs.root">
                                <button class="bg-transparent border-0 inline-flex gap-2 items-center"
                                    @click="activateCallback" v-bind="a11yAttrs.header">
                                    <span :class="[
                                        'rounded-full border w-7 h-7 inline-flex items-center justify-center transition-colors duration-200',
                                        {
                                            'bg-sky-500 text-white border-sky-500': value <= activeStep,
                                            'text-gray-500 border-gray-300 dark:border-gray-600': value > activeStep,
                                        },
                                    ]">
                                        <i v-if="value < activeStep" class="pi pi-check text-lg"></i>
                                        <span v-else class="font-semibold">{{ value }}</span>
                                    </span>

                                    <span :class="[
                                        'text-sm text-nowrap font-medium',
                                        { 'text-sky-600': value === activeStep, 'text-gray-500': value !== activeStep },
                                    ]">
                                        Complete booking
                                    </span>
                                </button>
                            </div>
                        </Step>
                    </StepList>
                    <StepPanels>
                        <!-- <StepPanel v-slot="{ activateCallback }" :value="1">
                            <div class="flex pt-6 justify-end">
                                <Button label="Next" icon="pi pi-arrow-right" iconPos="right"
                                    @click="activateCallback(2)" />
                            </div>
                        </StepPanel> -->
                        <StepPanel v-slot="{ activateCallback }" :value="2">
                            <div class="grid grid-cols-[auto_1fr] gap-6">
                                <div class="sidebar | flow" style="--flow-spacer:1em">
                                    <div class="box | p-3 flow" style="--flow-spacer:1em">
                                        <h3 class="text-lg text-center font-semibold">Chi tiết đặt phòng của bạn</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2">
                                            <div
                                                class="check-in | flex flex-col items-center gap-1 border-r border-gray-200">
                                                <label for="">Nhận phòng</label>
                                                <!-- checkin -->
                                                <span>{{ roomBookingDetail.date_range[0] }}</span>
                                            </div>
                                            <div class="check-out | flex flex-col items-center gap-1">
                                                <label for="">Trả phòng</label>
                                                <!-- checkout -->
                                                <span>{{ roomBookingDetail.date_range[1] }}</span>
                                            </div>
                                        </div>
                                        <div class="num-of-nights | grid">
                                            <label for="">Tổng thời gian lưu trú:</label>
                                            <!-- num of nights -->
                                            <span class="font-semibold">{{ roomBookingDetail.num_nights }} đêm</span>
                                        </div>
                                        <Divider />
                                        <Accordion value="0">
                                            <AccordionPanel value="0">
                                                <AccordionHeader>
                                                    <div class="grid p-2">
                                                        <label class="font-light">Bạn đã chọn</label>
                                                        <!-- summary text -->
                                                        <span
                                                            class="font-semibold">{{ getSummaryText(roomBookingDetail.num_rooms, roomBookingDetail.num_adults, roomBookingDetail.num_children) }}</span>
                                                    </div>
                                                </AccordionHeader>
                                                <AccordionContent>
                                                    <!-- num of rooms -->
                                                    <div class="grid p-2">
                                                        <template v-for="room in summaryNumOfRoomsList">
                                                            <span>{{ room.count }} x {{ room.name }}</span>
                                                        </template>
                                                    </div>
                                                </AccordionContent>
                                            </AccordionPanel>
                                            <Button class="mt-2" label="Đổi các lựa chọn của bạn" severity="info"
                                                size="small" variant="text" @click="onChangeBookingOptions" />
                                        </Accordion>
                                    </div>
                                    <div class="box | flow p-0!">
                                        <div class="detail-price | p-3 flow" style="--flow-spacer:1rem">
                                            <h3 class="text-lg text-center font-semibold">Tóm tắt giá</h3>
                                            <div class="price | flex justify-between items-center gap-1">
                                                <label for="">Giá gốc</label>
                                                <!-- base price -->
                                                <span>VND
                                                    {{ roomBookingDetail.selected_rooms[0].total_base_price }}</span>
                                            </div>
                                            <div class="discount | flex justify-between items-center gap-1">
                                                <label for="">Ưu đãi</label>
                                                <!-- discount -->
                                                <span>- VND
                                                    {{ roomBookingDetail.selected_rooms[0].total_base_price - roomBookingDetail.selected_rooms[0].total_price }}</span>
                                            </div>
                                        </div>
                                        <div
                                            class="summary-price | overflow-hidden flex items-center justify-between p-4">
                                            <h3 class="text-2xl font-semibold">Giá</h3>
                                            <div class="flex flex-col items-end">
                                                <span class="text-red-500 line-through">VND
                                                    {{ roomBookingDetail.selected_rooms[0].total_base_price }}</span>
                                                <h3 class="text-xl font-semibold">VND
                                                    {{ roomBookingDetail.selected_rooms[0].total_price }}</h3>
                                                <span>Bao gồm thuế và phí</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="main-content">
                                    <Form v-slot="$form" class="grid gap-y-6">
                                        <div class="booker-infor | box px-6 py-4 flow" style="--flow-spacer:.75em">
                                            <h3 class="font-semibold text-2xl">Nhập thông tin chi tiết của bạn</h3>
                                            <div class="grid gap-y-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                                    <FormField class="flex flex-col gap-1">
                                                        <label for="">Họ</label>
                                                        <InputText v-model="bookerInforForm.surname" type="text" />
                                                        <Message v-if="errors.surname" severity="error" size="small"
                                                            variant="simple">
                                                            {{ errors.surname }}</Message>
                                                    </FormField>
                                                    <FormField class="flex flex-col gap-1">
                                                        <label for="">Tên</label>
                                                        <InputText v-model="bookerInforForm.firstname" type="text" />
                                                        <Message v-if="errors.firstname" severity="error" size="small"
                                                            variant="simple">
                                                            {{ errors.firstname }}</Message>
                                                    </FormField>
                                                    <FormField class="flex flex-col gap-1">
                                                        <label for="">Địa chỉ email</label>
                                                        <InputText v-model="bookerInforForm.email" type="text" />
                                                        <Message v-if="errors.email" severity="error" size="small"
                                                            variant="simple">
                                                            {{ errors.email }}</Message>
                                                    </FormField>
                                                    <FormField class="flex flex-col gap-1">
                                                        <label for="">Xác nhận địa chỉ email</label>
                                                        <InputText v-model="bookerInforForm.confirm_email"
                                                            type="text" />
                                                        <Message v-if="errors.confirm_email" severity="error"
                                                            size="small" variant="simple">
                                                            {{ errors.confirm_email }}</Message>
                                                    </FormField>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="country">Vùng/Quốc gia</label>
                                                    <Select v-model="bookerInforForm.country" :options="countries"
                                                        optionLabel="name" placeholder="Select a country" />
                                                    <Message v-if="$form.city?.name?.invalid" severity="error"
                                                        size="small" variant="simple">
                                                        {{ $form.city.name.error?.message }}</Message>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="">Số điện thoại</label>
                                                    <InputText v-model="bookerInforForm.phone" />
                                                    <Message v-if="errors.phone" severity="error" size="small"
                                                        variant="simple">
                                                        {{ errors.phone }}</Message>
                                                </div>
                                                <div class="flex flex-col gap-2">
                                                    <label for="">Bạn sắp đi công tác? (không bắt buộc)</label>
                                                    <RadioButtonGroup name="business_trip" class="flex flex-wrap gap-4">
                                                        <div class="flex items-center gap-2">
                                                            <RadioButton value="true" />
                                                            <label for="cheese">Đúng</label>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <RadioButton value="false" />
                                                            <label for="mushroom">Sai</label>
                                                        </div>
                                                    </RadioButtonGroup>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- room list -->
                                        <template
                                            v-for="room in roomBookingDetail.selected_rooms.sort((a, b) => a.name.localeCompare(b.name))">
                                            <div class="room-booking-item | box flow" style="--flow-spacer:1rem">
                                                <!-- room type name -->
                                                <h3 class="text-xl font-semibold">{{ room.name }}</h3>
                                                <!-- rate policy -->
                                                <div class="flex items-center gap-x-2 text-green-600">
                                                    <i class="pi pi-check"></i>
                                                    <p>{{ getPaymentRequired(room.rate_policy.payment_requirement) }}
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-x-2 text-green-600">
                                                    <i class="pi pi-check"></i>
                                                    <p><b>{{ getCancellationType(room.rate_policy.cancellation_type) }}</b>
                                                        trước 3-7 ngày</p>
                                                </div>
                                                <!-- capacity -->
                                                <div class="flex items-center gap-x-2">
                                                    <i class="pi pi-user"></i>
                                                    <p><b>Khách: </b>
                                                        {{ room.num_adults + ' người lớn ' + (room.num_children > 0 ? `và ${room.num_children} trẻ em` : '') }}
                                                    </p>
                                                </div>
                                                <Divider />
                                                <div class="flex flex-col gap-1">
                                                    <label for="floor-option" class="font-semibold">Tùy chọn
                                                        tầng</label>
                                                    <Select name="floor-option" :options="countries" optionLabel="name"
                                                        placeholder="Không có" class="max-w-60" />
                                                </div>
                                            </div>
                                        </template>

                                        <div class="add-service | box flow" style="--flow-spacer:1rem">
                                            <h3 class="text-xl font-semibold">Thêm vào kỳ nghỉ</h3>
                                            <div>
                                                <CheckboxGroup name="service" class="flex flex-col flex-wrap gap-4">
                                                    <div class="flex items-center gap-2">
                                                        <Checkbox value="book-airport-transfer" />
                                                        <label for="book-airport-transfer">Đặt xe đưa đón/taxi</label>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <Checkbox value="air-ticket" />
                                                        <label for="air-ticket">Đặt vé máy bay cho chuyến đi</label>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <Checkbox value="car-rental" />
                                                        <label for="car-rental">Thuê xe</label>
                                                    </div>
                                                </CheckboxGroup>
                                            </div>
                                        </div>
                                        <div class="special-request | box flow" style="--flow-spacer:1rem">
                                            <h3 class="text-xl font-semibold">Các yêu cầu đặc biệt</h3>
                                            <div class="flex flex-col gap-2">
                                                <span>Vui lòng ghi yêu cầu của bạn vào đây.(không bắt buộc)</span>
                                                <Textarea v-model="bookerInforForm.special_request" rows="4" cols="30"
                                                    style="resize: none" />
                                                <div class="flex items-center gap-2">
                                                    <Checkbox name="special_request" value="close-room" />
                                                    <label for="air-ticket">Tôi muốn các phòng ở gần nhau</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="arrival-time | box flow" style="--flow-spacer:1rem">
                                            <h3 class="text-xl font-semibold">Thời gian đến của bạn</h3>
                                            <div class="flex flex-col gap-2">
                                                <span>Thêm thời gian đến dự kiến của bạn.(không bắt buộc)</span>
                                                <Select name="arrival_time" :options="checkInTimeSlots"
                                                    optionLabel="name" optionValue="value" placeholder="Vui lòng chọn"
                                                    class="max-w-60" />
                                            </div>
                                        </div>
                                    </Form>
                                </div>
                            </div>
                            <div class="flex p-2 justify-end">
                                <Button label="Next" icon="pi pi-arrow-right" iconPos="right"
                                    @click="activateCallback(3)" />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" :value="3">
                            <div class="flow" style="--flow-spacer:1rem">
                                <div class="payment-section | box flow p-6" style="--flow-spacer:1rem">
                                    <h3 class="text-xl font-semibold">Thông tin thanh toán</h3>
                                    <Button class="flex items-center gap-x-2" severity="info" variant="text" fluid
                                        @click="showAddPaymentMethod">
                                        <template #default>
                                            <div v-if="selectedPayment"
                                                class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-x-2 w-full">
                                                    <img :src="selectedPayment.imgPath" alt=""
                                                        class="h-15 w-15 object-contain">
                                                    <span
                                                        class="text-gray-700 font-medium">{{ selectedPayment.label }}</span>
                                                </div>
                                                <i class="pi pi-angle-right" style="font-size:1.75rem"></i>
                                            </div>
                                            <div v-else class="flex items-center gap-x-2 w-full">
                                                <i class="pi pi-credit-card" style="font-size:1.5rem"></i>
                                                <span class="text-md">Chọn phương thức thanh toán</span>
                                            </div>
                                        </template>
                                    </Button>
                                </div>
                                <div class="room-booking-item | box flow p-6" style="--flow-spacer:1rem">
                                    <h3 class="text-xl font-semibold">Thông tin người đặt phòng</h3>
                                    <div class="grid gap-3">
                                        <div class="flex justify-between">
                                            <label class="text-gray-500">Khách hàng</label>
                                            <span>Phạm Chí Trọng</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <label class="text-gray-500">Số điện thoại</label>
                                            <span>0376193244</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <label class="text-gray-500">Email</label>
                                            <span>pct2002@gmail.com</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <label class="text-gray-500">Dịch vụ</label>
                                            <span>Đưa đón bằng xe taxi</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <label class="text-gray-500">Yêu cầu đặc biệt</label>
                                            <span>LGBT</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex px-2 mt-4 justify-between">
                                <Button label="Back" severity="secondary" icon="pi pi-arrow-left"
                                    @click="activateCallback(2)" raised />
                                <Button label="Thanh toán" severity="info" raised
                                    @click="confirmPayment(() => activateCallback(2))" />
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(.p-steppanels .p-steppanel) {
    background-color: transparent;
}

:deep(.p-accordion.p-component .p-accordioncontent-content) {
    padding: 0 !important;
}

:deep(.p-accordionheader) {
    padding: 0 !important;
}

:deep(.p-accordion.p-component .p-accordionpanel) {
    border: none !important;
}

.summary-price {
    background: var(--accent-color-200);
}
</style>

<script setup>
import { ref, reactive, defineAsyncComponent, computed, watch } from 'vue';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Message from 'primevue/message';

// form
import { Form, FormField } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';
import Checkbox from 'primevue/checkbox';
import CheckboxGroup from 'primevue/checkboxgroup';
import Textarea from 'primevue/textarea';

// accordian
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';

// stepper
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import { usePage } from '@inertiajs/vue3'

// router
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const roomBookingDetail = reactive(page.props.roomBookingDetail);

function formatRoomBookingData(booking) {
    if (!booking.selected_rooms || booking.selected_rooms.length === 0) return booking;

    // Lấy total_base_price và total_price từ phòng đầu tiên
    const firstRoom = booking.selected_rooms[0];
    booking.total_base_price = firstRoom.total_base_price;
    booking.total_price = firstRoom.total_price;

    // Xóa total_base_price và total_price khỏi từng room
    booking.selected_rooms.forEach(room => {
        delete room.total_base_price;
        delete room.total_price;
    });
}
formatRoomBookingData(roomBookingDetail);
console.log(roomBookingDetail);

const getSummaryText = (numOfRooms, numOfAdults, numOfChildren) => {
    let text = `${numOfRooms} phòng cho ${numOfAdults} người lớn`;
    if (numOfChildren > 0) {
        text += ` và ${numOfChildren} trẻ em`
    }
    return text;
}

const summaryNumOfRoomsList = computed(() => {
    const map = {};

    roomBookingDetail.selected_rooms.forEach(room => {
        if (!map[room.name]) {
            map[room.name] = { name: room.name, count: 0 };
        }
        map[room.name].count += room.count;
    });

    return Object.values(map);
});

const getCancellationType = (type) => {
    switch (type) {
        case 'free_cancellation':
            return 'Hủy miễn phí';

        case 'flexible_change':
            return 'Linh hoạt đổi ngày';

        case 'partial_refund':
            return 'Hủy muộn giữ lại cọc';
    }
};

const getPaymentRequired = (type) => {
    switch (type) {
        case 'full_prepayment':
            return 'Thanh toán trước khi đến';

        case 'deposit_required':
            return 'Yêu cầu đặt cọc';

        case 'pay_at_hotel':
            return 'Không cần thanh toán trước, thanh toán tại chỗ nghỉ';
    }
};

const activeStep = ref(2);

const countries = ref([
    {
        name: 'Việt Nam',
        code: 'VN'
    },
    {
        name: 'Trung Quốc',
        code: 'CN'
    },
    {
        name: 'Úc',
        code: 'Australia'
    },
    {
        name: 'Mỹ',
        code: 'USA'
    },
    {
        name: 'Nga',
        code: 'Russian'
    }
])

// check in time slots
const checkInTimeSlots = Array.from({ length: 24 }, (_, i) => {
    const start = String(i).padStart(2, "0") + ":00";
    const end = String((i + 1) % 24).padStart(2, "0") + ":00";
    const label = `${start} - ${end}`;
    return {
        name: label,
        value: label
    };
});

// change booking options
const onChangeBookingOptions = () => {
    localStorage.removeItem('bookingFromHomePage');
    localStorage.setItem('bookingFromDetailPage', '1');
    localStorage.setItem('bookingFilterOptions', JSON.stringify({
        adults: roomBookingDetail.num_adults,
        children: roomBookingDetail.num_children,
        rooms: roomBookingDetail.num_rooms,
        dateRange: roomBookingDetail.date_range
    }));
    router.get(route('booking.index'));
}

// open payment method dialog
import { useDialog } from 'primevue/usedialog';

const dialog = useDialog();
const selectedPayment = ref(null);

const addPaymentMethod = defineAsyncComponent(() => import('../../Components/Dialog/AddPaymentMethod.vue'));

const showAddPaymentMethod = () => {
    const dialogRef = dialog.open(addPaymentMethod, {
        props: {
            header: 'Chọn phương thức thanh toán',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true
        },
        emits: {
            onConfirmPaymentMethod: (payment) => {
                selectedPayment.value = payment;
                console.log(selectedPayment.value);
                dialogRef.close(); // đóng dialog khi xác nhận
            }
        }
    });
}

// add customer infor
const bookerInforForm = reactive({
    surname: '',
    firstname: '',
    phone: '',
    email: '',
    confirm_email: '',
    country: '',
    special_request: '',
    arrival_time: '',
    payment_instrument: '',
});

const errors = reactive({
    surname: null,
    firstname: null,
    phone: null,
    email: null,
    confirm_email: null,
});

// REGEX ĐIỆN THOẠI VIỆT NAM (09, 03, 07, 08, 05)
const phoneRegex = /^(03|05|07|08|09)\d{8}$/;

// REGEX EMAIL CHUẨN
const emailRegex = /^\S+@\S+\.\S+$/;

const validateForm = () => {
    // surname
    errors.surname = bookerInforForm.surname.trim()
        ? null
        : "Vui lòng nhập họ";

    // firstname
    errors.firstname = bookerInforForm.firstname.trim()
        ? null
        : "Vui lòng nhập tên";

    // phone
    if (!bookerInforForm.phone.trim()) {
        errors.phone = "Vui lòng nhập số điện thoại";
    } else if (!phoneRegex.test(bookerInforForm.phone)) {
        errors.phone = "Số điện thoại không hợp lệ";
    } else {
        errors.phone = null;
    }

    // email
    if (!bookerInforForm.email.trim()) {
        errors.email = "Vui lòng nhập email";
    } else if (!emailRegex.test(bookerInforForm.email)) {
        errors.email = "Email không hợp lệ";
    } else {
        errors.email = null;
    }

    // confirm email
    if (!bookerInforForm.confirm_email.trim()) {
        errors.confirm_email = "Vui lòng nhập lại email";
    } else if (bookerInforForm.confirm_email !== bookerInforForm.email) {
        errors.confirm_email = "Email xác nhận không trùng";
    } else {
        errors.confirm_email = null;
    }

    // nếu không có lỗi → true
    return Object.values(errors).every(e => e === null);
};

// get payment instrument
watch(() => selectedPayment.value, (newValue) => {
    bookerInforForm.payment_instrument = newValue.value;
});

const confirmPayment = async (backToBookingForm) => {
    if (!validateForm()) {
        backToBookingForm();
    }
    else {
        let bookingData = {
            check_in: roomBookingDetail.date_range[0],
            check_out: roomBookingDetail.date_range[1],
            num_nights: roomBookingDetail.num_nights,
            num_rooms: roomBookingDetail.num_rooms,
            num_adults: roomBookingDetail.num_adults,
            num_children: roomBookingDetail.num_children,
            total_base_price: roomBookingDetail.total_base_price,
            total_price: roomBookingDetail.total_price,
            selected_rooms: roomBookingDetail.selected_rooms,
            full_name: bookerInforForm.surname + ' ' + bookerInforForm.firstname,
            ...bookerInforForm,
        };
        delete bookingData.surname;
        delete bookingData.firstname;
        console.log(bookingData);

        const res = await axios.post(route('booking.confirm'), bookingData);

        if (res.data.redirect_url) {
            window.location.href = res.data.redirect_url;
        }
    }
}

</script>