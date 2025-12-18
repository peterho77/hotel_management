<template>
    <section class="room-section | padding-block-600">
        <div class="container-fluid">
            <div class="grid gap-x-3 gap-y-3 md:grid-cols-2 lg:grid-cols-4">
                <div class="room-item | set-bg-img pop-up" v-for="room in rooms.slice(0, 4)" :key="room.id"
                    :style="room.images && room.images.length > 0
                        ? $getBgStyle(room.images[0].path)
                        : $getBgStyle('/img/default-blank-img.jpg')">

                    <div class="room-item__content | item-pop-up padding-block-400 flow" style="--flow-spacer:1em">
                        <h3 class="room-item__title | text-center fs-normal-heading">{{ room.name }}</h3>

                        <h2 class="room-item__price | text-center">
                            {{ formatCurrency(room.base_price) }}
                            <span>/ Đêm</span>
                        </h2>

                        <table class="room-item__desc w-full border-separate border-spacing-y-2">
                            <tbody class="flow" style="--flow-spacer:1em">
                                <tr v-for="(value, key) in simpleRoomTypeDesc(room)" :key="key">
                                    <td class="feature text-left align-top w-[140px] pr-2">
                                        {{ formatLabel(key) }}:
                                    </td>
                                    <td class="text-left align-top">
                                        {{ value }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { formatLabel, formatCurrency } from "@/Composables/formatData";

const props = defineProps({
    rooms: {
        type: Array,
        default: () => [] // Giá trị mặc định là mảng rỗng nếu chưa có dữ liệu
    }
});
console.log(props.rooms);

// 1. Lọc thông tin cần hiển thị trong bảng (Bỏ id, timestamps, status...)
function simpleRoomTypeDesc(room) {
    const displayFields = {
        'max_adults': 'Người lớn tối đa',
        'max_children': 'Trẻ em tối đa',
    };

    return Object.fromEntries(
        Object.entries(room)
            .filter(([key]) => Object.keys(displayFields).includes(key))
            .map(([key, value]) => [displayFields[key], value])
    );
}

// Hàm bổ trợ cho đường dẫn ảnh (Nếu $getBgStyle chưa xử lý đường dẫn)
function getImagePath(path) {
    if (!path) return '/img/default-blank-img.jpg';
    return path.startsWith('/') ? path : `/storage/${path}`;
}

</script>