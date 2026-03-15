<template>
    <section
        data-section
        class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center bg-wedding-sage px-4 py-12 md:py-16"
    >
        <div class="w-full max-w-xl">
            <h2
                class="reveal text-center font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
            >
                Xác nhận tham dự
            </h2>
            <p v-if="!submitted" class="reveal mt-3 text-center font-sans text-sm text-wedding-brown-warm/90 md:text-base" lang="vi">
                Rất mong được đón bạn trong ngày vui của chúng mình.
            </p>
            <!-- Card cảm ơn sau khi xác nhận thành công -->
            <div
                v-if="submitted"
                class="reveal mt-8 rounded-2xl border border-wedding-sage/60 bg-white/95 p-8 shadow-md md:p-10 text-center"
            >
                <p class="font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl" lang="vi">
                    Cảm ơn bạn đã xác nhận!
                </p>
                <p class="mt-4 font-sans text-wedding-brown-warm/90 md:text-lg" lang="vi">
                    Chúng mình rất mong được gặp bạn trong ngày vui.
                </p>
            </div>
            <form
                v-else
                class="reveal mt-8 rounded-2xl border border-wedding-sage/60 bg-white/95 p-6 shadow-[0_8px_30px_rgba(74,63,53,0.12)] md:p-8 md:shadow-[0_12px_40px_rgba(74,63,53,0.18)]"
                @submit.prevent="submit"
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-wedding-brown-warm"
                        >
                            Họ và tên
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-2 w-full rounded-xl border border-wedding-sage/70 bg-wedding-cream-warm/50 px-4 py-3 text-wedding-brown-warm placeholder:text-wedding-brown-warm/50 focus:border-wedding-gold-warm focus:outline-none focus:ring-2 focus:ring-wedding-gold-warm/30"
                            placeholder="Nhập họ tên"
                        />
                    </div>
                    <div class="space-y-3">
                        <p class="block text-sm font-medium text-wedding-brown-warm">
                            Bạn có thể đến chung vui cùng chúng mình không?
                        </p>
                        <div class="flex flex-col gap-2">
                            <label
                                :class="[
                                    'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                    form.attending === 'yes'
                                        ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                        : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                ]"
                            >
                                <input
                                    v-model="form.attending"
                                    type="radio"
                                    value="yes"
                                    class="rsvp-radio-input"
                                />
                                <span class="text-wedding-brown-warm">Tất nhiên rồi</span>
                                <span class="text-lg" aria-hidden="true">🎉 🎉 🎉</span>
                            </label>
                            <label
                                :class="[
                                    'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                    form.attending === 'no'
                                        ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                        : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                ]"
                            >
                                <input
                                    v-model="form.attending"
                                    type="radio"
                                    value="no"
                                    class="rsvp-radio-input"
                                />
                                <span class="text-wedding-brown-warm">Rất tiếc không thể tham dự</span>
                            </label>
                        </div>
                    </div>
                    <div v-if="form.attending === 'yes'" class="space-y-3">
                        <p class="block text-sm font-medium text-wedding-brown-warm">
                            Bạn có thể tham dự lễ gia tiên (tổ chức cùng ngày) không?
                        </p>
                        <div class="flex flex-col gap-2">
                            <label
                                :class="[
                                    'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                    form.ceremony_attendance === 'send_bride'
                                        ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                        : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                ]"
                            >
                                <input
                                    v-model="form.ceremony_attendance"
                                    type="radio"
                                    value="send_bride"
                                    class="rsvp-radio-input"
                                />
                                <span class="text-wedding-brown-warm">Có - đi đưa dâu</span>
                                <span class="text-lg" aria-hidden="true">👰</span>
                            </label>
                            <label
                                :class="[
                                    'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                    form.ceremony_attendance === 'receive_bride'
                                        ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                        : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                ]"
                            >
                                <input
                                    v-model="form.ceremony_attendance"
                                    type="radio"
                                    value="receive_bride"
                                    class="rsvp-radio-input"
                                />
                                <span class="text-wedding-brown-warm">Có - đi đón dâu</span>
                                <span class="text-lg" aria-hidden="true">🤵</span>
                            </label>
                            <label
                                :class="[
                                    'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                    form.ceremony_attendance === 'no'
                                        ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                        : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                ]"
                            >
                                <input
                                    v-model="form.ceremony_attendance"
                                    type="radio"
                                    value="no"
                                    class="rsvp-radio-input"
                                />
                                <span class="text-wedding-brown-warm">Rất tiếc không thể tham dự</span>
                            </label>
                        </div>
                    </div>
                    <div v-else class="rounded-xl bg-wedding-sage/20 px-4 py-3 text-center text-sm text-wedding-brown-warm/90" lang="vi">
                        Chúng mình sẽ nhớ bạn. Cảm ơn bạn đã báo lại.
                    </div>
                </div>
                <div
                    v-if="message"
                    :class="[
                        'mt-6 rounded-lg px-4 py-3 text-center text-sm',
                        messageSuccess
                            ? 'bg-wedding-sage/30 text-wedding-brown-warm'
                            : 'bg-red-50 text-red-800',
                    ]"
                >
                    {{ message }}
                </div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="mt-6 w-full rounded-xl bg-wedding-gold-warm py-3.5 font-medium text-white shadow-sm transition hover:opacity-90 disabled:opacity-70"
                >
                    {{ loading ? "Đang gửi..." : "Xác nhận ngay" }}
                </button>
            </form>
        </div>
    </section>
</template>

<script setup>
import { ref, reactive } from "vue";
import axios from "axios";

const form = reactive({
    name: "",
    attending: "yes",
    ceremony_attendance: "send_bride",
});
const loading = ref(false);
const message = ref("");
const messageSuccess = ref(false);
const submitted = ref(false);

async function submit() {
    if (submitted.value) return;
    message.value = "";
    loading.value = true;
    try {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        await axios.post(
            "/api/rsvp",
            {
                name: form.name,
                attending: form.attending,
                ceremony_attendance:
                    form.attending === "yes" ? form.ceremony_attendance : null,
            },
            {
                headers: token ? { "X-CSRF-TOKEN": token } : {},
            },
        );
        messageSuccess.value = true;
        message.value = "Cảm ơn bạn đã xác nhận!";
        submitted.value = true;
        form.name = "";
        form.attending = "yes";
        form.ceremony_attendance = "send_bride";
    } catch (err) {
        messageSuccess.value = false;
        message.value =
            err.response?.data?.message || "Có lỗi xảy ra, vui lòng thử lại.";
    } finally {
        loading.value = false;
    }
}
</script>
