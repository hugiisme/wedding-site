<template>
    <section
        data-section
        class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col bg-wedding-sage md:flex-row"
    >
        <div
            class="reveal reveal--image relative h-[min(42vh,16rem)] min-h-44 w-full shrink-0 overflow-hidden bg-wedding-sage md:h-screen md:min-h-0 md:w-1/2"
        >
            <img
                :src="coupleImage"
                alt="Cô dâu chú rể"
                class="h-full w-full object-cover object-[50%_33%] md:object-center"
            />
        </div>
        <div
            class="flex flex-1 flex-col justify-center bg-wedding-sage px-8 py-14 text-left md:px-14 md:py-20 lg:px-20"
        >
            <p class="reveal font-script text-xl text-wedding-brown-warm md:text-2xl">
                Câu chuyện của chúng mình
            </p>
            <h2
                class="reveal mt-2 font-dm-serif text-3xl font-semibold tracking-wider text-wedding-gold-warm md:text-4xl"
                lang="vi"
            >
                <span class="block">LỜI HỨA</span>
                <span class="block mt-1 pl-8 md:pl-10">TRỌN ĐỜI</span>
            </h2>
            <div
                class="reveal mt-6 h-0.5 w-28 bg-wedding-gold-warm/50 md:w-36"
                aria-hidden="true"
            />
            <p
                class="reveal mt-4 max-w-lg font-sans text-base leading-[1.8] text-wedding-brown-warm/95 md:text-lg"
                lang="vi"
            >
                Gặp nhau từ những năm tháng cấp ba,<br />
                Nhiều năm làm bạn, hai năm yêu,<br />
                Và hôm nay — chúng mình chọn nhau cho cả cuộc đời.
            </p>
            <p class="reveal mt-10 font-sans text-sm italic text-wedding-brown-warm/80 md:text-base">
                — Thành & Thư
            </p>
        </div>
    </section>
</template>

<script setup>
import coupleImage from "../../elements/TAH09800.jpg";
</script>
