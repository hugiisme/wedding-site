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
                        <svg
                            class="h-8 w-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                    <img
                        :src="lightboxUrl"
                        alt="Xem ảnh"
                        class="max-h-full max-w-full object-contain"
                        @click.stop
                    />
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import FilmStripCell from "./FilmStripCell.vue";

/**
 * Debug Section 7: set true để xem log warmup, measureTrack, startAnimation, tick-skip, lightbox.
 * Sau khi sửa xong nhớ set lại false.
 */
const DEBUG_S7 = false;
function log(...args) {
    if (DEBUG_S7) console.log("[Section7]", ...args);
}

const filmModules = import.meta.glob(
    "../../elements/film/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);
const fallbackModules = import.meta.glob(
    "../../elements/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);
const filmUrls = Object.values(filmModules);
const fallbackUrls = Object.values(fallbackModules).filter(
    (url) => !url.includes("film-strip-graphic-element-frame"),
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
            ],
);

const MAX_CELLS_AUTO = 12;
const displayImages = computed(() => {
    const list = images.value.slice(0, MAX_CELLS_AUTO);
    return list.length ? [...list, ...list] : [];
});
const displayImageCount = computed(
    () => Math.min(MAX_CELLS_AUTO, images.value.length) || 1,
);
const cellWidth = "280px";
const lightboxUrl = ref(null);

const sectionRef = ref(null);
const trackRef = ref(null);
const sectionInView = ref(false);
let sectionInViewTimeout = null;
const ANIM_START_DELAY_MS = 320;
const PX_PER_SECOND = 72;
const DT_CAP_MS = 80;
let animRunning = false;
let rafId = null;
let scrollPosition = 0;
let segmentWidthPx = 0;
let lastTickTime = 0;
let warmedUp = false;
/** Đã schedule startAnimation khi track ready (ResizeObserver), tránh gọi trùng */
let startScheduled = false;

function openLightbox(url) {
    // Trì hoãn cập nhật DOM lightbox sang frame tiếp theo để tránh reflow cùng lúc với film strip → giảm giật
    requestAnimationFrame(() => {
        lightboxUrl.value = url;
        if (DEBUG_S7) log("lightbox open (deferred to rAF):", url?.slice(-30));
    });
}

let sectionIo = null;
let preloadIo = null;
let animStartDelayTimer = null;
let resizeObserver = null;

/**
 * tick() không bao giờ đọc track.scrollWidth — reflow trong RAF loop gây giật khi scroll vào section.
 * segmentWidthPx phải luôn được set trước (warmup / ResizeObserver).
 */
function tick(now) {
    const track = trackRef.value;
    if (!track || !sectionInView.value) return;
    if (segmentWidthPx <= 0) {
        if (DEBUG_S7) log("tick: segmentWidthPx chưa có, skip frame (tránh reflow trong tick)");
        rafId = requestAnimationFrame(tick);
        return;
    }
    const dtSec = lastTickTime
        ? Math.min((now - lastTickTime) / 1000, DT_CAP_MS / 1000)
        : 1 / 60;
    lastTickTime = now;
    scrollPosition -= PX_PER_SECOND * dtSec;
    if (scrollPosition <= -segmentWidthPx) scrollPosition += segmentWidthPx;
    track.style.transform = `translate3d(${scrollPosition}px, 0, 0)`;
    rafId = requestAnimationFrame(tick);
}

function measureTrack() {
    const track = trackRef.value;
    if (!track) return false;
    const w = track.scrollWidth;
    if (w <= 0) return false;
    const t0 = performance.now();
    segmentWidthPx = w / 2;
    warmedUp = true;
    if (DEBUG_S7) log("measureTrack: scrollWidth =", w, "segmentWidthPx =", segmentWidthPx, "reflow ~", (performance.now() - t0).toFixed(2), "ms");
    return true;
}

function warmupTrack() {
    const track = trackRef.value;
    if (!track) {
        log("warmupTrack: trackRef chưa có, bỏ qua");
        return;
    }
    if (warmedUp) {
        log("warmupTrack: đã warmup, segmentWidthPx =", segmentWidthPx);
        return;
    }
    measureTrack();
}

/**
 * Chỉ start khi segmentWidthPx > 0. Nếu chưa có thì ResizeObserver sẽ gọi lại khi track ready.
 */
function startAnimation() {
    if (animRunning) return;
    const track = trackRef.value;
    if (!track) {
        log("startAnimation: trackRef null");
        return;
    }
    if (segmentWidthPx <= 0) {
        if (measureTrack()) {
            if (DEBUG_S7) log("startAnimation: measure ngay trong start (lần đầu)");
        } else {
            log("startAnimation: segmentWidthPx = 0, chờ ResizeObserver (tránh reflow khi scroll vào)");
            startScheduled = true;
            return;
        }
    }
    startScheduled = false;
    animRunning = true;
    lastTickTime = 0;
    rafId = requestAnimationFrame(tick);
    log("startAnimation: bắt đầu, segmentWidthPx =", segmentWidthPx);
}

function stopAnimation() {
    animRunning = false;
    if (rafId != null) {
        cancelAnimationFrame(rafId);
        rafId = null;
    }
}

function onTrackReady() {
    if (!sectionInView.value || animRunning) return;
    if (segmentWidthPx <= 0 && !measureTrack()) return;
    if (startScheduled) {
        startScheduled = false;
        startAnimation();
    }
}

onMounted(() => {
    const el = sectionRef.value;
    log("onMounted: sectionRef =", !!el, "trackRef =", !!trackRef.value);
    if (!el) return;

    nextTick(() => {
        requestAnimationFrame(() => {
            const track = trackRef.value;
            log("early warmup (nextTick+rAF): trackRef =", !!track);
            if (track) {
                warmupTrack();
                log("early warmup: segmentWidthPx =", segmentWidthPx);
            } else {
                log("early warmup: track chưa có (Vue chưa render), warmup khi section vào view / ResizeObserver");
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
                    startScheduled = true;
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
        { root: null, rootMargin: "0px", threshold: 0.1 },
    );
    preloadIo = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    log("preload: section gần viewport 280px, warmupTrack");
                    requestAnimationFrame(warmupTrack);
                }
            });
        },
        { root: null, rootMargin: "280px 0px", threshold: 0 },
    );
    preloadIo.observe(el);
    sectionIo.observe(el);
});

watch(
    trackRef,
    (track) => {
        if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
        }
        if (!track) return;
        resizeObserver = new ResizeObserver(() => {
            if (measureTrack()) onTrackReady();
        });
        resizeObserver.observe(track);
        if (DEBUG_S7) log("ResizeObserver attached to track");
    },
    { immediate: true },
);

onUnmounted(() => {
    if (sectionInViewTimeout) clearTimeout(sectionInViewTimeout);
    if (animStartDelayTimer) clearTimeout(animStartDelayTimer);
    stopAnimation();
    resizeObserver?.disconnect();
    sectionIo?.disconnect();
    preloadIo?.disconnect();
});
</script>

<style scoped>
.section-7 {
    background-color: #4a3f35;
    /* Không dùng content-visibility: auto để tránh layout “thình lình” khi scroll tới → giật snap */
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
