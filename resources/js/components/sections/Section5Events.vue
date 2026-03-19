<template>
    <section
        data-section
        class="flex h-screen w-full shrink-0 snap-start snap-always flex-col bg-wedding-cream px-4 py-6 md:px-8 md:py-10 overflow-hidden"
    >
        <div class="mx-auto h-full w-full max-w-6xl">
            <div
                class="grid h-full grid-rows-[auto_auto_auto_1fr] gap-3 md:gap-4"
            >
                <h2
                    class="reveal text-center font-serif text-2xl tracking-tight text-wedding-gold-warm md:text-3xl uppercase font-extrabold md:font-bold"
                >
                    Thông tin sự kiện
                </h2>
                <div
                    class="reveal mx-auto max-w-2xl text-center font-sans text-sm leading-[1.7] text-wedding-brown md:leading-relaxed md:text-base"
                    lang="vi"
                >
                    <p>
                        Khoảnh khắc đặc biệt này chỉ thực sự trọn vẹn khi có bạn
                        cùng sẻ chia.
                    </p>
                    <p class="mt-1 md:mt-2">
                        Gia đình đã dành trọn tâm huyết chuẩn bị từng chi tiết
                        nhỏ, với mong muốn mang lại không gian tiệc trọn vẹn
                        nhất để tiếp đón những người thân thương. Mời bạn cùng
                        điểm qua các thông tin chi tiết cho buổi tiệc sắp tới
                        tại đây.
                    </p>
                </div>
                <div class="reveal grid w-full grid-cols-3 gap-2">
                    <button
                        v-for="ev in events"
                        :key="ev.id"
                        type="button"
                        :class="[
                            'flex flex-col items-center justify-center rounded-xl border-2 px-2 py-3 text-center transition-all md:px-4 md:py-4',
                            selectedId === ev.id
                                ? 'border-wedding-gold-warm bg-wedding-sage/20 text-wedding-brown'
                                : 'border-wedding-sage/50 bg-white/80 text-wedding-brown hover:border-wedding-gold-warm/70',
                        ]"
                        @click="selectedId = ev.id"
                    >
                        <span class="text-lg md:text-2xl">{{ ev.icon }}</span>
                        <span
                            class="mt-1 text-[11px] font-sans font-normal text-wedding-brown md:mt-2 md:text-sm lg:text-base"
                        >
                            {{ ev.name }}
                        </span>
                        <span
                            class="mt-1 text-center text-[10px] font-sans font-normal text-wedding-brown md:text-sm"
                        >
                            {{ ev.time }} - {{ ev.date }}
                        </span>
                    </button>
                </div>
                <div
                    v-if="selected"
                    class="reveal min-h-0 self-start md:self-stretch grid w-full grid-cols-1 gap-4 md:gap-4 md:grid-cols-2 lg:grid-cols-[minmax(260px,0.75fr)_1fr]"
                >
                    <div
                        class="reveal min-h-0 min-w-0 self-start md:self-stretch md:h-full rounded-xl border border-wedding-sage/50 bg-white p-4 pb-3 shadow-sm md:rounded-xl md:p-5 md:pb-5"
                    >
                        <div class="flex flex-col md:h-full">
                            <h3
                                class="font-noto-thin font-normal text-xl text-wedding-brown md:text-2xl lg:text-3xl"
                            >
                                {{ selected.detail.title }}
                            </h3>
                            <p
                                class="mt-2 whitespace-pre-line text-sm leading-[1.7] text-wedding-brown md:mt-2 md:leading-relaxed md:text-base"
                            >
                                {{ selected.detail.content }}
                            </p>
                            <p
                                class="mt-3 text-[13px] font-medium leading-snug text-wedding-brown whitespace-normal wrap-break-word md:mt-3 md:text-base"
                            >
                                {{ selected.detail.placeName }}
                            </p>
                            <p
                                class="mt-1 text-xs text-wedding-brown/80 md:mt-1 md:text-sm"
                            >
                                {{ selected.detail.address }}
                            </p>
                            <a
                                :href="selected.detail.mapUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-4 inline-flex w-fit items-center gap-2 rounded-lg bg-wedding-gold-warm px-4 py-2.5 text-sm font-medium text-white transition hover:opacity-90 md:mt-auto md:text-base"
                            >
                                Mở Google Map
                            </a>
                        </div>
                    </div>
                    <div
                        class="reveal min-h-0 min-w-0 overflow-hidden rounded-xl border border-wedding-sage/50 md:rounded-xl"
                    >
                        <div
                            class="relative aspect-video min-h-0 md:aspect-auto md:h-full"
                        >
                            <iframe
                                v-if="selected.detail.embedUrl"
                                :src="selected.detail.embedUrl"
                                class="absolute inset-0 h-full w-full md:static md:h-full md:w-full"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Bản đồ"
                            />
                            <div
                                v-else
                                class="absolute inset-0 flex h-full items-center justify-center bg-wedding-sage/20 text-wedding-brown/60 md:static"
                            >
                                Chọn sự kiện để xem bản đồ
                            </div>
                        </div>
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
        id: "tiec",
        name: "Tiệc cưới",
        date: "03/04/2026",
        time: "11:00",
        icon: "🍽️",
        detail: {
            title: "Tiệc cưới",
            content:
                "11 giờ 00 phút ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
            time: "11 giờ 00",
            placeName:
                "Hội trường tầng 2 - Long Vĩ Palace Wedding & Convention",
            address: "3A P. Đào Duy Anh, Phương Mai, Đống Đa, Hà Nội, Việt Nam",
            mapUrl: mapSearchUrl("Nhà Hàng LONG VĨ"),
            embedUrl: mapEmbedUrl("Nhà Hàng LONG VĨ"),
        },
    },
    {
        id: "vuquy",
        name: "Lễ vu quy",
        date: "03/04/2026",
        time: "07:00",
        icon: "🏠",
        detail: {
            title: "Lễ vu quy",
            content:
                "07 giờ 00 phút ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
            time: "07 giờ 00",
            placeName: "Tư gia nhà gái",
            address:
                "Số 15, ngõ 120, đường Trần Duy Hưng, Phường Yên Hoà, Quận Cầu Giấy, Hà Nội",
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
                "08 giờ 00 phút 03 ngày 03 tháng 04 năm 2026\n(Tức ngày 16 tháng 02 năm Bính Ngọ)",
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
]);

const selectedId = ref(events.value[0].id);
const selected = computed(() =>
    events.value.find((e) => e.id === selectedId.value),
);
</script>
