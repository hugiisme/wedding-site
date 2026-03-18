<template>
    <section
        ref="sectionRef"
        data-section
        class="rsvp-section flex h-screen w-full shrink-0 snap-start snap-always flex-col items-center justify-center overflow-hidden bg-wedding-sage px-4 py-10 md:py-16"
    >
        <div class="w-full max-w-xl">
            <h2
                class="reveal text-center font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl"
            >
                Xác nhận tham dự
            </h2>
            <p v-if="!submitted" class="reveal mt-3 text-center font-serif text-sm text-wedding-brown-warm/90 md:text-base" lang="vi">
                Rất mong được đón bạn trong ngày vui của chúng mình.
            </p>
            <!-- Card cảm ơn sau khi xác nhận thành công (không dùng .reveal vì nội dung mới sau submit, observer không chạy lại) -->
            <div
                v-if="submitted"
                class="mt-8 rounded-2xl border border-wedding-sage/60 bg-white/95 p-8 shadow-md md:p-10 text-center"
            >
                <p class="font-serif text-2xl font-semibold text-wedding-gold-warm md:text-3xl" lang="vi">
                    {{ submittedTitle }}
                </p>
                <p class="mt-4 font-serif text-wedding-brown-warm/90 md:text-lg" lang="vi">
                    {{ submittedBody }}
                </p>
            </div>
            <form
                v-else
                class="reveal mt-6 max-h-[calc(100vh-220px)] overflow-auto overscroll-contain rounded-2xl border border-wedding-sage/60 bg-white/95 p-5 shadow-[0_8px_30px_rgba(74,63,53,0.12)] md:mt-8 md:max-h-[calc(100vh-260px)] md:p-8 md:shadow-[0_12px_40px_rgba(74,63,53,0.18)]"
                @submit.prevent="submit"
            >
                <div class="space-y-6">
                    <!-- Progress -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs font-medium text-wedding-brown-warm/80">
                            <span lang="vi">Câu {{ progressIndex }} / {{ totalQuestions }}</span>
                            <button
                                v-if="canGoBack"
                                type="button"
                                class="rounded-lg px-2 py-1 transition hover:bg-wedding-cream-warm/70"
                                @click="goBack"
                                lang="vi"
                            >
                                Quay lại
                            </button>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-wedding-cream-warm/70">
                            <div
                                class="h-full rounded-full bg-wedding-gold-warm transition-[width] duration-500 ease-out"
                                :style="{ width: `${progressPercent}%` }"
                            />
                        </div>
                    </div>

                    <!-- Step content -->
                    <Transition name="rsvp-step" mode="out-in">
                        <div :key="stepKey">
                            <!-- Step 1: Name -->
                            <div v-if="stepId === 'name'" class="space-y-3">
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-wedding-brown-warm"
                                    lang="vi"
                                >
                                    Họ và tên
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-wedding-sage/70 bg-wedding-cream-warm/50 px-4 py-3 text-wedding-brown-warm placeholder:text-wedding-brown-warm/50 focus:border-wedding-gold-warm focus:outline-none focus:ring-2 focus:ring-wedding-gold-warm/30"
                                    placeholder="Nhập họ tên"
                                    autocomplete="name"
                                    @keydown.enter.prevent="nextFromName"
                                />
                                <div class="pt-1">
                                    <button
                                        type="button"
                                        class="w-full rounded-xl bg-wedding-gold-warm py-3.5 font-medium text-white shadow-sm transition hover:opacity-90 disabled:opacity-60"
                                        :disabled="!isNameValid"
                                        @click="nextFromName"
                                        lang="vi"
                                    >
                                        Tiếp theo
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Attending -->
                            <div v-else-if="stepId === 'attending'" class="space-y-3">
                                <p class="block text-sm font-medium text-wedding-brown-warm" lang="vi">
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
                                        @click="onAttendingPick('yes')"
                                    >
                                        <input
                                            v-model="form.attending"
                                            type="radio"
                                            value="yes"
                                            class="rsvp-radio-input"
                                        />
                                        <span class="text-wedding-brown-warm" lang="vi">Tất nhiên rồi</span>
                                        <span class="text-lg" aria-hidden="true">🎉 🎉 🎉</span>
                                    </label>
                                    <label
                                        :class="[
                                            'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                            form.attending === 'no'
                                                ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                                : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                        ]"
                                        @click="onAttendingPick('no')"
                                    >
                                        <input
                                            v-model="form.attending"
                                            type="radio"
                                            value="no"
                                            class="rsvp-radio-input"
                                        />
                                        <span class="text-wedding-brown-warm" lang="vi">Rất tiếc không thể tham dự</span>
                                    </label>
                                </div>
                                <p v-if="showValidationHint" class="text-xs text-wedding-brown-warm/70" lang="vi">
                                    Chọn một phương án để tiếp tục.
                                </p>
                            </div>

                            <!-- Step 3: Ceremony (only if attending yes) -->
                            <div v-else-if="stepId === 'ceremony'" class="space-y-3">
                                <p class="block text-sm font-medium text-wedding-brown-warm" lang="vi">
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
                                        @click="onCeremonyPick('send_bride')"
                                    >
                                        <input
                                            v-model="form.ceremony_attendance"
                                            type="radio"
                                            value="send_bride"
                                            class="rsvp-radio-input"
                                        />
                                        <span class="text-wedding-brown-warm" lang="vi">Có - đi đưa dâu</span>
                                        <span class="text-lg" aria-hidden="true">👰</span>
                                    </label>
                                    <label
                                        :class="[
                                            'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                            form.ceremony_attendance === 'receive_bride'
                                                ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                                : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                        ]"
                                        @click="onCeremonyPick('receive_bride')"
                                    >
                                        <input
                                            v-model="form.ceremony_attendance"
                                            type="radio"
                                            value="receive_bride"
                                            class="rsvp-radio-input"
                                        />
                                        <span class="text-wedding-brown-warm" lang="vi">Có - đi đón dâu</span>
                                        <span class="text-lg" aria-hidden="true">🤵</span>
                                    </label>
                                    <label
                                        :class="[
                                            'rsvp-radio-card flex w-full cursor-pointer items-center gap-3 rounded-xl px-4 py-3.5 transition-all',
                                            form.ceremony_attendance === 'no'
                                                ? 'border-2 border-wedding-gold-warm bg-wedding-sage/25'
                                                : 'border-2 border-transparent bg-wedding-cream-warm/60 hover:bg-wedding-cream-warm/80',
                                        ]"
                                        @click="onCeremonyPick('no')"
                                    >
                                        <input
                                            v-model="form.ceremony_attendance"
                                            type="radio"
                                            value="no"
                                            class="rsvp-radio-input"
                                        />
                                        <span class="text-wedding-brown-warm" lang="vi">Rất tiếc không thể tham dự</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Step 4: Review + Submit -->
                            <div v-else class="space-y-4">
                                <div class="rounded-xl bg-wedding-cream-warm/60 px-4 py-4">
                                    <p class="text-sm font-medium text-wedding-brown-warm" lang="vi">
                                        Xác nhận lại giúp tụi mình nhé
                                    </p>
                                    <div class="mt-3 space-y-2 text-sm text-wedding-brown-warm/90">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="opacity-80" lang="vi">Tên</span>
                                            <span class="text-right font-medium">{{ form.name }}</span>
                                        </div>
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="opacity-80" lang="vi">Tham dự</span>
                                            <span class="text-right font-medium" lang="vi">
                                                {{ form.attending === "yes" ? "Có" : "Không" }}
                                            </span>
                                        </div>
                                        <div v-if="form.attending === 'yes'" class="flex items-start justify-between gap-3">
                                            <span class="opacity-80" lang="vi">Lễ gia tiên</span>
                                            <span class="text-right font-medium" lang="vi">
                                                {{
                                                    form.ceremony_attendance === "send_bride"
                                                        ? "Có - đi đưa dâu"
                                                        : form.ceremony_attendance === "receive_bride"
                                                            ? "Có - đi đón dâu"
                                                            : "Không"
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="form.attending === 'no'" class="rounded-xl bg-wedding-sage/20 px-4 py-3 text-center text-sm text-wedding-brown-warm/90" lang="vi">
                                    Chúng mình sẽ nhớ bạn. Cảm ơn bạn đã báo lại.
                                </div>

                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="w-full rounded-xl bg-wedding-gold-warm py-3.5 font-medium text-white shadow-sm transition hover:opacity-90 disabled:opacity-70"
                                >
                                    {{ loading ? "Đang gửi..." : "Xác nhận ngay" }}
                                </button>
                            </div>
                        </div>
                    </Transition>
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
            </form>
        </div>
    </section>
</template>

<script setup>
import { ref, reactive, computed, watch, nextTick } from "vue";
import axios from "axios";

const sectionRef = ref(null);

const form = reactive({
    name: "",
    attending: null,
    ceremony_attendance: null,
});
const loading = ref(false);
const message = ref("");
const messageSuccess = ref(false);
const submitted = ref(false);
const lastSubmitted = ref(null);

const stepIndex = ref(0);
const showValidationHint = ref(false);
const attendingTouched = ref(false);

const stepOrder = computed(() => {
    const base = ["name", "attending"];
    // Để số câu không “nhảy” (1/3 -> 3/4), mặc định coi như có câu lễ gia tiên
    // cho tới khi người dùng chọn rõ ràng là "không tham dự".
    const shouldIncludeCeremony = form.attending === "yes" || (!attendingTouched.value && form.attending !== "no");
    if (shouldIncludeCeremony) base.push("ceremony");
    base.push("review");
    return base;
});

const stepId = computed(() => stepOrder.value[stepIndex.value] ?? "name");
const stepKey = computed(() => `${stepId.value}-${form.attending ?? "?"}`);

const totalQuestions = computed(() => stepOrder.value.length);
const progressIndex = computed(() => Math.min(totalQuestions.value, stepIndex.value + 1));
const progressPercent = computed(() => {
    const denom = Math.max(1, totalQuestions.value - 1);
    return Math.round((stepIndex.value / denom) * 100);
});

const isNameValid = computed(() => (form.name ?? "").trim().length > 0);
const canGoBack = computed(() => stepIndex.value > 0 && !loading.value);

const submittedTitle = computed(() => {
    const data = lastSubmitted.value;
    const name = (data?.name ?? "").trim();
    const suffix = name ? `, ${name}` : "";
    return `Cảm ơn bạn đã dành thời gian phản hồi${suffix}!`;
});

const submittedBody = computed(() => {
    const data = lastSubmitted.value;
    const name = (data?.name ?? "").trim();
    const namePart = name ? ` ${name}` : "";

    if (!data?.attending) return "Chúng mình rất mong được gặp bạn trong ngày vui.";

    if (data.attending === "no") {
        return "Cảm ơn đã chúc phúc cho chúng mình nhé!";
    }

    // attending === "yes"
    if (
        data.ceremony_attendance === "send_bride" ||
        data.ceremony_attendance === "receive_bride"
    ) {
        // Có tham dự cưới và có tham gia đưa/đón dâu
        return "Chúng mình rất mong được gặp bạn trong ngày vui.";
    }

    // Có tham dự cưới nhưng không đi đưa/đón dâu
    return `Cảm ơn bạn${namePart} đã dành thời gian chung vui cùng chúng mình!`;
});

function goBack() {
    if (!canGoBack.value) return;
    showValidationHint.value = false;
    stepIndex.value = Math.max(0, stepIndex.value - 1);
}

function nextFromName() {
    showValidationHint.value = true;
    if (!isNameValid.value) return;
    showValidationHint.value = false;
    stepIndex.value = Math.min(stepOrder.value.length - 1, stepIndex.value + 1);
}

function advanceAfterChoice() {
    // small delay for “selected” highlight to be visible before step transitions
    window.setTimeout(() => {
        stepIndex.value = Math.min(stepOrder.value.length - 1, stepIndex.value + 1);
    }, 220);
}

function resnapSection() {
    const el = sectionRef.value;
    if (!el) return;
    // Pin snap position to this section to avoid “bleeding” neighboring backgrounds on step transitions
    el.scrollIntoView({ block: "start", behavior: "auto" });
}

watch(
    stepIndex,
    async () => {
        await nextTick();
        resnapSection();
    },
    { flush: "post" },
);

function onAttendingPick(val) {
    attendingTouched.value = true;
    showValidationHint.value = false;
    if (form.attending !== val) {
        form.attending = val;
        return; // watcher sẽ auto-advance
    }
    // Nếu quay lại và click lại option đang chọn, value không đổi -> tự advance thủ công
    if (stepId.value === "attending" && (val === "yes" || val === "no")) {
        if (val === "no") form.ceremony_attendance = null;
        advanceAfterChoice();
    }
}

function onCeremonyPick(val) {
    showValidationHint.value = false;
    if (form.ceremony_attendance !== val) {
        form.ceremony_attendance = val;
        return; // watcher sẽ auto-advance
    }
    if (stepId.value === "ceremony") {
        advanceAfterChoice();
    }
}

watch(
    () => form.attending,
    (val, oldVal) => {
        if (val === oldVal) return;
        attendingTouched.value = true;
        if (stepId.value === "attending" && (val === "yes" || val === "no")) {
            showValidationHint.value = false;
            if (val === "no") form.ceremony_attendance = null;
            advanceAfterChoice();
        }
    },
);

watch(
    () => form.ceremony_attendance,
    (val, oldVal) => {
        if (val === oldVal) return;
        if (stepId.value === "ceremony" && (val === "send_bride" || val === "receive_bride" || val === "no")) {
            advanceAfterChoice();
        }
    },
);

async function submit() {
    if (submitted.value) return;
    message.value = "";
    loading.value = true;
    try {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        const payload = {
            name: form.name,
            attending: form.attending,
            ceremony_attendance:
                form.attending === "yes" ? form.ceremony_attendance : null,
        };
        await axios.post(
            "/api/rsvp",
            payload,
            {
                headers: token ? { "X-CSRF-TOKEN": token } : {},
            },
        );
        lastSubmitted.value = payload;
        messageSuccess.value = true;
        message.value = "Cảm ơn bạn đã xác nhận!";
        submitted.value = true;
        form.name = "";
        form.attending = null;
        form.ceremony_attendance = null;
        attendingTouched.value = false;
        stepIndex.value = 0;
    } catch (err) {
        messageSuccess.value = false;
        message.value =
            err.response?.data?.message || "Có lỗi xảy ra, vui lòng thử lại.";
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.rsvp-section {
    /* Prevent browser scroll anchoring from nudging the snap container on content changes */
    overflow-anchor: none;
}

.rsvp-step-enter-active,
.rsvp-step-leave-active {
    transition:
        opacity 280ms ease,
        transform 320ms cubic-bezier(0.2, 0.8, 0.2, 1);
}
.rsvp-step-enter-from {
    opacity: 0;
    transform: translateY(14px) scale(0.985);
}
.rsvp-step-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.985);
}
</style>
