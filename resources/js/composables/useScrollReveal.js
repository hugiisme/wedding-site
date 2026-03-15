import { onMounted, nextTick } from 'vue';

const VISIBLE_CLASS = 'reveal-visible';
const SELECTOR = '.reveal';
const DEBUG = false; // bật debug log khi cần

function isInView(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

/** Kiểm tra el có trong vùng thấy của root (scroll container) hoặc viewport nếu root null */
function isInViewRoot(el, rootEl) {
  const rect = el.getBoundingClientRect();
  if (!rootEl) {
    return rect.top < window.innerHeight && rect.bottom > 0;
  }
  const rootRect = rootEl.getBoundingClientRect();
  return rect.top < rootRect.bottom && rect.bottom > rootRect.top;
}

export function useScrollReveal(options = {}) {
  const { threshold = 0.05, rootMargin = '80px', root = null } = options;

  onMounted(() => {
    nextTick(() => {
      // root có thể là ref: lúc setup ref = null, nên resolve lại khi mount
      const rootEl = typeof root === 'function' ? root() : root;

      if (DEBUG) {
        console.log('[ScrollReveal] init', {
          root: rootEl,
          rootTag: rootEl?.tagName,
          rootScrollHeight: rootEl?.scrollHeight,
          threshold,
          rootMargin,
        });
      }

      const allReveal = document.querySelectorAll(SELECTOR);
      const elements = Array.from(allReveal).filter((el) => !el.closest('[data-no-reveal]'));

      if (DEBUG) {
        console.log('[ScrollReveal] elements with .reveal:', elements.length);
        elements.forEach((el, i) => {
          const section = el.closest('[data-section]');
          const sectionIndex = section
            ? Array.from(document.querySelectorAll('[data-section]')).indexOf(section)
            : -1;
          console.log(`  [${i}]`, {
            tag: el.tagName,
            sectionIndex,
            text: el.textContent?.slice(0, 30),
          });
        });
      }

      if (elements.length === 0) {
        if (DEBUG) console.log('[ScrollReveal] no .reveal elements, skip observer');
        return;
      }

      // Bật/tắt theo intersection: vào view thì thêm class (reveal), ra khỏi view thì bỏ để lần sau vào lại còn hiệu ứng
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add(VISIBLE_CLASS);
            } else {
              entry.target.classList.remove(VISIBLE_CLASS);
            }
          });
        },
        { threshold, rootMargin: '80px 0px', root: null }
      );

      elements.forEach((el) => observer.observe(el));

      function markVisibleInRoot() {
        elements.forEach((el) => {
          const inView = isInViewRoot(el, rootEl);
          if (inView) {
            el.classList.add(VISIBLE_CLASS);
          } else {
            el.classList.remove(VISIBLE_CLASS);
          }
        });
      }

      // Fallback: ngay sau mount, đánh dấu mọi .reveal đang trong viewport (hoặc trong root nếu có)
      requestAnimationFrame(() => {
        requestAnimationFrame(markVisibleInRoot);
      });
      setTimeout(markVisibleInRoot, 300);

      // Khi scroll: gắn listener lên chính scroll container (main) để khi cuộn thì mark visible
      if (rootEl) {
        rootEl.addEventListener('scroll', markVisibleInRoot, { passive: true });
      } else {
        window.addEventListener('scroll', markVisibleInRoot, { passive: true });
      }
    });
  });
}
