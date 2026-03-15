import { onMounted } from 'vue';

const VISIBLE_CLASS = 'reveal-visible';
const SELECTOR = '.reveal';

export function useScrollReveal(options = {}) {
  const { threshold = 0.1, rootMargin = '50px', root = null } = options;

  onMounted(() => {
    const rootEl = typeof root === 'function' ? root() : root;
    const elements = document.querySelectorAll(SELECTOR);
    if (elements.length === 0) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add(VISIBLE_CLASS);
          }
        });
      },
      { threshold, rootMargin, root: rootEl || undefined }
    );

    elements.forEach((el) => observer.observe(el));

    return () => elements.forEach((el) => observer.unobserve(el));
  });
}
