@if(isset($cars))
    @foreach ($cars as $car)
        @php
        $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
        $carImageUrl = asset('images/no_image.svg');
        foreach ($thumbnails as $thumbnail) {
            $image = Helper::getPhotoById($thumbnail->imageId);
            $carImageUrl = $image->url;
        }
        $modifiedImgUrl = $carImageUrl;
        @endphp

        <!-- Your existing car item content goes here -->
        <div class="booking_img w-1/2 px-4 md:w-full mb-7" data-car-id="{{ $car->id }}" data-href="{{ route('partner.car.view', $car->id) }}">

                <!-- Your existing car item content goes here -->
                <div class="relative px-5 py-4 bg-white rounded-[9px]">
                    <div class="flex py-3">
                        <div class="w-2/5">
                            <div class="flex items-center justify-center ">
                                <img class="block w-[240px] h-[120px] m-auto object-contain "
                                src="{{ $modifiedImgUrl }}"
                                alt="{{ $car->name }} car">

                            </div>

                        </div>
                        <div class="w-3/5">
                            <div class="h-full xl:pl-4 xl:pr-4 pl-10 pr-6">
                                <div class="flex flex-col ">
                                    <h3
                                        class="text-base font-medium leading-4 text-[#2B2B2B] pb-1 capitalize">
                                        {{ $car->name }}</h3>
                                    <p class="text-sm font-normal leading-none text-[#2B2B2B] pb-1.5 last:pb-0 uppercase">
                                        {{ $car->registration_number }}</p>

                                    <div class="block py-4">
                                        <p
                                            class="text-sm font-medium leading-none text-[#272522]">
                                            ₹ {{ number_format($car->price, 2) }} per day</p>
                                    </div>
                                    <div class="w-full">
                                        <a class="inline-block links_item_cta" href="{{ route('partner.car.view', $car->id) }}">
                                            <div class="flex items-center">
                                                <div class="w-auto">
                                                    <span
                                                        class="text-[13px] font-normal leading-none text-[#342B18]">View
                                                        Details</span>
                                                </div>
                                                <div class="pl-4 min-w-12">
                                                    <img src="{{ asset('images/right_arrow_cars_page.svg') }}"
                                                        alt="arrow icon">
                                                </div>

                                            </div>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4">
                        <a href="javascript:void(0)"  class=" delete_btn flex items-center justify-center w-5 h-5" data-delete-url="{{ route('partner.car.delete', $car->id) }}">
                            <img src="{{ asset('images/delete_icon_red.svg') }}"
                                class="delete_icon" alt="delete_icon">
                        </a>
                    </div>
                    <div class="absolute bottom-4 right-4">
                        <a href="{{ route('partner.car.edit', $car->id) }}"  class="links_item_cta edit_btn flex items-center justify-center w-5 h-5" >
                            <img src="{{ asset('images/edit_icon_black.svg') }}" class="edit_icon" alt="edit_icon">
                        </a>
                    </div>
                </div>
        </div>

    @endforeach
@endif




