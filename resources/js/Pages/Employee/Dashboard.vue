<template>
    <div class="dashboard-wrapper min-h-screen bg-[#f3f4f6] p-4 font-sans">
        <div class="container">
            <div class="bg-white p-3 rounded-lg shadow-sm mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-1/3 relative">
                    <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Tìm kiếm khách hàng, mã đặt phòng..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
    
                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    <Button label="Đặt phòng" icon="pi pi-plus" severity="info" class="text-sm" />
                </div>
            </div>
    
            <div class="flex flex-wrap gap-2 mb-6">
                <div v-for="(stat, index) in statusStats" :key="index"
                    class="cursor-pointer bg-white border border-gray-200 rounded-full px-3 py-1.5 flex items-center gap-2 text-sm text-gray-600 hover:shadow-md transition select-none">
                    <span :class="stat.dotClass" class="w-2.5 h-2.5 rounded-full block"></span>
                    <span class="font-medium">{{ stat.label }} ({{ stat.count }})</span>
                </div>
            </div>
    
            <div v-for="floor in floorGroups" :key="floor.id" class="mb-8 animate-fade-in">
    
                <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    {{ floor.name }}
                    <span class="text-gray-400 text-base font-normal">({{ floor.rooms.length }})</span>
                </h2>
    
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
                    <div v-for="room in floor.rooms" :key="room.id" :class="getCardClasses(room)">
                        <div class="flex justify-between items-start mb-2">
                            <div v-if="room.isDirty" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-xs font-bold flex items-center gap-1 border border-red-200">
                                <i class="pi pi-ban text-[10px]"></i> Chưa dọn
                            </div>
                            
                            <div v-else-if="room.status === 'maintenance'" class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="pi pi-wrench text-[10px]"></i> Bảo trì
                            </div>
    
                            <div v-else class="px-2 py-0.5 rounded-full text-xs flex items-center gap-1 transition-colors"
                                :class="room.status === 'empty' ? 'text-gray-500 bg-gray-100' : 'text-white bg-white/20 backdrop-blur-sm'">
                                <i class="pi pi-sparkles text-[10px]"></i> Sạch
                            </div>
    
                            <i class="pi pi-ellipsis-v cursor-pointer p-1 rounded hover:bg-black/10 transition"
                                :class="room.status === 'empty' ? 'text-gray-400' : 'text-white'"></i>
                        </div>
    
                        <div class="flex-1 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold mb-1"
                                :class="room.status === 'empty' ? 'text-gray-800' : 'text-white'">
                                {{ room.name }}
                            </h3>
                            <span class="text-slate-700 font-semibold">{{ room.room_type.name }}</span>
    
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
            
            <div v-if="floorGroups.length === 0" class="text-center py-10 text-gray-500">
                <i class="pi pi-inbox text-4xl mb-2"></i>
                <p>Không có dữ liệu phòng.</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Hiệu ứng bóng đổ nhẹ cho thẻ phòng trống */
.room-card {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Animation xuất hiện dần */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Custom Scrollbar */
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
import { computed } from 'vue';
import Button from 'primevue/button';
import { formatCurrency } from "@/Composables/formatData";

// Nhận dữ liệu từ Controller Laravel truyền xuống
const props = defineProps({
    roomList: {
        type: Array,
        default: () => [],
    },
});

// --- 1. Xử lý thống kê trạng thái (Computed) ---
const statusStats = computed(() => {
    // Khởi tạo bộ đếm
    const counts = {
        empty: 0,
        occupied: 0,
        overdue: 0,
        checkout: 0,
        maintenance: 0
    };

    // Duyệt qua roomList để đếm
    props.roomList.forEach(room => {
        // room.status trả về từ backend (ui_status): 'empty', 'occupied', 'overdue', 'checkout', 'maintenance'
        if (counts[room.status] !== undefined) {
            counts[room.status]++;
        }
    });

    return [
        { label: 'Đang trống', count: counts.empty, dotClass: 'bg-gray-400' },
        { label: 'Sắp nhận', count: 0, dotClass: 'bg-orange-400' }, // Logic này cần thêm từ Booking 'confirmed' nếu muốn
        { label: 'Đang sử dụng', count: counts.occupied, dotClass: 'bg-emerald-500' },
        { label: 'Sắp trả', count: counts.checkout, dotClass: 'bg-teal-600' },
        { label: 'Quá giờ trả', count: counts.overdue, dotClass: 'bg-rose-600' },
        { label: 'Bảo trì', count: counts.maintenance, dotClass: 'bg-slate-600' }
    ].filter(item => item.count > 0 || item.label === 'Đang trống'); // Có thể filter chỉ hiện những cái > 0
});

// --- 2. Nhóm phòng theo Tầng (Computed) ---
const floorGroups = computed(() => {
    const groups = {};

    props.roomList.forEach(room => {
        // Nếu tầng chưa tồn tại trong groups, tạo mới
        if (!groups[room.floor]) {
            groups[room.floor] = {
                id: `floor_${room.floor}`,
                name: `Tầng ${room.floor}`,
                rooms: []
            };
        }
        // Thêm phòng vào tầng tương ứng
        groups[room.floor].rooms.push(room);
    });

    // Chuyển Object thành Array và sắp xếp theo số tầng (tăng dần)
    return Object.values(groups).sort((a, b) => {
        const floorA = parseInt(a.name.replace(/\D/g, '')) || 0;
        const floorB = parseInt(b.name.replace(/\D/g, '')) || 0;
        return floorA - floorB;
    });
});

// --- 3. Hàm class động ---
const getCardClasses = (room) => {
    let classes = ['room-card transition-all duration-200 border flex flex-col justify-between min-h-[160px] cursor-pointer relative rounded-xl p-4'];

    switch (room.status) {
        case 'occupied': // Đang ở
            classes.push('bg-emerald-600 text-white border-emerald-600 shadow-emerald-200/50 hover:bg-emerald-500');
            break;
        case 'overdue': // Quá giờ
            classes.push('bg-orange-500 text-white border-orange-500 shadow-orange-200/50 hover:bg-orange-400');
            break;
        case 'checkout': // Sắp trả
            classes.push('bg-teal-700 text-white border-teal-700 shadow-teal-200/50 hover:bg-teal-600');
            break;
        case 'maintenance': // Bảo trì
            classes.push('bg-red-300 text-slate-500 border-slate-200 opacity-75');
            break;
        case 'empty':
        default: // Trống
            classes.push('bg-white text-gray-800 border-gray-200 hover:border-emerald-400 hover:shadow-lg');
            break;
    }
    return classes.join(' ');
};
</script>