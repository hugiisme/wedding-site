<template>
    <section
        data-section
        class="relative flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center bg-wedding-sage px-4 py-12 md:px-10 md:py-24"
    >
        <div class="absolute inset-0 opacity-30 section4-pattern" />
        <p
            class="reveal relative z-10 max-w-xs text-center font-script text-2xl text-wedding-brown-warm md:max-w-3xl md:text-5xl lg:text-6xl"
            lang="vi"
        >
            Năm ấy chung một bầu trời Chuyên Sư phạm, chỉ còn ...
        </p>
            <div
                class="reveal relative z-10 mt-8 flex flex-wrap items-center justify-center gap-x-4 gap-y-4 md:mt-16 md:flex-nowrap md:gap-8"
            >
            <div class="reveal flex flex-col items-center">
                <span
                    class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-brown-warm md:text-7xl lg:text-8xl"
                    >{{ pad(days) }}</span
                >
                <span
                    class="mt-1 text-sm font-medium uppercase tracking-wider text-wedding-brown-warm/90 md:text-lg"
                    >Ngày</span
                >
            </div>
            <span
                class="countdown-sep font-mono text-3xl text-wedding-brown-warm/80 md:text-6xl"
                >:</span
            >
            <div class="reveal flex flex-col items-center">
                <span
                    class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-brown-warm md:text-7xl lg:text-8xl"
                    >{{ pad(hours) }}</span
                >
                <span
                    class="mt-1 text-sm font-medium uppercase tracking-wider text-wedding-brown-warm/90 md:text-lg"
                    >Giờ</span
                >
            </div>
            <span
                class="countdown-sep font-mono text-3xl text-wedding-brown-warm/80 md:text-6xl"
                >:</span
            >
            <div class="reveal flex flex-col items-center">
                <span
                    class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-brown-warm md:text-7xl lg:text-8xl"
                    >{{ pad(minutes) }}</span
                >
                <span
                    class="mt-1 text-sm font-medium uppercase tracking-wider text-wedding-brown-warm/90 md:text-lg"
                    >Phút</span
                >
            </div>
            <span
                class="countdown-sep font-mono text-3xl text-wedding-brown-warm/80 md:text-6xl"
                >:</span
            >
            <div class="reveal flex flex-col items-center">
                <span
                    class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-brown-warm md:text-7xl lg:text-8xl"
                    >{{ pad(seconds) }}</span
                >
                <span
                    class="mt-1 text-sm font-medium uppercase tracking-wider text-wedding-brown-warm/90 md:text-lg"
                    >Giây</span
                >
            </div>
        </div>
        <p
            class="reveal relative z-10 mt-8 max-w-xs text-center font-script text-2xl text-wedding-brown-warm md:mt-16 md:max-w-3xl md:text-5xl lg:text-6xl"
            lang="vi"
        >
            sẽ chung một mái nhà!
        </p>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const targetDate = new Date("2026-04-03T00:00:00");
const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);

function pad(n) {
    return String(Math.max(0, n)).padStart(2, "0");
}

function tick() {
    const now = new Date();
    const diff = targetDate - now;
    if (diff <= 0) {
        days.value = 0;
        hours.value = 0;
        minutes.value = 0;
        seconds.value = 0;
        return;
    }
    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((diff % (1000 * 60)) / 1000);
    days.value = d;
    hours.value = h;
    minutes.value = m;
    seconds.value = s;
}

let interval;
onMounted(() => {
    tick();
    interval = setInterval(tick, 1000);
});
onUnmounted(() => clearInterval(interval));
</script>

<style scoped>
.section4-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5 L35 20 L50 25 L35 30 L30 45 L25 30 L10 25 L25 20 Z' fill='%23f2f1eb' fill-opacity='0.3'/%3E%3C/svg%3E");
}
</style>
