<template>
    <div class="relative flex h-screen w-full overflow-x-hidden">
        <SideNav
            :active-index="activeSectionIndex"
            :total="7"
            @go="scrollToSection"
        />
        <main
            ref="scrollContainerRef"
            :class="[
                'scrollbar-hide h-full w-full overflow-y-auto overflow-x-hidden',
                isScrollSnapEnabled && 'snap-y snap-mandatory',
            ]"
        >
            <Section1Hero />
            <Section2Story />
            <Section4Countdown />
            <Section5Events />
            <Section6RSVP />
            <Section7Gallery />
            <Section8Closing />
        </main>
        <PreloadOverlay
            :show="showOverlay"
            :loaded="loaded"
            :total="total"
            :progress="displayProgress"
        />
        <div
            v-if="autoplayBlocked && !showOverlay"
            class="pointer-events-none fixed bottom-4 left-4 z-40 flex flex-col items-start gap-2"
        >
            <p
                v-if="showAutoplayNotice"
                class="max-w-sm rounded-lg bg-black/70 px-3 py-2 text-left text-xs text-white shadow-lg"
            >
                Trình duyệt không hỗ trợ tự động phát audio. Bấm vào nút dưới để
                thử lại.
            </p>
            <button
                type="button"
                class="pointer-events-auto flex h-11 w-11 items-center justify-center rounded-full bg-wedding-gold-warm text-wedding-brown-warm shadow-lg transition hover:brightness-95"
                aria-label="Play audio"
                @click="onPlayAudioClick"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="currentColor"
                >
                    <path
                        d="M8 5.14v13.72a1 1 0 001.53.85l10.45-6.86a1 1 0 000-1.68L9.53 4.3A1 1 0 008 5.14z"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { useScrollReveal } from "./composables/useScrollReveal";
import { usePreloadImagesWithStatus } from "./composables/usePreloadImagesWithStatus";
import { allImageUrls } from "./imageManifest";
import SideNav from "./components/SideNav.vue";
import PreloadOverlay from "./components/PreloadOverlay.vue";
import Section1Hero from "./components/sections/Section1Hero.vue";
import Section2Story from "./components/sections/Section2Story.vue";
import Section4Countdown from "./components/sections/Section4Countdown.vue";
import Section5Events from "./components/sections/Section5Events.vue";
import Section6RSVP from "./components/sections/Section6RSVP.vue";
import Section7Gallery from "./components/sections/Section7Gallery.vue";
import Section8Closing from "./components/sections/Section8Closing.vue";
import bgMusicUrl from "./elements/bg_music.mp3";

const scrollContainerRef = ref(null);
const activeSectionIndex = ref(0);
const displayProgress = ref(0);
const canHideOverlay = ref(false);
let overlayTimeoutId = null;
let overlayRafId = null;
let cancelled = false;
let backgroundAudio = null;
const isScrollSnapEnabled = resolveScrollSnapEnabled();
const autoplayBlocked = ref(false);
const showAutoplayNotice = ref(false);
let autoplayNoticeTimeoutId = null;

function parseBooleanEnv(value, defaultValue = false) {
    if (value == null || value === "") return defaultValue;
    const normalized = String(value).trim().toLowerCase();
    if (["true", "1", "yes", "on"].includes(normalized)) return true;
    if (["false", "0", "no", "off"].includes(normalized)) return false;
    return defaultValue;
}

function resolveScrollSnapEnabled() {
    const envEnabled = parseBooleanEnv(
        import.meta.env.VITE_ENABLE_SCROLL_SNAP,
        true,
    );
    if (!envEnabled) return false;
    // return !isIosDevice();
    return true;
}

// Truyền getter để useScrollReveal nhận đúng scroll container sau khi mount
useScrollReveal({ root: () => scrollContainerRef.value });

// Preload toàn bộ ảnh ngay khi trang mount để scroll-snap qua các section mượt hơn
const { loaded, total, done, progress } =
    usePreloadImagesWithStatus(allImageUrls);

// Hiển thị overlay cho tới khi có tín hiệu ẩn
const showOverlay = computed(() => !canHideOverlay.value);

// Đồng bộ progress thực tế sang progress hiển thị (displayProgress) một cách mượt
watch(
    progress,
    (val) => {
        const next = Math.max(0, Math.min(1, val ?? 0));
        if (next > displayProgress.value) {
            displayProgress.value = next;
        }
    },
    { immediate: true },
);

