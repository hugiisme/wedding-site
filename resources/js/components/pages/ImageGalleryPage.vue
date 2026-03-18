<template>
    <div class="min-h-screen bg-wedding-brown px-4 py-10 md:px-8 md:py-12">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
            <div class="flex items-center justify-between gap-4">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-wedding-cream-warm/40 px-4 py-2 text-sm font-sans text-wedding-cream-warm hover:bg-wedding-cream-warm/10"
                    @click="goBack"
                >
                    <span class="text-lg">&larr;</span>
                    <span>Quay lại thiệp mời</span>
                </button>
                <p class="hidden text-xs font-sans text-wedding-cream-warm/60 md:block">
                    {{ images.length }} khoảnh khắc được lưu lại
                </p>
            </div>

            <div class="text-center">
                <h1
                    class="font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
                >
                    Tất cả khoảnh khắc của chúng mình
                </h1>
                
            </div>

            <div ref="masonryRef" class="mt-6 masonry">
                <div
                    v-for="(img, index) in images"
                    :key="img.src"
                    class="masonry-item"
                >
                    <button
                        type="button"
                        class="group relative w-full overflow-hidden rounded-xl"
                        @click="openLightbox(index)"
                    >
                        <div class="relative flex items-center justify-center">
                            <img
                                :src="
                                    visibleSet.has(index)
                                        ? img.src
                                        : BLANK_IMG
                                "
                                :alt="img.alt"
                                decoding="async"
                                loading="lazy"
                                :fetchpriority="index < 4 ? 'high' : 'low'"
                                sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, 50vw"
                                :data-index="index"
                                class="gallery-image transition-all duration-500 ease-out group-hover:scale-[1.02]"
                                :class="
                                    !visibleSet.has(index)
                                        ? 'opacity-0'
                                        : isLoaded(index)
                                          ? 'blur-0 opacity-100 scale-100'
                                          : 'opacity-80 scale-[1.02]'
                                "
                                @load="handleLoaded(index)"
                            />
                        </div>
                    </button>
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
                                :src="currentImage?.src"
                                :alt="currentImage?.alt || 'Ảnh kỷ niệm'"
                                decoding="async"
                                loading="eager"
                                fetchpriority="high"
                                class="max-h-[80vh] w-full max-w-full object-contain"
                            />
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

const modules = import.meta.glob(
    "../../elements/Gallery/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);
const fallbackModules = import.meta.glob(
    "../../elements/*.{jpg,jpeg,png,webp}",
    { eager: true, as: "url" },
);

const filmUrls = Object.values(modules);
const allUrls =
    filmUrls.length > 0
        ? filmUrls
        : Object.values(fallbackModules).filter(
              (url) => !url.includes("film-strip-graphic-element-frame"),
          );

const images = allUrls.map((src, index) => ({
    id: index,
    src,
    thumb: src,
    alt: `Khoảnh khắc ${index + 1}`,
}));

const lightboxIndex = ref(null);
const router = useRouter();
const loadedSet = ref(new Set());
const visibleSet = ref(new Set());
const masonryRef = ref(null);
let observer = null;

let previousHtmlOverflow = "";
let previousBodyOverflow = "";

// 1x1 transparent GIF để tránh request thừa khi ảnh chưa vào viewport
const BLANK_IMG = "data:image/gif;base64,R0lGODlhAQABAAAAACwAAAAAAQABAAA=";

onMounted(() => {
    const html = document.documentElement;
    const body = document.body;
    previousHtmlOverflow = html.style.overflow;
    previousBodyOverflow = body.style.overflow;
    html.style.overflow = "";
    body.style.overflow = "auto";

    // Prime a few images so the top isn't blank on iOS
    for (let i = 0; i < Math.min(6, images.length); i++) {
        visibleSet.value.add(i);
    }

    // IntersectionObserver: chỉ set src khi ảnh sắp vào tầm nhìn
    if ("IntersectionObserver" in window && masonryRef.value) {
        const imgEls = Array.from(
            masonryRef.value.querySelectorAll("img[data-index]"),
        );

        observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (!entry.isIntersecting) continue;
                    const el = entry.target;
                    const idx = Number(el.getAttribute("data-index"));
                    if (Number.isNaN(idx)) continue;
                    visibleSet.value.add(idx);
                    observer?.unobserve(el);
                }
            },
            {
                root: null,
                rootMargin: "300px 0px",
                threshold: 0.01,
            },
        );

        imgEls.forEach((el) => observer?.observe(el));
    } else {
        // Fallback: nếu không có IntersectionObserver thì load đủ ảnh
        for (let i = 0; i < images.length; i++) {
            visibleSet.value.add(i);
        }
    }
});

onUnmounted(() => {
    if (observer) observer.disconnect();

    const html = document.documentElement;
    const body = document.body;
    html.style.overflow = previousHtmlOverflow;
    body.style.overflow = previousBodyOverflow;
});

function openLightbox(index) {
    lightboxIndex.value = index;
}

function closeLightbox() {
    lightboxIndex.value = null;
}

const currentImage = computed(() =>
    lightboxIndex.value != null ? images[lightboxIndex.value] : null,
);

function goBack() {
    router.push({ name: "home" });
}

function handleLoaded(index) {
    // Chỉ mark loaded thật khi ảnh đã được phép set src
    if (!visibleSet.value.has(index)) return;
    loadedSet.value.add(index);
}

function isLoaded(index) {
    return loadedSet.value.has(index);
}
</script>

<style scoped>
.masonry {
    column-count: 2;
    column-gap: 1rem;
}

@media (min-width: 768px) {
    .masonry {
        column-count: 3;
    }
}

@media (min-width: 1024px) {
    .masonry {
        column-count: 4;
    }
}

.masonry-item {
    break-inside: avoid;
    margin-bottom: 1rem;
}

.gallery-image {
    display: block;
    width: 100%;
    height: auto;
    max-height: 20rem;
    object-fit: cover;
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

