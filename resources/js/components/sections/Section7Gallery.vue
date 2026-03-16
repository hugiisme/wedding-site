<template>
    <!-- Section 7: không dùng .reveal để tránh giật khi scroll vào -->
    <section
        data-section
        data-no-reveal
        class="section-7 flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center overflow-hidden bg-wedding-brown px-4 py-10 md:px-8 md:py-12"
    >
        <div class="w-full max-w-5xl">
            <h2
                class="text-center font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
            >
                Khoảnh khắc của chúng mình
            </h2>
            <p
                class="mt-3 text-center font-serif text-sm text-wedding-cream-warm/90 md:text-base"
                lang="vi"
            >
                Lướt qua những hình ảnh đáng nhớ trong hành trình của hai đứa
                mình.
            </p>
            <div class="mt-8">
                <div class="relative" ref="carouselRef">
                    <button
                        type="button"
                        class="absolute left-3 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 -translate-x-1/2 items-center justify-center rounded-full bg-wedding-gold-warm/95 text-wedding-brown shadow-lg hover:bg-wedding-gold-warm focus:outline-none focus-visible:ring-2 focus-visible:ring-wedding-gold-warm md:flex md:h-12 md:w-12"
                        @click="handlePrevClick"
                        aria-label="Xem ảnh trước"
                    >
                        <span class="inline-block rotate-180 text-xl md:text-2xl"
                            >&rsaquo;</span
                        >
                    </button>
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 translate-x-1/2 items-center justify-center rounded-full bg-wedding-gold-warm/95 text-wedding-brown shadow-lg hover:bg-wedding-gold-warm focus:outline-none focus-visible:ring-2 focus-visible:ring-wedding-gold-warm md:flex md:h-12 md:w-12"
                        @click="handleNextClick"
                        aria-label="Xem ảnh sau"
                    >
                        <span class="inline-block text-xl md:text-2xl"
                            >&rsaquo;</span
                        >
                    </button>
                    <div
                        class="overflow-hidden"
                    >
                        <div
                            ref="trackRef"
                            class="carousel-track flex transition-transform duration-700 ease-in-out"
                            :class="{ 'carousel-track--no-transition': isJumping }"
                            :style="trackStyle"
                            @transitionend="onTrackTransitionEnd"
                        >
                            <div
                                v-for="(slide, index) in slidesDoubled"
                                :key="`${slide.originalIndex}-${index}`"
                                class="shrink-0 basis-1/3 px-0"
                            >
                                <button
                                    type="button"
                                    class="group block w-full overflow-hidden rounded-xl transition-transform duration-500"
                                    :class="{
                                        'scale-105 opacity-100 shadow-xl':
                                            isCenter(index),
                                        'scale-75 opacity-70':
                                            !isCenter(index),
                                    }"
                                    @click="openLightbox(slide.originalIndex)"
                                >
                                    <div
                                        class="aspect-4/5 overflow-hidden bg-black/40"
                                    >
                                        <img
                                            :src="slide.src"
                                            :alt="slide.alt"
                                            decoding="async"
                                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        />
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxIndex !== null"
                    class="fixed inset-0 z-100 flex items-center justify-center bg-black/90 p-4"
                    @click.self="closeLightbox"
                >
                    <button
                        type="button"
                        aria-label="Đóng"
                        class="absolute right-4 top-4 rounded-full p-2 text-white hover:bg-white/20"
                        @click="closeLightbox"
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
                    <div class="relative flex max-h-full max-w-5xl flex-col">
                        <div class="relative flex-1">
                            <img
                                :src="currentLightboxSlide?.src"
                                :alt="currentLightboxSlide?.alt || 'Ảnh kỷ niệm'"
                                class="max-h-[70vh] w-full max-w-full object-contain"
                            />
                            <button
                                type="button"
                                class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white hover:bg-black/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-wedding-gold-warm"
                                @click.stop="prevLightbox"
                                aria-label="Ảnh trước"
                            >
                                <span class="inline-block rotate-180"
                                    >&rsaquo;</span
                                >
                            </button>
                            <button
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white hover:bg-black/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-wedding-gold-warm"
                                @click.stop="nextLightbox"
                                aria-label="Ảnh sau"
                            >
                                <span class="inline-block">&rsaquo;</span>
                            </button>
                        </div>
                        <p
                            v-if="currentLightboxSlide?.caption"
                            class="mt-4 text-center font-serif text-sm text-wedding-cream-warm/90"
                        >
                            {{ currentLightboxSlide.caption }}
                        </p>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from "vue";
import { usePreloadImages } from "../../composables/usePreloadImages";
import { allImageUrls } from "../../imageManifest";

