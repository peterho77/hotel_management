<section class="hero | align-center justify-center padding-block-800">
    <div class="hero__content">
        <div class="container">
            <div class="row row-gap-4">
                <div class="col-lg-6 align-center">
                    <div class="hero__text | flow">
                        <h1 class="fs-primary-heading">{{__('hero-section.title')}}</h1>
                        <p data-width="wide">{{__('hero-section.description')}}</p>
                        <button class="button">{{ __('hero-section.discover-now') }}</button>
                    </div>
                </div>
                <div class="col-xl-4 offset-xl-2 col-lg-5 offset-lg-1">
                    <div class="booking-form | padding-block-600 flow">
                        <h5 class="fs-normal-heading | text-center">{{__('hero-section.form.title')}}</h5>
                        <form  action="">
                            <div class="check-date | mb-3">
                                <label class="form-label" for="date-in">{{__('hero-section.form.checkin')}}</label>
                                <input class="date-input | form-control" type="text" id="date-in">
                            </div>
                            <div class="check-date | mb-3">
                                <label class="form-label" for="date-out">{{__('hero-section.form.checkout')}}</label>
                                <input class="date-input | form-control" type="text" id="date-out">
                            </div>
                            <div class="select-option | mb-3">
                                <label class="form-label" for="guest">{{__('hero-section.form.guest')}}</label>
                                <select name="guest" id="guest" class="nice-select form-control">
                                    <option value="">2 Adults</option>
                                    <option value="">3 Adults</option>
                                </select>
                            </div>
                            <div class="select-option | mb-3">
                                <label class="form-label" for="room">{{__('hero-section.form.room')}}</label>
                                <select name="room" id="room" class="nice-select form-control">
                                    <option value="">1 Room</option>
                                    <option value="">2 Room</option>
                                </select>
                            </div>
                            <div class="submit-btn | mt-4">
                                <button class="button" data-type="inverted">{{__('hero-section.form.check-availability')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero__slider owl-carousel owl-theme">
        <div class="slider-item set-bg-img" data-set-bg="./img/hero/hero-1.jpg">
        </div>
        <div class="slider-item set-bg-img" data-set-bg="./img/hero/hero-2.jpg">
        </div>
        <div class="slider-item set-bg-img" data-set-bg="./img/hero/hero-3.jpg">
        </div>
    </div>
</section>