// Khi preload thực sự xong và progress đã đầy, cho phép ẩn overlay ngay (không chờ timeout)
watch(
    [done, displayProgress],
    ([isDone, uiProgress]) => {
        if (isDone && uiProgress >= 1) {
            canHideOverlay.value = true;
        }
    },
    { immediate: true },
);

function scrollToSection(index) {
    const container = scrollContainerRef.value;
    if (!container) return;
    const sections = container.querySelectorAll("[data-section]");
    const el = sections[index];
    if (el) el.scrollIntoView({ behavior: "smooth" });
}

function updateActiveSection() {
    const container = scrollContainerRef.value;
    if (!container) return;
    const sections = container.querySelectorAll("[data-section]");
    const scrollTop = container.scrollTop;
    const viewportMid = scrollTop + container.clientHeight / 2;

    for (let i = 0; i < sections.length; i++) {
        const section = sections[i];
        const top = section.offsetTop;
        const bottom = top + section.offsetHeight;
        if (viewportMid >= top && viewportMid < bottom) {
            activeSectionIndex.value = i;
            break;
        }
    }
}

async function playBackgroundAudio() {
    if (!backgroundAudio) return;
    try {
        await backgroundAudio.play();
        autoplayBlocked.value = false;
        showAutoplayNotice.value = false;
        if (autoplayNoticeTimeoutId != null) {
            window.clearTimeout(autoplayNoticeTimeoutId);
            autoplayNoticeTimeoutId = null;
        }
    } catch {
        autoplayBlocked.value = true;
        showAutoplayNotice.value = true;
        if (autoplayNoticeTimeoutId != null) {
            window.clearTimeout(autoplayNoticeTimeoutId);
        }
        autoplayNoticeTimeoutId = window.setTimeout(() => {
            showAutoplayNotice.value = false;
            autoplayNoticeTimeoutId = null;
        }, 4000);
    }
}

function onPlayAudioClick() {
    showAutoplayNotice.value = false;
    void playBackgroundAudio();
}

onMounted(() => {
    cancelled = false;
    backgroundAudio = new Audio(bgMusicUrl);
    backgroundAudio.loop = true;
    backgroundAudio.preload = "auto";

    // Timeout tối đa cho overlay để tránh chặn người dùng trên mạng yếu
    const timeoutMs = 20000;
    overlayTimeoutId = window.setTimeout(() => {
        if (cancelled) return;
        if (displayProgress.value < 1) {
            const start = displayProgress.value;
            const duration = 1200;
            const startTime = performance.now();

            const animate = (now) => {
                const elapsed = now - startTime;
                const t = Math.min(1, elapsed / duration);
                displayProgress.value = start + (1 - start) * t;
                if (t < 1) {
                    overlayRafId = window.requestAnimationFrame(animate);
                } else {
                    canHideOverlay.value = true;
                }
            };

            overlayRafId = window.requestAnimationFrame(animate);
        } else {
            canHideOverlay.value = true;
        }
    }, timeoutMs);

    const container = scrollContainerRef.value;
    if (container) {
        container.addEventListener("scroll", updateActiveSection, {
            passive: true,
        });
        updateActiveSection();
    }
});

watch(
    showOverlay,
    (isVisible) => {
        if (!isVisible) {
            void playBackgroundAudio();
        }
    },
    { immediate: true },
);

onUnmounted(() => {
    const container = scrollContainerRef.value;
    if (container) container.removeEventListener("scroll", updateActiveSection);
    cancelled = true;
    if (overlayTimeoutId != null) {
        window.clearTimeout(overlayTimeoutId);
        overlayTimeoutId = null;
    }
    if (overlayRafId != null) {
        window.cancelAnimationFrame(overlayRafId);
        overlayRafId = null;
    }
    if (backgroundAudio) {
        backgroundAudio.pause();
        backgroundAudio.currentTime = 0;
        backgroundAudio = null;
    }
    if (autoplayNoticeTimeoutId != null) {
        window.clearTimeout(autoplayNoticeTimeoutId);
        autoplayNoticeTimeoutId = null;
    }
    autoplayBlocked.value = false;
    showAutoplayNotice.value = false;
});
</script>
