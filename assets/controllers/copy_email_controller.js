import {Controller} from '@hotwired/stimulus';

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
  static classes = ['buttonBlurred', 'copyTextHidden']

  connect() {
    if (!this.hasButtonTarget) {
      throw new Error('Missing "button" target');
    }
    if (!this.hasCopyTextTarget) {
      throw new Error('Missing "copyText" target');
    }

    this.buttonTarget.addEventListener('click', this.#onButtonClick.bind(this))
  }

  disconnect() {
    this.buttonTarget.removeEventListener('click', this.#onButtonClick.bind(this))
  }

  #onButtonClick() {
    this.buttonTarget.classList.add(...this.buttonBlurredClasses);
    this.copyTextTarget.classList.remove(...this.copyTextHiddenClasses);
    navigator.clipboard.writeText(this.buttonTarget.textContent.trim());

    setTimeout(() => this.#reset(), 1500);
  }

  #reset() {
    this.buttonTarget.classList.remove(...this.buttonBlurredClasses);
    this.copyTextTarget.classList.add(...this.copyTextHiddenClasses);
  }
}
