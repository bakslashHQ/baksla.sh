import { Controller } from '@hotwired/stimulus';

const WORD_INTERVAL_MS = 2400;
const BADGE_TILT_RANGE_DEG = 3;

/**
 * @property {HTMLElement} badgeTarget
 * @property {Array<String>} wordsValue
 */
export default class extends Controller {
  static targets = ['badge'];
  static values = { words: Array };

  connect() {
    if (!this.wordsValue.length) return;

    this.wordIndex = 0;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.badgeTarget.textContent = this.wordsValue[0];
      return;
    }

    this.renderWord();
    this.wordTimer = setInterval(() => {
      this.wordIndex = (this.wordIndex + 1) % this.wordsValue.length;
      this.renderWord();
    }, WORD_INTERVAL_MS);
  }

  disconnect() {
    clearInterval(this.wordTimer);
  }

  renderWord() {
    const tilt = (Math.random() * 2 - 1) * BADGE_TILT_RANGE_DEG;
    this.badgeTarget.style.setProperty('--tilt', `${tilt.toFixed(2)}deg`);

    this.badgeTarget.textContent = this.wordsValue[this.wordIndex];
    this.badgeTarget.classList.remove('animate-word-in');
    void this.badgeTarget.offsetWidth; // force reflow so the animation restarts
    this.badgeTarget.classList.add('animate-word-in');
  }
}
