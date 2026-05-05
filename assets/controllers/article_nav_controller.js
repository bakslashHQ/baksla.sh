import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} readoutTarget
 * @property {number} readingTimeValue
 */
export default class extends Controller {
  static targets = ['readout'];
  static values = { readingTime: Number };

  connect() {
    this.article = document.querySelector('[data-article-body]');
    this.pinThreshold = this.element.getBoundingClientRect().bottom;
    this.onScroll = this.onScroll.bind(this);
    window.addEventListener('scroll', this.onScroll, { passive: true });
    this.update();
  }

  disconnect() {
    window.removeEventListener('scroll', this.onScroll);
    if (this.scheduledFrame) cancelAnimationFrame(this.scheduledFrame);
  }

  onScroll() {
    if (this.scheduledFrame) return;
    this.scheduledFrame = requestAnimationFrame(() => {
      this.scheduledFrame = null;
      this.update();
    });
  }

  update() {
    if (!this.article) return;

    const rect = this.article.getBoundingClientRect();
    const pinned = rect.top < this.pinThreshold;
    this.element.toggleAttribute('inert', !pinned);
    this.element.classList.toggle('opacity-0', !pinned);
    this.element.classList.toggle('-translate-y-3', !pinned);

    const start = rect.top + window.scrollY;
    const end = Math.max(rect.bottom + window.scrollY - window.innerHeight, start + 1);
    const pct = Math.min(100, Math.max(0, ((window.scrollY - start) / (end - start)) * 100));
    const minutes = this.readingTimeValue || 1;
    this.readoutTarget.textContent = `${Math.round(pct)}% · ${minutes} min`;
  }

  scrollToTop() {
    if (this.article) {
      this.article.focus({ preventScroll: true });
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}
