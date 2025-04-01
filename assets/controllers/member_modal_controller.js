import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} modalTarget
 * @property {HTMLElement} backdropTarget
 * @property {String} memberValue
 */
export default class extends Controller {
  static targets = ['modal', 'backdrop'];
  static values = {
    member: String,
  };

  connect() {
    if (!this.hasModalTarget) {
      throw new Error('Missing "modal" target.');
    }
    if (!this.hasBackdropTarget) {
      throw new Error('Missing "backdrop" target.');
    }

    this.openers = Array.from(document.querySelectorAll(`[data-open-member-modal="${this.memberValue}"]`));
    for (const opener of this.openers) {
      opener.addEventListener('click', this.open.bind(this));
    }

    document.addEventListener('keydown', this.closeOnEscape.bind(this));
  }

  open() {
    document.getElementById('modal-background').classList.add('fixed', 'overflow-y-hidden');

    this.element.classList.remove('hidden');

    this.timeoutOpen = setTimeout(() => {
      this.backdropTarget.classList.add('opacity-100');
      this.backdropTarget.classList.remove('opacity-0');

      this.modalTarget.classList.add('opacity-100', 'translate-y-0', 'scale-100');
      this.modalTarget.classList.remove('opacity-0', 'translate-y-4', 'scale-80');
    }, 100);
  }

  close() {
    document.getElementById('modal-background').classList.remove('fixed', 'overflow-y-hidden');

    this.timeoutClose = setTimeout(() => {
      this.element.classList.add('hidden');
    }, 300);

    this.backdropTarget.classList.add('opacity-0');
    this.backdropTarget.classList.remove('opacity-100');

    this.modalTarget.classList.add('opacity-0', 'translate-y-4', 'scale-80');
    this.modalTarget.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
  }

  closeOnEscape(event) {
    if (event.key !== 'Escape') {
      return;
    }

    this.close();
  }

  disconnect() {
    clearTimeout(this.timeoutOpen);
    clearTimeout(this.timeoutClose);

    for (const opener of this.openers) {
      opener.removeEventListener('click', this.open.bind(this));
    }

    document.removeEventListener('keydown', this.closeOnEscape.bind(this));
  }
}
