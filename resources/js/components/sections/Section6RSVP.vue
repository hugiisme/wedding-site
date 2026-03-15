<template>
    <section
        data-section
        class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center bg-wedding-sage px-4 py-12"
    >
        <div class="w-full max-w-md">
            <h2
                class="reveal text-center font-serif text-2xl font-semibold text-wedding-gold md:text-3xl"
            >
                Xác nhận tham dự
            </h2>
            <form class="reveal mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-wedding-brown"
                        >Họ và tên</label
                    >
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="mt-1 w-full rounded-lg border border-wedding-brown/30 bg-wedding-cream/80 px-3 py-2 text-wedding-brown focus:border-wedding-gold focus:outline-none focus:ring-1 focus:ring-wedding-gold"
                        placeholder="Nhập họ tên"
                    />
                </div>
                <div>
                    <p class="block text-sm font-medium text-wedding-brown">
                        Bạn có thể đến chung vui cùng chúng mình không?
                    </p>
                    <div class="mt-2 space-y-2">
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.attending"
                                type="radio"
                                value="yes"
                                class="text-wedding-gold focus:ring-wedding-gold"
                            />
                            <span class="text-wedding-brown"
                                >Tất nhiên rồi</span
                            >
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.attending"
                                type="radio"
                                value="no"
                                class="text-wedding-gold focus:ring-wedding-gold"
                            />
                            <span class="text-wedding-brown"
                                >Rất tiếc không thể tham dự</span
                            >
                        </label>
                    </div>
                </div>
                <div v-if="form.attending === 'yes'">
                    <p class="block text-sm font-medium text-wedding-brown">
                        Bạn có thể tham dự lễ gia tiên (tổ chức cùng ngày)
                        không?
                    </p>
                    <div class="mt-2 space-y-2">
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.ceremony_attendance"
                                type="radio"
                                value="send_bride"
                                class="text-wedding-gold focus:ring-wedding-gold"
                            />
                            <span class="text-wedding-brown"
                                >Có - đi đưa dâu</span
                            >
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.ceremony_attendance"
                                type="radio"
                                value="receive_bride"
                                class="text-wedding-gold focus:ring-wedding-gold"
                            />
                            <span class="text-wedding-brown"
                                >Có - đi đón dâu</span
                            >
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.ceremony_attendance"
                                type="radio"
                                value="no"
                                class="text-wedding-gold focus:ring-wedding-gold"
                            />
                            <span class="text-wedding-brown"
                                >Rất tiếc không thể tham dự</span
                            >
                        </label>
                    </div>
                </div>
                <div v-else class="text-sm text-wedding-brown/80">
                    Chúng mình sẽ nhớ bạn. Cảm ơn bạn đã báo lại.
                </div>
                <div
                    v-if="message"
                    :class="messageSuccess ? 'text-green-700' : 'text-red-700'"
                    class="text-center text-sm"
                >
                    {{ message }}
                </div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-wedding-gold px-4 py-3 font-medium text-white transition hover:opacity-90 disabled:opacity-70"
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

async function submit() {
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
