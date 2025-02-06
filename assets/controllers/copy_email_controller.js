import { Controller } from '@hotwired/stimulus';
import { debounce } from '../helpers.js';

/**
 * @property {HTMLElement} buttonTarget
 * @property {Boolean} hasButtonTarget
 * @property {HTMLElement} copyTextTarget
 * @property {Boolean} hasCopyTextTarget
 * @property {Array<string>} buttonBlurredClasses
 * @property {Array<string>} copyTextHiddenClasses
 */
export default class extends Controller {
  static targets = ['button', 'copyText'];
  static classes = ['buttonBlurred', 'copyTextHidden'];

  connect() {
    if (!this.hasButtonTarget) {
      throw new Error('Missing "button" target');
    }
    if (!this.hasCopyTextTarget) {
      throw new Error('Missing "copyText" target');
    }

    this.buttonTarget.addEventListener('click', this.#onButtonClick.bind(this));
    window.addEventListener('resize', this.#onWindowResize.bind(this));

    this.#syncPositions();
  }

  disconnect() {
    this.buttonTarget.removeEventListener('click', this.#onButtonClick.bind(this));
  }

  #onButtonClick() {
    this.#syncPositions();

    this.buttonTarget.classList.add(...this.buttonBlurredClasses);
    this.copyTextTarget.classList.remove(...this.copyTextHiddenClasses);
    navigator.clipboard.writeText(this.buttonTarget.textContent.trim());

    setTimeout(() => this.#reset(), 1500);
  }

  #onWindowResize() {
    debounce(this.#syncPositions());
  }

  #syncPositions() {
    const buttonWidth = this.buttonTarget.getBoundingClientRect().right - this.buttonTarget.getBoundingClientRect().left;
    const copyTextWidth = this.copyTextTarget.getBoundingClientRect().right - this.copyTextTarget.getBoundingClientRect().left;
    const gap = (copyTextWidth - buttonWidth) / 2;

    this.copyTextTarget.style.left = `${this.buttonTarget.getBoundingClientRect().left - gap}px`;
  }

  #reset() {
    this.buttonTarget.classList.remove(...this.buttonBlurredClasses);
    this.copyTextTarget.classList.add(...this.copyTextHiddenClasses);
  }
}
