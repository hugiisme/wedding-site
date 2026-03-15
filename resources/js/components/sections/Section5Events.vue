<template>
    <section
        data-section
        class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col bg-wedding-cream px-4 py-12 md:px-8 md:py-16"
    >
        <div class="mx-auto w-full max-w-6xl">
            <h2
                class="reveal text-center font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
            >
                Thông tin sự kiện
            </h2>
            <div class="reveal mx-auto mt-4 max-w-2xl text-center font-sans text-wedding-brown md:text-lg" lang="vi">
                <p>Khoảnh khắc đặc biệt này chỉ thực sự trọn vẹn khi có bạn cùng sẻ chia.</p>
                <p class="mt-2">Gia đình đã dành trọn tâm huyết chuẩn bị từng chi tiết nhỏ, với mong muốn mang lại không gian tiệc trọn vẹn nhất để tiếp đón những người thân thương. Mời bạn cùng điểm qua các thông tin chi tiết cho buổi tiệc sắp tới tại đây.</p>
            </div>
            <div class="reveal mt-10 grid w-full grid-cols-3 gap-2">
                <button
                    v-for="ev in events"
                    :key="ev.id"
                    type="button"
                    :class="[
                        'flex flex-col items-center justify-center rounded-xl border-2 px-4 py-5 transition-all',
                        selectedId === ev.id
                            ? 'border-wedding-gold-warm bg-wedding-sage/20 text-wedding-brown'
                            : 'border-wedding-sage/50 bg-white/80 text-wedding-brown hover:border-wedding-gold-warm/70',
                    ]"
                    @click="selectedId = ev.id"
                >
                    <span class="text-2xl">{{ ev.icon }}</span>
                    <span class="mt-2 font-semibold">{{ ev.name }}</span>
                    <span class="mt-1 text-center text-sm opacity-90"
                        >{{ ev.time }} - {{ ev.date }}</span
                    >
                </button>
            </div>
            <div
                v-if="selected"
                class="reveal mt-10 grid w-full grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-[minmax(260px,0.75fr)_1fr]"
            >
                <div
                    class="reveal flex min-h-64 min-w-0 max-w-[420px] flex-col rounded-xl border border-wedding-sage/50 bg-white p-6 shadow-sm md:min-h-80 lg:max-w-none"
                >
                    <h3
                        class="font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
                    >
                        {{ selected.detail.title }}
                    </h3>
                    <p
                        class="mt-3 whitespace-pre-line text-base text-wedding-brown md:text-lg"
                    >
                        {{ selected.detail.content }}
                    </p>
                    <p class="mt-4 text-base font-medium text-wedding-brown md:text-lg">
                        {{ selected.detail.placeName }}
                    </p>
                    <p class="mt-1 text-sm text-wedding-brown/80 md:text-base">
                        {{ selected.detail.address }}
                    </p>
                    <a
                        :href="selected.detail.mapUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-auto inline-flex w-fit items-center gap-2 rounded-lg bg-wedding-gold-warm px-4 py-2.5 text-base font-medium text-white transition hover:opacity-90"
                    >
                        Mở Google Map
                    </a>
                </div>
                <div
                    class="reveal min-w-0 h-64 overflow-hidden rounded-xl border border-wedding-sage/50 md:h-80 lg:min-w-[380px]"
                >
                    <iframe
                        v-if="selected.detail.embedUrl"
                        :src="selected.detail.embedUrl"
                        class="h-full w-full"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ"
                    />
                    <div
                        v-else
                        class="flex h-full items-center justify-center bg-wedding-sage/20 text-wedding-brown/60"
                    >
                        Chọn sự kiện để xem bản đồ
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from "vue";

function mapEmbedUrl(address) {
    return `https://www.google.com/maps?q=${encodeURIComponent(address)}&output=embed`;
}
function mapSearchUrl(address) {
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
}

// Sắp xếp theo trình tự thời gian: Lễ vu quy (07:00) → Lễ thành hôn (08:03) → Tiệc cưới (11:00)
const events = ref([
    {
        id: "vuquy",
        name: "Lễ vu quy",
        date: "03/04/2026",
        time: "07:00",
        icon: "🏠",
        detail: {
            title: "Lễ vu quy",
            content:
                "07 giờ 00 ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
            time: "07 giờ 00",
            placeName: "Tư gia nhà gái",
            address: "Số 15, ngõ 120, đường Trần Duy Hưng, Yên Hoà, Hà Nội",
            mapUrl: mapSearchUrl(
                "Bibo Mart, 120 Đ. Trần Duy Hưng, Trung Hoà, Cầu Giấy, Hà Nội 100000, Việt Nam",
            ),
            embedUrl: mapEmbedUrl(
                "Bibo Mart, 120 Đ. Trần Duy Hưng, Trung Hoà, Cầu Giấy, Hà Nội 100000, Việt Nam",
            ),
        },
    },
    {
        id: "le",
        name: "Lễ thành hôn",
        date: "03/04/2026",
        time: "08:03",
        icon: "💒",
        detail: {
            title: "Lễ thành hôn",
            content:
                "08 giờ 03 ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
            time: "08 giờ 03",
            placeName: "Tư gia nhà trai",
            address:
                "P01-28, Solasta Mansion, KĐT Dương Nội 2, Dương Nội, Hà Nội",
            mapUrl: mapSearchUrl(
                "Biệt thự Solasta Mansion, XP8V+3WF, Dương Kinh, Hà Đông, Hà Nội, Việt Nam",
            ),
            embedUrl: mapEmbedUrl(
                "Biệt thự Solasta Mansion, XP8V+3WF, Dương Kinh, Hà Đông, Hà Nội, Việt Nam",
            ),
        },
    },
    {
        id: "tiec",
        name: "Tiệc cưới",
        date: "03/04/2026",
        time: "11:00",
        icon: "🍽️",
        detail: {
            title: "Tiệc cưới",
            content:
                "11 giờ 00 ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
            time: "11 giờ 00",
            placeName:
                "Hội trường tầng 2 - Long Vĩ Palace Wedding & Convention",
            address: "3A Đào Duy Anh, Kim Liên, Hà Nội",
            mapUrl: mapSearchUrl("3A Đào Duy Anh, Kim Liên, Hà Nội"),
            embedUrl: mapEmbedUrl("3A Đào Duy Anh, Kim Liên, Hà Nội"),
        },
    },
]);

const selectedId = ref(events.value[0].id);
const selected = computed(() =>
    events.value.find((e) => e.id === selectedId.value),
);
</script>
