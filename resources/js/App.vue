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
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useScrollReveal } from "./composables/useScrollReveal";
import { usePreloadImages } from "./composables/usePreloadImages";
import { allImageUrls } from "./imageManifest";
import SideNav from "./components/SideNav.vue";
import Section1Hero from "./components/sections/Section1Hero.vue";
import Section2Story from "./components/sections/Section2Story.vue";
import Section4Countdown from "./components/sections/Section4Countdown.vue";
import Section5Events from "./components/sections/Section5Events.vue";
import Section6RSVP from "./components/sections/Section6RSVP.vue";
import Section7Gallery from "./components/sections/Section7Gallery.vue";
import Section8Closing from "./components/sections/Section8Closing.vue";

const scrollContainerRef = ref(null);
const activeSectionIndex = ref(0);

// Truyền getter để useScrollReveal nhận đúng scroll container sau khi mount
useScrollReveal({ root: () => scrollContainerRef.value });

// Preload toàn bộ ảnh ngay khi trang mount để scroll-snap qua các section mượt hơn
usePreloadImages(allImageUrls);

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
});
</script>
