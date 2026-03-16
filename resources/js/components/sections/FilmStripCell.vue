<template>
    <div
        class="film-strip-cell overflow-hidden"
        :class="showGap ? 'flex shrink-0 items-stretch' : 'block w-full'"
    >
        <div
            class="frame-wrapper relative cursor-pointer overflow-hidden rounded-sm bg-white isolate"
            :style="{
                width: cellWidth,
                minWidth: cellWidth,
                height: cellHeight,
            }"
            @click="$emit('click')"
        >
            <!-- Vùng ảnh inset để khớp với “cửa sổ” của frame film strip, không bị kẻ trắng đè lên ảnh -->
            <div
                class="film-cell-photo absolute inset-0 min-h-[80px] overflow-hidden py-[6%] px-0 contain-strict"
            >
                <img
                    v-if="src"
                    :src="src"
                    :alt="alt"
                    decoding="async"
                    class="h-full w-full object-cover object-center"
                    style="clip-path: inset(5% 0 5% 0);"
                />
            </div>
            <img
                v-if="frameUrl"
                :src="frameUrl"
                alt=""
                aria-hidden="true"
                class="pointer-events-none absolute left-0 right-0 top-1/2 z-1 h-[120%] w-full -translate-y-1/2 object-cover object-center opacity-90"
            />
        </div>
        <div
            v-if="showGap"
            class="film-gap shrink-0 bg-black"
            :style="{ width: gapWidth }"
            aria-hidden="true"
        />
    </div>
</template>

<script setup>
defineProps({
    src: { type: String, required: true },
    alt: { type: String, default: "Ảnh" },
    cellWidth: { type: String, default: "280px" },
    cellHeight: { type: String, default: "18rem" },
    gapWidth: { type: String, default: "20px" },
    showGap: { type: Boolean, default: true },
});

defineEmits(["click"]);

const frameModules = import.meta.glob(
    "../../elements/shape-templates-1-film-strip-graphic-element-frame-black.webp",
    { eager: true, as: "url" },
);
const frameUrl = Object.values(frameModules)[0] ?? null;
</script>
