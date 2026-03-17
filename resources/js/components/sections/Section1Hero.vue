<template>
    <section
        data-section
        class="relative flex min-h-screen w-full shrink-0 snap-start snap-always items-center justify-center overflow-hidden bg-wedding-brown"
    >
        <video
            class="absolute inset-0 h-full w-full object-cover opacity-60"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            :poster="heroPoster"
            :src="heroVideo"
        />
        <div
            class="absolute inset-0 bg-gradient-to-b from-wedding-brown/50 via-transparent to-wedding-brown/60"
            style="background-color: rgba(74, 63, 53, 0.4)"
        />
        <div
            class="relative z-10 flex flex-col items-center justify-center px-4 text-center"
        >
            <p
                class="font-sans text-wedding-cream-warm/95 text-sm tracking-[0.3em] md:text-base"
            >
                2026. APRIL 03.
            </p>
            <h1
                class="mt-2 font-serif text-4xl font-bold tracking-wide text-wedding-gold-warm md:text-5xl lg:text-6xl"
            >
                THANH & THU
            </h1>
            <p class="mt-2 font-script text-2xl text-wedding-cream-warm md:text-3xl">
                are getting married
            </p>
        </div>
    </section>
</template>

<script setup>
import heroVideo from "../../elements/202603160026.mp4";
import heroPoster from "../../elements/poster.png";
</script>
