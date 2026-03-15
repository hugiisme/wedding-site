<template>
  <section
    data-section
    class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center bg-wedding-brown px-4 py-12"
  >
    <div class="filmstrip relative w-full max-w-4xl">
      <!-- Film strip top edge (sprocket holes) -->
      <div class="film-edge absolute left-0 right-0 top-0 h-4 bg-black/80" aria-hidden="true">
        <div class="flex justify-around gap-1 px-2 py-0.5">
          <span v-for="n in 24" :key="'t'+n" class="h-2 w-2 rounded-sm bg-wedding-brown/60" />
        </div>
      </div>
      <!-- Carousel -->
      <div class="relative flex items-center justify-center gap-2 px-4 pt-8 pb-4 md:gap-4 md:pt-10 md:pb-6">
        <button
          type="button"
          aria-label="Ảnh trước"
          class="shrink-0 rounded-full p-2 text-wedding-cream transition hover:bg-white/10"
          @click="prev"
        >
          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <div class="relative flex flex-1 items-center justify-center overflow-hidden">
          <div
            class="flex items-center justify-center gap-2 md:gap-4"
          >
            <div
              v-for="(img, i) in visibleTriple"
              :key="`${currentIndex}-${i}-${img}`"
              class="shrink-0 cursor-pointer transition-all duration-500 ease-out"
              :class="i === 1 ? 'scale-110 opacity-100 md:w-72' : 'w-20 opacity-70 md:w-40'"
              @click="i === 1 ? openLightbox(images[currentIndex]) : (i === 0 ? prev() : next())"
            >
              <img
                :src="img"
                :alt="`Ảnh ${i + 1}`"
                class="h-40 w-full object-cover object-center rounded md:h-56"
              />
            </div>
          </div>
        </div>
        <button
          type="button"
          aria-label="Ảnh sau"
          class="shrink-0 rounded-full p-2 text-wedding-cream transition hover:bg-white/10"
          @click="next"
        >
          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
      </div>
      <!-- Dots -->
      <div class="flex justify-center gap-2 pb-4">
        <button
          v-for="(_, i) in images"
          :key="i"
          type="button"
          :aria-label="`Slide ${i + 1}`"
          :class="[
            'h-2 w-2 rounded-full transition-all',
            i === currentIndex ? 'w-6 bg-wedding-gold' : 'bg-wedding-cream/50 hover:bg-wedding-cream/80'
          ]"
          @click="goTo(i)"
        />
      </div>
      <!-- Film strip bottom edge -->
      <div class="film-edge absolute bottom-0 left-0 right-0 h-4 bg-black/80" aria-hidden="true">
        <div class="flex justify-around gap-1 px-2 py-0.5">
          <span v-for="n in 24" :key="'b'+n" class="h-2 w-2 rounded-sm bg-wedding-brown/60" />
        </div>
      </div>
    </div>
    <!-- Lightbox -->
    <Teleport to="body">
      <Transition name="lightbox">
        <div
          v-if="lightboxUrl"
          class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4"
          @click.self="lightboxUrl = null"
        >
          <button
            type="button"
            aria-label="Đóng"
            class="absolute right-4 top-4 rounded-full p-2 text-white hover:bg-white/20"
            @click="lightboxUrl = null"
          >
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          <img :src="lightboxUrl" alt="Xem ảnh" class="max-h-full max-w-full object-contain" @click.stop />
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const images = ref([
  'https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80',
  'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=600&q=80',
  'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=600&q=80',
  'https://images.unsplash.com/photo-1529634806799-e8f648f3e0b2?w=600&q=80',
  'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=600&q=80'
]);

const currentIndex = ref(0);
const lightboxUrl = ref(null);

const visibleTriple = computed(() => {
  const n = images.value.length;
  const prev = (currentIndex.value - 1 + n) % n;
  const next = (currentIndex.value + 1) % n;
  return [
    images.value[prev],
    images.value[currentIndex.value],
    images.value[next]
  ];
});

function next() {
  currentIndex.value = (currentIndex.value + 1) % images.value.length;
}
function prev() {
  currentIndex.value = (currentIndex.value - 1 + images.value.length) % images.value.length;
}
function goTo(i) {
  currentIndex.value = i;
}
function openLightbox(url) {
  lightboxUrl.value = url;
}

let autoInterval;
onMounted(() => {
  autoInterval = setInterval(next, 3000);
});
onUnmounted(() => clearInterval(autoInterval));
</script>

<style scoped>
.film-edge {
  border-radius: 2px;
}
.lightbox-enter-active,
.lightbox-leave-active {
  transition: opacity 0.2s ease;
}
.lightbox-enter-from,
.lightbox-leave-to {
  opacity: 0;
}
</style>
