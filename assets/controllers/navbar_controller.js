import { Controller } from '@hotwired/stimulus';

const SCROLL_THRESHOLD_PX = 20;

export default class extends Controller {
  connect() {
    this.scrolled = null;
    this.onScroll = () => {
      const scrolled = window.scrollY > SCROLL_THRESHOLD_PX;
      if (scrolled === this.scrolled) return;
      this.scrolled = scrolled;
      this.element.classList.toggle('border-ink/10', scrolled);
      this.element.classList.toggle('border-transparent', !scrolled);
    };
    window.addEventListener('scroll', this.onScroll, { passive: true });
    this.onScroll();
  }

  disconnect() {
    window.removeEventListener('scroll', this.onScroll);
  }
}
