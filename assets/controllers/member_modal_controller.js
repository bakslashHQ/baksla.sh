import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} modalTarget
 * @property {String} memberValue
 */
export default class extends Controller {
  static targets = ['modal'];
  static values = {
    member: String,
  };

  connect() {
    if (!this.hasModalTarget) {
      throw new Error('Missing "modal" target.');
    }

    this.openers = Array.from(document.querySelectorAll(`[data-open-member-modal="${this.memberValue}"]`));
    for (const opener of this.openers) {
      opener.addEventListener('click', this.open.bind(this));
    }

    this.element.addEventListener('cancel', this.closeOnEscape.bind(this));
    document.addEventListener('keydown', this.closeOnEscape.bind(this));
  }

  open() {
    // Prevent page scroll
    document.body.classList.add('overflow-hidden');

    // Prevent focus on the first interactive element inside the modal,
    // because it's the buttons at the footer and it made the modal scrolled to the bottom
    this.element.inert = true;

    this.element.showModal();

    this.timeoutOpen = setTimeout(() => {
      this.modalTarget.classList.add('opacity-100', 'scale-100');
      this.modalTarget.classList.remove('opacity-0', 'translate-y-4', 'scale-80');
      this.element.inert = false;
    }, 100);
  }

  close() {
    document.body.classList.remove('overflow-hidden');

    this.timeoutClose = setTimeout(() => {
      this.element.close();
    }, 300);

    this.modalTarget.classList.add('opacity-0', 'translate-y-4', 'scale-80');
    this.modalTarget.classList.remove('opacity-100', 'scale-100');
  }

  closeOnEscape(event) {
    if (event.key === 'Escape') {
      event.preventDefault();
      this.close();
    }
  }

  disconnect() {
    clearTimeout(this.timeoutOpen);
    clearTimeout(this.timeoutClose);

    for (const opener of this.openers) {
      opener.removeEventListener('click', this.open.bind(this));
    }

    this.element.removeEventListener('cancel', this.closeOnEscape.bind(this));
    document.removeEventListener('keydown', this.closeOnEscape.bind(this));
  }
}
