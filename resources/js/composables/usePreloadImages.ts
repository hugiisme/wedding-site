import { onMounted } from "vue";

export function usePreloadImages(urls: string[]) {
    onMounted(() => {
        urls.forEach((src) => {
            if (!src) return;
            const img = new Image();
            img.src = src;
        });
    });
}

