import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} backgroundTarget
 * @property {HTMLElement} foregroundTarget
 * @property {Boolean} hasBackgroundTarget
 * @property {Boolean} hasForegroundTarget
 */
export default class extends Controller {
    static targets = ['background', 'foreground'];

    connect() {
      if (!this.hasBackgroundTarget) {
        throw new Error('Missing "background" target.');
      }
      if (!this.hasForegroundTarget) {
        throw new Error('Missing "foreground" target.');
      }

      this.#syncDimensions();
      window.addEventListener('resize', this.#onWindowResize.bind(this));
      window.addEventListener('scroll', this.#onWindowScroll.bind(this));
    }

    disconnect() {
      window.removeEventListener('resize', this.#onWindowResize.bind(this));
      window.removeEventListener('scroll', this.#onWindowScroll.bind(this));
    }

    #syncDimensions() {
      this.backgroundTarget.style.height = `${this.foregroundTarget.offsetHeight}px`;
      this.backgroundTarget.style.width = `${this.foregroundTarget.offsetWidth}px`;
    }

    #onWindowResize(){
      // TODO: use debounce?
      this.#syncDimensions();
    }

    #onWindowScroll() {
      const scrolledPercentage = window.scrollY / (document.documentElement.scrollHeight - document.documentElement.clientHeight)

      this.backgroundTarget.style.backgroundPositionY = `${-1 * (scrolledPercentage * 1000)}px`;
    }
}
