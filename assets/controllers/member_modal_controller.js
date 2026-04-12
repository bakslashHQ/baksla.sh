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

    this._boundOpen = this.open.bind(this);
    this._boundCloseOnCancel = this.closeOnCancel.bind(this);

    this.openers = Array.from(document.querySelectorAll(`[data-open-member-modal="${this.memberValue}"]`));
    for (const opener of this.openers) {
      opener.addEventListener('click', this._boundOpen);
    }

    this.element.addEventListener('cancel', this._boundCloseOnCancel);
  }

  open() {
    // Save last focused element for restoration
    this._lastFocused = document.activeElement;

    // Prevent page scroll
    document.body.classList.add('overflow-hidden');

    // Prevent autofocus on first interactive element inside the modal,
    // because it's the buttons at the footer and it would scroll the modal to the bottom
    this.element.inert = true;

    this.element.showModal();

    this.modalTarget.classList.add('opacity-100', 'scale-100');
    this.modalTarget.classList.remove('opacity-0', 'translate-y-4', 'scale-80');

    // Re-enable focus - showModal() already traps focus natively in the dialog
    this.element.inert = false;

    // Focus first focusable element (optional, autofocus attribute can handle this)
    const focusable = this.element.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
      focusable.focus();
    }
  }

  close() {
    document.body.classList.remove('overflow-hidden');

    this.timeoutClose = setTimeout(() => {
      this.element.close();
      // Restore focus to last focused element
      if (this._lastFocused) {
        this._lastFocused.focus();
        this._lastFocused = null;
      }
    }, 150);

    this.modalTarget.classList.add('opacity-0', 'translate-y-4', 'scale-80');
    this.modalTarget.classList.remove('opacity-100', 'scale-100');
  }

  closeOnCancel(event) {
    event.preventDefault();
    this.close();
  }

  disconnect() {
    // Clean up overflow-hidden if modal still open
    document.body.classList.remove('overflow-hidden');

    clearTimeout(this.timeoutClose);

    for (const opener of this.openers) {
      opener.removeEventListener('click', this._boundOpen);
    }

    this.element.removeEventListener('cancel', this._boundCloseOnCancel);
  }
}
