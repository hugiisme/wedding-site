import { onMounted, onUnmounted, ref, computed } from "vue";

export function usePreloadImagesWithStatus(urls: string[]) {
    const loaded = ref(0);
    const total = ref(urls.length);
    const done = ref(false);

    const progress = computed(() => {
        if (total.value === 0) return 1;
        return loaded.value / total.value;
    });

    let active = true;

    onMounted(() => {
        active = true;
        const uniqueUrls = Array.from(
            new Set(urls.filter((src) => typeof src === "string" && src.length)),
        );

        total.value = uniqueUrls.length;

        if (uniqueUrls.length === 0) {
            done.value = true;
            return;
        }

        uniqueUrls.forEach((src) => {
            const img = new Image();
            img.onload = img.onerror = () => {
                if (!active) return;
                loaded.value += 1;
                if (loaded.value >= total.value) {
                    done.value = true;
                }
            };
            img.src = src;
        });
    });

    onUnmounted(() => {
        active = false;
    });

    return {
        loaded,
        total,
        done,
        progress,
    };
}

