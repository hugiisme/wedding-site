<template>
    <section
        data-section
        class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col bg-wedding-sage md:flex-row"
    >
        <div
            class="reveal reveal--image relative h-[min(55vh,22rem)] min-h-44 w-full shrink-0 overflow-hidden bg-wedding-sage md:h-screen md:min-h-0 md:w-1/2"
        >
            <img
                :src="coupleImage"
                alt="Cô dâu chú rể"
                class="h-full w-full object-cover object-[50%_33%] md:object-center"
            />
        </div>
        <div
            class="flex flex-1 flex-col justify-start bg-wedding-sage px-8 pt-8 pb-14 text-left md:justify-center md:px-14 md:py-20 lg:px-20"
        >
            <p class="reveal font-script text-xl text-wedding-brown-warm md:text-2xl hidden">
                Câu chuyện của chúng mình
            </p>
            <h2
                class="reveal mt-2 font-noto-thin text-4xl tracking-wider text-white md:text-5xl"
                lang="vi"
            >
                <span class="block">LỜI HỨA</span>
                <span class="block mt-1 pl-8 md:pl-10">TRỌN ĐỜI</span>
            </h2>
            <div
                class="reveal mt-6 h-0.5 w-28 bg-white/80 md:w-36"
                aria-hidden="true"
            />
            <p
                class="reveal mt-4 max-w-lg font-noto-thin text-lg leading-[1.8] text-white/90 md:text-xl"
                lang="vi"
            >
                Gặp nhau từ những năm tháng cấp ba,<br />
                Nhiều năm làm bạn, hai năm yêu,<br />
                Và hôm nay — chúng mình chọn nhau cho cả cuộc đời.
            </p>
        </div>
    </section>
</template>

<script setup>
import coupleImage from "../../elements/section_2.webp";
</script>
