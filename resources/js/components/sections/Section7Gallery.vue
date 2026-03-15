<template>
    <!-- Section 7: không dùng .reveal để tránh giật khi scroll vào -->
    <section
        data-section
        data-no-reveal
        ref="sectionRef"
        class="section-7 flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center overflow-hidden px-0 py-12"
    >
        <div class="filmstrip relative w-full">
            <!-- Mode auto: track chạy liên tục, loop vô tận bằng JS (không reset CSS nên không thấy "end") -->
            <template v-if="mode === 'auto'">
                <div class="relative w-full overflow-hidden py-6 md:py-8">
                    <div ref="trackRef" class="film-track flex">
                        <FilmStripCell
                            v-for="(url, idx) in displayImages"
                            :key="`auto-${idx}-${url}`"
                            :src="url"
                            :alt="`Ảnh ${(idx % limitedImages.length) + 1}`"
                            :cell-width="cellWidth"
                            cell-height="18rem"
                            gap-width="20px"
                            :show-gap="idx < displayImages.length - 1"
                            @click="openLightbox(url)"
                        />
                    </div>
                </div>
            </template>
            <!-- Mode manual: 1 ảnh, arrow + pagination, 3s tự đổi -->
            <template v-else>
                <div class="relative mx-auto w-full max-w-4xl px-4 py-6 md:py-8">
                    <button
                        type="button"
                        aria-label="Ảnh trước"
                        class="absolute left-0 top-1/2 z-10 -translate-y-1/2 rounded-full p-2 text-wedding-brown-warm transition hover:bg-black/10"
                        @click="prev"
                    >
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="mx-auto flex max-w-xl justify-center px-12">
                        <FilmStripCell
                            v-if="currentSrc"
                            :src="currentSrc"
                            :alt="`Ảnh ${currentIndex + 1}`"
                            cell-width="100%"
                            cell-height="18rem"
                            gap-width="0"
                            :show-gap="false"
                            @click="openLightbox(currentSrc)"
                        />
                    </div>
                    <button
                        type="button"
                        aria-label="Ảnh sau"
                        class="absolute right-0 top-1/2 z-10 -translate-y-1/2 rounded-full p-2 text-wedding-brown-warm transition hover:bg-black/10"
                        @click="next"
                    >
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div class="mt-6 flex flex-wrap justify-center gap-2 px-2">
                        <button
                            v-for="(_, i) in limitedImages"
                            :key="i"
                            type="button"
                            :aria-label="`Ảnh ${i + 1}`"
                            :class="[
                                'h-2 rounded-full transition-all',
                                i === currentIndex ? 'w-6 bg-wedding-gold-warm' : 'w-2 bg-wedding-brown-warm/40 hover:bg-wedding-brown-warm/70',
                            ]"
                            @click="goTo(i)"
                        />
                    </div>
                </div>
            </template>
        </div>
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxUrl"
                    class="fixed inset-0 z-100 flex items-center justify-center bg-black/90 p-4"
                    @click.self="lightboxUrl = null"
                >
                    <button
                        type="button"
                        aria-label="Đóng"
                        class="absolute right-4 top-4 rounded-full p-2 text-white hover:bg-white/20"
                        @click="lightboxUrl = null"
                    >
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <img :src="lightboxUrl" alt="Xem ảnh" class="max-h-full max-w-full object-contain" @click.stop />
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import FilmStripCell from "./FilmStripCell.vue";

// auto = ảnh tự động chạy liên tục từ phải sang trái
// manual = pagination, 3s tự đổi ảnh, có arrow sang ảnh khác
const mode = ref("auto"); // 'manual'/'auto'

const filmModules = import.meta.glob("../../elements/film/*.{jpg,jpeg,png,webp}", { eager: true, as: "url" });
const fallbackModules = import.meta.glob("../../elements/*.{jpg,jpeg,png,webp}", { eager: true, as: "url" });
const filmUrls = Object.values(filmModules);
const fallbackUrls = Object.values(fallbackModules).filter(
    (url) => !url.includes("film-strip-graphic-element-frame")
);
const images = ref(
    filmUrls.length > 0
        ? filmUrls
        : fallbackUrls.length > 0
          ? fallbackUrls
          : [
                "https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80",
                "https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=600&q=80",
                "https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=600&q=80",
            ]
);

