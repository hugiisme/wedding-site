<template>
    <div class="relative flex h-screen w-full overflow-x-hidden">
        <SideNav
            :active-index="activeSectionIndex"
            :total="7"
            @go="scrollToSection"
        />
        <main
            ref="scrollContainerRef"
            class="scrollbar-hide h-full w-full snap-y snap-mandatory overflow-y-auto overflow-x-hidden"
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

const scrollContainerRef = ref(null);
const activeSectionIndex = ref(0);
const displayProgress = ref(0);
const canHideOverlay = ref(false);
let overlayTimeoutId = null;
let overlayRafId = null;
let cancelled = false;

// Truyền getter để useScrollReveal nhận đúng scroll container sau khi mount
useScrollReveal({ root: () => scrollContainerRef.value });

// Preload toàn bộ ảnh ngay khi trang mount để scroll-snap qua các section mượt hơn
const { loaded, total, done, progress } = usePreloadImagesWithStatus(allImageUrls);

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

onMounted(() => {
    cancelled = false;
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
});
</script>
