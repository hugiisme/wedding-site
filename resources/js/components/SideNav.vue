<template>
  <nav
    class="fixed bottom-6 left-1/2 z-50 flex -translate-x-1/2 flex-row items-center gap-2 md:bottom-auto md:right-6 md:left-auto md:top-1/2 md:-translate-y-1/2 md:translate-x-0 md:flex-col md:gap-3"
    aria-label="Điều hướng theo section"
  >
    <button
      v-for="(_, i) in total"
      :key="i"
      type="button"
      :aria-label="`Đi tới section ${i + 1}`"
      :class="[
        'shrink-0 rounded-full transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-wedding-brown focus-visible:ring-offset-2 focus-visible:ring-offset-transparent',
        i === activeIndex
          ? 'h-8 w-1.5 border-0 border-transparent bg-wedding-gold-warm shadow-[0_0_0_1px_rgba(74,63,53,0.2)] md:h-10 md:w-2'
          : 'h-2.5 w-2.5 border-2 border-wedding-brown bg-wedding-cream shadow-[0_0_0_1px_rgba(74,63,53,0.15)] hover:border-wedding-brown hover:bg-wedding-cream md:h-3 md:w-3'
      ]"
      @click="onClick(i)"
    />
  </nav>
</template>

<script setup>
defineProps({
  activeIndex: { type: Number, default: 0 },
  total: { type: Number, default: 8 }
});
const emit = defineEmits(['go']);

function onClick(i) {
  emit('go', i);
  // Remove focus after click so scroll doesn't leave "active-looking" ring on previous dot
  requestAnimationFrame(() => {
    const el = document.activeElement;
    if (el && el.blur) el.blur();
  });
}
</script>
