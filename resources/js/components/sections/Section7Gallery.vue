<template>
    <!-- Section 7: không dùng .reveal để tránh giật khi scroll vào -->
    <section
        data-section
        data-no-reveal
        ref="sectionRef"
        class="section-7 flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center overflow-hidden px-0 py-12"
    >
        <div class="filmstrip relative w-full">
            <div class="relative w-full overflow-hidden py-6 md:py-8">
                <div ref="trackRef" class="film-track flex">
                    <FilmStripCell
                        v-for="(url, idx) in displayImages"
                        :key="`auto-${idx}-${url}`"
                        :src="url"
                        :alt="`Ảnh ${(idx % displayImageCount) + 1}`"
                        :cell-width="cellWidth"
                        cell-height="18rem"
                        gap-width="20px"
                        :show-gap="idx < displayImages.length - 1"
                        @click="openLightbox(url)"
                    />
                </div>
            </div>
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
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import FilmStripCell from "./FilmStripCell.vue";

const DEBUG_S7 = false;
function log(...args) {
    if (DEBUG_S7) console.log("[Section7]", ...args);
}

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

const MAX_CELLS_AUTO = 12;
const displayImages = computed(() => {
    const list = images.value.slice(0, MAX_CELLS_AUTO);
    return list.length ? [...list, ...list] : [];
});
const displayImageCount = computed(() => Math.min(MAX_CELLS_AUTO, images.value.length) || 1);
const cellWidth = "280px";
const lightboxUrl = ref(null);

const sectionRef = ref(null);
const trackRef = ref(null);
const sectionInView = ref(false);
let sectionInViewTimeout = null;
const ANIM_START_DELAY_MS = 280;
const PX_PER_SECOND = 72;
const DT_CAP_MS = 80;
let animRunning = false;
let rafId = null;
let scrollPosition = 0;
let segmentWidthPx = 0;
let lastTickTime = 0;
let warmedUp = false;

function openLightbox(url) {
    lightboxUrl.value = url;
}

let sectionIo = null;
let preloadIo = null;
let animStartDelayTimer = null;

let tickLogCount = 0;
function tick(now) {
    const track = trackRef.value;
    if (!track || !sectionInView.value) return;
    if (segmentWidthPx <= 0) {
        const before = performance.now();
        segmentWidthPx = track.scrollWidth / 2;
        if (DEBUG_S7 && tickLogCount < 2) log("tick: tính segmentWidthPx trong RAF =", segmentWidthPx, "reflow mất", (performance.now() - before).toFixed(2), "ms");
        tickLogCount++;
    }
    const dtSec = lastTickTime ? Math.min((now - lastTickTime) / 1000, DT_CAP_MS / 1000) : 1 / 60;
    lastTickTime = now;
    scrollPosition -= PX_PER_SECOND * dtSec;
    if (scrollPosition <= -segmentWidthPx) scrollPosition += segmentWidthPx;
    track.style.transform = `translate3d(${scrollPosition}px, 0, 0)`;
    rafId = requestAnimationFrame(tick);
}

function warmupTrack() {
    const track = trackRef.value;
    const t0 = performance.now();
    if (!track) {
        log("warmupTrack: trackRef chưa có, bỏ qua");
        return;
    }
    if (warmedUp) {
        log("warmupTrack: đã warmup rồi, segmentWidthPx =", segmentWidthPx);
        return;
    }
    segmentWidthPx = track.scrollWidth / 2;
    warmedUp = true;
    const t1 = performance.now();
    log("warmupTrack: scrollWidth =", track.scrollWidth, "segmentWidthPx =", segmentWidthPx, "reflow mất", (t1 - t0).toFixed(2), "ms");
}

function startAnimation() {
    const t0 = performance.now();
    if (animRunning) return;
    const track = trackRef.value;
    if (!track) {
        log("startAnimation: trackRef null");
        return;
    }
    if (segmentWidthPx <= 0) {
        segmentWidthPx = track.scrollWidth / 2;
        log("startAnimation: tính segmentWidthPx lần đầu =", segmentWidthPx, "(có thể gây reflow khi scroll vào)");
    }
    animRunning = true;
    lastTickTime = 0;
    rafId = requestAnimationFrame(tick);
    log("startAnimation: bắt đầu sau", (performance.now() - t0).toFixed(2), "ms");
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
    log("onMounted: sectionRef =", !!el, "trackRef =", !!trackRef.value);
    if (!el) return;

    // Tính toán ngay từ đầu khi load trang: measure track sau khi DOM sẵn sàng (nextTick + rAF)
    nextTick(() => {
        requestAnimationFrame(() => {
            const track = trackRef.value;
            log("early warmup (sau nextTick+rAF): trackRef =", !!track);
            if (track) {
                warmupTrack();
                log("early warmup: segmentWidthPx đã set =", segmentWidthPx);
            } else {
                log("early warmup: track chưa có (Vue chưa render?), sẽ warmup khi section vào view");
            }
        });
    });

    sectionIo = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    log("section IN viewport");
                    if (sectionInViewTimeout) clearTimeout(sectionInViewTimeout);
                    sectionInView.value = true;
                    const scheduleWarmup = () => {
                        if (typeof requestIdleCallback !== "undefined") {
                            requestIdleCallback(() => warmupTrack(), { timeout: 80 });
                        } else {
                            setTimeout(warmupTrack, 50);
                        }
                    };
                    scheduleWarmup();
                    if (animStartDelayTimer) clearTimeout(animStartDelayTimer);
                    animStartDelayTimer = setTimeout(startAnimation, ANIM_START_DELAY_MS);
                } else {
                    log("section OUT viewport");
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
                if (entry.isIntersecting) {
                    log("preload: section gần viewport (280px), gọi warmupTrack");
                    requestAnimationFrame(warmupTrack);
                }
            });
        },
        { root: null, rootMargin: "280px 0px", threshold: 0 }
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
    content-visibility: auto;
    contain-intrinsic-size: auto 100vh;
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
