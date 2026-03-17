<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-wedding-brown-warm text-white"
        >
            <div class="flex flex-col items-center px-8 text-center">
                <div
                    class="card-bounce mb-6 flex h-24 w-32 items-center justify-center rounded-2xl border-2 border-wedding-gold-warm/80 bg-wedding-brown shadow-md"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        class="h-12 w-12 text-wedding-gold-warm"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect x="3.5" y="5.5" width="17" height="13" rx="1.5" />
                        <path d="M4 7l8 6 8-6" />
                    </svg>
                </div>
                <p class="mb-3 max-w-md font-sans text-base md:text-lg">
                    Vui lòng đợi để tải những khoảnh khắc đẹp nhất của chúng mình.
                </p>
                <p class="mb-3 max-w-md text-xs md:text-sm text-wedding-cream-warm/90">
                    Nếu mạng chậm, trang vẫn sẽ mở sau một lúc nhé.
                </p>
                <p class="mb-2 text-xs md:text-sm text-wedding-cream-warm/90">
                    {{ Math.round(progress * 100) }}%
                </p>
                <div
                    class="h-4 w-72 overflow-hidden rounded-full border border-white/40 bg-white/10"
                >
                    <div
                        class="h-full rounded-full bg-wedding-gold-warm transition-[width] duration-400 ease-out"
                        :style="{ width: `${Math.round(progress * 100)}%` }"
                    />
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
interface Props {
    show: boolean;
    loaded: number;
    total: number;
    progress: number; // 0..1, giá trị hiển thị cho thanh progress
}

defineProps<Props>();
</script>

<style scoped>
.card-bounce {
    animation: card-bounce 1.2s ease-in-out infinite;
    transform-origin: center bottom;
}

@keyframes card-bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-6px);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

