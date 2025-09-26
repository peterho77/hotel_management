<template>
    <section class="about-section | padding-block-1000 section-divider">
        <div class="container">
            <div class="even-columns" style="--custom-gap:2rem">
                <div class="align-center justify-center">
                    <div class="about__content | text-center flow" style="--flow-spacer:1rem">
                        <span class="label">About us</span>
                        <h3 class="fs-secondary-heading">
                            Intercontinental LA
                            <br>
                            Westlake Hotel
                        </h3>
                        <p>
                            Sona.com is a leading online accommodation site. We’re passionate about travel. Every day,
                            we
                            inspire and reach millions of travelers across 90 local websites in 41 languages.
                        </p>
                        <p>
                            So when it comes to booking the perfect hotel, vacation rental, resort, apartment, guest
                            house,
                            or tree house, we’ve got you covered.
                        </p>
                        <button class="button">Read more</button>
                    </div>
                </div>
                <div>
                    <div class="about__img h-full">
                        <div class="even-columns h-full">
                            <div class="set-bg-img h-full" v-for="(image, i) in imagesList" :key="i" :style="$getBgStyle(image)">                           
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref } from 'vue';

    let imagesList = ref(['/img/about/about-1.jpg','/img/about/about-2.jpg']);
</script>