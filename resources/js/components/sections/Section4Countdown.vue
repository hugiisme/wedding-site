<template>
  <section
    data-section
    class="relative flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center bg-wedding-sage px-4 py-16"
  >
    <div
      class="absolute inset-0 opacity-30"
      style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M30 5 L35 20 L50 25 L35 30 L30 45 L25 30 L10 25 L25 20 Z\' fill=\'%23F3F4ED\' fill-opacity=\'0.3\'/%3E%3C/svg%3E');"
    />
    <p class="reveal relative z-10 font-script text-2xl text-wedding-cream md:text-3xl">
      Năm ấy chung một bầu trời Chuyên Sư phạm, chỉ còn ...
    </p>
    <div class="reveal relative z-10 mt-8 flex flex-wrap items-center justify-center gap-2 md:gap-4">
      <div class="flex flex-col items-center">
        <span class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-cream md:text-5xl lg:text-6xl">{{ pad(days) }}</span>
        <span class="mt-1 text-xs uppercase tracking-wider text-wedding-cream/90 md:text-sm">Ngày</span>
      </div>
      <span class="countdown-sep font-mono text-3xl text-wedding-cream/80 md:text-4xl">:</span>
      <div class="flex flex-col items-center">
        <span class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-cream md:text-5xl lg:text-6xl">{{ pad(hours) }}</span>
        <span class="mt-1 text-xs uppercase tracking-wider text-wedding-cream/90 md:text-sm">Giờ</span>
      </div>
      <span class="countdown-sep font-mono text-3xl text-wedding-cream/80 md:text-4xl">:</span>
      <div class="flex flex-col items-center">
        <span class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-cream md:text-5xl lg:text-6xl">{{ pad(minutes) }}</span>
        <span class="mt-1 text-xs uppercase tracking-wider text-wedding-cream/90 md:text-sm">Phút</span>
      </div>
      <span class="countdown-sep font-mono text-3xl text-wedding-cream/80 md:text-4xl">:</span>
      <div class="flex flex-col items-center">
        <span class="countdown-digit font-mono text-4xl font-bold tracking-tight text-wedding-cream md:text-5xl lg:text-6xl">{{ pad(seconds) }}</span>
        <span class="mt-1 text-xs uppercase tracking-wider text-wedding-cream/90 md:text-sm">Giây</span>
      </div>
    </div>
    <p class="reveal relative z-10 mt-8 font-script text-2xl text-wedding-cream md:text-3xl">
      sẽ chung một mái nhà!
    </p>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const targetDate = new Date('2026-04-03T00:00:00');
const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);

function pad(n) {
  return String(Math.max(0, n)).padStart(2, '0');
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
