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
                                        'rounded-full border-1 w-7 h-7 inline-flex items-center justify-center transition-colors duration-200',
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
                                                class="check-in | flex flex-col items-center gap-1 border-r-1 border-gray-200">
                                                <label for="">Nhận phòng</label>
                                                <!-- checkin -->
                                                <span>{{ bookingDetail.date_range[0] }}</span>
                                            </div>
                                            <div class="check-out | flex flex-col items-center gap-1">
                                                <label for="">Trả phòng</label>
                                                <!-- checkout -->
                                                <span>{{ bookingDetail.date_range[1] }}</span>
                                            </div>
                                        </div>
                                        <div class="num-of-nights | grid">
                                            <label for="">Tổng thời gian lưu trú:</label>
                                            <!-- num of nights -->
                                            <span class="font-semibold">{{ bookingDetail.num_nights }} đêm</span>
                                        </div>
                                        <Divider />
                                        <Accordion value="0">
                                            <AccordionPanel value="0">
                                                <AccordionHeader>
                                                    <div class="grid p-2">
                                                        <label class="font-light">Bạn đã chọn</label>
                                                        <!-- summary text -->
                                                        <span
                                                            class="font-semibold">{{ getSummaryText(bookingDetail.num_rooms, bookingDetail.num_adults, bookingDetail.num_children) }}</span>
                                                    </div>
                                                </AccordionHeader>
                                                <AccordionContent>
                                                    <!-- num of rooms -->
                                                    <div class="grid p-2">
                                                        <template v-for="room in bookingDetail.selected_rooms">
                                                            <span>{{ room.count }} x {{ room.name }}</span>
                                                        </template>
                                                    </div>
                                                </AccordionContent>
                                            </AccordionPanel>
                                        </Accordion>
                                    </div>
                                    <div class="box | flow p-0!">
                                        <div class="detail-price | p-3 flow" style="--flow-spacer:1rem">
                                            <h3 class="text-lg text-center font-semibold">Tóm tắt giá</h3>
                                            <div class="price | flex justify-between items-center gap-1">
                                                <label for="">Giá gốc</label>
                                                <!-- base price -->
                                                <span>VND {{ bookingDetail.selected_rooms[0].total_base_price }}</span>
                                            </div>
                                            <div class="discount | flex justify-between items-center gap-1">
                                                <label for="">Ưu đãi</label>
                                                <!-- discount -->
                                                <span>- VND
                                                    {{ bookingDetail.selected_rooms[0].total_base_price - bookingDetail.selected_rooms[0].total_price }}</span>
                                            </div>
                                        </div>
                                        <div
                                            class="summary-price | overflow-hidden flex items-center justify-between p-4">
                                            <h3 class="text-2xl font-semibold">Giá</h3>
                                            <div class="flex flex-col items-end">
                                                <span class="text-red-500 line-through">VND
                                                    {{ bookingDetail.selected_rooms[0].total_base_price }}</span>
                                                <h3 class="text-xl font-semibold">VND
                                                    {{ bookingDetail.selected_rooms[0].total_price }}</h3>
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
                                                    <FormField v-slot="$field" name="surname"
                                                        class="flex flex-col gap-1">
                                                        <label for="">Họ</label>
                                                        <InputText type="text" />
                                                        <Message v-if="$field?.invalid" severity="error" size="small"
                                                            variant="simple">
                                                            {{ $field.error?.message }}</Message>
                                                    </FormField>
                                                    <FormField v-slot="$field" name="firstname"
                                                        class="flex flex-col gap-1">
                                                        <label for="">Tên</label>
                                                        <InputText type="text" />
                                                        <Message v-if="$field?.invalid" severity="error" size="small"
                                                            variant="simple">
                                                            {{ $field.error?.message }}</Message>
                                                    </FormField>
                                                    <FormField v-slot="$field" name="email" class="flex flex-col gap-1">
                                                        <label for="">Địa chỉ email</label>
                                                        <InputText type="text" />
                                                        <Message v-if="$field?.invalid" severity="error" size="small"
                                                            variant="simple">
                                                            {{ $field.error?.message }}</Message>
                                                    </FormField>
                                                    <FormField v-slot="$field" name="confirm-email"
                                                        class="flex flex-col gap-1">
                                                        <label for="">Xác nhận địa chỉ email</label>
                                                        <InputText type="text" />
                                                        <Message v-if="$field?.invalid" severity="error" size="small"
                                                            variant="simple">
                                                            {{ $field.error?.message }}</Message>
                                                    </FormField>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="country">Vùng/Quốc gia</label>
                                                    <Select name="country" :options="countries" optionLabel="name"
                                                        placeholder="Select a country" />
                                                    <Message v-if="$form.city?.name?.invalid" severity="error"
                                                        size="small" variant="simple">
                                                        {{ $form.city.name.error?.message }}</Message>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <label for="">Số điện thoại</label>
                                                    <InputText name="phone" />
                                                    <Message v-if="$form.city?.name?.invalid" severity="error"
                                                        size="small" variant="simple">
                                                        {{ $form.city.name.error?.message }}</Message>
                                                </div>
                                                <div class="flex flex-col gap-2">
                                                    <label for="">Bạn sắp đi công tác? (không bắt buộc)</label>
                                                    <RadioButtonGroup name="business-trip" class="flex flex-wrap gap-4">
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
                                        <template v-for="room in bookingDetail.selected_rooms">
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
                                                <Textarea name="special_request" rows="4" cols="30"
                                                    style="resize: none" />
                                                <div class="flex items-center gap-2">
                                                    <Checkbox name="special_request" value="close-room" />
                                                    <label for="air-ticket">Tôi muốn các phòng ở gần nhau</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="special-request | box flow" style="--flow-spacer:1rem">
                                            <h3 class="text-xl font-semibold">Thời gian đến của bạn</h3>
                                            <div class="flex flex-col gap-2">
                                                <span>Thêm thời gian đến dự kiến của bạn.(không bắt buộc)</span>
                                                <Select name="check_in_time" :options="checkInTimeSlots"
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
                            <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 24rem">
                                <div class="text-center mt-4 mb-4 text-xl font-semibold">Account created
                                    successfully</div>
                                <div class="flex justify-center">
                                    <img alt="logo"
                                        src="https://primefaces.org/cdn/primevue/images/stepper/content.svg" />
                                </div>
                            </div>
                            <div class="flex p-2 justify-start">
                                <Button label="Back" severity="secondary" icon="pi pi-arrow-left"
                                    @click="activateCallback(2)" />
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
import { ref, reactive } from 'vue';
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
import StepItem from 'primevue/stepitem';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import { usePage } from '@inertiajs/vue3'

const page = usePage();
const bookingDetail = reactive(page.props.detail);
console.log(bookingDetail);
const getSummaryText = (numOfRooms, numOfAdults, numOfChildren) => {
    let text = `${numOfRooms} phòng cho ${numOfAdults} người lớn`;
    if (numOfChildren > 0) {
        text += ` và ${numOfChildren} trẻ em`
    }
    return text;
}

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

</script>