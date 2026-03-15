<template>
    <section
        data-section
        class="section3 relative h-screen w-full shrink-0 snap-start snap-always overflow-hidden bg-wedding-sage px-3 py-4 md:px-6 md:py-6"
    >
        <div class="section3-outer w-full min-w-0 h-full">
            <!-- 1 card: ảnh chú rể | thông tin cả hai | ảnh cô dâu -->
            <div
                class="section3-card reveal grid min-w-0 grid-cols-3 grid-rows-1 h-full min-h-0 overflow-hidden rounded-2xl shadow-lg"
            >
                <div
                    class="section3-img-wrap section3-img-wrap--left relative min-h-0 min-w-0 overflow-hidden bg-wedding-cream-warm/30"
                >
                    <img
                        :src="section3Image"
                        alt="Chú rể"
                        class="section3-img"
                    />
                </div>
                <div
                    class="section3-center flex min-w-0 flex-col justify-center gap-4 border-x border-wedding-sage/40 bg-white/95 px-4 py-5 md:gap-6 md:px-8 md:py-8"
                >
                    <div class="section3-groom">
                        <p
                            class="font-serif text-base font-semibold text-wedding-brown-warm md:text-xl"
                        >
                            Thành
                        </p>
                        <p
                            class="mt-0.5 text-xs text-wedding-brown-warm/80 md:text-sm"
                        >
                            Chú rể
                        </p>
                        <p
                            class="mt-1.5 text-xs text-wedding-brown-warm/70 md:mt-2 md:text-sm"
                        >
                            Ngày sinh:
                            <span class="text-wedding-brown-warm/50">—</span>
                        </p>
                    </div>
                    <div
                        class="section3-divider h-px w-full bg-wedding-sage/40"
                        aria-hidden="true"
                    />
                    <div class="section3-bride text-right">
                        <p
                            class="font-serif text-base font-semibold text-wedding-brown-warm md:text-xl"
                        >
                            Nguyễn Anh Thư
                        </p>
                        <p
                            class="mt-0.5 text-xs text-wedding-brown-warm/80 md:text-sm"
                        >
                            Cô dâu
                        </p>
                        <p
                            class="mt-1.5 text-xs text-wedding-brown-warm/70 md:mt-2 md:text-sm"
                        >
                            Ngày sinh: 09/06/2000
                        </p>
                    </div>
                </div>
                <div
                    class="section3-img-wrap section3-img-wrap--right relative min-h-0 min-w-0 overflow-hidden bg-wedding-cream-warm/30"
                >
                    <img
                        :src="section3Image"
                        alt="Cô dâu"
                        class="section3-img"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import section3Image from "../../elements/section3.jpg";
</script>

<style scoped>
/* Crop bằng clip-path inset: ảnh 200% width, clip 50% để nửa trái/phải lấp đầy ô */
/* Căn giữa section: flex + wrapper dùng left/transform để không lệch do scrollbar */
.section3 {
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 768px) {
    .section3-outer {
        width: 96%;
        max-width: none;
        margin-left: auto;
        margin-right: auto;
    }
}
.section3-img-wrap {
    position: relative;
    min-height: 0;
    overflow: hidden;
}
.section3-img-wrap .section3-img {
    position: absolute;
    top: 0;
    bottom: 0;
    display: block;
    height: 100%;
    width: 200%;
    max-width: none;
    object-fit: cover;
}
.section3-img-wrap--left .section3-img {
    left: 0;
    object-position: 0% 15%;
    transform: translateX(-5%);
}
.section3-img-wrap--right .section3-img {
    right: 0;
    left: auto;
    object-position: 0% 15%;
}

/* Hover ảnh cô dâu: ảnh + thông tin cô dâu nổi lên, phần còn lại mờ đi */
.section3-img-wrap--right,
.section3-bride,
.section3-img-wrap--left,
.section3-groom,
.section3-divider {
    transition: transform 0.35s ease, opacity 0.35s ease, box-shadow 0.35s ease;
}
.section3-groom,
.section3-bride {
    transition: transform 0.35s ease, opacity 0.35s ease, color 0.35s ease;
}
.section3-groom p,
.section3-groom span,
.section3-bride p,
.section3-bride span {
    transition: color 0.35s ease;
}
.section3-card:has(.section3-img-wrap--right:hover) .section3-img-wrap--right {
    transform: translateY(-10px);
    box-shadow: 0 12px 28px rgba(74, 63, 53, 0.2);
    z-index: 1;
}
.section3-card:has(.section3-img-wrap--right:hover) .section3-bride {
    transform: translateY(-10px);
    z-index: 1;
}
.section3-card:has(.section3-img-wrap--right:hover) .section3-bride,
.section3-card:has(.section3-img-wrap--right:hover) .section3-bride p,
.section3-card:has(.section3-img-wrap--right:hover) .section3-bride span {
    color: #111;
}
.section3-card:has(.section3-img-wrap--right:hover) .section3-img-wrap--left,
.section3-card:has(.section3-img-wrap--right:hover) .section3-groom,
.section3-card:has(.section3-img-wrap--right:hover) .section3-divider {
    opacity: 0.4;
}

/* Hover ảnh chú rể: ảnh + thông tin chú rể nổi lên, phần còn lại mờ đi */
.section3-card:has(.section3-img-wrap--left:hover) .section3-img-wrap--left {
    transform: translateY(-10px);
    box-shadow: 0 12px 28px rgba(74, 63, 53, 0.2);
    z-index: 1;
}
.section3-card:has(.section3-img-wrap--left:hover) .section3-groom {
    transform: translateY(-10px);
    z-index: 1;
}
.section3-card:has(.section3-img-wrap--left:hover) .section3-groom,
.section3-card:has(.section3-img-wrap--left:hover) .section3-groom p,
.section3-card:has(.section3-img-wrap--left:hover) .section3-groom span {
    color: #111;
}
.section3-card:has(.section3-img-wrap--left:hover) .section3-img-wrap--right,
.section3-card:has(.section3-img-wrap--left:hover) .section3-bride,
.section3-card:has(.section3-img-wrap--left:hover) .section3-divider {
    opacity: 0.4;
}
</style>
