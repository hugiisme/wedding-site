import { onMounted, ref, computed } from "vue";

export function usePreloadImagesWithStatus(urls: string[]) {
    const loaded = ref(0);
    const total = ref(urls.length);
    const done = ref(false);

    const progress = computed(() => {
        if (total.value === 0) return 1;
        return loaded.value / total.value;
    });

    onMounted(() => {
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
                loaded.value += 1;
                if (loaded.value >= total.value) {
                    done.value = true;
                }
            };
            img.src = src;
        });
    });

    return {
        loaded,
        total,
        done,
        progress,
    };
}

