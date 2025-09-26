<template>
    <section class="room-section | padding-block-600">
        <div class="container-fluid">
            <div class="grid gap-x-3 gap-y-3 md:grid-cols-2 lg:grid-cols-4">
                <div class="room-item | set-bg-img pop-up" v-for="roomType in topRoomTypeList"
                    :style="$getBgStyle(roomType.imgUrl)" :key="roomType.name">
                    <div class="room-item__content | item-pop-up padding-block-400 flow" style="--flow-spacer:1em">
                        <h3 class="room-item__title | text-center fs-normal-heading">{{ roomType.name }}</h3>
                        <h2 class="room-item__price | text-center">
                            {{ roomType.pricePerNight }}$
                            <span>Per Night</span>
                        </h2>
                        <table class="room-item__desc">
                            <tbody class="flow" style="--flow-spacer:1em">
                                <tr v-for="(value, key) in roomTypeDesc(roomType)">
                                    <td class="feature">{{ capitalize(key) }}:</td>
                                    <td>{{ formatValue(value) }}</td>
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
import { reactive } from 'vue';

let topRoomTypeList = reactive([
    {
        name: 'Double Room',
        pricePerNight: 199,
        size: { value: 300, unit: 'ft²' },
        capacity: 5,
        services: ['wifi', 'TV', 'bathroom'],
        imgUrl: '/img/room/room-b1.jpg'
    },
    {
        name: 'Premium King Room',
        pricePerNight: 159,
        size: { value: 280, unit: 'ft²' },
        capacity: 5,
        services: ['wifi', 'TV', 'bathroom'],
        imgUrl: '/img/room/room-b2.jpg'
    },
    {
        name: 'Deluxe Room',
        pricePerNight: 198,
        size: { value: 350, unit: 'ft²' },
        capacity: 5,
        services: ['wifi', 'TV', 'bathroom', 'balcony'],
        imgUrl: '/img/room/room-b3.jpg'
    },
    {
        name: 'Family Room',
        pricePerNight: 299,
        size: { value: 400, unit: 'ft²' },
        capacity: 5,
        services: ['wifi', 'TV', 'bathroom', 'kitchenette'],
        imgUrl: '/img/room/room-b4.jpg'
    }
]);

function roomTypeDesc(room) {
    return Object.fromEntries(Object.entries(room).filter(([key]) => !['name','pricePerNight','imgUrl'].includes(key)));
}

function formatValue(value){
    if(Array.isArray(value)) return value.map(capitalize).join(', ');
    if(typeof value === "object" && value !== null) return `${value.value} ${value.unit}`;
    return value;
};

function capitalize(str) {
  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1);
}

</script>