// Giới hạn số ảnh để tránh quá nhiều DOM (auto) và quá nhiều dots (manual)
const MAX_IMAGES_MANUAL = 20;
const MAX_CELLS_AUTO = 12; // số ảnh dùng cho auto (sẽ nhân đôi = 24 ô)
const limitedImages = computed(() => images.value.slice(0, MAX_IMAGES_MANUAL));
const displayImages = computed(() => {
    const list = images.value.slice(0, MAX_CELLS_AUTO);
    return list.length ? [...list, ...list] : [];
});
const cellWidth = "280px";
const currentIndex = ref(0);
const currentSrc = computed(() => limitedImages.value[currentIndex.value] ?? null);
const lightboxUrl = ref(null);

const sectionRef = ref(null);
const trackRef = ref(null);
const sectionInView = ref(false);
let sectionInViewTimeout = null;
const ANIM_START_DELAY_MS = 280;
const PX_PER_FRAME = 1.2;
let animRunning = false;
let rafId = null;
let scrollPosition = 0;
let segmentWidthPx = 0;
let warmedUp = false;

function next() {
    const n = limitedImages.value.length;
    if (!n) return;
    currentIndex.value = (currentIndex.value + 1) % n;
}
function prev() {
    const n = limitedImages.value.length;
    if (!n) return;
    currentIndex.value = (currentIndex.value - 1 + n) % n;
}
function goTo(i) {
    currentIndex.value = Math.max(0, Math.min(i, limitedImages.value.length - 1));
}
function openLightbox(url) {
    lightboxUrl.value = url;
}

let manualInterval;
watch(
    mode,
    (m) => {
        if (manualInterval) clearInterval(manualInterval);
        if (m === "manual") {
            manualInterval = setInterval(next, 3000);
            stopAnimation();
        } else if (m === "auto" && sectionInView.value) {
            if (animStartDelayTimer) clearTimeout(animStartDelayTimer);
            animStartDelayTimer = setTimeout(startAnimation, ANIM_START_DELAY_MS);
        }
    },
    { immediate: true }
);
onUnmounted(() => clearInterval(manualInterval));

let sectionIo = null;
let preloadIo = null;
let animStartDelayTimer = null;

function tick() {
    const track = trackRef.value;
    if (!track || !sectionInView.value || mode.value !== "auto") return;
    if (segmentWidthPx <= 0) {
        segmentWidthPx = track.scrollWidth / 2;
    }
    scrollPosition -= PX_PER_FRAME;
    if (scrollPosition <= -segmentWidthPx) scrollPosition += segmentWidthPx;
    track.style.transform = `translate3d(${scrollPosition}px, 0, 0)`;
    rafId = requestAnimationFrame(tick);
}

function warmupTrack() {
    const track = trackRef.value;
    if (!track || warmedUp) return;
    segmentWidthPx = track.scrollWidth / 2;
    warmedUp = true;
}

function startAnimation() {
    if (animRunning || mode.value !== "auto") return;
    const track = trackRef.value;
    if (!track) return;
    if (segmentWidthPx <= 0) segmentWidthPx = track.scrollWidth / 2;
    animRunning = true;
    rafId = requestAnimationFrame(tick);
}

function stopAnimation() {
    animRunning = false;
    if (rafId != null) {
        cancelAnimationFrame(rafId);
        rafId = null;
    }
}

onMounted(() => {
    const el = sectionRef.value;
    if (!el) return;
    sectionIo = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    if (sectionInViewTimeout) clearTimeout(sectionInViewTimeout);
                    sectionInView.value = true;
                    requestAnimationFrame(() => {
                        requestAnimationFrame(warmupTrack);
                    });
                    if (animStartDelayTimer) clearTimeout(animStartDelayTimer);
                    animStartDelayTimer = setTimeout(startAnimation, ANIM_START_DELAY_MS);
                } else {
                    sectionInViewTimeout = setTimeout(() => {
                        sectionInView.value = false;
                        stopAnimation();
                    }, 250);
                }
            });
        },
        { root: null, rootMargin: "0px", threshold: 0.1 }
    );
    preloadIo = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) requestAnimationFrame(warmupTrack);
            });
        },
        { root: null, rootMargin: "150px 0px", threshold: 0 }
    );
    preloadIo.observe(el);
    sectionIo.observe(el);
});
onUnmounted(() => {
    if (sectionInViewTimeout) clearTimeout(sectionInViewTimeout);
    if (animStartDelayTimer) clearTimeout(animStartDelayTimer);
    stopAnimation();
    sectionIo?.disconnect();
    preloadIo?.disconnect();
});
</script>

<style scoped>
.section-7 {
    background-color: #4a3f35;
}
.film-track {
    will-change: transform;
    backface-visibility: hidden;
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
