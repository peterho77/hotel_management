<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const breadcrumb = page.props.breadcrumb;
const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, '')} Page`;

let mainServiceList = [{
    name: 'Restaurants',
    imgUrl: '/img/about/about-p1.jpg'
}, {
    name: 'Travel & Camping',
    imgUrl: '/img/about/about-p2.jpg'
},
{
    name: 'Event & Party',
    imgUrl: '/img/about/about-p3.jpg'
}];

let galleryImageList = ['/img/gallery/gallery-1.jpg', '/img/gallery/gallery-4.jpg', '/img/gallery/gallery-3.jpg', '/img/gallery/gallery-2.jpg'];

</script>

<template>
     <Head :title="pageTitle" />

    <Breadcrumb :model="breadcrumb" :pt="{ root: { class: 'justify-center' }, item: { class: 'text-xl' } }">
        <template #item="{ item, props }">
            <a v-if="item === breadcrumb[breadcrumb.length - 1]" class="font-medium text-orange-300">
                {{ item.label }}
            </a>
            <a v-else v-bind="props.action">{{ item.label }}</a>
        </template>
    </Breadcrumb>

    <div class="about-us-page-section | padding-block-600">
        <div class="container">
            <div class="about-us__text | even-columns padding-block-400" style="--custom-gap:3rem">
                <div class="about-us__title | flow text-center flex flex-col justify-center align-center" style="--flow-spacer:1em">
                    <h2 class="fs-secondary-heading">Welcome to Sona.</h2>
                    <p>Built in 1910 during the Belle Epoque period, this hotel is located in the center of Paris,
                        with easy access to the city’s tourist attractions. It offers tastefully decorated rooms.
                    </p>
                </div>
                <ul class="about-us__services-list | flow pl-8" style="--flow-spacer:.75em">
                    <li class="grid grid-cols-[20px_1fr] items-center gap-2">
                        <SvgSprite symbol="check" role="presentation" class="icon service-icon" data-size="small" />
                        <span class="fs-700">20% Off On Accommodation.</span>
                    </li>
                    <li class="grid grid-cols-[20px_1fr] items-center gap-2">
                        <SvgSprite symbol="check" role="presentation" class="icon service-icon" data-size="small" />
                        <span class="fs-700">Complimentary Daily Breakfast</span>
                    </li>
                    <li class="grid grid-cols-[20px_1fr] items-center gap-2">
                        <SvgSprite symbol="check" role="presentation" class="icon service-icon" data-size="small" />
                        <span class="fs-700">3 Pcs Laundry Per Day</span>
                    </li>
                    <li class="grid grid-cols-[20px_1fr] items-center gap-2">
                        <SvgSprite symbol="check" role="presentation" class="icon service-icon" data-size="small" />
                        <span class="fs-700">Free Wifi.</span>
                    </li>
                    <li class="grid grid-cols-[20px_1fr] items-center gap-2">
                        <SvgSprite symbol="check" role="presentation" class="icon service-icon" data-size="small" />
                        <span class="fs-700">Discount 20% On F&B</span>
                    </li>
                </ul>
            </div>

            <div class="about-us__services | padding-block-600 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="about-us__services-item | set-bg-img" v-for="item in mainServiceList"
                    :style="$getBgStyle(item.imgUrl)">
                    <span class="about-us__services-item-title">{{ item.name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-section | padding-block-400">
        <div class="container">
            <div class="gallery__text | text-center padding-block-400 flow" style="--flow-spacer:.5em">
                <span class="label">Our Gallery</span>
                <h2 class="fs-secondary-heading">Discover Our Work</h2>
            </div>
            <div class="gallery__list | grid grid-cols-1 lg:grid-cols-2 gap-4 items-stretch padding-block-600">
                <div class="grid grid-rows-2 gap-3">
                    <div class="gallery-item | set-bg-img" v-if="galleryImageList[0]"
                        :style="$getBgStyle(galleryImageList[0])">
                        <div class="gallery-item__content | align-center justify-center"><span
                                class="gallery-item__title">Room
                                luxury</span></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="gallery-item | set-bg-img"
                            v-for="img in galleryImageList.slice(1, 3)" :style="$getBgStyle(img)">
                            <div class="gallery-item__content | align-center justify-center">
                                <span class="gallery-item__title">Room luxury
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gallery-item | set-bg-img aspect-[6/5]" v-if="galleryImageList[3]"
                    :style="$getBgStyle(galleryImageList[3])">
                    <div class=" gallery-item__content | align-center justify-center"><span
                            class="gallery-item__title">Room
                            luxury</span></div>
                </div>
            </div>
        </div>
    </div>
</template>
