<template>
    <footer class="footer">
        <div class="footer-content | padding-block-600">
            <div class="container">
                <div class="footer-content__wrapper">
                    <div class="footer-content__about | flow text-center-only-md" style="--flow-spacer: 1em">
                        <img class="footer-logo" src="../../../../public/img/footer-logo.png" alt="" />
                        <p data-width="narrow">
                            We inspire and reach millions of travelers across 90
                            local websites
                        </p>
                        <ul class="social-list | justify-center-only-md" data-type="inverted" data-size="big">
                            <li>
                                <a href="">
                                    <SvgSprite symbol="facebook" size="0 0 24 24" role="presentation"
                                        class="icon social-icon" />
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <SvgSprite symbol="instagram" size="0 0 24 24" role="presentation"
                                        class="icon social-icon" />
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <SvgSprite symbol="twitch" size="0 0 24 24" role="presentation"
                                        class="icon social-icon" />
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <SvgSprite symbol="twitter-x" size="0 0 24 24" role="presentation"
                                        class="icon social-icon" />
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-content__contact | flow text-center-only-md" style="--flow-spacer: 1em">
                        <span class="label">Contact us</span>
                        <ul class="contact-list">
                            <div class="flow" style="--flow-spacer: 0.75em">
                                <li>037 619 3244</li>
                                <li>peterho5477@gmail.com</li>
                                <li>12 Do Luong HCM city</li>
                            </div>
                        </ul>
                    </div>
                    <div class="footer-content__newsletter | flow text-center-only-md"
                        style="--flow-spacer: 1em">
                        <span class="label">New latest</span>
                        <p>Get the latest updates and offers</p>
                        <form action="" class="letter-form">
                            <input type="text" placeholded="Email" />
                            <button class="button send-button">
                                <SvgSprite symbol="send-letter" size="0 0 24 24" role="presentation" class="icon" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright | padding-block-800">
            <div class="container">
                <div class="justify-center align-center">
                    <ul class="policy-list | justify-center-only-md">
                        <li>Contact</li>
                        <li>Terms of use</li>
                        <li>Privacy</li>
                        <li>Environmental Policy</li>
                    </ul>
                </div>
                <div class="footer-copyright__text | align-center justify-center mt-4">
                    <p>Copyright @2025 made by Peter Ho</p>
                </div>
            </div>
        </div>
        <FloatingButton/>
    </footer>
</template>

<script setup>
import FloatingButton from "../../Components/Global/Chat/FloatingButton.vue";
</script>