const modules = import.meta.glob(
    "../../elements/film/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);
const fallbackModules = import.meta.glob(
    "../../elements/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);

const urls = Object.values(modules);
const allUrls = urls.length
    ? urls
    : Object.values(fallbackModules).filter(
          (url) => !url.includes("film-strip-graphic-element-frame"),
      );

const slides = allUrls.map((src, index) => ({
    id: index,
    src,
    alt: `Khoảnh khắc ${index + 1}`,
}));

const N = slides.length;
// Nhân đôi để nối vòng: [slide0..slideN-1, slide0..slideN-1]
const slidesDoubled = computed(() =>
    [...slides, ...slides].map((s, i) => ({
        ...s,
        originalIndex: i % N,
    })),
);
const totalDoubled = computed(() => slidesDoubled.value.length);

const currentIndex = ref(0);
const lightboxIndex = ref(null);
const carouselRef = ref(null);
const trackRef = ref(null);
const isJumping = ref(false);

// Preload thêm cho chắc chắn mọi ảnh trong carousel đã được cache
usePreloadImages(allImageUrls);

const trackStyle = computed(() => {
    const widthPerSlide = 100 / 3;
    const offsetPercent = (1 - currentIndex.value) * widthPerSlide;
    return {
        transform: `translate3d(${offsetPercent}%, 0, 0)`,
    };
});

function onTrackTransitionEnd() {
    const cur = currentIndex.value;
    const total = totalDoubled.value;
    if (total === 0) return;
    // Vừa next từ ảnh cuối → đang ở 0 (clone đầu): nhảy về N để tiếp tục next mượt
    if (cur === 0) {
        isJumping.value = true;
        currentIndex.value = N;
        nextTick(() => {
            isJumping.value = false;
        });
        return;
    }
    // Vừa prev từ ảnh đầu → đang ở 2N-1 (clone cuối): nhảy về N-1
    if (cur === total - 1) {
        isJumping.value = true;
        currentIndex.value = N - 1;
        nextTick(() => {
            isJumping.value = false;
        });
    }
}

function goPrev() {
    const total = totalDoubled.value;
    if (total === 0) return;
    if (currentIndex.value === 0) {
        currentIndex.value = total - 1; // sang clone cuối, sau đó transitionend sẽ reset về N-1
    } else {
        currentIndex.value = currentIndex.value - 1;
    }
}

function goNext() {
    const total = totalDoubled.value;
    if (total === 0) return;
    if (currentIndex.value === total - 1) {
        currentIndex.value = 0; // sang clone đầu, sau đó transitionend sẽ reset về N
    } else {
        currentIndex.value = currentIndex.value + 1;
    }
}

function openLightbox(index) {
    lightboxIndex.value = index;
}

function closeLightbox() {
    lightboxIndex.value = null;
}

const currentLightboxSlide = computed(() =>
    lightboxIndex.value != null ? slides[lightboxIndex.value] : null,
);

function isCenter(index) {
    return index === currentIndex.value;
}

function prevLightbox() {
    if (!slides.length) return;
    const idx =
        lightboxIndex.value == null
            ? 0
            : (lightboxIndex.value - 1 + slides.length) % slides.length;
    lightboxIndex.value = idx;
}

function nextLightbox() {
    if (!slides.length) return;
    const idx =
        lightboxIndex.value == null
            ? 0
            : (lightboxIndex.value + 1) % slides.length;
    lightboxIndex.value = idx;
}

// Autoplay 3s/slide, reset khi user tương tác
let autoplayTimer = null;

function startAutoplay() {
    if (typeof window === "undefined") return;
    stopAutoplay();
    autoplayTimer = window.setInterval(() => {
        goNext();
    }, 3500);
}

function stopAutoplay() {
    if (autoplayTimer != null && typeof window !== "undefined") {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

function restartAutoplay() {
    startAutoplay();
}

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});

// Swipe support trên mobile
let touchStartX = 0;
let touchStartY = 0;

function onTouchStart(e) {
    if (!e.touches || e.touches.length === 0) return;
    const t = e.touches[0];
    touchStartX = t.clientX;
    touchStartY = t.clientY;
}

function onTouchEnd(e) {
    if (!e.changedTouches || e.changedTouches.length === 0) return;
    const t = e.changedTouches[0];
    const deltaX = t.clientX - touchStartX;
    const deltaY = t.clientY - touchStartY;

    if (Math.abs(deltaX) < 30 || Math.abs(deltaX) < Math.abs(deltaY)) {
        return;
    }

    if (deltaX < 0) {
        goNext();
    } else {
        goPrev();
    }
    restartAutoplay();
}

onMounted(() => {
    const el = carouselRef.value;
    if (!el) return;
    el.addEventListener("touchstart", onTouchStart, { passive: true });
    el.addEventListener("touchend", onTouchEnd, { passive: true });
});

onUnmounted(() => {
    const el = carouselRef.value;
    if (!el) return;
    el.removeEventListener("touchstart", onTouchStart);
    el.removeEventListener("touchend", onTouchEnd);
});

// Bọc goPrev/goNext để reset autoplay khi user click
function handlePrevClick() {
    goPrev();
    restartAutoplay();
}

function handleNextClick() {
    goNext();
    restartAutoplay();
}
</script>

<style scoped>
.section-7 {
    background-color: #4a3f35;
}
.carousel-track {
    will-change: transform;
    backface-visibility: hidden;
}
.carousel-track--no-transition {
    transition: none !important;
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
