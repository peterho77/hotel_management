<template>
    <div class="dashboard-wrapper min-h-screen bg-[#f3f4f6] p-4 font-sans">
        <div
            class="bg-white p-3 rounded-lg shadow-sm mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="w-full md:w-1/3 relative">
                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Tìm kiếm khách hàng, mã đặt phòng..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <div
                    class="hidden md:flex items-center border px-3 py-2 rounded bg-white text-sm text-gray-600 cursor-pointer hover:bg-gray-50 gap-2">
                    <span>Bảng giá chung</span>
                    <i class="pi pi-chevron-down text-xs"></i>
                </div>

                <button
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded flex items-center gap-2 text-sm font-medium transition shadow-sm">
                    <i class="pi pi-plus"></i> Đặt phòng
                </button>

                <button class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 ml-2">
                    <i class="pi pi-check-circle"></i> <span class="hidden sm:inline">Chọn nhiều phòng</span>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            <div v-for="(stat, index) in statuses" :key="index"
                class="cursor-pointer bg-white border border-gray-200 rounded-full px-3 py-1.5 flex items-center gap-2 text-sm text-gray-600 hover:shadow-md transition select-none">
                <span :class="stat.dotClass" class="w-2.5 h-2.5 rounded-full block"></span>
                <span class="font-medium">{{ stat.label }} ({{ stat.count }})</span>
            </div>
        </div>

        <div v-for="floor in floorList" :key="floor.id" class="mb-8 animate-fade-in">

            <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                {{ floor.name }}
                <span class="text-gray-400 text-base font-normal">({{ floor.rooms.length }})</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div v-for="room in floor.rooms" :key="room.id" :class="getCardClasses(room)">

                    <div class="flex justify-between items-start mb-2">
                        <div v-if="room.isDirty" class="dirty-badge">
                            <i class="pi pi-ban text-[10px]"></i> Chưa dọn
                        </div>
                        <div v-else class="clean-badge"
                            :class="room.status === 'empty' ? 'text-gray-500 bg-gray-100' : 'text-white bg-white/20 backdrop-blur-sm'">
                            <i class="pi pi-sparkles text-[10px]"></i> Sạch
                        </div>

                        <i class="pi pi-ellipsis-v cursor-pointer p-1 rounded hover:bg-black/10 transition"
                            :class="room.status === 'empty' ? 'text-gray-400' : 'text-white'"></i>
                    </div>

                    <div class="flex-1 flex flex-col justify-center">
                        <h3 class="text-2xl font-bold mb-1"
                            :class="room.status === 'empty' ? 'text-gray-800' : 'text-white'">
                            {{ room.number }}
                        </h3>

                        <div class="text-sm font-medium mb-1 truncate"
                            :class="room.status === 'empty' ? 'text-gray-600' : 'text-white/90'">
                            {{ room.guestName || room.type }}
                        </div>

                        <div class="text-xs" :class="room.status === 'empty' ? 'text-gray-400' : 'text-white/80'">
                            <i class="pi pi-sun text-[10px] mr-1"></i> {{ formatCurrency(room.price) }}
                        </div>
                    </div>

                    <div v-if="room.time" class="mt-3 pt-2 border-t border-white/20">
                        <div
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-sm">
                            <i class="pi pi-clock text-[10px]"></i> {{ room.time }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CSS Tùy chỉnh (Scoped) */

.dirty-badge {
    @apply bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-xs font-bold flex items-center gap-1 border border-red-200;
}

.clean-badge {
    @apply px-2 py-0.5 rounded-full text-xs flex items-center gap-1 transition-colors;
}

/* Hiệu ứng bóng đổ nhẹ cho thẻ phòng trống */
.room-card {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Animation xuất hiện dần */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Tùy chỉnh thanh cuộn để đẹp hơn trên Desktop */
.dashboard-wrapper::-webkit-scrollbar {
    width: 8px;
}

.dashboard-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.dashboard-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dashboard-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script setup>
import { ref } from 'vue';

// --- 1. Dữ liệu bộ lọc trạng thái ---
const statuses = ref([
    { label: 'Đang trống', count: 12, dotClass: 'bg-gray-400' },
    { label: 'Sắp nhận', count: 1, dotClass: 'bg-orange-400' },
    { label: 'Đang sử dụng', count: 3, dotClass: 'bg-emerald-500' },
    { label: 'Sắp trả', count: 2, dotClass: 'bg-teal-600' },
    { label: 'Quá giờ trả', count: 2, dotClass: 'bg-rose-600' }
]);

// --- 2. Dữ liệu danh sách tầng và phòng (floorList) ---
const floorList = ref([
    {
        id: 'floor_5_8',
        name: "Tầng 5-8",
        rooms: [
            { id: 101, number: 'SUP501', type: 'Superior', price: 2500000, status: 'empty', isDirty: false },
            { id: 102, number: 'SUP502', type: 'Superior', guestName: 'Nguyễn Văn A', price: 2500000, status: 'occupied', isDirty: true, time: '3 ngày / 3 ngày' },
            { id: 103, number: 'SUP503', type: 'Superior', guestName: 'Trần Thị B', price: 2500000, status: 'overdue', isDirty: false, time: 'Quá giờ nhận' },
            { id: 104, number: 'SUP504', type: 'Superior', price: 2500000, status: 'empty', isDirty: false },
        ]
    },
    {
        id: 'floor_9_12',
        name: "Tầng 9-12",
        rooms: [
            { id: 201, number: 'DLX901', type: 'Deluxe', guestName: 'Lê Văn C', price: 4000000, status: 'checkout', isDirty: true, time: '4 ngày / 3 ngày' },
            { id: 202, number: 'DLX902', type: 'Deluxe', price: 4000000, status: 'empty', isDirty: false },
            { id: 203, number: 'DLX903', type: 'Deluxe', price: 4000000, status: 'empty', isDirty: true },
            { id: 204, number: 'DLX904', type: 'Deluxe', guestName: 'Phạm Văn D', price: 4000000, status: 'checkout', isDirty: true, time: '5 ngày / 3 ngày' }
        ]
    },
    {
        id: 'floor_13_15',
        name: "Tầng 13-15",
        rooms: [
            { id: 301, number: 'JS1301', type: 'Junior Suite', price: 6500000, status: 'empty', isDirty: false },
            { id: 302, number: 'JS1302', type: 'Junior Suite', price: 6500000, status: 'empty', isDirty: true },
            { id: 303, number: 'JS1303', type: 'Junior Suite', price: 6500000, status: 'empty', isDirty: false },
            { id: 304, number: 'JS1304', type: 'Junior Suite', price: 6500000, status: 'empty', isDirty: false },
        ]
    }
]);

// --- 3. Các hàm xử lý giao diện ---

// Hàm lấy class CSS động cho thẻ phòng dựa trên trạng thái
const getCardClasses = (room) => {
    let classes = ['room-card transition-all duration-200 border flex flex-col justify-between min-h-[160px] cursor-pointer relative rounded-xl p-4'];

    switch (room.status) {
        case 'occupied': // Đang ở (Xanh lá)
            classes.push('bg-emerald-600 text-white border-emerald-600 shadow-emerald-200/50 hover:bg-emerald-500');
            break;
        case 'overdue': // Quá giờ/Sắp nhận (Cam)
            classes.push('bg-orange-500 text-white border-orange-500 shadow-orange-200/50 hover:bg-orange-400');
            break;
        case 'checkout': // Sắp trả (Xanh cổ vịt đậm)
            classes.push('bg-teal-700 text-white border-teal-700 shadow-teal-200/50 hover:bg-teal-600');
            break;
        case 'empty':
        default: // Trống (Trắng)
            classes.push('bg-white text-gray-800 border-gray-200 hover:border-emerald-400 hover:shadow-lg');
            break;
    }
    return classes.join(' ');
};

// Hàm định dạng tiền tệ
const formatCurrency = (value) => {
    if (!value) return '0 đ';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};
</script>