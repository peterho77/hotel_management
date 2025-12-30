<template>
    <div class="chart-section | mt-8 padding-block-200">
        <div class="container | grid gap-8">
            <div class="room-booking-chart | p-4 border border-slate-200 card flex flex-col items-center justify-center">
                <h2 class="text-xl font-bold mb-4">Tỷ lệ đặt phòng theo loại</h2>

                <Chart v-if="chartData.datasets[0].data.length > 0" type="doughnut" :data="chartData"
                    :options="chartOptions" class="w-full md:w-120" />

                <div v-else class="text-gray-500 py-10">Chưa có dữ liệu đặt phòng</div>
            </div>
            <div class="card flex flex-col items-center justify-center p-4 mt-4 border border-slate-200">
                <h2 class="text-xl font-bold mb-4">Doanh thu năm nay</h2>
                <Chart type="bar" :data="chartData1" :options="chartOptions1" class="w-full h-120" />
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, computed, defineProps } from "vue";

import Chart from 'primevue/chart';
import { 
    Chart as ChartJS, 
    Title, 
    Tooltip, 
    Legend, 
    BarElement,    // <--- Cần để vẽ cột
    CategoryScale, // <--- Cần để vẽ trục X (Tháng)
    LinearScale    // <--- Cần để vẽ trục Y (Số tiền)
} from 'chart.js';

// Đăng ký với ChartJS
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

// 1. Nhận dữ liệu từ Controller
const props = defineProps({
    bookingItems: {
        type: Array,
        default: () => []
    },
    revenueData: {
        type: Array,
        default: () => Array(12).fill(0) // Mặc định 12 số 0
    }
});
console.log(props.bookingItems);

// 2. Xử lý dữ liệu (Gom nhóm và đếm)
const chartData = computed(() => {
    const stats = {};

    // Duyệt qua từng item booking để đếm
    props.bookingItems.forEach(item => {
        // Lấy tên loại phòng (Dùng optional chaining ?. để tránh lỗi nếu null)
        // Ví dụ: item.room_option.room_type.name -> "Deluxe Room"
        const typeName = item.room_option?.room_type?.name || 'Khác';

        if (!stats[typeName]) {
            stats[typeName] = 0;
        }
        stats[typeName]++;
    });

    // Tách key (labels) và value (data)
    const labels = Object.keys(stats);
    const dataValues = Object.values(stats);

    return {
        labels: labels,
        datasets: [
            {
                data: dataValues,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',  // Blue
                    'rgba(16, 185, 129, 0.7)',  // Green
                    'rgba(249, 115, 22, 0.7)',  // Orange
                    'rgba(6, 182, 212, 0.7)',   // Cyan
                    'rgba(139, 92, 246, 0.7)',  // Purple
                    'rgba(239, 68, 68, 0.7)',   // Red
                    'rgba(107, 114, 128, 0.7)'  // Gray
                ],
                hoverBackgroundColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(249, 115, 22, 1)',
                    'rgba(6, 182, 212, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(107, 114, 128, 1)'
                ],
                borderWidth: 1
            }
        ]
    };
});

// 3. Cấu hình giao diện biểu đồ
const chartOptions = computed(() => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');

    return {
        plugins: {
            legend: {
                position: 'bottom', // Chú thích nằm dưới
                labels: {
                    color: textColor,
                    usePointStyle: true // Dùng hình tròn thay vì hình vuông ở chú thích
                }
            },
            tooltip: {
                callbacks: {
                    // Custom hiển thị tooltip: "Deluxe Room: 15 lượt (25%)"
                    label: function (context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        let total = context.chart._metasets[context.datasetIndex].total;
                        let percentage = Math.round((value / total) * 100) + '%';

                        return ` ${label}: ${value} phòng (${percentage})`;
                    }
                }
            }
        }
    };
});

//doanh thu
const chartData1 = computed(() => {
    return {
        // Nhãn trục X (12 tháng)
        labels: [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 
            'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 
            'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
        ],
        datasets: [
            {
                label: 'Doanh thu (VND)',
                data: props.revenueData, // Dữ liệu từ Laravel
                backgroundColor: 'rgba(59, 130, 246, 0.6)', // Màu xanh dương nhạt
                borderColor: 'rgb(59, 130, 246)', // Viền xanh dương đậm
                borderWidth: 1,
                borderRadius: 4, // Bo tròn góc cột
                barPercentage: 0.6 // Độ rộng cột
            }
        ]
    };
});
// 3. Cấu hình giao diện (Format tiền tệ)
const chartOptions1 = computed(() => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: textColor }
            },
            tooltip: {
                callbacks: {
                    // Format tiền trong Tooltip (khi rê chuột vào)
                    label: function(context) {
                        let value = context.raw;
                        return ' Doanh thu: ' + new Intl.NumberFormat('vi-VN', { 
                            style: 'currency', 
                            currency: 'VND' 
                        }).format(value);
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: { color: textColor },
                grid: { color: surfaceBorder, display: false } // Ẩn lưới dọc cho đẹp
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: textColor,
                    // Format tiền trên trục Y (Ex: 1.000.000 ₫)
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN', { 
                            notation: "compact", 
                            compactDisplay: "short" 
                        }).format(value) + ' ₫';
                    }
                },
                grid: { color: surfaceBorder }
            }
        }
    };
});
</script>