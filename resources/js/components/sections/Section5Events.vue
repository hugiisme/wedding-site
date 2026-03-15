<template>
  <section
    data-section
    class="flex min-h-screen w-full shrink-0 snap-start snap-always flex-col bg-wedding-cream px-4 py-12 md:px-8 md:py-16"
  >
    <div class="mx-auto w-full max-w-5xl">
      <p class="reveal text-center font-sans text-wedding-brown md:text-lg">
        Khoảnh khắc đặc biệt này chỉ thực sự trọn vẹn khi có bạn cùng sẻ chia.
        Gia đình đã dành trọn tâm huyết chuẩn bị từng chi tiết nhỏ, với mong muốn mang lại không gian tiệc trọn vẹn nhất để tiếp đón những người thân thương.
        Mời bạn cùng điểm qua các thông tin chi tiết cho buổi tiệc sắp tới tại đây.
      </p>
      <div class="reveal mt-10 flex flex-wrap gap-4 justify-center">
        <button
          v-for="(ev, i) in events"
          :key="ev.id"
          type="button"
          :class="[
            'flex flex-col items-center rounded-xl border-2 px-6 py-4 transition-all md:min-w-[140px]',
            selectedId === ev.id
              ? 'border-wedding-gold bg-wedding-sage/20 text-wedding-brown'
              : 'border-wedding-sage/50 bg-white/80 text-wedding-brown hover:border-wedding-gold/70'
          ]"
          @click="selectedId = ev.id"
        >
          <span class="text-2xl">{{ ev.icon }}</span>
          <span class="mt-2 font-semibold">{{ ev.name }}</span>
          <span class="mt-1 text-sm opacity-90">{{ ev.date }}</span>
          <span class="text-sm opacity-90">{{ ev.time }}</span>
        </button>
      </div>
      <div v-if="selected" class="reveal mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-wedding-sage/50 bg-white p-6 shadow-sm">
          <h3 class="font-serif text-xl font-semibold text-wedding-gold">{{ selected.detail.title }}</h3>
          <p class="mt-2 text-wedding-brown">{{ selected.detail.content }}</p>
          <p class="mt-2 text-sm text-wedding-brown/80">{{ selected.detail.time }}</p>
          <p class="mt-1 font-medium text-wedding-brown">{{ selected.detail.placeName }}</p>
          <p class="text-sm text-wedding-brown/80">{{ selected.detail.address }}</p>
          <a
            :href="selected.detail.mapUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-wedding-gold px-4 py-2 text-sm font-medium text-white transition hover:opacity-90"
          >
            Mở Google Map
          </a>
        </div>
        <div class="h-64 overflow-hidden rounded-xl border border-wedding-sage/50 md:h-80">
          <iframe
            v-if="selected.detail.embedUrl"
            :src="selected.detail.embedUrl"
            class="h-full w-full"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Bản đồ"
          />
          <div v-else class="flex h-full items-center justify-center bg-wedding-sage/20 text-wedding-brown/60">
            Chọn sự kiện để xem bản đồ
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';

const events = ref([
  {
    id: 'le',
    name: 'Lễ thành hôn',
    date: '03/04/2026',
    time: '08:00',
    icon: '💒',
    detail: {
      title: 'Lễ thành hôn',
      content: 'Lễ cưới chính thức tại nhà thờ / tư gia.',
      time: '08:00 - 10:00',
      placeName: 'Địa điểm lễ',
      address: 'Địa chỉ sẽ cập nhật',
      mapUrl: 'https://www.google.com/maps',
      embedUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59587.977654479!2d105.845526!3d21.028511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab5e9a7b3e3d%3A0x3c3b3b3b3b3b3b3b!2sHanoi!5e0!3m2!1sen!2s!4v1234567890'
    }
  },
  {
    id: 'tiec',
    name: 'Tiệc chiêu đãi',
    date: '03/04/2026',
    time: '11:30',
    icon: '🍽️',
    detail: {
      title: 'Tiệc chiêu đãi',
      content: 'Tiệc cưới tại hội trường.',
      time: '11:30 - 14:30',
      placeName: 'Trung tâm tiệc cưới',
      address: 'Địa chỉ sẽ cập nhật',
      mapUrl: 'https://www.google.com/maps',
      embedUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59587.977654479!2d105.845526!3d21.028511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab5e9a7b3e3d%3A0x3c3b3b3b3b3b3b3b!2sHanoi!5e0!3m2!1sen!2s!4v1234567890'
    }
  },
  {
    id: 'giatien',
    name: 'Lễ gia tiên',
    date: '03/04/2026',
    time: '07:00',
    icon: '🏠',
    detail: {
      title: 'Lễ gia tiên',
      content: 'Lễ gia tiên tổ chức cùng ngày.',
      time: '07:00 - 08:00',
      placeName: 'Nhà gia tiên',
      address: 'Địa chỉ sẽ cập nhật',
      mapUrl: 'https://www.google.com/maps',
      embedUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59587.977654479!2d105.845526!3d21.028511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab5e9a7b3e3d%3A0x3c3b3b3b3b3b3b3b!2sHanoi!5e0!3m2!1sen!2s!4v1234567890'
    }
  }
]);

const selectedId = ref(events.value[0].id);
const selected = computed(() => events.value.find((e) => e.id === selectedId.value));
</script>
